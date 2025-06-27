<?php

class Homepages extends BaseController
{

    public function index()
    {
        $data = [
            'title' => 'Welkom bij de Voedselbank'
        ];
        
        $this->view('homepages/index', $data);
    }
}