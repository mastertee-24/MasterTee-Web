<?php

return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => getenv('MASTERTEE_SMTP_USER'),
    'password' => getenv('MASTERTEE_SMTP_PASSWORD'),
    'from_email' => 'masterteetech@gmail.com',
    'from_name' => 'MasterTee Tech Communication',
    'notification_email' => 'masterteetech@gmail.com'
];