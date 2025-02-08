<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vuelos extends CI_Controller
{

    public function index()
    {
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('vuelos/vuelos');
        $this->load->view('footer');
    }

}
