<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inicio extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "VIAGGI API Vr 1.0";
    }
}
