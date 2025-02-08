<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class General extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "VYT API Vr 1.0 - Method: General";

    }

    public function set_subscribe_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        if (count($data_input) == 0) {
            $this->response([
                'status' => false,
                'message' => 'No se enviaron parámetros.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($data_input->email == "") {
            $this->response([
                'status' => false,
                'message' => 'Dirección de correo incorrecta.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!filter_var($data_input->email, FILTER_VALIDATE_EMAIL)) {
            $this->response([
                'status' => false,
                'message' => 'Dirección de correo incorrecta.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->model("Model_users");

        $query_user_email = $this->Model_users->query_subscribe_email($data_input->email);

        if ($query_user_email->num_rows() != 0) {
            $this->response([
                'status' => false,
                'message' => 'El correo electrónico que intentas registrar ya existe en nuestros registros.',
            ], 200);
        }

        $data_insert = [
            'email' => $data_input->email,
            'status' => 1,
        ];
        $this->db->insert('suscripciones', $data_insert);

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Suscripción realizada.',
        ];

        $this->response($response, 200);

    }
}