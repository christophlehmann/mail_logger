<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Mail\Mailer::class] = [
    'className' => \Lemming\MailLogger\Mailer::class
];
