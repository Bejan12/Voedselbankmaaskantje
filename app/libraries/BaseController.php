<?php

/**
 * Basis controller class waar alle controllers van erven
 */
class BaseController
{
    /**
     * Laad een model
     */
    public function model($model)
    {
        // Require model file
        require_once '../app/models/' . $model . '.php';
        
        // Instantiate model
        return new $model();
    }

    /**
     * Laad een view
     */
    public function view($view, $data = [])
    {
        // Don't check authentication for login/register pages
        $publicViews = ['accounts/login', 'accounts/register', 'homepages/index'];
        if (!in_array($view, $publicViews)) {
            $this->checkAuth();
        }
        
        // Check of view file bestaat
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // View bestaat niet
            die('View bestaat niet');
        }
    }
    
    /**
     * Check if the current page requires authentication
     */
    protected function checkAuth()
    {
        // Include the session helper if not already included
        if (!class_exists('SessionHelper')) {
            require_once '../app/helpers/SessionHelper.php';
        }
        
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . URLROOT . '/accounts/login');
            exit();
        }
    }
}