<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Agenda extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_agenda');
    }

    public function agenda_get()
    {
        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $info_usuario = $this->Model_usuarios->query_usuario($valida_token["id_usuario"])->row();

        $id_usuario = $info_usuario->perfil == 1 ? null : $valida_token["id_usuario"];

        $data_input = $this->input->get(null, true);

        $fecha = isset($data_input["fecha"]) ? $data_input["fecha"] : null;

        $estado = isset($data_input["estado"]) ? $data_input["estado"] : null;

        $query_agenda = $this->Model_agenda->query_agenda($fecha, $id_usuario);

        $response = [
            'status' => true,
            'data' => [
                'agenda' => $query_agenda->result(),
            ],
        ];

        $this->response($response, 200);
    }

    public function set_reunion_post()
    {
        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $id_usuario = $valida_token["id_usuario"];

        $data_input = json_decode(file_get_contents("php://input"));

        $this->db->insert('agenda', [
            'fecha_reg' => date("Y-m-d H:i:s"),
            'id_cliente' => $data_input->id_cliente,
            'id_usuario' => $id_usuario,
            'fecha' => $data_input->fecha,
            'hora' => $data_input->hora,
            'url' => $data_input->url,
            'observaciones' => $data_input->observaciones
        ]);

        $response = [
            'status' => true,
            'message' => "Se ha creado la nueva reunión exitosamente.",
        ];

        $this->response($response, 200);
    }

    public function update_reunion_post()
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $this->db->where('agenda.id_agenda', $data_input->id_agenda);
        $this->db->update('agenda', [
            'fecha' => $data_input->fecha,
            'hora' => $data_input->hora,
            'url' => $data_input->url,
            'observaciones' => $data_input->observaciones
        ]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la  reunión exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_reunion_post()
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $this->db->where('agenda.id_agenda', $data_input->id_agenda);
        $this->db->delete('agenda');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado la  reunión exitosamente.",
        ];

        $this->response($response, 200);

    }

}