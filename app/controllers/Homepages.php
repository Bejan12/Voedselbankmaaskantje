<?php

class Homepages extends BaseController
{

    public function index($firstname = NULL, $infix = NULL, $lastname = NULL)
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/accounts/login');
            exit();
        }




        $data = [
            'title' => 'Homepagina',
        ];


        $this->view('homepages/index', $data);
    }

}    