<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notificaciones extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_reservas');
        $this->load->model('Model_actividades');
        $this->load->model('Model_servicios');

    }

    public function reservas()
    {

        $manana = date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 days"));

        $query_reservas = $this->Model_reservas->query_reservas_fecha($manana, 1);

        if ($query_reservas->num_rows() != 0) {

            foreach ($query_reservas->result() as $info) {

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
                        if ($servicio->id_horario_actividad != null) {
                            $info_horario = $this->Model_actividades->query_horario($servicio->id_horario_actividad)->row();
                            $horario = [
                                'id_horario' => $info_horario->id_horario,
                                'desde' => $info_horario->desde,
                                'hasta' => $info_horario->hasta,
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
                            'horario' => $horario,
                            'estar' => $info_actividad->estar,
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
                    'to' => $info_cliente->email,
                    'nombre_cliente' => mb_strtoupper($cliente["nombres"] . " " . $cliente["apellidos"]),
                    'asunto' => "TE RECORDAMOS TU RESERVA CON NOSOTROS",
                    'html' => $html,
                ];

                send_mail($data_email);

            }

        }

    }

    public function reviews()
    {

        $fecha = date("Y-m-d", strtotime(date("Y-m-d") . "- 1 days"));

        $query_reservas = $this->Model_reservas->query_reservas_fecha($fecha, 1);

        if ($query_reservas->num_rows() != 0) {

            foreach ($query_reservas->result() as $info) {

                $info_cliente = $this->Model_reservas->query_cliente($info->id_cliente)->row();

                $cliente = [
                    'id_cliente' => $info_cliente->id_cliente,
                    'nombres' => $info_cliente->nombres,
                    'apellidos' => $info_cliente->apellidos,
                    'email' => $info_cliente->email,
                ];

                $reserva = [
                    'id_reserva' => encode($info->id_reserva),
                    'cliente' => $cliente,
                ];

                $this->load->helper("templates_email");
                $this->load->helper("email");

                $html = get_mail_reviews($reserva);

                $data_email = [
                    'to' => $info_cliente->email,
                    'nombre_cliente' => mb_strtoupper($cliente["nombres"] . " " . $cliente["apellidos"]),
                    'asunto' => "CUÉNTANOS TU EXPERIENCIA",
                    'html' => $html,
                ];

                send_mail($data_email);

            }

        }

    }

}
