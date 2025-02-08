<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Paquetes extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_usuarios');
        $this->load->model('Model_servicios');
        $this->load->model('Model_paquetes');
    }

    public function paquetes_get()
    {

        $data_input = $this->input->get(null, true);

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 6;
            $offset = ($page - 1) * $per_page;
        }

        $paquetes = [];

        $query_paquetes = $this->Model_servicios->query_paquetes(1, null, null, true, $offset, $per_page);

        if ($query_paquetes->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_paquetes->result() as $info) {

                $destino = [];
                if ($info->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $paquetes[] = [
                    'num' => $num,
                    'id_servicio' => $info->id_servicio,
                    'id_paquete' => $info->id_paquete,
                    'destino' => $destino,
                    'servicio' => $info->servicio,
                    'servicio_slug' => $info->servicio_slug,
                    'resumen' => $info->resumen,
                    'descripcion' => $info->descripcion,
                    'estado_servicio' => $info->estado_servicio,
                    'valor_desde' => $info->valor_desde,
                    'img_principal' => $img_principal,
                    'destacado' => $info->destacado,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_paquetes(1, null, null, true);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'paquetes' => $paquetes,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);
    }

    public function destacados_get()
    {

        $data_input = $this->input->get(null, true);

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 6;
            $offset = ($page - 1) * $per_page;
        }

        $paquetes = [];

        $query_destacados = $this->Model_servicios->query_paquetes_destacados(1, $offset, $per_page);

        if ($query_destacados->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_destacados->result() as $info_paquete) {

                $destino = [];
                if ($info_paquete->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info_paquete->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info_paquete->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $paquetes[] = [
                    'num' => $num,
                    'id_servicio' => $info_paquete->id_servicio,
                    'id_paquete' => $info_paquete->id_paquete,
                    'destino' => $destino,
                    'servicio' => $info_paquete->servicio,
                    'servicio_slug' => $info_paquete->servicio_slug,
                    'resumen' => $info_paquete->resumen,
                    'descripcion' => $info_paquete->descripcion,
                    'estado_servicio' => $info_paquete->estado_servicio,
                    'valor_desde' => $info_paquete->valor_desde,
                    'img_principal' => $img_principal,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_paquetes_destacados(1);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'paquetes' => $paquetes,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);
    }

    public function paquete_slug_get($slug = null)
    {

        $query_paquete = $this->Model_servicios->query_paquete_slug($slug);

        if ($query_paquete->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info = $query_paquete->row();

        $destino = [];
        if ($info->id_destino != null) {
            $info_destino = $this->Model_servicios->query_destino($info->id_destino)->row();
            $destino = [
                'id_destino' => $info_destino->id_destino,
                'id_ciudad' => $info_destino->id_ciudad,
                'destino' => $info_destino->destino,
            ];
        }

        $galeria = [];
        $query_galeria = $this->Model_servicios->query_galeria($info->id_servicio);

        if ($query_galeria->num_rows() != 0) {

            foreach ($query_galeria->result() as $info_galeria) {

                switch ($info_galeria->tipo) {
                    case 0:
                        $url = base_url() . $info_galeria->img_galeria;
                        if (ENVIRONMENT == 'development') {
                            $url = URL_API_PRD . $info_galeria->img_galeria;
                        }
                        break;
                    case 1:
                        $url = "https://www.youtube.com/embed/" . $info_galeria->img_galeria;
                        break;
                }

                $galeria[] = [
                    'id_galeria' => $info_galeria->id_galeria,
                    'url' => $url,
                    'principal' => $info_galeria->principal,
                    'tipo' => $info_galeria->tipo,
                ];
            }

        }

        $img_principal = null;
        $query_img_principal = $this->Model_servicios->query_img_principal($info->id_servicio);
        if ($query_img_principal->num_rows() != 0) {
            $img_principal = base_url() . $query_img_principal->row()->img_galeria;
        }

        $img = null;
        if ($info->img != null) {
            $img = base_url() . $info->img;
            if (ENVIRONMENT == 'development') {
                $img = URL_API_DEV . $info->img;
            }
        }

        $paquete = [
            'id_servicio' => $info->id_servicio,
            'id_paquete' => $info->id_paquete,
            'destino' => $destino,
            'servicio' => $info->servicio,
            'resumen' => $info->resumen,
            'descripcion' => $info->descripcion,
            'valor_desde' => $info->valor_desde,
            'valor_desde_c' => formato_moneda($info->valor_desde),
            'estado_servicio' => $info->estado_servicio,
            'img' => $img,
            'img_principal' => $img_principal,
            'galeria' => $galeria,
            'meta_descripcion' => $info->meta_descripcion,
            'tags' => $info->tags,
        ];

        $response = [
            'status' => true,
            'data' => [
                'paquete' => $paquete,
            ],
        ];

        $this->response($response, 200);

    }

}
