<?php

use Ayacoo\AyacooSoundcloud\Controller\OnlineMediaUpdateController;

return [
    'ayacoo_soundcloud_online_media_updater' => [
        'path' => '/ayacoo-soundcloud/update',
        'target' => OnlineMediaUpdateController::class . '::updateAction',
    ],
];
