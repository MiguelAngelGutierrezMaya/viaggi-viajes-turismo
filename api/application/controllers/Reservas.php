<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Reservas extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_reservas');
        $this->load->model('Model_actividades');
        $this->load->model('Model_servicios');
        $this->load->model('Model_agencias');
        $this->load->model('Model_destinos');
        $this->load->model('Model_comentarios_reservas');
        $this->load->helper('slugs');

    }

    public function set_reserva_post()
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

        $errores = [];

        if (count($data_input->servicios) == 0) {
            $response = [
                'status' => false,
                'message' => "No se enviaron los servicios a reservar.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
        $cod_reserva = get_codigo_reserva();

        $data_insert_reserva = [
            'fecha_reg' => date("Y-m-d H:i:s"),
            'id_cliente' => $data_input->id_cliente,
            'cod_reserva' => $cod_reserva,
            'canal' => 2,
            'estado_reserva' => 0,
            'descuento' => $data_input->descuento,
        ];

        $this->db->insert('reservas', $data_insert_reserva);

        $id_reserva = $this->db->insert_id();

        foreach ($data_input->servicios as $servicio) {

            switch ($servicio->tipo) {
                case 1:

                    if ($servicio->fecha_actividad < date("Y-m-d")) {
                        $errores[] = "La fecha de actividad debe ser superior o igual a la fecha actual.";
                        break;
                    }

                    //Consultar la temporada
                    $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();
                    $info_modalidad = $this->Model_actividades->query_modalidad_actividad($info_actividad->id_actividad)->row();

                    $query_temporada = $this->Model_actividades->query_temporada_actividad($info_modalidad->id_modalidad_actividad, $servicio->fecha_actividad);

                    if ($query_temporada->num_rows() == 0) {
                        $response = [
                            'status' => false,
                            'message' => "No se han definido tarifas para la fecha seleccionada.",
                        ];
                        $this->response($response, 200);
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

                    $id_horario = isset($servicio->id_horario) ? $servicio->id_horario : null;

                    $this->db->insert('servicio_reserva_actividad', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'fecha_actividad' => $servicio->fecha_actividad,
                        'id_horario_actividad' => $id_horario,
                    ]);

                    break;
                case 2:

                    $data_insert = [
                        'id_reserva' => $id_reserva,
                        'id_servicio' => null,
                        'tipo_servicio' => 2,
                        'adultos' => $servicio->num_pasajeros,
                        'valor_neto' => $servicio->valor_neto,
                        'valor_venta' => $servicio->valor_venta,
                    ];

                    $this->db->insert('servicios_reserva', $data_insert);

                    $id_servicio_reserva = $this->db->insert_id();

                    $this->db->insert('servicio_reserva_paquete', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'id_ciudad_origen' => $servicio->origen,
                        'id_ciudad_destino' => $servicio->destino,
                        'fecha_ida' => $servicio->fecha_ida,
                        'fecha_regreso' => $servicio->fecha_regreso,
                        'id_proveedor' => $servicio->id_proveedor,
                    ]);

                    break;
                case 3:

                    #Valida si se creará un nuevo hotel
                    if ($servicio->nuevo_hotel || $servicio->nuevo_hotel == 1) {

                        #Valida si existe el destino.
                        $query_destino = $this->Model_destinos->query_destino_ciudad($servicio->hotel->id_ciudad);

                        if ($query_destino->num_rows() == 0) {

                            $query_ciudad = $this->Model_destinos->query_ciudad($servicio->hotel->id_ciudad);

                            if ($query_ciudad->num_rows() == 0) {

                                $this->db->where('reservas.id_reserva', $id_reserva);
                                $this->db->delete('reservas');

                                $response = [
                                    'status' => false,
                                    'message' => "No se encontró la ciudad enviada.",
                                ];
                                $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
                            }

                            $destino_slug = clean($query_ciudad->row()->ciudad);
                            $destino_slug = noDuplicarSlug($destino_slug, null, 1);

                            $data_insert_destino = [
                                'id_ciudad' => $query_ciudad->row()->id_ciudad,
                                'destino' => $query_ciudad->row()->ciudad,
                                'destino_slug' => $destino_slug,
                                'estado' => 1,
                            ];

                            $this->db->insert('destinos', $data_insert_destino);

                            $id_destino = $this->db->insert_id();
                        } else {
                            $id_destino = $query_destino->row()->id_destino;
                        }

                        $servicio_slug = clean($servicio->hotel->nombre);
                        $servicio_slug = noDuplicarSlug($servicio_slug, null, 0);

                        $data_insert_servicio = [
                            'id_agencia' => 1,
                            'id_tipo_servicio' => 3,
                            'id_destino' => $id_destino,
                            'servicio' => $servicio->hotel->nombre,
                            'servicio_slug' => $servicio_slug,
                            'estado_servicio' => 0,
                            'deleted_servicio' => 0,
                        ];

                        $this->db->insert('servicios', $data_insert_servicio);

                        $id_servicio = $this->db->insert_id();

                    } else {

                        $id_servicio = $servicio->id_servicio;

                    }

                    $data_insert = [
                        'id_reserva' => $id_reserva,
                        'id_servicio' => $id_servicio,
                        'tipo_servicio' => 3,
                        'adultos' => $servicio->num_pasajeros,
                        'valor_neto' => $servicio->valor_neto,
                        'valor_venta' => $servicio->valor_venta,
                    ];

                    $this->db->insert('servicios_reserva', $data_insert);

                    $id_servicio_reserva = $this->db->insert_id();

                    $this->db->insert('servicio_reserva_hoteles', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'fecha_ida' => $servicio->fecha_ida,
                        'fecha_regreso' => $servicio->fecha_regreso,
                    ]);

                    break;
                case 4:

                    $data_insert = [
                        'id_reserva' => $id_reserva,
                        'id_servicio' => null,
                        'tipo_servicio' => 4,
                        'adultos' => $servicio->num_pasajeros,
                        'valor_neto' => $servicio->valor_neto,
                        'valor_venta' => $servicio->valor_venta,
                    ];

                    $this->db->insert('servicios_reserva', $data_insert);

                    $id_servicio_reserva = $this->db->insert_id();

                    $this->db->insert('servicio_reserva_tiquete', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'tipo_tiquete' => $servicio->tipo_tiquete,
                        'id_ciudad_origen' => $servicio->origen,
                        'id_ciudad_destino' => $servicio->destino,
                        'fecha_ida' => $servicio->fecha_ida,
                        'fecha_regreso' => $servicio->fecha_regreso,
                        'id_proveedor' => $servicio->id_proveedor,
                    ]);

                    break;
                case 5:

                    $data_insert = [
                        'id_reserva' => $id_reserva,
                        'id_servicio' => null,
                        'tipo_servicio' => 5,
                        'adultos' => $servicio->num_pasajeros,
                        'valor_neto' => $servicio->valor_neto,
                        'valor_venta' => $servicio->valor_venta,
                    ];

                    $this->db->insert('servicios_reserva', $data_insert);

                    $id_servicio_reserva = $this->db->insert_id();

                    $this->db->insert('servicio_reserva_asistencia', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'id_ciudad_origen' => $servicio->origen,
                        'id_ciudad_destino' => $servicio->destino,
                        'fecha_ida' => $servicio->fecha_ida,
                        'fecha_regreso' => $servicio->fecha_regreso,
                        'id_proveedor' => $servicio->id_proveedor,
                    ]);

                    break;
                case 6:
                    $data_insert = [
                        'id_reserva' => $id_reserva,
                        'id_servicio' => null,
                        'tipo_servicio' => 6,
                        'valor_neto' => $servicio->valor_neto,
                        'valor_venta' => $servicio->valor_venta,
                    ];

                    $this->db->insert('servicios_reserva', $data_insert);

                    $id_servicio_reserva = $this->db->insert_id();

                    $this->db->insert('servicio_reserva_otros', [
                        'id_reserva' => $id_reserva,
                        'id_servicio_reserva' => $id_servicio_reserva,
                        'descripcion' => $servicio->descripcion,
                    ]);
                    break;
            }

        }

        set_nota($id_usuario, $id_reserva, "Se registró la reserva.");

        if (count($errores) != 0) {
            $response = [
                'status' => true,
                'message' => "Hubo errores registrando la reserva.",
                'data' => [
                    'id_reserva' => $id_reserva,
                ],
            ];
        } else {
            $response = [
                'status' => true,
                'message' => "Se registró satisfactoriamente la reserva.",
                'data' => [
                    'id_reserva' => $id_reserva,
                ],
            ];
        }

        $this->response($response, 200);

    }

    public function reservas_get()
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

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

        $query_reservas = $this->Model_reservas->query_reservas($search, $estado, $id_servicio, $desde, $hasta, $offset, $per_page);

        if ($query_reservas->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_reservas->result() as $info) {

                $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();

                $cliente = [
                    'id_cliente' => $info_cliente->id_cliente,
                    'nombres' => $info_cliente->nombres,
                    'apellidos' => $info_cliente->apellidos,
                    'telefono' => $info_cliente->cod_pais . $info_cliente->telefono,
                    'email' => $info_cliente->email,
                ];

                $servicios = [];
                $valor = 0;
                $reviews = 0;

                $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);

                if ($query_servicios->num_rows() != 0) {

                    foreach ($query_servicios->result() as $servicio) {

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

                                $detalle = [
                                    'tipo' => 1,
                                    'id_servicio' => $servicio->id_servicio,
                                    'servicio' => $info_actividad->servicio,
                                    'valor' => $valor_servicio,
                                    'fecha_actividad' => formato_fecha($info_reserva_actividad->fecha_actividad, 1),
                                    'num_pasajeros' => $servicio->adultos + $servicio->ninos + $servicio->infantes,
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
                                    'id_servicio' => $servicio->id_servicio,
                                    'servicio' => $info_hotel->servicio,
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

                                $query_reserva_otros = $this->Model_reservas->query_reserva_otros($servicio->id_servicio_reserva);

                                if ($query_reserva_otros->num_rows() == 0) {
                                    break;
                                }

                                $info_reserva = $query_reserva_otros->row();

                                $detalle = [
                                    'tipo' => 6,
                                    'descripcion' => $info_reserva->descripcion,
                                ];

                                break;

                        }

                        $servicios[] = $detalle;

                        $valor = $valor + $valor_servicio;

                    }
                }

                $descuento = 0;
                if ($info->descuento != 0) {
                    $descuento = $info->descuento;

                }

                $total = $valor - $descuento;

                $reservas[] = [
                    'num' => $num,
                    'id_reserva' => $info->id_reserva,
                    'fecha_reg' => formato_fecha($info->fecha_reg, 2),
                    'canal' => $info->canal,
                    'cod_reserva' => $info->cod_reserva,
                    'cliente' => $cliente,
                    'servicios' => $servicios,
                    'valor' => $valor,
                    'descuento' => $descuento,
                    'total' => $total,
                    'estado' => $info->estado_reserva,
                    'reviews' => $reviews,
                ];

                $num++;

            }
        }

        $total_results = $this->Model_reservas->query_num_reservas($search, $estado, $id_servicio, $desde, $hasta);

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

    public function reserva_get($id_reserva = null)
    {

        $valida_token = validate_token($this->_args);

        if (!$valida_token["status"]) {
            $response = [
                'status' => false,
                'message' => "Token incorrecto",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        if ($id_reserva == null) {
            $response = [
                'status' => false,
                'message' => "No se envió el ID de reserva.",
            ];
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

        $query_reserva = $this->Model_reservas->query_reserva($id_reserva);

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
            'email' => $info_cliente->email,
            'telefono' => $info_cliente->cod_pais . $info_cliente->telefono,
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
        $reviews = [];
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

                switch ($servicio->tipo_servicio) {

                    case 1:

                        $valor_reserva = get_valor_reserva($servicio->adultos, $servicio->ninos, $servicio->infantes, $servicio->valor_neto, $servicio->valor_venta, $servicio->valor_neto_ninos, $servicio->valor_venta_ninos, $servicio->valor_neto_infantes, $servicio->valor_venta_infantes);

                        $valor_servicio = $valor_reserva["valor_venta"];

                        $valor_neto = $valor_reserva["valor_neto"];

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
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
                            'id_servicio_reserva_x' => encode($servicio->id_servicio_reserva),
                            'servicio' => [
                                'id_servicio' => $servicio->id_servicio,
                                'id_actividad' => $info_actividad->id_actividad,
                                'servicio' => $info_actividad->servicio,
                            ],
                            'valor_neto' => $valor_neto,
                            'valor_venta' => $valor_servicio,
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

                        $valor_neto = $servicio->valor_neto;
                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_paquete = $this->Model_reservas->query_reserva_paquete($servicio->id_servicio_reserva);

                        if ($query_reserva_paquete->num_rows() == 0) {
                            break;
                        }

                        $info_reserva_paquete = $query_reserva_paquete->row();

                        $detalle = [
                            'tipo' => 2,
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
                            'id_servicio_reserva_x' => encode($servicio->id_servicio_reserva),
                            'valor_neto' => $valor_neto,
                            'valor_venta' => $valor_servicio,
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
                                'ciudad_pais' => $info_reserva_paquete->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva_paquete->id_ciudad_destino,
                                'ciudad_pais' => $info_reserva_paquete->ciudad_destino,
                            ],
                            'proveedor' => [
                                'id_proveedor' => $info_reserva_paquete->id_proveedor,
                                'proveedor' => $info_reserva_paquete->proveedor,
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
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
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

                        $valor_neto = $servicio->valor_neto;
                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_tiquete = $this->Model_reservas->query_reserva_tiquete($servicio->id_servicio_reserva);

                        if ($query_reserva_tiquete->num_rows() == 0) {
                            break;
                        }

                        $info_reserva_tiquete = $query_reserva_tiquete->row();

                        $detalle = [
                            'tipo' => 4,
                            'tipo_tiquete' => $info_reserva_tiquete->tipo_tiquete,
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
                            'id_servicio_reserva_x' => encode($servicio->id_servicio_reserva),
                            'valor_neto' => $valor_neto,
                            'valor_venta' => $valor_servicio,
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
                                'ciudad_pais' => $info_reserva_tiquete->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva_tiquete->id_ciudad_destino,
                                'ciudad_pais' => $info_reserva_tiquete->ciudad_destino,
                            ],
                            'proveedor' => [
                                'id_proveedor' => $info_reserva_tiquete->id_proveedor,
                                'proveedor' => $info_reserva_tiquete->proveedor,
                            ],
                            'vouchers' => $vouchers
                        ];

                        break;
                    case 5:

                        $valor_neto = $servicio->valor_neto;
                        $valor_servicio = $servicio->valor_venta;

                        $query_reserva_asistencia = $this->Model_reservas->query_reserva_asistencia($servicio->id_servicio_reserva);

                        if ($query_reserva_asistencia->num_rows() == 0) {
                            break;
                        }

                        $info_reserva = $query_reserva_asistencia->row();

                        $detalle = [
                            'tipo' => 5,
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
                            'id_servicio_reserva_x' => encode($servicio->id_servicio_reserva),
                            'valor_neto' => $valor_neto,
                            'valor_venta' => $valor_servicio,
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
                                'ciudad_pais' => $info_reserva->ciudad_origen,
                            ],
                            'destino' => [
                                'id_ciudad' => $info_reserva->id_ciudad_destino,
                                'ciudad_pais' => $info_reserva->ciudad_destino,
                            ],
                            'proveedor' => [
                                'id_proveedor' => $info_reserva->id_proveedor,
                                'proveedor' => $info_reserva->proveedor,
                            ],
                            'vouchers' => $vouchers
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
                            'id_servicio_reserva' => $servicio->id_servicio_reserva,
                            'id_servicio_reserva_x' => encode($servicio->id_servicio_reserva),
                            'descripcion' => $info_reserva->descripcion,
                            'valor_neto' => $valor_neto,
                            'valor_venta' => $valor_servicio,
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

        $pasajeros = $this->Model_reservas->query_pasajeros($info->id_reserva)->result();

        $notas = $this->Model_reservas->query_notas($info->id_reserva)->result();

        $reserva = [
            'id_reserva' => $info->id_reserva,
            'id_reserva_x' => encode($info->id_reserva),
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cliente' => $cliente,
            'pasajero' => $pasajero,
            'servicios' => $servicios,
            'valor' => $valor,
            'estado' => $info->estado_reserva,
            'pagos' => $pagos,
            'total_pagos' => $total_pagos,
            'saldo' => $saldo,
            'descuento' => $descuento,
            'total' => $total,
            'reviews' => $reviews,
            'notas' => $notas,
            'pasajeros' => $pasajeros
        ];

        $response = [
            'status' => true,
            'data' => $reserva,
        ];
        $this->response($response, 200);

    }

    public function update_servicio_post()
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
        if (!valida_permiso($id_usuario, 'GRESERVAS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        switch ($data_input->servicio->tipo) {
            case 1:

                if ($data_input->servicio->fecha_actividad < date("Y-m-d")) {
                    $response = [
                        'status' => false,
                        'message' => "La fecha de actividad debe ser superior o igual a la fecha actual.",
                    ];
                    $this->response($response, 200);
                }

                //Consultar la temporada
                $info_actividad = $this->Model_servicios->query_actividad_servicio($data_input->servicio->id_servicio)->row();
                $info_modalidad = $this->Model_actividades->query_modalidad_actividad($info_actividad->id_actividad)->row();

                $query_temporada = $this->Model_actividades->query_temporada_actividad($info_modalidad->id_modalidad_actividad, $data_input->servicio->fecha_actividad);

                if ($query_temporada->num_rows() == 0) {
                    $response = [
                        'status' => false,
                        'message' => "No se han definido tarifas para la fecha seleccionada.",
                    ];
                    $this->response($response, 200);
                }

                $data_update = [
                    'id_servicio' => $data_input->servicio->id_servicio,
                    'adultos' => $data_input->servicio->adultos,
                    'ninos' => $data_input->servicio->ninos,
                    'infantes' => $data_input->servicio->infantes,
                    'valor_neto' => $query_temporada->row()->valor_neto_adultos,
                    'valor_venta' => $query_temporada->row()->valor_venta_adultos,
                    'valor_neto_ninos' => $query_temporada->row()->valor_neto_ninos,
                    'valor_venta_ninos' => $query_temporada->row()->valor_venta_ninos,
                    'valor_neto_infantes' => $query_temporada->row()->valor_neto_infantes,
                    'valor_venta_infantes' => $query_temporada->row()->valor_venta_infantes
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_actividad.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_actividad', [
                    'fecha_actividad' => $data_input->servicio->fecha_actividad,
                    'id_horario_actividad' => $data_input->servicio->id_horario,
                ]);

                break;

            case 2:
                $data_update = [
                    'adultos' => $data_input->servicio->num_pasajeros,
                    'valor_neto' => $data_input->servicio->valor_neto,
                    'valor_venta' => $data_input->servicio->valor_venta,
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_paquete.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_paquete', [
                    'fecha_ida' => $data_input->servicio->fecha_ida,
                    'fecha_regreso' => $data_input->servicio->fecha_regreso,
                    'id_ciudad_origen' => $data_input->servicio->origen,
                    'id_ciudad_destino' => $data_input->servicio->destino,
                    'id_proveedor' => $data_input->servicio->id_proveedor,
                ]);
                break;
            case 3:
                $data_update = [
                    'id_servicio' => $data_input->servicio->id_servicio,
                    'adultos' => $data_input->servicio->num_pasajeros,
                    'valor_neto' => $data_input->servicio->valor_neto,
                    'valor_venta' => $data_input->servicio->valor_venta,
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_hoteles.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_hoteles', [
                    'fecha_ida' => $data_input->servicio->fecha_ida,
                    'fecha_regreso' => $data_input->servicio->fecha_regreso,
                ]);
                break;

            case 4:
                $data_update = [
                    'adultos' => $data_input->servicio->num_pasajeros,
                    'valor_neto' => $data_input->servicio->valor_neto,
                    'valor_venta' => $data_input->servicio->valor_venta,
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_tiquete.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_tiquete', [
                    'tipo_tiquete' => $data_input->servicio->tipo_tiquete,
                    'fecha_ida' => $data_input->servicio->fecha_ida,
                    'fecha_regreso' => $data_input->servicio->fecha_regreso,
                    'id_ciudad_origen' => $data_input->servicio->origen,
                    'id_ciudad_destino' => $data_input->servicio->destino,
                    'id_proveedor' => $data_input->servicio->id_proveedor,
                ]);
                break;
            case 5:
                $data_update = [
                    'adultos' => $data_input->servicio->num_pasajeros,
                    'valor_neto' => $data_input->servicio->valor_neto,
                    'valor_venta' => $data_input->servicio->valor_venta,
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_asistencia.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_asistencia', [
                    'fecha_ida' => $data_input->servicio->fecha_ida,
                    'fecha_regreso' => $data_input->servicio->fecha_regreso,
                    'id_ciudad_origen' => $data_input->servicio->origen,
                    'id_ciudad_destino' => $data_input->servicio->destino,
                    'id_proveedor' => $data_input->servicio->id_proveedor,
                ]);
                break;
            case 6:

                $data_update = [
                    'valor_neto' => $data_input->servicio->valor_neto,
                    'valor_venta' => $data_input->servicio->valor_venta,
                ];

                $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicios_reserva', $data_update);

                $this->db->where('servicio_reserva_otros.id_servicio_reserva', $data_input->servicio->id_servicio_reserva);
                $this->db->update('servicio_reserva_otros', [
                    'descripcion' => $data_input->servicio->descripcion,
                ]);
                break;
        }

        $servicio_reserva = $this->Model_reservas->query_servicio_reserva($data_input->servicio->id_servicio_reserva)->row();

        set_nota($id_usuario, $servicio_reserva->id_reserva, "Se actualizó el servicio ID {$data_input->servicio->id_servicio_reserva} la reserva.");

        $response = [
            'status' => true,
            'message' => "Se ha actualizado la reserva.",
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

        #Validación de permiso de gestión de servicios
        if (!valida_permiso($id_usuario, 'GRESERVAS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $servicio_reserva = $this->Model_reservas->query_servicio_reserva($data_input->id_servicio_reserva)->row();

        set_nota($id_usuario, $servicio_reserva->id_reserva, "Se eliminó un servicio de la reserva.");

        $this->db->where('servicios_reserva.id_servicio_reserva', $data_input->id_servicio_reserva);
        $this->db->delete('servicios_reserva');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el servicio de la reserva.",
        ];

        $this->response($response, 200);

    }

    public function set_pago_post()
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

        $data_insert = [
            'id_reserva' => $data_input->id_reserva,
            'id_usuario' => $id_usuario,
            'fecha_reg' => date("Y-m-d H:i:s"),
            'fecha_pago' => $data_input->pago->fecha,
            'medio' => $data_input->pago->medio,
            'valor' => $data_input->pago->valor,
        ];

        $this->db->insert('pagos', $data_insert);

        $valor = number_format($data_input->pago->valor, 0, ',', '.');

        set_nota($id_usuario, $data_input->id_reserva, "Se agregó pago por {$valor} a la reserva.");

        $response = [
            'status' => true,
            'message' => "Se ha registro el pago en la reserva.",
        ];

        $this->response($response, 200);

    }

    public function delete_pago_post()
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

        $info_pago = $this->Model_reservas->query_pago($data_input->id_pago)->row();

        set_nota($id_usuario, $info_pago->id_reserva, "Se eliminó pago por {$info_pago->valor} a la reserva.");

        $this->db->where('pagos.id_pago', $data_input->id_pago);
        $this->db->delete('pagos');

        $response = [
            'status' => true,
            'message' => "Se ha eliminó el pago en la reserva.",
        ];

        $this->response($response, 200);

    }

    public function aprobar_reserva_post()
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
        if (!valida_permiso($id_usuario, 'GRESERVAS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $cod_voucher = get_cod_voucher();

        $this->db->where('servicios_reserva.id_reserva', $data_input->id_reserva);
        $this->db->update('servicios_reserva', ['estado_servicio' => 1, "cod_voucher" => $cod_voucher]);

        $this->db->where('reservas.id_reserva', $data_input->id_reserva);
        $this->db->update('reservas', ['estado_reserva' => 1]);

        $query_reserva = $this->Model_reservas->query_reserva($data_input->id_reserva);

        $info = $query_reserva->row();

        $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
        $cliente = [
            'id_cliente' => $info_cliente->id_cliente,
            'nombres' => $info_cliente->nombres,
            'apellidos' => $info_cliente->apellidos,
            'email' => $info_cliente->email,
        ];

        $reserva = [
            'id_reserva' => encode($info->id_reserva),
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cliente' => $cliente,
            'estado_reserva' => $info->estado_reserva,
        ];

        $this->load->helper("templates_email");
        $this->load->helper("email");

        $html = get_mail_reserva($reserva);

        $data_email = [
            'to' => $cliente["email"],
            'nombre_cliente' => mb_strtoupper($cliente["nombres"] . " " . $cliente["apellidos"]),
            'asunto' => "RESERVA APROBADA",
            'html' => $html,
        ];

        send_mail($data_email);

        set_nota($id_usuario, $data_input->id_reserva, "Se aprobó la reserva.");

        $response = [
            'status' => true,
            'message' => "Se ha aprobó la reserva.",
        ];

        $this->response($response, 200);

    }

    public function set_pago_epayco_post()
    {

        $data_input = json_decode(file_get_contents("php://input"));

        $data_insert = [
            'id_reserva' => decode($data_input->id_reserva),
            'fecha' => $data_input->fecha,
            'referencia_epayco' => $data_input->referencia_epayco,
            'valor' => $data_input->valor,
            'cod_respuesta' => $data_input->cod_respuesta,
            'transaccion_id' => $data_input->transaccion_id,
            'test' => $data_input->test,
        ];

        $this->db->insert('pagos_epayco', $data_insert);

        if ($data_input->cod_respuesta == 1) {

            $data_insert = [
                'id_reserva' => decode($data_input->id_reserva),
                'fecha_reg' => date("Y-m-d H:i:s"),
                'fecha_pago' => $data_input->fecha,
                'valor' => $data_input->valor,
                'medio' => 0,
            ];
            $this->db->insert('pagos', $data_insert);

            //Consulta parámetros para validar si se aprueba  o no la reserva automáticamente.
            $query_aprobar = $this->Model_agencias->query_parametro('APROBACION_RESERVAS');
            if ($query_aprobar->num_rows() != 0) {
                if ($query_aprobar->row()->valor == "1") {

                    $cod_voucher = get_cod_voucher();

                    $this->db->where('servicios_reserva.id_reserva', decode($data_input->id_reserva));
                    $this->db->update('servicios_reserva', ['estado_servicio' => 1, "cod_voucher" => $cod_voucher]);

                    $this->db->where('reservas.id_reserva', decode($data_input->id_reserva));
                    $this->db->update('reservas', ['estado_reserva' => 1]);

                    $query_reserva = $this->Model_reservas->query_reserva(decode($data_input->id_reserva));

                    $info = $query_reserva->row();

                    $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();

                    $cliente = [
                        'id_cliente' => $info_cliente->id_cliente,
                        'nombres' => $info_cliente->nombres,
                        'apellidos' => $info_cliente->apellidos,
                        'email' => $info_cliente->email,
                    ];

                    $servicios = [];
                    $valor = 0;
                    $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
                    if ($query_servicios->num_rows() != 0) {
                        foreach ($query_servicios->result() as $servicio) {

                            $valor_servicio = ($servicio->adultos + $servicio->ninos) * $servicio->valor_venta;

                            $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                            $servicios[] = [
                                'id_servicio_reserva' => $servicio->id_servicio_reserva,
                                'servicio' => [
                                    'id_servicio' => $servicio->id_servicio,
                                    'id_actividad' => $info_actividad->id_actividad,
                                    'servicio' => $servicio->servicio,
                                ],
                                'valor_venta' => $valor_servicio,
                                'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                                'fecha_actividad' => [
                                    'f' => formato_fecha($servicio->fecha_actividad, 1),
                                    'sf' => $servicio->fecha_actividad,
                                ],
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
                        'servicios' => $servicios,
                        'valor' => $valor,
                        'estado_reserva' => $info->estado_reserva,
                    ];

                    $this->load->helper("templates_email");
                    $this->load->helper("email");

                    $html = get_mail_reserva($reserva);

                    $data_email = [
                        'to' => $cliente["email"],
                        'nombre_cliente' => mb_strtoupper($cliente["nombres"] . " " . $cliente["apellidos"]),
                        'asunto' => "RESERVA APROBADA",
                        'html' => $html,
                    ];

                    send_mail($data_email);
                }
            }

        }

        $response = [
            'status' => true,
        ];

        $this->response($response, 200);

    }

    public function reporte_get()
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

        $url = null;

        if ($data_input["tipo_fecha"] == 3) {
            $reservas = $this->Model_comentarios_reservas->query_comentarios_reservas_reporte($data_input["desde"], $data_input["hasta"], $data_input["estado"]);
            $path = "download/reporte_comentarios/" . $data_input["estado"] . "/";
        } else {
            $reservas = $this->Model_reservas->query_reservas_reporte($data_input["tipo_fecha"], $data_input["desde"], $data_input["hasta"]);
            $path = "download/reporte/" . $data_input["tipo_fecha"] . "/";
        }

        if ($reservas["total_reservas"] != 0) {
            $url = base_url() . $path . $data_input["desde"] . "/" . $data_input["hasta"] . "/" . encode($valida_token["id_usuario"]);
        }

        $response = [
            'status' => true,
            'results' => $reservas["total_reservas"],
            'url' => $url,
        ];

        $this->response($response, 200);

    }

    public function enviar_email_post()
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

        $query_reserva = $this->Model_reservas->query_reserva($data_input->id_reserva);

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
            'email' => $info_cliente->email,
        ];

        $servicios = [];
        $valor = 0;
        $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);
        if ($query_servicios->num_rows() != 0) {
            foreach ($query_servicios->result() as $servicio) {

                $valor_servicio = ($servicio->adultos + $servicio->ninos) * $servicio->valor_venta;

                $info_actividad = $this->Model_servicios->query_actividad_servicio($servicio->id_servicio)->row();

                $horario = null;
                $estar = null;
                if ($servicio->id_horario_actividad != null) {
                    $info_horario = $this->Model_actividades->query_horario($servicio->id_horario_actividad)->row();
                    $horario = $info_horario->desde;

                    $query_horario_estar = $this->Model_actividades->query_horario_estar($servicio->id_punto_salida, $info_horario->id_horario);

                    if ($query_horario_estar->num_rows() != 0) {
                        $estar = $query_horario_estar->row()->estar;
                    } else {
                        $estar = $info_horario->estar;
                    }
                }

                $punto_salida = null;
                if ($servicio->id_punto_salida != null) {
                    $info_punto_salida = $this->Model_actividades->query_punto_salida($servicio->id_punto_salida)->row();
                    $punto_salida = [
                        'punto_salida' => $info_punto_salida->punto_salida,
                        'link_mapa' => $info_punto_salida->link_mapa,
                    ];
                }

                $servicios[] = [
                    'id_servicio_reserva' => $servicio->id_servicio_reserva,
                    'servicio' => [
                        'id_servicio' => $servicio->id_servicio,
                        'id_actividad' => $info_actividad->id_actividad,
                        'servicio' => $servicio->servicio,
                    ],
                    'valor_venta' => $valor_servicio,
                    'num_pasajeros' => $servicio->adultos + $servicio->ninos,
                    'fecha_actividad' => [
                        'f' => formato_fecha($servicio->fecha_actividad, 1),
                        'sf' => $servicio->fecha_actividad,
                    ],
                    'estar' => $estar,
                    'horario' => $horario,
                    'punto_salida' => $punto_salida,
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
            'servicios' => $servicios,
            'valor' => $valor,
            'estado_reserva' => $info->estado_reserva,
        ];

        $this->load->helper("templates_email");
        $this->load->helper("email");

        $html = get_mail_reserva($reserva);

        $data_email = [
            'to' => $data_input->email,
            'nombre_cliente' => mb_strtoupper($cliente["nombres"] . " " . $cliente["apellidos"]),
            'asunto' => "TU RESERVA EN VIAGGI",
            'html' => $html,
        ];

        send_mail($data_email);

        $response = [
            'status' => true,
            'message' => "Se ha enviado el email de la reserva.",
        ];

        $this->response($response, 200);

    }

    public function reviews_get()
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

        $page = 1;
        if (isset($data_input["page"])) {
            $page = $data_input["page"];
        }

        $estado = null;
        if (isset($data_input["estado"])) {
            $estado = $data_input["estado"];
        }

        $id_servicio = null;
        if (isset($data_input["id_servicio"])) {
            $id_servicio = $data_input["id_servicio"];
        }

        $reviews = [];

        $per_page = 10;

        $offset = ($page - 1) * $per_page;

        $query_reviews = $this->Model_reservas->query_reviews($estado, $id_servicio, $offset, $per_page);

        if ($query_reviews->num_rows() != 0) {

            $num = $offset + 1;

            foreach ($query_reviews->result() as $info) {

                $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();
                $cliente = [
                    'id_cliente' => $info_cliente->id_cliente,
                    'nombres' => $info_cliente->nombres,
                    'apellidos' => $info_cliente->apellidos,
                ];

                $reviews[] = [
                    'num' => $num,
                    'id_review' => $info->id_review,
                    'id_reserva' => $info->id_reserva,
                    'cod_reserva' => $info->cod_reserva,
                    'cliente' => $cliente,
                    'fecha_reg' => formato_fecha($info->fecha_reg, 2),
                    'servicio' => $info->servicio,
                    'valor' => $info->valor,
                    'comentarios' => $info->comentarios,
                    'resumen_comentarios' => substr($info->comentarios, 0, 80) . "...",
                    'estado' => $info->estado,
                ];

                $num++;

            }
        }

        $total_results = $this->Model_reservas->query_num_reviews($estado, $id_servicio);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'results' => count($reviews),
            'data' => $reviews,
            'total_pages' => $total_pages,
        ];

        $this->response($response, 200);

    }

    public function update_review_post()
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

        $this->db->where('reviews.id_review', $data_input->id_review);
        $this->db->update('reviews', ["estado" => $data_input->estado]);

        $response = [
            'status' => true,
            'message' => "Se ha actualizado el estado de la reseña.",
        ];

        $this->response($response, 200);
    }

    public function quitar_descuento_post()
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
        if (!valida_permiso($id_usuario, 'GRESERVAS')) {
            $response = [
                'status' => false,
                'message' => "Acceso no autorizado",
            ];
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        }

        $data_input = json_decode(file_get_contents("php://input"));

        $this->db->where('reservas.id_reserva', $data_input->id_reserva);
        $this->db->update('reservas', ['descuento' => 0]);

        set_nota($id_usuario, $data_input->id_reserva, "Se eliminó el descuento de la reserva.");

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el descuento.",
        ];

        $this->response($response, 200);
    }

    public function cotizaciones_get()
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

        $per_page = 20;

        $offset = ($page - 1) * $per_page;

        $query_cotizaciones = $this->Model_reservas->query_cotizaciones($search, $estado, $offset, $per_page);

        $total_results = $this->Model_reservas->query_num_cotizaciones($search, $estado);

        $total_pages = 1;
        if ($page != null) {
            $total_pages = ceil($total_results / $per_page);
        }

        $response = [
            'status' => true,
            'data' => $query_cotizaciones->result(),
            'total_pages' => $total_pages,
        ];

        $this->response($response, 200);

    }

    public function delete_voucher_post()
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

        $voucher = $this->Model_reservas->query_voucher($data_input->id_voucher)->row();

        unlink($voucher->url);

        $this->db->where('vouchers.id_voucher', $data_input->id_voucher);
        $this->db->delete('vouchers');

        $response = [
            'status' => true,
            'message' => "Se ha eliminado el voucher.",
        ];

        $this->response($response, 200);

    }

    public function set_nota_post()
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

        $id_usuario = $valida_token["id_usuario"];

        set_nota($id_usuario, $data_input->id_reserva, $data_input->nota->nota, $data_input->nota->tipo);

        $response = [
            'status' => true,
            'message' => "Se ha agregado la nota a la reserva.",
        ];

        $this->response($response, 200);

    }

    public function update_pasajeros_post()
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

        $this->db->where("pasajeros.id_reserva", $data_input->id_reserva);
        $this->db->delete("pasajeros");

        if (count($data_input->pasajeros) != 0) {
            foreach ($data_input->pasajeros as $pasajero) {
                $this->db->insert("pasajeros", [
                    'id_reserva' => $data_input->id_reserva,
                    'nombres' => $pasajero->nombres,
                    'apellidos' => $pasajero->apellidos
                ]);
            }
        }

        $id_usuario = $valida_token["id_usuario"];

        set_nota($id_usuario, $data_input->id_reserva, "Se actualizaron los pasajeros de la reserva.");

        $response = [
            'status' => true,
            'message' => "Se actualizaron los pasajeros de la reserva.",
        ];

        $this->response($response, 200);

    }

    public function anular_reserva_post()
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

        $this->db->where("reservas.id_reserva", $data_input->id_reserva);
        $this->db->update("reservas", ["estado_reserva" => 3]);

        $id_usuario = $valida_token["id_usuario"];

        set_nota($id_usuario, $data_input->id_reserva, "Se anuló la reserva.");

        $response = [
            'status' => true,
            'message' => "Se anuló la reserva.",
        ];

        $this->response($response, 200);

    }

}