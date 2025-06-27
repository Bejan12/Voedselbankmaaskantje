<?php
// Prevent output during tests
ob_start();

// Test configuratie
define('TESTING', true);

// Mock session voor tests
if (!session_id()) {
    session_start();
}
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// Laad het complete framework via require.php (zoals in index.php)
require_once dirname(__DIR__) . '/app/require.php';

// Laad mocks
require_once __DIR__ . '/mocks/MockDatabase.php';

// Clean output buffer
ob_end_clean();