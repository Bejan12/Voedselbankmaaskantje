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
        // Check of view file bestaat
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // View bestaat niet
            die('View bestaat niet');
        }
    }
}