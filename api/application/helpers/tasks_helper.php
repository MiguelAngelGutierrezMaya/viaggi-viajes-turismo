<?php

function encode($value)
{
    $ci = &get_instance();
    $ci->load->library('encryption');
    $value = $ci->encryption->encrypt($value);
    $value = strtr($value, array('+' => '.', '=' => '-', '/' => '~'));
    return $value;
}

function decode($value)
{

    $ci = &get_instance();
    $ci->load->library('encryption');
    $value = strtr($value, array('.' => '+', '-' => '=', '~' => '/'));
    $value = $ci->encryption->decrypt($value);
    return $value;
}

function valida_permiso($id_usuario, $permiso)
{

    $ci = &get_instance();

    $ci->load->model("Model_usuarios");

    $usuario = $ci->Model_usuarios->query_usuario($id_usuario)->row();

    if ($usuario->perfil == 1) {
        return true;
    }

    $query_permiso = $ci->Model_usuarios->query_permiso_usuario($id_usuario, $permiso);

    $status = $query_permiso->num_rows() != 0 ? true : false;

    return $status;

}

function validate_token($Headers)
{

    $token = isset($Headers["token"]) ? $Headers["token"] : $Headers["Token"];

    if ($token == null) {
        return false;
    }

    $ci = &get_instance();

    $ci->load->model("Model_usuarios");

    $query_token = $ci->Model_usuarios->query_token_session($token);

    if ($query_token->num_rows() == 0) {
        $data = [
            'status' => false,
        ];
    } else {
        $data = [
            'status' => true,
            'id_usuario' => $query_token->row()->id_usuario,
        ];
    }

    return $data;

}

function formato_fecha($fecha, $tipo)
{

    $fecha_format = null;

    if (($fecha != null) and $fecha != "0000-00-00") {

        date_default_timezone_set('America/Bogota');

        $diasem = date("N", strtotime($fecha));
        $dia = date("d", strtotime($fecha));
        $mes = date("m", strtotime($fecha));
        $mesc = date("m", strtotime($fecha));
        $ano = date("Y", strtotime($fecha));
        $anoc = date("y", strtotime($fecha));
        $hora = date("H:i:s", strtotime($fecha));
        $hora2 = date("H:i", strtotime($fecha));

        switch ($mes) {
            case "01":
                $mesn = $mes;
                $mes = "enero";
                $mescorto = "ene";
                $mescortoM = "ENE";
                break;
            case "02":
                $mesn = $mes;
                $mes = "febrero";
                $mescorto = "feb";
                $mescortoM = "FEB";
                break;
            case "03":
                $mesn = $mes;
                $mes = "marzo";
                $mescorto = "mar";
                $mescortoM = "MAR";
                break;
            case "04":
                $mesn = $mes;
                $mes = "abril";
                $mescorto = "abr";
                $mescortoM = "ABR";
                break;
            case "05":
                $mesn = $mes;
                $mes = "mayo";
                $mescorto = "may";
                $mescortoM = "MAY";
                break;
            case "06":
                $mesn = $mes;
                $mes = "junio";
                $mescorto = "jun";
                $mescortoM = "JUN";
                break;
            case "07":
                $mesn = $mes;
                $mes = "julio";
                $mescorto = "jul";
                $mescortoM = "JUL";
                break;
            case "08":
                $mesn = $mes;
                $mes = "agosto";
                $mescorto = "ago";
                $mescortoM = "AGO";
                break;
            case "09":
                $mesn = $mes;
                $mes = "setiembre";
                $mescorto = "sep";
                $mescortoM = "SEP";
                break;
            case "10":
                $mesn = $mes;
                $mes = "octubre";
                $mescorto = "oct";
                $mescortoM = "OCT";
                break;
            case "11":
                $mesn = $mes;
                $mes = "noviembre";
                $mescorto = "nov";
                $mescortoM = "NOV";
                break;
            case "12":
                $mesn = $mes;
                $mes = "diciembre";
                $mescorto = "dic";
                $mescortoM = "DIC";
                break;
        }

        switch ($diasem) {
            case 1:
                $diasem = "lunes";
                break;
            case 2:
                $diasem = "martes";
                break;
            case 3:
                $diasem = "miércoles";
                break;
            case 4:
                $diasem = "jueves";
                break;
            case 5:
                $diasem = "viernes";
                break;
            case 6:
                $diasem = "sábado";
                break;
            case 7:
                $diasem = "domingo";
                break;
        }

        switch ($tipo) {
            case 1:
            default:
                $fecha_format = $dia . "-" . $mescorto . "-" . $anoc;
                break;

            case 2:
                $fecha_format = $dia . " " . $mescorto . " " . $anoc . ", " . $hora;
                break;

            case 3:
                $fecha_format = $dia . " " . $dia . " de " . $ano;
                break;

            case 4:
                $fecha_format = "$dia-$mesc-$ano";
                break;

            case 5:
                $fecha_format = $mescortoM . "" . $dia . "/" . $ano;
                break;

            case 6:
                $fecha_format = $dia . "/" . $mesn . "/" . $ano;
                break;

            case 7:
                $fecha_format = $hora;
                break;

            case 8:
                $fecha_format = $dia . " de " . $mes . " de " . $ano;
                break;
        }
    }

    return $fecha_format;
}

function get_codigo_otp()
{
    $an = "0123456789";
    $su = strlen($an) - 1;
    return substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1);
}

function get_codigo_reserva()
{
    $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $su = strlen($an) - 1;
    return substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1) .
        substr($an, rand(0, $su), 1);
}

function formato_moneda($valor)
{

    if (ENVIRONMENT == "development") {
        $fmt = numfmt_create('es_CO', NumberFormatter::CURRENCY);
        $valor = numfmt_format($fmt, $valor);

        return $valor;
    }

    $lenguage = 'es_CO.UTF-8';
    putenv("LANG=$lenguage");
    setlocale(LC_MONETARY, $lenguage);
    $valor = money_format('%.0i', $valor);

    return $valor;
}

function get_cod_voucher()
{

    $ci = &get_instance();

    $ci->load->model("Model_reservas");

    $query_ultimo_voucher = $ci->Model_reservas->query_ultimo_voucher();

    $cod_voucher = 10000;
    if ($query_ultimo_voucher->num_rows() != 0) {
        if ($query_ultimo_voucher->row()->cod_voucher != null) {
            $cod_voucher = $query_ultimo_voucher->row()->cod_voucher + 1;

        }

    }

    return $cod_voucher;

}

function set_nota($id_usuario, $id_reserva, $nota, $tipo = 0)
{
    $ci = &get_instance();

    $ci->db->insert('notas_reserva', [
        'fecha_reg' => date('Y-m-d H:i:s'),
        'id_reserva' => $id_reserva,
        'id_usuario' => $id_usuario,
        'tipo' => $tipo,
        'nota' => $nota
    ]);
}

function get_valor_reserva($adultos, $ninos, $infantes, $valor_neto, $valor_venta, $valor_neto_ninos, $valor_venta_ninos, $valor_neto_infantes, $valor_venta_infantes)
{

    return [
        'valor_neto' => ($adultos * $valor_neto + $ninos * $valor_neto_ninos + $infantes * $valor_neto_infantes),
        'valor_venta' => ($adultos * $valor_venta + $ninos * $valor_venta_ninos + $infantes * $valor_venta_infantes)
    ];

}
