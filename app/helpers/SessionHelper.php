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

    // Flash message setter/getter
    public static function flash($name = '', $message = '') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($name)) {
            if (!empty($message)) {
                $_SESSION[$name] = $message;
            } elseif (!empty($_SESSION[$name])) {
                echo '<div class="alert alert-success">' . $_SESSION[$name] . '</div>';
                unset($_SESSION[$name]);
            }
        }
    }
}
