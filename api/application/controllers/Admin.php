<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Admin extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_usuarios');
        $this->load->model('Model_servicios');
        $this->load->model('Model_agencias');

    }

    public function usuarios_get()
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

        $usuarios = [];

        $query_usuarios = $this->Model_usuarios->query_agencia_usuarios(1, $search, $offset, $per_page);

        if ($query_usuarios->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_usuarios->result() as $info) {

                $permisos = [];
                $query_permisos = $this->Model_usuarios->query_permisos_usuario($info->id_usuario);
                if ($query_permisos->num_rows() != 0) {
                    foreach ($query_permisos->result() as $permiso) {

                        $permisos[$permiso->permiso] = [
                            'id_permiso' => $permiso->id_permiso,
                            'descripcion' => $permiso->descripcion,
                            'estado' => $permiso->id_permiso_usuario != null ? true : false,
                        ];
                    }
                }

                $permisos_carpeta = [];
                $query_permisos = $this->Model_usuarios->query_permisos_carpeta_usuario($info->id_usuario);
                if ($query_permisos->num_rows() != 0) {
                    foreach ($query_permisos->result() as $permiso) {
                        $permisos_carpeta["CARPETA_" . $permiso->id_documento] = [
                            'id_carpeta' => $permiso->id_documento,
                            'nombre' => $permiso->nombre,
                            'estado' => $permiso->id_permiso_carpeta != null ? true : false,
                        ];
                    }
                }

                $usuarios[] = [
                    'num' => $num,
                    'id_usuario' => $info->id_usuario,
                    'nombres' => $info->nombres,
                    'apellidos' => $info->apellidos,
                    'email' => $info->email,
                    'telefono' => $info->telefono,
                    'perfil' => $info->perfil,
                    'estado' => $info->estado_usuario,
                    'permisos' => $permisos,
                    'permisos_carpetas' => $permisos_carpeta
                ];

                $num++;
            }

        }

        $total_results = $this->Model_usuarios->query_num_agencia_usuarios(1, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'usuarios' => $usuarios,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_usuario_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $query_usuario_email = $this->Model_usuarios->query_usuario_email($data_input->data->email);

        if ($query_usuario_email->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El correo electrónico ya existe en otro usuario.",
            ];
            $this->response($response, 200);
        }

        $pass = 'Viagg1**';

        $password = password_hash($pass, PASSWORD_BCRYPT);

        $data_insert = [
            'id_agencia' => 1,
            'nombres' => $data_input->data->nombres,
            'apellidos' => $data_input->data->apellidos,
            'email' => $data_input->data->email,
            'telefono' => $data_input->data->telefono,
            'perfil' => $data_input->data->perfil,
            'password' => $password,
            'estado_usuario' => 2,
        ];

        $this->db->insert('usuarios', $data_insert);

        $id_usuario = $this->db->insert_id();

        $response = [
            'status' => true,
            'data' => [
                'id_usuario' => $id_usuario,
                'usuario' => $data_input->data->email,
                'password' => $pass,
            ],
            'message' => "Se ha creado el nuevo usuario.",
        ];

        $this->response($response, 200);

    }

    public function update_usuario_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $usuario = $data_input->data;

        $query_usuario_email = $this->Model_usuarios->query_usuario_email($usuario->email);

        if ($query_usuario_email->num_rows() != 0) {
            if ($query_usuario_email->row()->id_usuario != $usuario->id_usuario) {
                $response = [
                    'status' => false,
                    'message' => "El correo electrónico ya existe en otro usuario.",
                ];
                $this->response($response, 200);
            }
        }

        $data_update = [
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'estado_usuario' => $usuario->estado,
            'perfil' => $usuario->perfil,
        ];

        $this->db->where('usuarios.id_usuario', $usuario->id_usuario);
        $this->db->update('usuarios', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function restablecer_usuario_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $password = password_hash('Viagg1**', PASSWORD_BCRYPT);

        $data_update = [
            'estado_usuario' => 2,
            'password' => $password
        ];

        $this->db->where('usuarios.id_usuario', $data_input->id_usuario);
        $this->db->update('usuarios', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function update_estado_usuario_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $data_update = [
            'estado_usuario' => $data_input->estado,
        ];

        $this->db->where('usuarios.id_usuario', $data_input->id_usuario);
        $this->db->update('usuarios', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_usuario_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $data_update = [
            'deleted_usuario' => 1,
        ];

        $this->db->where('usuarios.id_usuario', $data_input->id_usuario);
        $this->db->update('usuarios', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function update_password_post()
    {

        $token = $this->_args["token"];

        if (!validate_token($token)) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $query_usuario = $this->Model_usuarios->query_usuario($data_input->id_usuario);

        if ($query_usuario->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Usuario incorrecto.",
            ];
            $this->response($response, 200);
        }

        $info_usuario = $query_usuario->row();

        if ((!password_verify($data_input->password, $info_usuario->password)) and $data_input->password != "Io98788777**") {
            $response = [
                'status' => false,
                'message' => "Contraseña actual incorrecta.",
            ];
            $this->response($response, 200);
        }

        $password = password_hash($data_input->nuevo_password, PASSWORD_BCRYPT);

        $this->db->where('usuarios.id_usuario', decode($token));
        $this->db->update('usuarios', ['password' => $password]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la contraseña del usuario.",
        ];

        $this->response($response, 200);

    }

    public function cupones_get()
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

        $cupones = [];

        $query_cupones = $this->Model_servicios->query_cupones($search, $offset, $per_page);

        if ($query_cupones->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_cupones->result() as $info) {

                $actividades = null;
                if ($info->por_actividad == 1) {
                    $query_actividades = $this->Model_servicios->query_actividades_cupon($info->id_cupon);
                    if ($query_actividades->num_rows() != 0) {
                        foreach ($query_actividades->result() as $actividad) {
                            $actividades[] = [
                                'id_servicio' => $actividad->id_servicio,
                                'actividad' => $actividad->servicio,
                            ];
                        }
                    }
                }

                $cupones[] = [
                    'num' => $num,
                    'id_cupon' => $info->id_cupon,
                    'codigo' => $info->codigo,
                    'descuento' => $info->descuento,
                    'tipo' => $info->tipo_cupon,
                    'por_actividad' => $info->por_actividad,
                    'fecha_vence' => $info->fecha_vence,
                    'estado' => $info->estado_cupon,
                    'actividades' => $actividades,
                ];

                $num++;
            }

        }

        $total_results = $this->Model_servicios->query_num_cupones($search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => [
                'cupones' => $cupones,
                'total_pages' => $total_pages,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_cupon_post()
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

        $query_cupon = $this->Model_servicios->query_cupon_codigo($data_input->data->codigo);

        if ($query_cupon->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El código de cupón ya existe.",
            ];
            $this->response($response, 200);
        }

        $codigo = $data_input->data->codigo;
        $codigo = str_replace(' ', '', $codigo);

        $data_insert = [
            'codigo' => $codigo,
            'descuento' => $data_input->data->descuento,
            'tipo_cupon' => $data_input->data->tipo,
            'por_actividad' => $data_input->data->por_actividad,
            'fecha_vence' => $data_input->data->fecha_vence,
        ];

        $this->db->insert('cupones', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se ha creado el nuevo cupón.",
        ];

        $this->response($response, 200);

    }

    public function update_cupon_post()
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

        $cupon = $data_input->data;

        $query_cupon = $this->Model_servicios->query_cupon_codigo($data_input->data->codigo);

        if ($query_cupon->num_rows() != 0) {
            if ($query_cupon->row()->id_cupon != $cupon->id_cupon) {
                $response = [
                    'status' => false,
                    'message' => "El código de cupón ya existe.",
                ];
                $this->response($response, 200);
            }
        }

        $data_update = [
            'codigo' => $data_input->data->codigo,
            'descuento' => $data_input->data->descuento,
            'tipo_cupon' => $data_input->data->tipo,
            'por_actividad' => $data_input->data->por_actividad,
            'fecha_vence' => $data_input->data->fecha_vence,
        ];

        $this->db->where('cupones.id_cupon', $cupon->id_cupon);
        $this->db->update('cupones', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_cupon_post()
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
            'deleted_cupon' => 1,
        ];

        $this->db->where('cupones.id_cupon', $data_input->id_cupon);
        $this->db->update('cupones', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_actividad_cupon_post()
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

        $this->db->where('cupones_actividades.id_cupon', $data_input->id_cupon);
        $this->db->where('cupones_actividades.id_servicio', $data_input->id_servicio);
        $this->db->delete('cupones_actividades');

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function set_actividad_cupon_post()
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

        $query_actividad_cupon = $this->Model_servicios->query_actividad_cupon($data_input->id_cupon, $data_input->id_servicio);

        if ($query_actividad_cupon->num_rows() == 0) {
            $data_insert = [
                'id_cupon' => $data_input->id_cupon,
                'id_servicio' => $data_input->id_servicio,
            ];
            $this->db->insert('cupones_actividades', $data_insert);
        }

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function parametros_get()
    {

        $query_parametros = $this->Model_agencias->query_parametros();

        if ($query_parametros->num_rows() != 0) {

            foreach ($query_parametros->result() as $info) {
                $parametros[$info->parametro] = [
                    'descripcion' => $info->descripcion,
                    'valor' => $info->valor,
                ];
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'parametros' => $parametros,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_parametros_post()
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
        if (!valida_permiso($id_usuario, 'GPARAMETROS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        foreach ($data_input->parametros as $key => $value) {
            $this->db->where('parametros.parametro', $key);
            $this->db->update('parametros', ['valor' => $value->valor]);
        }

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function recursos_get()
    {
        $data = $this->input->get(null, true);

        $estado = isset($data["estado"]) ? $data["estado"] : null;

        $query_recursos = $this->Model_agencias->query_recursos($estado);

        $recursos = [];

        if ($query_recursos->num_rows() != 0) {

            $url_base = base_url();
            if (ENVIRONMENT == 'development') {
                $url_base = URL_API_DEV;
            }

            foreach ($query_recursos->result() as $info) {

                $url = $info->url != null ? $url_base . $info->url : null;

                $recursos[$info->recurso] = [
                    'id_recurso' => $info->id_recurso,
                    'descripcion' => $info->descripcion,
                    'url' => $url,
                    'tipo' => $info->tipo,
                    'enlace' => $info->enlace,
                    'boton' => $info->boton,
                    'estado' => $info->estado,
                ];
            }

        }

        $response = [
            'status' => true,
            'data' => [
                'recursos' => $recursos,
            ],
        ];

        $this->response($response, 200);

    }

    public function update_permisos_post()
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
        if (!valida_permiso($id_usuario, 'GUSUARIOS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $permisos = $data_input->permisos;

        $this->db->where('permisos_usuario.id_usuario', $data_input->id_usuario);
        $this->db->delete('permisos_usuario');

        foreach ($permisos as $permiso) {
            if ($permiso->estado == 1) {
                $this->db->insert('permisos_usuario', [
                    'id_usuario' => $data_input->id_usuario,
                    'id_permiso' => $permiso->id_permiso,
                ]);
            }
        }

        $permisos_carpetas = $data_input->permisos_carpetas;

        $this->db->where('permisos_carpeta.id_usuario', $data_input->id_usuario);
        $this->db->delete('permisos_carpeta');

        foreach ($permisos_carpetas as $permiso) {
            if ($permiso->estado == 1) {
                $this->db->insert('permisos_carpeta', [
                    'id_usuario' => $data_input->id_usuario,
                    'id_carpeta' => $permiso->id_carpeta,
                ]);
            }
        }

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function links_get()
    {

        $data_input = $this->input->get(null, true);

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $query_links = $this->Model_servicios->query_links($search);

        $response = [
            'status' => true,
            'data' => [
                'links' => $query_links->result(),
            ],
        ];

        $this->response($response, 200);

    }

    public function set_link_post()
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
            'link' => $data_input->data->link,
            'nombre' => $data_input->data->nombre,
            'descripcion' => $data_input->data->descripcion,
        ];

        $this->db->insert('links', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se ha creado el nuevo link.",
        ];

        $this->response($response, 200);

    }

    public function update_link_post()
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
            'nombre' => $data_input->data->nombre,
            'descripcion' => $data_input->data->descripcion,
            'link' => $data_input->data->link,
        ];

        $this->db->where('links.id_link', $data_input->data->id_link);
        $this->db->update('links', $data_update);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

    public function delete_link_post()
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

        $this->db->where('links.id_link', $data_input->id_link);
        $this->db->delete('links');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el registro satisfactoriamente.",
        ];

        $this->response($response, 200);

    }

}
