<?php

namespace Lemming\MailLogger;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class Mailer extends \TYPO3\CMS\Core\Mail\Mailer
{
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        parent::send($message, $envelope);

        $mail = $this->getSentMessage();
        $recipients = [];
        foreach($mail->getEnvelope()->getRecipients() as $recipient) {
            $recipients[] = $recipient->toString();
        }
        $context = GeneralUtility::makeInstance(Context::class);

        $pageId = $this->getTypoScriptFrontendController() ? $this->getTypoScriptFrontendController()->id : null;

        $logData = [
            'sender' => $mail->getEnvelope()->getSender()->toString(),
            'recipients' => implode(', ', $recipients),
            'subject' => $mail->getOriginalMessage()->getHeaders()->get('subject')->getValue(),
            'messageId' => $mail->getMessageId(),
            'pageId' => $pageId
        ];
        if (VersionNumberUtility::getCurrentTypo3Version() < 11) {
            $logData = serialize($logData);
            $details = 'Sender: %s Recipients: %s Subject: %s MessageID %s' . ($pageId ? ' Page: %s' : '');
        } else {
            $logData = json_encode($logData);
            $details = 'Sender: {sender} Recipients: {recipients} Subject: {subject} MessageID: {messageId}' . ($pageId ? ' Page: {pageId}' : '');
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_log');
        $connection->insert(
            'sys_log',
            [
                'channel' => 'mail',
                'level' => LogLevel::INFO,
                'details' => $details,
                'log_data' => $logData,
                'userid' => $context->getPropertyFromAspect('backend.user', 'id'),
                'IP' => GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                'tstamp' => $context->getAspect('date')->get('timestamp'),
                'event_pid' => $pageId ?? -1
            ],
            [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            ]
        );
    }

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}