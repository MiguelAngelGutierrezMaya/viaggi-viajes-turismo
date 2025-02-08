<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function index()
    {

        #Recursos
        $url = BASE_URL_API . 'admin/recursos?estado=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json",
                "x-api-key: " . API_KEY,
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($data);

        if (!$result->status) {
            exit;
        }

        $recursos = $result->data->recursos;

        $data_body = [
            'recursos' => $recursos,
        ];

        $this->load->view('header');
        $this->load->view('home', $data_body);
        $this->load->view('footer');
    }

    public function politica_privacidad_datos()
    {

        #Recursos
        $url = BASE_URL_API . 'admin/recursos?estado=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json",
                "x-api-key: " . API_KEY,
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($data);

        if (!$result->status) {
            exit;
        }

        $recursos = $result->data->recursos;

        $data_body = [
            'recursos' => $recursos,
        ];

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('pol_pri_dat', $data_body);
        $this->load->view('footer');

    }
}
