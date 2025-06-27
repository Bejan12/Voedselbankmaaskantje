<?php

class Homepages extends BaseController
{

    public function index($firstname = NULL, $infix = NULL, $lastname = NULL)
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in, if not redirect to login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/accounts/login');
            exit();
        }

        /**
         * Het $data-array geeft informatie mee aan de view-pagina
         */


        $data = [
            'title' => 'Homepagina',
        ];

        /**
         * Met de view-method uit de BaseController-class wordt de view
         * aangeroepen met de informatie uit het $data-array
         */
        $this->view('homepages/index', $data);
    }

    /**
     * De optellen-method berekent de som van twee getallen
     * We gebruiken deze method voor een unittest
     */
}