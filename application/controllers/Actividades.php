<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Actividades extends CI_Controller
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
        $this->load->view('menu');
        $this->load->view('actividades/actividades', $data_body);
        $this->load->view('footer');

    }

    public function destino($id_ciudad = null, $slug = null)
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



        if ($id_ciudad == null) {
            show_404();
        }

        $data_input = $this->input->get(null, true);

        $data_body = [
            'id_ciudad' => $id_ciudad,
            'recursos' => $recursos,
        ];

        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('actividades/destino', $data_body);
        $this->load->view('footer');
    }

    public function ver($slug)
    {

        //Información de la actividad
        $url = BASE_URL_API . 'actividades/actividad_slug/' . $slug;
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

        $actividad = $result->data->actividad;

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

        $data_header = [
            'meta' => true,
            'title' => $actividad->servicio,
            'description' => $actividad->meta_descripcion,
            'tags' => $actividad->tags,
            'img' => $actividad->img_principal,
            'url' => base_url() . 'actividades/ver/' . $slug,
        ];

        $data_body = [
            'actividad' => $actividad,
            'recursos' => $recursos
        ];

        $this->load->view('header', $data_header);
        $this->load->view('menu', ['tipo' => 1]);
        $this->load->view('actividades/ver', $data_body);
        $this->load->view('footer');
    }

    public function tipo($slug)
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
        $this->load->view('actividades/actividades', ['slug' => $slug, 'recursos' => $recursos]);
        $this->load->view('footer');

    }

}
