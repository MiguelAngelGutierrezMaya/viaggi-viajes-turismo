<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Destinos extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_destinos');
    }

    public function ciudades_get()
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $query_ciudades = $this->Model_destinos->query_ciudades($search);

        $response = [
            'status' => true,
            'data' => [
                'ciudades' => $query_ciudades->result(),
            ],
        ];

        $this->response($response, 200);

    }

    public function ciudad_get($id_ciudad)
    {
        $query_ciudad = $this->Model_destinos->query_destino_ciudad($id_ciudad);

        $response = [
            'status' => true,
            'data' => [
                'num_rows' => $query_ciudad->num_rows(),
                'ciudad' => $query_ciudad->row(),
            ],
        ];

        $this->response($response, 200);
    }

    public function destinos_get()
    {
        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $query_destinos = $this->Model_destinos->query_destinos($search);

        $response = [
            'status' => true,
            'data' => [
                'ciudades' => $query_destinos->result(),
            ],
        ];

        $this->response($response, 200);
    }

}
