<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

ExtensionManagementUtility::addStaticFile('ayacoo_soundcloud', 'Configuration/TypoScript', 'Ayacoo Soundcloud');
