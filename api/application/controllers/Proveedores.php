<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Proveedores extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_proveedores');
    }

    public function proveedores_get()
    {

        $data_input = $this->input->get(null, true);

        $tipo = isset($data_input["tipo"]) ? $data_input["tipo"] : null;

        $search = isset($data_input["search"]) ? $data_input["search"] : null;

        $estado = isset($data_input["estado"]) ? $data_input["estado"] : null;

        $query_proveedores = $this->Model_proveedores->query_proveedores($tipo, $search, $estado);

        $proveedores = [];
        if ($query_proveedores->num_rows() != 0) {
            foreach ($query_proveedores->result() as $proveedor) {

                $query_tipos_servicio_proveedor = $this->Model_proveedores->query_tipos_servicio_proveedor($proveedor->id_proveedor);

                $proveedores[] = [
                    'id_proveedor' => $proveedor->id_proveedor,
                    'proveedor' => $proveedor->proveedor,
                    'tipos_servicio' => $query_tipos_servicio_proveedor->result(),
                    'estado' => $proveedor->estado,
                ];
            }
        }

        $response = [
            'status' => true,
            'data' => [
                'proveedores' => $proveedores,
            ],
        ];

        $this->response($response, 200);
    }

    public function set_proveedor_post()
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

        $data_input = $data_input->data;

        $this->db->insert('proveedores', ['proveedor' => $data_input->proveedor, 'estado_proveedor' => $data_input->estado]);

        $id_proveedor = $this->db->insert_id();

        if (count($data_input->tipos_servicio) != 0) {

            foreach ($data_input->tipos_servicio as $tipo) {
                $this->db->insert('proveedores_tipo', [
                    'id_proveedor' => $id_proveedor,
                    'id_tipo_servicio' => $tipo->id_tipo_servicio,
                ]);
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'id_proveedor' => $id_proveedor,
            ],
            'message' => "Se ha creado el nuevo proveedor exitosamente.",
        ];

        $this->response($response, 200);
    }

    public function update_proveedor_post()
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

        $this->db->where('proveedores.id_proveedor', $data_input->proveedor->id_proveedor);
        $this->db->update('proveedores', [
            'proveedor' => $data_input->proveedor->proveedor,
            'estado_proveedor' => $data_input->proveedor->estado,
        ]);

        $this->db->where('proveedores_tipo.id_proveedor', $data_input->proveedor->id_proveedor);
        $this->db->delete('proveedores_tipo');

        if (count($data_input->proveedor->tipos_servicio) != 0) {

            foreach ($data_input->proveedor->tipos_servicio as $tipo) {
                $this->db->insert('proveedores_tipo', [
                    'id_proveedor' => $data_input->proveedor->id_proveedor,
                    'id_tipo_servicio' => $tipo->id_tipo_servicio,
                ]);
            }

        }

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el proveedor exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_proveedor_post()
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

        $this->db->where('proveedores.id_proveedor', $data_input->id_proveedor);
        $this->db->update('proveedores', [
            'deleted_proveedor' => 1,
        ]);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el proveedor exitosamente.",
        ];

        $this->response($response, 200);

    }
}
