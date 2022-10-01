<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mail Logger',
    'description' => 'Log mails to sys_log table',
    'category' => 'misc',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Christoph Lehmann',
    'author_email' => 'post@christophlehmann.eu',
    'author_company' => '',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99'
        ],
    ],
    'autoload' => [
        'classmap' => ['Classes'],
    ]
];
