<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Paquetes extends CI_Controller
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
        $this->load->view('paquetes/paquetes', ["recursos" => $recursos]);
        $this->load->view('footer');
    }

    public function ver($slug)
    {

        //Información de la actividad
        $url = BASE_URL_API . 'paquetes/paquete_slug/' . $slug;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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

        $paquete = $result->data->paquete;

        $data_header = [
            'meta' => true,
            'title' => $paquete->servicio,
            'description' => $paquete->meta_descripcion,
            'tags' => $paquete->tags,
            'img' => $paquete->img,
            'url' => base_url() . 'paquetes/ver/' . $slug,
        ];

        $data_body = [
            'paquete' => $paquete,
        ];

        $this->load->view('header', $data_header);
        $this->load->view('menu', ['tipo' => 1]);
        $this->load->view('paquetes/ver', $data_body);
        $this->load->view('footer');
    }
}
