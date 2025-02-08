<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends CI_Controller
{

    public function index()
    {
        $this->load->view('header');
        $this->load->view('menu', ['tipo' => 1]);
        $this->load->view('cart/cart');
        $this->load->view('footer');
    }

    public function checkout()
    {
        $this->load->view('header');
        $this->load->view('menu', ['tipo' => 1]);
        $this->load->view('cart/checkout');
        $this->load->view('footer');

    }

    public function pay($id_reserva)
    {

        //Información de la reserva
        $url = BASE_URL_API . 'clientes/reserva/' . $id_reserva;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "x-api-key: " . API_KEY,
        ));
        $data = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($data);

        if (!$result->status) {
            echo "Ocurrió un error consultando la reserva. " . $result->message;
            exit;
        }

        $reserva = $result->data;

        $data_body = [
            'id_reserva' => $id_reserva,
            'cod_reserva' => $reserva->cod_reserva,
            'nombre_cliente' => $reserva->cliente->nombres . " " . $reserva->cliente->apellidos,
            'telefono_cliente' => $reserva->cliente->telefono,
            'valor' => $reserva->saldo,
        ];

        redirect("clientes/reserva/" . $id_reserva);

        /* switch (ENVIRONMENT) {
    case 'development':
    $this->load->view('cart/pay', $data_body);
    break;
    case 'testing':
    case 'production':
    $this->load->view('cart/pay', $data_body);
    break;
    } */

    }

}
