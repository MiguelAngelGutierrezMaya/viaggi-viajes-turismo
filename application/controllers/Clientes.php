<?php
defined('BASEPATH') or exit('No direct script access allowed');
//Include Configuration File
//Include Google Client Library for PHP autoload file

class Clientes extends CI_Controller
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

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('clientes/clientes', ["recursos" => $recursos]);
        $this->load->view('footer');
    }

    public function reserva($id_reserva = null)
    {

        if ($id_reserva == null) {
            redirect("home");
        }

        //Información de la actividad
        $url = BASE_URL_API . 'clientes/reserva/' . $id_reserva;
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

        $reserva = $result->data;

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

        #Parámetros
        $url_parametros = BASE_URL_API . 'admin/parametros';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_parametros);
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
        $data_parametros = curl_exec($ch);
        curl_close($ch);

        $result_parametros = json_decode($data_parametros);

        if (!$result_parametros->status) {
            exit;
        }

        $parametros = $result_parametros->data;

        $data_body = [
            'id_reserva' => $id_reserva,
            'reserva' => $reserva,
            'parametros' => $parametros->parametros,
            'recursos' => $recursos
        ];

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('clientes/reserva', $data_body);
        $this->load->view('footer');

    }

    public function recovery($token = null)
    {
        if ($token == null) {
            redirect("home");
        }

        $data_body = [
            'token' => $token,
        ];

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('clientes/recovery', $data_body);
        $this->load->view('footer');

    }

}
