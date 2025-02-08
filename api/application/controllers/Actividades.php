<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Actividades extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_usuarios');
        $this->load->model('Model_destinos');
        $this->load->model('Model_servicios');
        $this->load->model('Model_actividades');
        $this->load->helper('slugs');
    }

    public function modalidades_actividad_get($id_actividad = null)
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

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $modalidades = [];

        $query_modalidades = $this->Model_actividades->query_modalidades_actividad($id_actividad, $search, $offset, $per_page);

        if ($query_modalidades->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_modalidades->result() as $info_modalidad) {

                $modalidades[] = [
                    'num' => $num,
                    'id_actividad' => $info_modalidad->id_actividad,
                    'id_modalidad' => $info_modalidad->id_modalidad_actividad,
                    'modalidad' => $info_modalidad->modalidad,
                    'descripcion' => $info_modalidad->descripcion,
                    'indicaciones' => $info_modalidad->indicaciones,
                    'edad_min_ninos' => $info_modalidad->edad_min_ninos,
                    'edad_max_ninos' => $info_modalidad->edad_max_ninos,
                    'estado_modalidad' => $info_modalidad->estado_modalidad,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_actividades->query_num_modalidades_actividad($id_actividad, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'modalidades' => $modalidades,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_modalidad_post()
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

        $data_insert = [
            'id_actividad' => $data_input->id_actividad,
            'modalidad' => $data_input->modalidad,
            'estado_modalidad' => 1,
        ];

        $this->db->insert('modalidades_actividades', $data_insert);

        $id_modalidad = $this->db->insert_id();

        $response = [
            'status' => true,
            'data' => [
                'id_modalidad' => $id_modalidad,
            ],
            'message' => "Se ha creado la nueva modalidad.",
        ];

        $this->response($response, 200);
    }

    public function update_estado_modalidad_post()
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

        $this->db->where('modalidades_actividades.id_modalidad_actividad', $data_input->id_modalidad);
        $this->db->update('modalidades_actividades', ['estado_modalidad' => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la modalidad.",
        ];

        $this->response($response, 200);

    }

    public function delete_modalidad_post()
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

        $this->db->where('modalidades_actividades.id_modalidad_actividad', $data_input->id_modalidad);
        $this->db->update('modalidades_actividades', ['deleted_modalidad' => 1]);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado la modalidad.",
        ];

        $this->response($response, 200);
    }

    public function modalidad_get($id_modalidad = null)
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        if ($id_modalidad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la modalidad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $id_usuario = $valida_token["id_usuario"];

        $info_usuario = $this->Model_usuarios->query_usuario($id_usuario)->row();

        $modalidad = [];

        $query_modalidad = $this->Model_actividades->query_modalidad_actividad($id_modalidad);

        if ($query_modalidad->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró la modalidad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_modalidad = $query_modalidad->row();

        $info_actividad = $this->Model_servicios->query_actividad($info_modalidad->id_actividad)->row();

        if ($info_actividad->id_agencia != 1) {
            $response = [
                'status' => false,
                'message' => "La actividad no corresponde a la agencia.",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $modalidad = [
            'id_actividad' => $info_modalidad->id_actividad,
            'id_modalidad' => $info_modalidad->id_modalidad_actividad,
            'modalidad' => $info_modalidad->modalidad,
            'descripcion' => $info_modalidad->descripcion,
            'indicaciones' => $info_modalidad->indicaciones,
            'edad_min_ninos' => $info_modalidad->edad_min_ninos,
            'edad_max_ninos' => $info_modalidad->edad_max_ninos,
            'estado_modalidad' => $info_modalidad->estado_modalidad,
        ];

        $response = [
            'status' => true,
            'data' => [
                'modalidad' => $modalidad,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_modalidad_post()
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

        $data_update = [
            'modalidad' => $data_input->modalidad->modalidad,
            'descripcion' => $data_input->modalidad->descripcion,
            'indicaciones' => $data_input->modalidad->indicaciones,
            'edad_min_ninos' => $data_input->modalidad->edad_min_ninos,
            'edad_max_ninos' => $data_input->modalidad->edad_max_ninos,
            'estado_modalidad' => $data_input->modalidad->estado_modalidad,
        ];

        $this->db->where('modalidades_actividades.id_modalidad_actividad', $data_input->id_modalidad);
        $this->db->update('modalidades_actividades', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la modalidad.",
        ];

        $this->response($response, 200);

    }

    public function tarifas_actividad_get($id_actividad)
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

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $tarifas = [];

        $query_modalidades = $this->Model_actividades->query_modalidades_actividad($id_actividad, $search, $offset, $per_page);

        if ($query_modalidades->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_modalidades->result() as $info_modalidad) {

                $temporadas = [];
                $query_temporadas_modalidad = $this->Model_actividades->query_temporadas_modalidad($info_modalidad->id_modalidad_actividad);
                if ($query_temporadas_modalidad->num_rows() != 0) {
                    foreach ($query_temporadas_modalidad->result() as $info_temporada) {

                        $proveedor = [];
                        if ($info_temporada->id_proveedor != null) {
                            $query_proveedor = $this->Model_servicios->query_proveedor($info_temporada->id_proveedor);
                            if ($query_proveedor->num_rows() != 0) {
                                $proveedor = [
                                    'id_proveedor' => $query_proveedor->row()->id_proveedor,
                                    'proveedor' => $query_proveedor->row()->proveedor,
                                ];
                            }
                        }

                        $temporadas[] = [
                            'id_temporada_modalidad' => $info_temporada->id_temporada_modalidad,
                            'fecha_desde' => $info_temporada->fecha_desde,
                            'fecha_desde_f' => formato_fecha($info_temporada->fecha_desde, 1),
                            'fecha_hasta' => $info_temporada->fecha_hasta,
                            'fecha_hasta_f' => formato_fecha($info_temporada->fecha_hasta, 1),
                            'proveedor' => $proveedor,
                            'valor_neto_adultos' => $info_temporada->valor_neto_adultos,
                            'valor_neto_ninos' => $info_temporada->valor_neto_ninos,
                            'valor_neto_infantes' => $info_temporada->valor_neto_infantes,
                            'valor_venta_adultos' => $info_temporada->valor_venta_adultos,
                            'valor_venta_ninos' => $info_temporada->valor_venta_ninos,
                            'valor_venta_infantes' => $info_temporada->valor_venta_infantes,
                            'estado_temporada' => $info_temporada->estado_temporada,
                        ];

                    }
                }

                $tarifas[] = [
                    'num' => $num,
                    'id_actividad' => $info_modalidad->id_actividad,
                    'id_modalidad' => $info_modalidad->id_modalidad_actividad,
                    'modalidad' => $info_modalidad->modalidad,
                    'temporadas' => $temporadas,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_actividades->query_num_modalidades_actividad($id_actividad, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'tarifas' => $tarifas,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function tarifas_actividades_get()
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

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $tarifas = [];

        $query_tarifas = $this->Model_actividades->query_tarifas_actividades(1, $search, $offset, $per_page);

        if ($query_tarifas->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_tarifas->result() as $info_temporada) {

                $info_modalidad = $this->Model_actividades->query_modalidad($info_temporada->id_modalidad)->row();

                $info_actividad = $this->Model_servicios->query_actividad($info_modalidad->id_actividad)->row();

                $proveedor = [];
                if ($info_temporada->id_proveedor != null) {
                    $query_proveedor = $this->Model_servicios->query_proveedor($info_temporada->id_proveedor);
                    if ($query_proveedor->num_rows() != 0) {
                        $proveedor = [
                            'id_proveedor' => $query_proveedor->row()->id_proveedor,
                            'proveedor' => $query_proveedor->row()->proveedor,
                        ];
                    }
                }

                $tarifas[] = [
                    'num' => $num,
                    'id_temporada_modalidad' => $info_temporada->id_temporada_modalidad,
                    'actividad' => [
                        'id_actividad' => $info_actividad->id_actividad,
                        'actividad' => $info_actividad->servicio,
                    ],
                    'modalidad' => [
                        'id_modalidad' => $info_modalidad->id_modalidad_actividad,
                        'modalidad' => $info_modalidad->modalidad,
                    ],
                    'fecha_desde' => $info_temporada->fecha_desde,
                    'fecha_desde_f' => formato_fecha($info_temporada->fecha_desde, 1),
                    'fecha_hasta' => $info_temporada->fecha_hasta,
                    'fecha_hasta_f' => formato_fecha($info_temporada->fecha_hasta, 1),
                    'proveedor' => $proveedor,
                    'valor_neto_adultos' => $info_temporada->valor_neto_adultos,
                    'valor_neto_ninos' => $info_temporada->valor_neto_ninos,
                    'valor_neto_infantes' => $info_temporada->valor_neto_infantes,
                    'valor_venta_adultos' => $info_temporada->valor_venta_adultos,
                    'valor_venta_ninos' => $info_temporada->valor_venta_ninos,
                    'valor_venta_infantes' => $info_temporada->valor_venta_infantes,
                    'estado_temporada' => $info_temporada->estado_temporada,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_actividades->query_num_tarifas_actividades(1, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'tarifas' => $tarifas,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function tarifa_actividad_get()
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

        $info_actividad = $this->Model_servicios->query_actividad_servicio($data_input["id_servicio"])->row();

        $query_tarifa = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, $data_input["fecha_actividad"]);

        if ($query_tarifa->num_rows() == 0) {

            $response = [
                'status' => false,
                'message' => "No se encontraron tarifas para la fecha seleccioanda.",
            ];
            $this->response($response, 200);

        }

        $info_temporada = $query_tarifa->row();

        $tarifa = [
            'valor_neto_adultos' => $info_temporada->valor_neto_adultos,
            'valor_neto_ninos' => $info_temporada->valor_neto_ninos,
            'valor_neto_infantes' => $info_temporada->valor_neto_infantes,
            'valor_venta_adultos' => $info_temporada->valor_venta_adultos,
            'valor_venta_ninos' => $info_temporada->valor_venta_ninos,
            'valor_venta_infantes' => $info_temporada->valor_venta_infantes,
        ];

        $response = [
            'status' => true,
            'data' => [
                'tarifa' => $tarifa,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_temporada_tarifa_post()
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

        $data_insert_temporada = [
            'id_proveedor' => 1,
            'id_modalidad' => $data_input->id_modalidad,
            'fecha_desde' => $data_input->temporada->fecha_desde,
            'fecha_hasta' => $data_input->temporada->fecha_hasta,
            'valor_neto_adultos' => $data_input->temporada->valor_neto_adultos,
            'valor_neto_ninos' => $data_input->temporada->valor_neto_ninos,
            'valor_neto_infantes' => $data_input->temporada->valor_neto_infantes,
            'valor_venta_adultos' => $data_input->temporada->valor_venta_adultos,
            'valor_venta_ninos' => $data_input->temporada->valor_venta_ninos,
            'valor_venta_infantes' => $data_input->temporada->valor_venta_infantes,
        ];

        $this->db->insert('temporadas_modalidades', $data_insert_temporada);

        $id_modalidad = $this->db->insert_id();

        $response = [
            'status' => true,
            'data' => [
                'id_modalidad' => $id_modalidad,
            ],
            'message' => "Se ha creado la nueva temporada.",
        ];

        $this->response($response, 200);

    }

    public function update_temporada_tarifa_post()
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

        $data_update_temporada = [
            'fecha_desde' => $data_input->temporada->fecha_desde,
            'fecha_hasta' => $data_input->temporada->fecha_hasta,
            'valor_neto_adultos' => $data_input->temporada->valor_neto_adultos,
            'valor_neto_ninos' => $data_input->temporada->valor_neto_ninos,
            'valor_neto_infantes' => $data_input->temporada->valor_neto_infantes,
            'valor_venta_adultos' => $data_input->temporada->valor_venta_adultos,
            'valor_venta_ninos' => $data_input->temporada->valor_venta_ninos,
            'valor_venta_infantes' => $data_input->temporada->valor_venta_infantes,
        ];

        $this->db->where('temporadas_modalidades.id_temporada_modalidad', $data_input->temporada->id_temporada_modalidad);
        $this->db->update('temporadas_modalidades', $data_update_temporada);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la temporada.",
        ];

        $this->response($response, 200);

    }

    public function delete_temporada_tarifa_post()
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

        $this->db->where('temporadas_modalidades.id_temporada_modalidad', $data_input->id_temporada_modalidad);
        $this->db->delete('temporadas_modalidades');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado la tarifa.",
        ];

        $this->response($response, 200);
    }

    public function update_estado_temporada_post()
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

        $this->db->where('temporadas_modalidades.id_temporada_modalidad', $data_input->id_temporada_modalidad);
        $this->db->update('temporadas_modalidades', ["estado_temporada" => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la temporada.",
        ];

        $this->response($response, 200);

    }

    public function destacadas_get()
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

        $tipo_destino = isset($data_input["tipo_destino"]) && $data_input["tipo_destino"] != null ? $data_input["tipo_destino"] : null;

        $actividades = [];

        $query_actividades = $this->Model_servicios->query_actividades_destacadas(1, $tipo_destino, $offset, $per_page);

        if ($query_actividades->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_actividades->result() as $info_actividad) {

                $destino = [];
                if ($info_actividad->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info_actividad->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                switch ($info_actividad->id_tipo_servicio) {
                    case 1:
                        //Valor mínimo
                        $desde = 0;
                        $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
                        if ($query_tarifa_desde->num_rows() != 0) {
                            $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                        }
                        break;

                    case 2:
                        $desde = $info_actividad->valor_desde;
                        break;
                }



                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $actividades[] = [
                    'num' => $num,
                    'id_tipo_servicio' => $info_actividad->id_tipo_servicio,
                    'id_servicio' => $info_actividad->id_servicio,
                    'id_actividad' => $info_actividad->id_actividad,
                    'destino' => $destino,
                    'servicio' => $info_actividad->servicio,
                    'servicio_slug' => $info_actividad->servicio_slug,
                    'resumen' => $info_actividad->resumen,
                    'estado_servicio' => $info_actividad->estado_servicio,
                    'duracion' => $info_actividad->duracion,
                    'valor_desde' => $desde,
                    'img_principal' => $img_principal,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_actividades_destacadas(1, $tipo_destino);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'destacadas' => $actividades,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);
    }

    public function actividades_get()
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

        $slug_tipo = isset($data_input["slug"]) ? $data_input["slug"] : null;

        $actividades = [];

        $query_actividades = $this->Model_servicios->query_actividades(1, null, null, true, $slug_tipo, $offset, $per_page);

        if ($query_actividades->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_actividades->result() as $info_actividad) {

                $destino = [];
                if ($info_actividad->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info_actividad->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                //Valor mínimo
                $desde = 0;
                $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
                if ($query_tarifa_desde->num_rows() != 0) {
                    $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $actividades[] = [
                    'num' => $num,
                    'id_servicio' => $info_actividad->id_servicio,
                    'id_actividad' => $info_actividad->id_actividad,
                    'destino' => $destino,
                    'servicio' => $info_actividad->servicio,
                    'servicio_slug' => $info_actividad->servicio_slug,
                    'resumen' => $info_actividad->resumen,
                    'descripcion' => $info_actividad->descripcion,
                    'estado_servicio' => $info_actividad->estado_servicio,
                    'valor_desde' => $desde,
                    'img_principal' => $img_principal,
                    'destacada' => $info_actividad->destacada,
                    'duracion' => $info_actividad->duracion,
                ];

                $num++;
            }

        }

        $tipo_actividad = null;
        if ($slug_tipo != null) {
            $info_tipo = $this->Model_actividades->query_tipo_actividad_slug($slug_tipo)->row();
            $tipo_actividad = $info_tipo->tipo_actividad;
        }

        $total_results = $this->Model_servicios->query_num_actividades(1, null, null, true, $slug_tipo);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'tipo_actividad' => $tipo_actividad,
                'actividades' => $actividades,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);
    }

    public function actividades_list_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $actividades = [];

        $query_actividades = $this->Model_servicios->query_actividades_list(1, $data_input->actividades);

        if ($query_actividades->num_rows() != 0) {

            $num = 1;

            foreach ($query_actividades->result() as $info_actividad) {

                $destino = [];
                if ($info_actividad->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info_actividad->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                //Valor mínimo
                $desde = 0;
                $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
                if ($query_tarifa_desde->num_rows() != 0) {
                    $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $actividades[] = [
                    'num' => $num,
                    'id_servicio' => $info_actividad->id_servicio,
                    'id_actividad' => $info_actividad->id_actividad,
                    'destino' => $destino,
                    'servicio' => $info_actividad->servicio,
                    'servicio_slug' => $info_actividad->servicio_slug,
                    'resumen' => $info_actividad->resumen,
                    'descripcion' => $info_actividad->descripcion,
                    'estado_servicio' => $info_actividad->estado_servicio,
                    'valor_desde' => $desde,
                    'img_principal' => $img_principal,
                    'destacada' => $info_actividad->destacada,
                ];

                $num++;
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'actividades' => $actividades,
            ],
        ];

        $this->response($response, 200);

    }

    public function actividades_destino_get($id_ciudad = null)
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        //Valida la existencia del destino.
        $query_destino = $this->Model_destinos->query_destino_ciudad($id_ciudad);
        if ($query_destino->num_rows() == 0) {
            $response = [
                'status' => true,
                'data' => [
                    'actividades' => [],
                    'total_pages' => 0,
                ],
            ];
            $this->response($response, 200);
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $actividades = [];

        $query_actividades = $this->Model_servicios->query_actividades(1, $query_destino->row()->id_destino, $search, true, $offset, $per_page);

        if ($query_actividades->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_actividades->result() as $info_actividad) {

                $destino = [];
                if ($info_actividad->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info_actividad->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                        'name_es' => $info_destino->destino,
                    ];
                }

                //Valor mínimo
                $desde = 0;
                $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
                if ($query_tarifa_desde->num_rows() != 0) {
                    $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $actividades[] = [
                    'num' => $num,
                    'id_servicio' => $info_actividad->id_servicio,
                    'id_actividad' => $info_actividad->id_actividad,
                    'destino' => $destino,
                    'servicio' => $info_actividad->servicio,
                    'slug' => $info_actividad->servicio_slug,
                    'resumen' => $info_actividad->resumen,
                    'descripcion' => $info_actividad->descripcion,
                    'estado_servicio' => $info_actividad->estado_servicio,
                    'img_principal' => $img_principal,
                    'valor_desde' => $desde,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_actividades(1, $query_destino->row()->id_destino, $search, true);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'actividades' => $actividades,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function tipos_actividad_get()
    {

        $query_tipos_actividad = $this->Model_actividades->query_tipos_actividad();

        $response = [
            'status' => true,
            'data' => [
                'tipos_actividad' => $query_tipos_actividad->result(),
            ],
        ];

        $this->response($response, 200);
    }

    public function horarios_get($id_actividad)
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

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $horarios = [];

        $query_horarios = $this->Model_actividades->query_horarios($id_actividad);

        if ($query_horarios->num_rows() != 0) {

            $num = 1;

            foreach ($query_horarios->result() as $horario) {

                $estar = [];
                $query_puntos_salida = $this->Model_actividades->query_puntos_salida($id_actividad);
                if ($query_puntos_salida->num_rows() != 0) {
                    foreach ($query_puntos_salida->result() as $punto) {

                        $hora_estar = null;
                        $query_horario_estar = $this->Model_actividades->query_horario_estar($punto->id_punto_salida, $horario->id_horario);
                        if ($query_horario_estar->num_rows() != 0) {
                            $hora_estar = $query_horario_estar->row()->estar;
                        }

                        $estar[] = [
                            'id_punto_salida' => $punto->id_punto_salida,
                            'punto_salida' => $punto->punto_salida,
                            'estar' => $hora_estar,
                        ];
                    }
                }

                $horarios[] = [
                    'num' => $num,
                    'id_horario' => $horario->id_horario,
                    'desde' => $horario->desde,
                    'hasta' => $horario->hasta,
                    'estar' => $estar,
                ];

                $num++;

            }

        }

        $response = [
            'status' => true,
            'data' => [
                'horarios' => $horarios,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_horario_post()
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

        $data_insert = [
            'id_actividad' => $data_input->id_actividad,
            'desde' => $data_input->horario->desde,
            'hasta' => $data_input->horario->hasta,
        ];

        $this->db->insert('horarios_actividad', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se ha creado el nuevo horario.",
        ];

        $this->response($response, 200);

    }

    public function update_horario_post()
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

        $data_update = [
            'desde' => $data_input->horario->desde,
            'hasta' => $data_input->horario->hasta,
        ];

        $this->db->where('horarios_actividad.id_horario', $data_input->horario->id_horario);
        $this->db->update('horarios_actividad', $data_update);

        if (count($data_input->horario->estar) != 0) {
            foreach ($data_input->horario->estar as $estar) {

                $query_horario_estar = $this->Model_actividades->query_horario_estar($estar->id_punto_salida, $data_input->horario->id_horario);

                if ($query_horario_estar->num_rows() != 0) {
                    $this->db->where('horarios_estar.id_horario', $data_input->horario->id_horario);
                    $this->db->where('horarios_estar.id_punto_salida', $estar->id_punto_salida);
                    $this->db->update('horarios_estar', ["estar" => $estar->estar]);
                } else {
                    $this->db->insert('horarios_estar', ["estar" => $estar->estar, "id_horario" => $data_input->horario->id_horario, "id_punto_salida" => $estar->id_punto_salida]);
                }

            }
        }

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el horario.",
        ];

        $this->response($response, 200);

    }

    public function delete_horario_post()
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

        $data_update = [
            'deleted' => 1,
        ];

        $this->db->where('horarios_actividad.id_horario', $data_input->id_horario);
        $this->db->update('horarios_actividad', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el horario.",
        ];

        $this->response($response, 200);

    }

    public function puntos_salida_get($id_actividad)
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

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $puntos_salida = [];

        $query_puntos_salida = $this->Model_actividades->query_puntos_salida($id_actividad);

        if ($query_puntos_salida->num_rows() != 0) {

            $num = 1;

            foreach ($query_puntos_salida->result() as $punto) {

                $foto = null;
                if ($punto->foto_punto_salida != null) {
                    $foto = base_url() . $punto->foto_punto_salida;
                }

                $puntos_salida[] = [
                    'num' => $num,
                    'id_punto_salida' => $punto->id_punto_salida,
                    'punto_salida' => $punto->punto_salida,
                    'ubicacion' => $punto->ubicacion_punto_salida,
                    'link_mapa' => $punto->link_mapa,
                    'foto' => $foto,
                ];

                $num++;

            }

        }

        $response = [
            'status' => true,
            'data' => [
                'puntos_salida' => $puntos_salida,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_punto_salida_post()
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

        $data_insert = [
            'id_actividad' => $data_input->id_actividad,
            'punto_salida' => $data_input->punto,
            'ubicacion_punto_salida' => $data_input->ubicacion,
            'link_mapa' => $data_input->link_mapa,
            'deleted' => 0,
        ];

        $this->db->insert('puntos_salida', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se ha creado el nuevo punto.",
        ];

        $this->response($response, 200);

    }

    public function update_punto_salida_post()
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

        $data_update = [
            'punto_salida' => $data_input->punto->punto_salida,
            'ubicacion_punto_salida' => $data_input->punto->ubicacion,
            'link_mapa' => $data_input->punto->link_mapa,
        ];

        $this->db->where('puntos_salida.id_punto_salida', $data_input->punto->id_punto_salida);
        $this->db->update('puntos_salida', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el punto.",
        ];

        $this->response($response, 200);

    }

    public function delete_punto_salida_post()
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

        $data_update = [
            'deleted' => 1,
        ];

        $this->db->where('puntos_salida.id_punto_salida', $data_input->id_punto);
        $this->db->update('puntos_salida', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el punto.",
        ];

        $this->response($response, 200);

    }

    public function actividad_slug_get($slug = null)
    {

        $actividad = [];

        $query_actividad = $this->Model_servicios->query_actividad_slug($slug);

        if ($query_actividad->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_actividad = $query_actividad->row();

        $destino = [];
        if ($info_actividad->id_destino != null) {
            $info_destino = $this->Model_servicios->query_destino($info_actividad->id_destino)->row();
            $destino = [
                'id_destino' => $info_destino->id_destino,
                'id_ciudad' => $info_destino->id_ciudad,
                'destino' => $info_destino->destino,
            ];
        }

        //Valor mínimo
        $desde = 0;
        $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
        if ($query_tarifa_desde->num_rows() != 0) {
            $desde = $query_tarifa_desde->row()->valor_venta_adultos;
        }

        $galeria = [];
        $query_galeria = $this->Model_servicios->query_galeria($info_actividad->id_servicio);

        if ($query_galeria->num_rows() != 0) {

            foreach ($query_galeria->result() as $info) {

                switch ($info->tipo) {
                    case 0:
                        $url = base_url() . $info->img_galeria;
                        if (ENVIRONMENT == 'development') {
                            $url = URL_API_PRD . $info->img_galeria;
                        }
                        break;
                    case 1:
                        $url = "https://www.youtube.com/embed/" . $info->img_galeria;
                        break;
                }

                $galeria[] = [
                    'id_galeria' => $info->id_galeria,
                    'url' => $url,
                    'principal' => $info->principal,
                    'tipo' => $info->tipo,
                ];
            }

        }

        $horarios = [];

        $query_horarios = $this->Model_actividades->query_horarios($info_actividad->id_actividad);

        if ($query_horarios->num_rows() != 0) {

            $num = 1;

            foreach ($query_horarios->result() as $horario) {

                $horarios[] = [
                    'id_horario' => $horario->id_horario,
                    'desde' => $horario->desde,
                    'hasta' => $horario->hasta,
                    'disponible' => true,
                ];

                $num++;

            }

        }

        $img_principal = null;
        $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
        if ($query_img_principal->num_rows() != 0) {
            $img_principal = base_url() . $query_img_principal->row()->img_galeria;
        }

        $actividad = [
            'id_servicio' => $info_actividad->id_servicio,
            'id_actividad' => $info_actividad->id_actividad,
            'destino' => $destino,
            'servicio' => $info_actividad->servicio,
            'resumen' => $info_actividad->resumen,
            'descripcion' => $info_actividad->descripcion,
            'incluye' => $info_actividad->incluye,
            'datos_tour' => $info_actividad->datos_tour,
            'cancelaciones' => $info_actividad->cancelaciones,
            'punto_encuentro' => $info_actividad->punto_encuentro,
            'duracion' => $info_actividad->duracion,
            'valor_desde' => $desde,
            'valor_desde_c' => formato_moneda($desde),
            'estado_servicio' => $info_actividad->estado_servicio,
            'img_principal' => $img_principal,
            'galeria' => $galeria,
            'horarios' => $horarios,
            'meta_descripcion' => $info_actividad->meta_descripcion,
            'tags' => $info_actividad->tags,
        ];

        $response = [
            'status' => true,
            'data' => [
                'actividad' => $actividad,
            ],
        ];

        $this->response($response, 200);

    }

    public function disponibilidad_get($id_actividad = null)
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

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $fecha = date("Y-m-d");
        if (isset($data_input["fecha"])) {
            $fecha = $data_input["fecha"];
        }

        $horarios = [];

        $query_horarios = $this->Model_actividades->query_horarios($id_actividad);

        if ($query_horarios->num_rows() != 0) {

            $num = 1;

            foreach ($query_horarios->result() as $horario) {

                $estado = true;
                $query_cierre = $this->Model_actividades->query_cierre_horario($horario->id_horario, $fecha);
                if ($query_cierre->num_rows() != 0) {
                    $estado = false;
                }

                $horarios[] = [
                    'id_horario' => $horario->id_horario,
                    'desde' => $horario->desde,
                    'estado' => $estado,
                ];

                $num++;

            }

        }

        $inoperaciones = [];

        $query_inoperaciones = $this->Model_actividades->query_inoperaciones($id_actividad);

        if ($query_inoperaciones->num_rows() != 0) {
            foreach ($query_inoperaciones->result() as $inoperacion) {

                $inoperaciones[] = [
                    'id_inoperacion' => $inoperacion->id_inoperacion,
                    'desde' => $inoperacion->desde,
                    'desde_sf' => [
                        'ano' => date("Y", strtotime($inoperacion->desde)),
                        'mes' => date("m", strtotime($inoperacion->desde)) - 1,
                        'dia' => date("d", strtotime($inoperacion->desde)),
                    ],
                    'hasta' => $inoperacion->hasta,
                    'hasta_sf' => [
                        'ano' => date("Y", strtotime($inoperacion->hasta)),
                        'mes' => date("m", strtotime($inoperacion->hasta)) - 1,
                        'dia' => date("d", strtotime($inoperacion->hasta)) + 1,
                    ],
                ];
            }
        }

        $response = [
            'status' => true,
            'data' => [
                'fecha' => $fecha,
                'fecha_f' => formato_fecha($fecha, 1),
                'horarios' => $horarios,
                'inoperaciones' => $inoperaciones,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_disponibilidad_post()
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

        foreach ($data_input->horarios as $horario) {

            $desde = $data_input->fecha . " 00:00:00";
            $hasta = $data_input->fecha . " 23:59:59";

            $this->db->where('cierres_actividad.id_horario', $horario->id_horario);
            $this->db->where('cierres_actividad.desde', $desde);
            $this->db->where('cierres_actividad.hasta', $hasta);
            $this->db->delete('cierres_actividad');

            if ($horario->estado != 1) {
                $data_insert = [
                    'id_horario' => $horario->id_horario,
                    'desde' => $desde,
                    'hasta' => $hasta,
                ];
                $this->db->insert('cierres_actividad', $data_insert);
            }

        }

        $response = [
            'status' => true,

        ];

        $this->response($response, 200);

    }

    public function update_dias_disponibilidad_post()
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

        $dias_inoperacion = [];
        foreach ($data_input->dias as $dia) {

            if ($dia->estado == 1) {
                $dias_inoperacion[] = $dia->key;
            }

        }

        $dias_inoperacion = implode(",", $dias_inoperacion);

        $this->db->where('actividades.id_actividad', $data_input->id_actividad);
        $this->db->update('actividades', ['dias_inoperacion' => $dias_inoperacion]);

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function recomendadas_get($id_actividad = null)
    {

        if ($id_actividad == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de la actividad.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data_input = $this->input->get(null, true);

        $full = false;
        if (isset($data_input["full"]) && $data_input["full"] == 1) {
            $full = true;
        }

        $actividades = [];

        $query_actividades = $this->Model_actividades->query_actividades_recomendadas($id_actividad);

        if ($query_actividades->num_rows() != 0) {

            foreach ($query_actividades->result() as $actividad) {

                $info_actividad = $this->Model_servicios->query_actividad($actividad->id_actividad_recomendada)->row();

                if ($full) {
                    //Valor mínimo
                    $desde = 0;
                    $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info_actividad->id_actividad, date("Y-m-d"));
                    if ($query_tarifa_desde->num_rows() != 0) {
                        $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                    }

                    $img_principal = null;
                    $query_img_principal = $this->Model_servicios->query_img_principal($info_actividad->id_servicio);
                    if ($query_img_principal->num_rows() != 0) {
                        $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                    }

                    $actividades[] = [
                        'id_servicio' => $info_actividad->id_servicio,
                        'id_actividad' => $info_actividad->id_actividad,
                        'servicio' => $info_actividad->servicio,
                        'servicio_slug' => $info_actividad->servicio_slug,
                        'resumen' => $info_actividad->resumen,
                        'descripcion' => $info_actividad->descripcion,
                        'estado_servicio' => $info_actividad->estado_servicio,
                        'duracion' => $info_actividad->duracion,
                        'valor_desde' => $desde,
                        'img_principal' => $img_principal,
                    ];

                } else {

                    $actividades[] = [
                        'id_servicio' => $info_actividad->id_servicio,
                        'id_actividad' => $info_actividad->id_actividad,
                        'servicio' => $info_actividad->servicio,
                    ];

                }

            }

        }

        $response = [
            'status' => true,
            'data' => [
                'recomendadas' => $actividades,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_recomendada_post()
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

        $query_actividad = $this->Model_actividades->query_actividad_recomendada($data_input->id_actividad_recomendada, $data_input->id_actividad);

        if ($query_actividad->num_rows() == 0) {
            $data_insert = [
                'id_actividad' => $data_input->id_actividad,
                'id_actividad_recomendada' => $data_input->id_actividad_recomendada,
            ];
            $this->db->insert('actividades_recomendadas', $data_insert);
        }

        $response = [
            'status' => true,
            'message' => "Se agregó la actividad recomendada.",

        ];

        $this->response($response, 200);

    }

    public function delete_recomendada_post()
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

        $this->db->where('actividades_recomendadas.id_actividad', $data_input->id_actividad);
        $this->db->where('actividades_recomendadas.id_actividad_recomendada', $data_input->id_actividad_recomendada);
        $this->db->delete('actividades_recomendadas');

        $response = [
            'status' => true,
            'message' => "Se eliminó la actividad recomendada.",

        ];

        $this->response($response, 200);

    }

    public function set_inoperacion_post()
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

        $data_insert = [
            'id_actividad' => $data_input->id_actividad,
            'desde' => $data_input->desde,
            'hasta' => $data_input->hasta,
        ];
        $this->db->insert('inoperaciones', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se agregó la inoperación.",

        ];

        $this->response($response, 200);

    }

    public function delete_inoperacion_post()
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

        $this->db->where('inoperaciones.id_inoperacion', $data_input->id_inoperacion);
        $this->db->delete('inoperaciones');

        $response = [
            'status' => true,
            'message' => "Se eliminó la inoperación.",

        ];

        $this->response($response, 200);

    }

    public function set_tipo_actividad_post()
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

        $tipo_slug = clean($data_input->tipo_actividad);
        $tipo_slug = noDuplicarSlug($tipo_slug, null, 2);

        $this->db->insert('tipos_actividad', [
            'tipo_actividad' => $data_input->tipo_actividad,
            'slug' => $tipo_slug,
        ]);

        $response = [
            'status' => true,
            'message' => "Se ha creado el tipo de actividad.",
        ];

        $this->response($response, 200);

    }

    public function update_tipo_actividad_post()
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

        $this->db->where('tipos_actividad.id_tipo_actividad', $data_input->tipo_actividad->id_tipo_actividad);
        $this->db->update('tipos_actividad', [
            'tipo_actividad' => $data_input->tipo_actividad->tipo_actividad,
        ]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el tipo de actividad.",
        ];

        $this->response($response, 200);

    }

    public function delete_tipo_actividad_post()
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

        $this->db->where('tipos_actividad.id_tipo_actividad', $data_input->id_tipo_actividad);
        $this->db->delete('tipos_actividad');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el tipo de actividad.",
        ];

        $this->response($response, 200);

    }

}