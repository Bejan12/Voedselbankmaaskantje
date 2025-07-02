<?php

class SessionHelper {
    // Check if user is logged in
    public static function isLoggedIn() {
        // Start session at the very beginning if not started
        if (session_status() === PHP_SESSION_NONE) {
            // Start output buffering to prevent headers already sent
            if (!ob_get_level()) {
                ob_start();
            }
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Redirect if not logged in
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            // Clean any output buffer before redirect
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Location: ' . URLROOT . 'accounts/login');
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
            if (!ob_get_level()) {
                ob_start();
            }
            session_start();
        }
        if (!empty($name)) {
            if (!empty($message)) {
                $_SESSION[$name] = $message;
            } elseif (!empty($_SESSION[$name])) {
                $type = isset($_SESSION[$name.'_type']) ? $_SESSION[$name.'_type'] : 'success';
                $class = ($type === 'error' || $type === 'danger') ? 'alert-danger' : 'alert-success';

                echo '<div class="alert '.$class.'">'.$_SESSION[$name].'</div>';

                unset($_SESSION[$name]);
                unset($_SESSION[$name.'_type']);
            }
        }
    }

    // Add type parameter to set message with a type
    public static function setFlash($name, $message, $type = 'success') {
        if (session_status() === PHP_SESSION_NONE) {
            if (!ob_get_level()) {
                ob_start();
            }
            session_start();
        }
        $_SESSION[$name] = $message;
        $_SESSION[$name.'_type'] = $type;
    }
}
