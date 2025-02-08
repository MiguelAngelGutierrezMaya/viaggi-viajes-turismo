<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Documentos extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_usuarios');
        $this->load->model('Model_documentos');
    }

    public function documentos_get()
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

        $info_usuario = $this->Model_usuarios->query_usuario($id_usuario)->row();

        $data_input = $this->input->get(null, true);

        $id_carpeta = null;
        $carpeta = null;
        if (isset($data_input["id_carpeta"]) && $data_input["id_carpeta"] != 0) {
            $id_carpeta = $data_input["id_carpeta"];
            $query_carpeta = $this->Model_documentos->query_carpeta($id_carpeta)->row();
            $carpeta = [
                'id_carpeta' => $query_carpeta->id_carpeta,
                'carpeta' => $query_carpeta->nombre,
                'descripcion' => $query_carpeta->descripcion,
            ];
        }

        $query_documentos = $this->Model_documentos->query_documentos($id_carpeta);

        $documentos = [];
        foreach ($query_documentos->result() as $documento) {

            if ($documento->tipo == 0) {
                if ($info_usuario->perfil != 1) {
                    $query_permiso_carpeta = $this->Model_usuarios->query_permiso_carpeta($id_usuario, $documento->id_documento);
                    if ($query_permiso_carpeta->num_rows() == 0) {
                        continue;
                    }
                }
            }

            $documentos[] = [
                'id_documento' => $documento->id_documento,
                'id_carpeta' => $documento->id_carpeta,
                'tipo' => $documento->tipo,
                'nombre' => $documento->nombre,
                'descripcion' => $documento->descripcion,
                'url' => $documento->url,
            ];

        }

        $response = [
            'status' => true,
            'data' => [
                'carpeta' => $carpeta,
                'documentos' => $documentos,
            ],
        ];

        $this->response($response, 200);

    }

    public function delete_documento_post()
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

        #Validación de permiso de gestión de servicios
        if (!valida_permiso($id_usuario, 'GSERVICIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        #Valida si es un folder para eliminar los archivos dentro.
        $query_documento = $this->Model_documentos->query_documento($data_input->id_documento);

        if ($query_documento->num_rows() > 0) {
            $info_documento = $query_documento->row();

            if ($info_documento->tipo == 0) {
                $query_documentos = $this->Model_documentos->query_documentos($data_input->id_documento);

                if ($query_documentos->num_rows() > 0) {
                    foreach ($query_documentos->result() as $archivo) {
                        if ($archivo->tipo == 1) {
                            unlink($archivo->url);
                        }
                    }
                }

            }
        }

        $this->db->where('documentos.id_documento', $data_input->id_documento);
        $this->db->delete('documentos');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el recurso satisfactoriamente.",
        ];

        $this->response($response, 200);
    }
}