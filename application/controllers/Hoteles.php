<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hoteles extends CI_Controller
{

    public function index()
    {
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('hoteles/hoteles');
        $this->load->view('footer');
    }

}
