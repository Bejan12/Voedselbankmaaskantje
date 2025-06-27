<?php

class SessionHelper {
    // Check if user is logged in
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Redirect if not logged in
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . URLROOT . '/accounts/login');
            exit();
        }
    }

    // List of pages that don't require login
    public static function publicPages() {
        return [
            'homepages/index',
            'accounts/login',
            'accounts/register'
        ];
    }
}
