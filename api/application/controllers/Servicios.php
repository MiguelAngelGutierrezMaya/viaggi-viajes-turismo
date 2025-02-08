<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Servicios extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_usuarios');
        $this->load->model('Model_servicios');
        $this->load->model('Model_destinos');
        $this->load->model('Model_actividades');
        $this->load->model('Model_reservas');
        $this->load->helper('slugs');
    }

    public function actividades_get()
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $id_destino = null;
        if (isset($data_input["id_destino"])) {
            $id_destino = $data_input["id_destino"];
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

        $query_actividades = $this->Model_servicios->query_actividades(1, $id_destino, $search, null, null, $offset, $per_page);

        $total_results = $this->Model_servicios->query_num_actividades(1, $id_destino, $search, null, null);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $actividades = [];

        if ($query_actividades->num_rows() != 0) {
            foreach ($query_actividades->result() as $actividad) {

                $query_tipos_actividad = $this->Model_actividades->query_tipos_actividad_actividad($actividad->id_actividad);

                $actividad->tipos_actividad = $query_tipos_actividad->result();

                #Días de inoperación.
                $dias = [
                    [
                        'key' => 1,
                        'dia' => 'Lunes',
                        'estado' => false,
                    ],
                    [
                        'key' => 2,
                        'dia' => 'Martes',
                        'estado' => false,
                    ],
                    [
                        'key' => 3,
                        'dia' => 'Miércoles',
                        'estado' => false,
                    ],
                    [
                        'key' => 4,
                        'dia' => 'Jueves',
                        'estado' => false,
                    ],
                    [
                        'key' => 5,
                        'dia' => 'Viernes',
                        'estado' => false,
                    ],
                    [
                        'key' => 6,
                        'dia' => 'Sábado',
                        'estado' => false,
                    ],
                    [
                        'key' => 0,
                        'dia' => 'Domingo',
                        'estado' => false,
                    ]
                ];

                if ($actividad->dias_inoperacion != null) {

                    $dias_inoperacion = explode(",", $actividad->dias_inoperacion);

                    foreach ($dias as $key => $dia) {

                        $dias[$key] = [
                            'key' => $dia['key'],
                            'dia' => $dia['dia'],
                            'estado' => in_array($dia['key'], $dias_inoperacion)
                        ];
                    }
                }

                $actividad->dias_inoperacion = $dias;

                $actividades[] = $actividad;
            }
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

    public function actividades_list_get()
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $actividades = [];

        $query_actividades = $this->Model_servicios->query_actividades(1, null, $search, false);

        if ($query_actividades->num_rows() != 0) {

            foreach ($query_actividades->result() as $info_actividad) {

                $horarios = [];
                $query_horarios = $this->Model_actividades->query_horarios($info_actividad->id_actividad);
                if ($query_horarios->num_rows() != 0) {
                    foreach ($query_horarios->result() as $horario) {
                        $horarios[] = [
                            'id_horario' => $horario->id_horario,
                            'desde' => $horario->desde,
                            'hasta' => $horario->hasta,
                        ];
                    }
                }

                if ($info_actividad->estado_servicio == 1) {
                    $actividades[] = [
                        'id_servicio' => $info_actividad->id_servicio,
                        'servicio' => $info_actividad->servicio,
                        'horarios' => $horarios,
                        'slug' => $info_actividad->servicio_slug,
                    ];
                }

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

    public function destino_search_get()
    {

        $data_input = $this->input->get(null, true);

        if (!isset($data_input["search"])) {
            $response = [
                'status' => false,
                'message' => "No se envió el destino a buscar.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $headers = [
            'Accept: application/json',
            'X-API-KEY: ' . AR_DEV_API_KEY,
        ];

        $url = URL_API_AR_DEV;

        $params = [
            'search' => $data_input["search"],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($params));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        //needed for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //make the request
        $result = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($result);

        if (!$result->status) {
            $response = [
                'status' => false,
                'message' => "Error consultando destinos.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $response = [
            'status' => true,
            'data' => [
                'destinos' => $result->data,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_actividad_post()
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

        #Buscar o crear el destino
        $query_destino = $this->Model_destinos->query_destino_ciudad($data_input->destino->id_ciudad);

        if ($query_destino->num_rows() == 0) {

            $destino_slug = clean($data_input->destino->ciudad);
            $destino_slug = noDuplicarSlug($destino_slug, null, 1);

            $data_insert_destino = [
                'id_ciudad' => $data_input->destino->id_ciudad,
                'destino' => $data_input->destino->ciudad,
                'destino_slug' => $destino_slug,
                'estado' => 1,
            ];

            $this->db->insert('destinos', $data_insert_destino);

            $id_destino = $this->db->insert_id();
        } else {
            $id_destino = $query_destino->row()->id_destino;
        }

        $servicio_slug = clean($data_input->servicio);
        $servicio_slug = noDuplicarSlug($servicio_slug, null, 0);

        $data_insert_servicio = [
            'id_agencia' => 1,
            'id_tipo_servicio' => 1,
            'id_destino' => $id_destino,
            'servicio' => $data_input->servicio,
            'servicio_slug' => $servicio_slug,
            'estado_servicio' => 0,
            'deleted_servicio' => 0,
        ];

        $this->db->insert('servicios', $data_insert_servicio);

        $id_servicio = $this->db->insert_id();

        $data_insert_actividad = [
            'id_servicio' => $id_servicio,
        ];

        $this->db->insert('actividades', $data_insert_actividad);

        $id_actividad = $this->db->insert_id();

        $data_insert = [
            'id_actividad' => $id_actividad,
            'modalidad' => $data_input->servicio,
            'estado_modalidad' => 1,
        ];

        $this->db->insert('modalidades_actividades', $data_insert);

        $id_modalidad = $this->db->insert_id();

        $response = [
            'status' => true,
            'data' => [
                'id_servicio' => $id_servicio,
                'id_actividad' => $id_actividad,
            ],
            'message' => "Se ha creado la nueva actividad exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function update_estado_servicio_post()
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

        $this->db->where('servicios.id_servicio', $data_input->id_servicio);
        $this->db->update('servicios', ['estado_servicio' => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function update_destacada_actividad_post()
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

        $this->db->where('actividades.id_actividad', $data_input->id_actividad);
        $this->db->update('actividades', ['destacada' => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function update_oferta_post()
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

        $this->db->where('servicios.id_servicio', $data_input->id_servicio);
        $this->db->update('servicios', ['oferta' => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function delete_servicio_post()
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

        if (!valida_permiso($id_usuario, 'GSERVICIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $this->db->where('servicios.id_servicio', $data_input->id_servicio);
        $this->db->update('servicios', ['deleted_servicio' => 1]);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el servicio.",
        ];

        $this->response($response, 200);
    }

    public function actividad_get($id_servicio)
    {

        $actividad = [];

        $query_actividad = $this->Model_servicios->query_actividad_servicio($id_servicio);

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

        //Inoperaciones
        $inoperaciones = [];

        $query_inoperaciones = $this->Model_actividades->query_inoperaciones($info_actividad->id_actividad);

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

        $puntos_salida = [];

        $query_puntos_salida = $this->Model_actividades->query_puntos_salida($info_actividad->id_actividad);

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

        $query_tipos_actividad = $this->Model_actividades->query_tipos_actividad_actividad($info_actividad->id_actividad);

        $dias_inoperacion = [];
        if ($info_actividad->dias_inoperacion != null) {
            $dias_inoperacion = explode(",", $info_actividad->dias_inoperacion);

            foreach ($dias_inoperacion as $key => $value) {
                $dias_inoperacion[$key] = intval($value);
            }

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
            'ubicacion_punto_encuentro' => $info_actividad->ubicacion_punto_encuentro,
            'duracion' => $info_actividad->duracion,
            'estado_servicio' => $info_actividad->estado_servicio,
            'horarios' => $horarios,
            'limite_pasajeros' => $info_actividad->limite_pasajeros,
            'max_pasajeros' => $info_actividad->max_pasajeros,
            'tiempo_min_reserva' => $info_actividad->tiempo_min_reserva,
            'inoperaciones' => $inoperaciones,
            'puntos_salida' => $puntos_salida,
            'meta_descripcion' => $info_actividad->meta_descripcion,
            'tags' => $info_actividad->tags,
            'tipos_actividad' => $query_tipos_actividad->result(),
            'dias_inoperacion' => $dias_inoperacion
        ];

        $response = [
            'status' => true,
            'data' => [
                'actividad' => $actividad,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_actividad_post()
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

        if (!valida_permiso($id_usuario, 'GSERVICIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $this->load->helper('slugs');
        $servicio_slug = clean($data_input->actividad->servicio);
        $servicio_slug = noDuplicarSlug($servicio_slug, $data_input->id_servicio, 0);

        $data_update_servicio = [
            'servicio' => $data_input->actividad->servicio,
            'servicio_slug' => $servicio_slug,
            'resumen' => $data_input->actividad->resumen,
            'tiempo_min_reserva' => $data_input->actividad->tiempo_min_reserva,
            'estado_servicio' => $data_input->actividad->estado_servicio,
            'meta_descripcion' => $data_input->actividad->meta_descripcion,
            'tags' => $data_input->actividad->tags,
        ];

        $this->db->where('servicios.id_servicio', $data_input->id_servicio);
        $this->db->update('servicios', $data_update_servicio);

        $data_update_actividad = [
            'descripcion' => $data_input->actividad->descripcion,
            'incluye' => $data_input->actividad->incluye,
            'datos_tour' => $data_input->actividad->datos_tour,
            'cancelaciones' => $data_input->actividad->cancelaciones,
            'punto_encuentro' => $data_input->actividad->punto_encuentro,
            'duracion' => $data_input->actividad->duracion,
        ];

        $this->db->where('actividades.id_servicio', $data_input->id_servicio);
        $this->db->update('actividades', $data_update_actividad);

        #Actualizar tipos de actividad.
        $this->Model_actividades->update_tipos_actividad($data_input->actividad->id_actividad, $data_input->actividad->tipos_actividad);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function proveedores_get()
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

        $id_destino = null;
        if (isset($data_input["id_destino"])) {
            $id_destino = $data_input["id_destino"];
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $proveedores = [];

        $query_proveedores = $this->Model_servicios->query_proveedores(1, $search, $offset, $per_page);

        if ($query_proveedores->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_proveedores->result() as $info_proveedor) {

                $proveedores[] = [
                    'num' => $num,
                    'id_proveedor' => $info_proveedor->id_proveedor,
                    'proveedor' => $info_proveedor->proveedor,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_proveedores(1, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'proveedores' => $proveedores,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function galeria_get($id_servicio = null)
    {

        $galeria = [];

        $query_servicio = $this->Model_servicios->query_servicio($id_servicio);

        if ($query_servicio->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $query_galeria = $this->Model_servicios->query_galeria($id_servicio, 0);

        if ($query_galeria->num_rows() != 0) {

            foreach ($query_galeria->result() as $info) {

                $url = base_url() . $info->img_galeria;
                if (ENVIRONMENT == 'development') {
                    $url = URL_API_DEV . $info->img_galeria;
                }

                $galeria[] = [
                    'id_galeria' => $info->id_galeria,
                    'url' => $url,
                    'principal' => $info->principal,
                    'tipo' => $info->tipo,
                ];
            }

        }

        $videos = [];
        $query_galeria = $this->Model_servicios->query_galeria($id_servicio, 1);

        if ($query_galeria->num_rows() != 0) {

            foreach ($query_galeria->result() as $info) {

                $url = "https://www.youtube.com/embed/" . $info->img_galeria;

                $videos[] = [
                    'id_galeria' => $info->id_galeria,
                    'url' => $url,
                    'principal' => $info->principal,
                    'tipo' => $info->tipo,
                ];
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'galeria' => $galeria,
                'videos' => $videos,
            ],
        ];

        $this->response($response, 200);

    }

    public function delete_imagen_post()
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

        $url = $this->Model_servicios->query_img_galeria($data_input->id_galeria)->row()->img_galeria;

        unlink($url);

        $this->db->where('galeria.id_galeria', $data_input->id_galeria);
        $this->db->delete('galeria');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado la imagen.",
        ];

        $this->response($response, 200);

    }

    public function set_imagen_principal_post()
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

        $info_galeria = $this->Model_servicios->query_imagen_galeria($data_input->id_galeria)->row();

        $this->db->where('galeria.id_servicio', $info_galeria->id_servicio);
        $this->db->update('galeria', ["principal" => 0]);

        $this->db->where('galeria.id_galeria', $data_input->id_galeria);
        $this->db->update('galeria', ["principal" => 1]);

        $response = [
            'status' => true,
            'message' => "Se ha definido la imagen como principal.",
        ];

        $this->response($response, 200);
    }

    public function set_video_galeria_post()
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
            'id_servicio' => $data_input->id_servicio,
            'img_galeria' => $data_input->id_video,
            'tipo' => 1,
        ];

        $this->db->insert('galeria', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se ha agregado el video a la galería.",
        ];

        $this->response($response, 200);
    }

    public function eliminar_video_galeria_post()
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

        $this->db->where('galeria.id_galeria', $data_input->id_galeria);
        $this->db->delete('galeria');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el video.",
        ];

        $this->response($response, 200);

    }

    public function horarios_servicio_get($id_servicio)
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

        if ($id_servicio == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID del servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_actividad = $this->Model_servicios->query_actividad_servicio($id_servicio)->row();

        $id_actividad = $info_actividad->id_actividad;

        $horarios = [];

        $query_horarios = $this->Model_actividades->query_horarios($id_actividad);

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

        $response = [
            'status' => true,
            'data' => [
                'horarios' => $horarios,
            ],
        ];

        $this->response($response, 200);

    }

    public function disponibilidad_servicio_get()
    {

        $data_input = $this->input->get(null, true);

        $id_horario = null;
        if (isset($data_input["id_horario"])) {
            $id_horario = $data_input["id_horario"];
        }

        if ($data_input["fecha"] == date("Y-m-d") && ($id_horario == null || $id_horario == 'null')) {
            $horarios = [];
            $info_actividad = $this->Model_servicios->query_actividad_servicio($data_input["id_servicio"])->row();
            $query_horarios = $this->Model_actividades->query_horarios($info_actividad->id_actividad);
            $horarios = [];
            if ($query_horarios->num_rows() != 0) {
                $count_block = 0;
                foreach ($query_horarios->result() as $horario) {

                    $disponible = true;
                    $hora_desde = date("H:i:s", strtotime($horario->desde));
                    if ($hora_desde < date("H:i:s")) {
                        $disponible = false;
                        $count_block++;
                    }

                    $horarios[] = [
                        'id_horario' => $horario->id_horario,
                        'desde' => $horario->desde,
                        'hasta' => $horario->hasta,
                        'disponible' => $disponible,
                    ];
                }
            }

            $status = true;
            if ($count_block == count($horarios)) {
                $status = false;
            }

            $response = [
                'status' => $status,
                'data' => [
                    'horarios' => $horarios,
                ],
            ];
            $this->response($response, 200);

        }

        $query_cierre = $this->Model_actividades->query_cierre_horario($id_horario, $data_input["fecha"]);
        if ($query_cierre->num_rows() != 0) {
            $response = [
                'status' => false,
                'data' => [
                    'disponibles' => 0,
                ],
            ];
            $this->response($response, 200);
        }

        $info_actividad = $this->Model_servicios->query_actividad_servicio($data_input["id_servicio"])->row();

        //Tarifa
        $query_tarifa = $this->Model_actividades->query_tarifa_fecha($info_actividad->id_actividad, $data_input["fecha"]);
        if ($query_tarifa->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => 'No hay tarifas vigentes para la fecha indicada.',
            ];
            $this->response($response, 200);
        }
        $tarifa = $query_tarifa->row()->valor_venta_adultos;

        if ($info_actividad->limite_pasajeros == 0) {
            $response = [
                'status' => true,
                'data' => [
                    'disponibles' => null,
                    'tarifa_adultos' => $query_tarifa->row()->valor_venta_adultos,
                    'tarifa_ninos' => $query_tarifa->row()->valor_venta_ninos,
                    'tarifa_infantes' => $query_tarifa->row()->valor_venta_infantes,
                ],
            ];
            $this->response($response, 200);
        }

        $max = $info_actividad->max_pasajeros;

        $query_reservas = $this->Model_reservas->query_reservas_horario($data_input["id_servicio"], $data_input["fecha"], $id_horario);

        $pasajeros = 0;
        if ($query_reservas->num_rows() != 0) {
            foreach ($query_reservas->result() as $reserva) {
                $pasajeros = $pasajeros + ($reserva->adultos + $reserva->ninos);
            }
        }

        $dif = $max - $pasajeros;

        if ($data_input["num_pasajeros"] > $dif) {
            $response = [
                'status' => false,
                'data' => [
                    'disponibles' => $dif,
                ],
            ];
            $this->response($response, 200);
        }

        $response = [
            'status' => true,
            'data' => [
                'disponibles' => $dif,
                'tarifa' => $tarifa,
            ],
        ];
        $this->response($response, 200);

    }

    public function popup_get()
    {
        $data_input = $this->input->get(null, true);

        $popup = null;

        $vigente = false;
        if (isset($data_input["vigente"])) {
            $vigente = $data_input["vigente"];
        }

        $query_popup = $this->Model_servicios->query_popup($vigente);

        if ($query_popup->num_rows() != 0) {

            $popup = [
                'img' => $query_popup->row()->img,
                'desde' => $query_popup->row()->desde,
                'hasta' => $query_popup->row()->hasta,
                'url' => $query_popup->row()->url,
            ];

        }

        $response = [
            'status' => true,
            'data' => [
                'popup' => $popup,
            ],
        ];

        $this->response($response, 200);

    }

    public function reviews_get($id_servicio)
    {

        if ($id_servicio == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID del servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $reviews = [];

        $query_servicio = $this->Model_servicios->query_servicio($id_servicio);

        if ($query_servicio->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $query_reviews = $this->Model_servicios->query_reviews_servicio($id_servicio);

        if ($query_reviews->num_rows() != 0) {

            foreach ($query_reviews->result() as $info) {

                $reviews[] = [
                    'id_review' => $info->id_review,
                    'fecha_reg' => formato_fecha($info->fecha_reg, 2),
                    'cliente' => mb_strtoupper($info->nombres . " " . $info->apellidos),
                    'valor' => $info->valor,
                    'comentarios' => $info->comentarios,
                    'estado' => $info->estado,
                ];
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'reviews' => $reviews,
            ],
        ];

        $this->response($response, 200);

    }

    public function paquetes_get()
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $id_destino = null;
        if (isset($data_input["id_destino"])) {
            $id_destino = $data_input["id_destino"];
        }

        $page = null;
        $per_page = null;
        $offset = null;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
        }

        $query_paquetes = $this->Model_servicios->query_paquetes(1, $id_destino, $search, $offset, $per_page);

        $total_results = $this->Model_servicios->query_num_paquetes(1, $id_destino, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'paquetes' => $query_paquetes->result(),
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_paquete_post()
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

        #Buscar o crear el destino
        $query_destino = $this->Model_destinos->query_destino_ciudad($data_input->destino->id_ciudad);

        if ($query_destino->num_rows() == 0) {

            $destino_slug = clean($data_input->destino->ciudad);
            $destino_slug = noDuplicarSlug($destino_slug, null, 1);

            $data_insert_destino = [
                'id_ciudad' => $data_input->destino->id_ciudad,
                'destino' => $data_input->destino->ciudad,
                'destino_slug' => $destino_slug,
                'estado' => 1,
            ];

            $this->db->insert('destinos', $data_insert_destino);

            $id_destino = $this->db->insert_id();
        } else {
            $id_destino = $query_destino->row()->id_destino;
        }

        $servicio_slug = clean($data_input->servicio);
        $servicio_slug = noDuplicarSlug($servicio_slug, null, 0);

        $data_insert_servicio = [
            'id_agencia' => 1,
            'id_tipo_servicio' => 2,
            'id_destino' => $id_destino,
            'servicio' => $data_input->servicio,
            'servicio_slug' => $servicio_slug,
            'estado_servicio' => 0,
            'deleted_servicio' => 0,
        ];

        $this->db->insert('servicios', $data_insert_servicio);

        $id_servicio = $this->db->insert_id();

        $data_insert_actividad = [
            'id_servicio' => $id_servicio,
        ];

        $this->db->insert('paquetes', $data_insert_actividad);

        $id_paquete = $this->db->insert_id();

        $response = [
            'status' => true,
            'data' => [
                'id_servicio' => $id_servicio,
                'id_paquete' => $id_paquete,
            ],
            'message' => "Se ha creado el nuevo paquete exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function paquete_get($id_servicio)
    {

        $query_paquete = $this->Model_servicios->query_paquete_servicio($id_servicio);

        if ($query_paquete->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el servicio.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_paquete = $query_paquete->row();

        $destino = [];
        if ($info_paquete->id_destino != null) {
            $info_destino = $this->Model_servicios->query_destino($info_paquete->id_destino)->row();
            $destino = [
                'id_destino' => $info_destino->id_destino,
                'id_ciudad' => $info_destino->id_ciudad,
                'destino' => $info_destino->destino,
            ];
        }

        $img = null;
        if ($info_paquete->img != null) {
            $img = base_url() . $info_paquete->img;
            if (ENVIRONMENT == 'development') {
                $img = URL_API_DEV . $info_paquete->img;
            }
        }

        $paquete = [
            'id_servicio' => $info_paquete->id_servicio,
            'id_paquete' => $info_paquete->id_paquete,
            'destino' => $destino,
            'servicio' => $info_paquete->servicio,
            'resumen' => $info_paquete->resumen,
            'descripcion' => $info_paquete->descripcion,
            'estado_servicio' => $info_paquete->estado_servicio,
            'meta_descripcion' => $info_paquete->meta_descripcion,
            'tags' => $info_paquete->tags,
            'valor_desde' => $info_paquete->valor_desde,
            'img' => $img,
        ];

        $response = [
            'status' => true,
            'data' => [
                'paquete' => $paquete,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_paquete_post()
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

        $this->load->helper('slugs');
        $servicio_slug = clean($data_input->paquete->servicio);
        $servicio_slug = noDuplicarSlug($servicio_slug, $data_input->id_servicio, 0);

        $data_update_servicio = [
            'servicio' => $data_input->paquete->servicio,
            'servicio_slug' => $servicio_slug,
            'resumen' => $data_input->paquete->resumen,
            'estado_servicio' => $data_input->paquete->estado_servicio,
            'meta_descripcion' => $data_input->paquete->meta_descripcion,
            'tags' => $data_input->paquete->tags,
        ];

        $this->db->where('servicios.id_servicio', $data_input->id_servicio);
        $this->db->update('servicios', $data_update_servicio);

        $data_update_paquete = [
            'descripcion' => $data_input->paquete->descripcion,
            'valor_desde' => $data_input->paquete->valor_desde,
        ];

        $this->db->where('paquetes.id_servicio', $data_input->id_servicio);
        $this->db->update('paquetes', $data_update_paquete);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function update_destacada_paquete_post()
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

        $this->db->where('paquetes.id_paquete', $data_input->id_paquete);
        $this->db->update('paquetes', ['destacado' => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el servicio.",
        ];

        $this->response($response, 200);

    }

    public function hoteles_list_get()
    {

        $data_input = $this->input->get(null, true);

        $query_hoteles = $this->Model_servicios->query_hoteles($data_input["search"]);

        $response = [
            'status' => true,
            'data' => [
                'hoteles' => $query_hoteles->result(),
            ],
        ];

        $this->response($response, 200);

    }

    public function ofertas_get()
    {

        $data_input = $this->input->get(null, true);

        $ofertas = [];

        $query_ofertas = $this->Model_servicios->query_ofertas(1);

        if ($query_ofertas->num_rows() != 0) {

            $num = 1;

            foreach ($query_ofertas->result() as $info) {

                $destino = [];
                if ($info->id_destino != null) {
                    $info_destino = $this->Model_servicios->query_destino($info->id_destino)->row();
                    $destino = [
                        'id_destino' => $info_destino->id_destino,
                        'id_ciudad' => $info_destino->id_ciudad,
                        'destino' => $info_destino->destino,
                    ];
                }

                switch ($info->id_tipo_servicio) {
                    case 1:
                        //Valor mínimo
                        $desde = 0;
                        $query_tarifa_desde = $this->Model_actividades->query_tarifa_desde($info->id_actividad, date("Y-m-d"));
                        if ($query_tarifa_desde->num_rows() != 0) {
                            $desde = $query_tarifa_desde->row()->valor_venta_adultos;
                        }
                        break;

                    case 2:
                        $desde = $info->valor_desde;
                        break;
                }

                $img_principal = null;
                $query_img_principal = $this->Model_servicios->query_img_principal($info->id_servicio);
                if ($query_img_principal->num_rows() != 0) {
                    $img_principal = base_url() . $query_img_principal->row()->img_galeria;
                }

                $ofertas[] = [
                    'num' => $num,
                    'id_tipo_servicio' => $info->id_tipo_servicio,
                    'id_servicio' => $info->id_servicio,
                    'id_actividad' => $info->id_actividad,
                    'destino' => $destino,
                    'servicio' => $info->servicio,
                    'servicio_slug' => $info->servicio_slug,
                    'resumen' => $info->resumen,
                    'estado_servicio' => $info->estado_servicio,
                    'duracion' => $info->duracion,
                    'valor_desde' => $desde,
                    'img_principal' => $img_principal,
                ];

                $num++;
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'ofertas' => $ofertas,
            ],
        ];

        $this->response($response, 200);
    }
}
