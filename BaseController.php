<?php

/**
 * Basis controller class waar alle controllers van erven
 */
class BaseController
{
    /**
     * Hier maken we een nieuw model object aan en geven deze 
     * terug aan de controller
     */
    public function model($model)
    {
        // Check if APPROOT is defined, otherwise use relative path
        if (defined('APPROOT')) {
            require_once APPROOT . '/models/' . $model . '.php';
        } else {
            require_once '../app/models/' . $model . '.php';
        }
        
        // Instantiate model
        return new $model();
    }

    /**
     * De view method laadt het view-bestand en geeft informatie
     * mee aan de view met het $data-array
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
