<?php

// Test configuratie
define('TESTING', true);

// Set paths
$appPath = dirname(__DIR__) . '/app';

// Mock session voor tests - zonder session_start() 
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// NIET require.php gebruiken omdat dat Core class start
// In plaats daarvan handmatig de benodigde bestanden laden

// Laad config
if (file_exists($appPath . '/config/config.php')) {
    require_once $appPath . '/config/config.php';
}

// Laad libraries (maar NIET Core.php)
if (file_exists($appPath . '/libraries/Database.php')) {
    require_once $appPath . '/libraries/Database.php';
}

if (file_exists($appPath . '/libraries/BaseController.php')) {
    require_once $appPath . '/libraries/BaseController.php';
}

// Laad models
if (file_exists($appPath . '/models/MagazijnvoorraadModel.php')) {
    require_once $appPath . '/models/MagazijnvoorraadModel.php';
}

// Laad controllers
if (file_exists($appPath . '/controllers/Magazijnvoorraad.php')) {
    require_once $appPath . '/controllers/Magazijnvoorraad.php';
}

// Laad mocks
require_once __DIR__ . '/mocks/MockDatabase.php';