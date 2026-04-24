<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$additionalColumns = [
    'soundcloud_thumbnail_url' => [
        'exclude' => true,
        'label' => 'LLL:EXT:ayacoo_soundcloud/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.soundcloud_thumbnail_url',
        'config' => [
            'type' => 'link',
            'allowedTypes' => ['url'],
            'readOnly' => true,
            'size' => 40,
        ],
        'displayCond' => 'USER:Ayacoo\\AyacooSoundcloud\\Tca\\DisplayCond\\IsSoundcloud->match',
    ],
    'soundcloud_html' => [
        'exclude' => true,
        'label' => 'LLL:EXT:ayacoo_soundcloud/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.soundcloud_html',
        'config' => [
            'type' => 'text',
            'dbType' => 'text',
            'cols' => 40,
            'rows' => 4,
            'readOnly' => true,
        ],
        'displayCond' => 'USER:Ayacoo\\AyacooSoundcloud\\Tca\\DisplayCond\\IsSoundcloud->match',
    ],
    'soundcloud_author_url' => [
        'exclude' => true,
        'label' => 'LLL:EXT:ayacoo_soundcloud/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.soundcloud_author_url',
        'config' => [
            'type' => 'link',
            'allowedTypes' => ['url'],
            'default' => '',
            'readOnly' => true,
            'size' => 40,
        ],
        'displayCond' => 'USER:Ayacoo\\AyacooSoundcloud\\Tca\\DisplayCond\\IsSoundcloud->match',
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $additionalColumns);
ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_metadata',
    '--div--;LLL:EXT:ayacoo_soundcloud/Resources/Private/Language/locallang_db.xlf:tab.soundcloud, soundcloud_thumbnail_url, soundcloud_html, soundcloud_author_url'
);
