<?php
$opts = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
];
copy('https://getcomposer.org/installer', 'composer-setup.php', stream_context_create($opts));
