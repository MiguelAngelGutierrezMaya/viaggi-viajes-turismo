<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_reservas');
        $this->load->model('Model_actividades');
        $this->load->model('Model_servicios');
        $this->load->model('Model_clientes');
    }

    public function voucher($id_reserva_servicio = null)
    {

        if ($id_reserva_servicio == null) {
            echo "<center>404 - No found</center>";
            exit;
        }

        $query_reserva_servicio = $this->Model_reservas->query_servicio_reserva(decode($id_reserva_servicio));

        if ($query_reserva_servicio->num_rows() == 0) {
            echo "<center>404 - No found</center>";
            exit;
        }

        $reserva = null;

        $info_servicio_reserva = $query_reserva_servicio->row();

        $query_reserva = $this->Model_reservas->query_reserva($info_servicio_reserva->id_reserva);

        $info = $query_reserva->row();

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

        $horario = null;
        $estar = null;
        if ($info_servicio_reserva->id_horario_actividad != null) {

            $info_horario = $this->Model_actividades->query_horario($info_servicio_reserva->id_horario_actividad)->row();
            $horario = [
                'id_horario' => $info_horario->id_horario,
                'desde' => $info_horario->desde,
                'hasta' => $info_horario->hasta,
                'estar' => $info_horario->estar,
            ];

            $query_horario_estar = $this->Model_actividades->query_horario_estar($info_servicio_reserva->id_punto_salida, $info_horario->id_horario);

            if ($query_horario_estar->num_rows() != 0) {
                $estar = $query_horario_estar->row()->estar;
            } else {
                $estar = $info_horario->estar;
            }

        }

        $punto_salida = null;
        if ($info_servicio_reserva->id_punto_salida != null) {
            $info_punto_salida = $this->Model_actividades->query_punto_salida($info_servicio_reserva->id_punto_salida)->row();

            $foto = null;
            if ($info_punto_salida->foto_punto_salida != null) {
                $foto = base_url() . $info_punto_salida->foto_punto_salida;
            }

            $punto_salida = [
                'id_punto_salida' => $info_punto_salida->id_punto_salida,
                'punto_salida' => $info_punto_salida->punto_salida,
                'ubicacion' => $info_punto_salida->ubicacion_punto_salida,
                'link_mapa' => $info_punto_salida->link_mapa,
                'foto' => $foto,
            ];
        }

        $info_actividad = $this->Model_servicios->query_actividad_servicio($info_servicio_reserva->id_servicio)->row();

        $valor_servicio = ($info_servicio_reserva->adultos + $info_servicio_reserva->ninos) * $info_servicio_reserva->valor_venta;

        if ($estar == null) {
            $estar = $info_actividad->estar;
        }

        $descuento = 0;
        if ($info->descuento != 0) {
            $descuento = $info->descuento;
        }

        $query_servicios = $this->Model_reservas->query_servicios_reserva($info->id_reserva);

        $descuento_por_servicio = 0;
        if ($query_servicios->num_rows() != 0) {
            $descuento_por_servicio = $descuento / $query_servicios->num_rows();
        }

        $valor_servicio = $valor_servicio - $descuento_por_servicio;

        $reserva = [
            'id_reserva' => $info->id_reserva,
            'fecha_reg' => formato_fecha($info->fecha_reg, 2),
            'canal' => $info->canal,
            'cod_reserva' => $info->cod_reserva,
            'cod_voucher' => $info_servicio_reserva->cod_voucher,
            'cliente' => $cliente,
            'pasajero' => $pasajero,
            'num_pasajeros' => $info_servicio_reserva->adultos + $info_servicio_reserva->ninos,
            'fecha_actividad' => [
                'f' => formato_fecha($info_servicio_reserva->fecha_actividad, 1),
                'sf' => $info_servicio_reserva->fecha_actividad,
            ],
            'horario' => $horario,
            'punto_salida' => $punto_salida,
            'servicio' => [
                'id_servicio' => $info_servicio_reserva->id_servicio,
                'id_actividad' => $info_actividad->id_actividad,
                'servicio' => $info_servicio_reserva->servicio,
                'datos_tour' => $info_actividad->datos_tour,
                'estar' => $estar,
            ],
            'valor_servicio' => formato_moneda($valor_servicio),
        ];

        $this->load->view('voucher', $reserva);

    }

    public function reporte($tipo_fecha, $desde, $hasta, $id_usuario)
    {

        if (PHP_SAPI == 'cli') {
            die('Este archivo solo se puede ver desde un navegador web');
        }

        $id_usuario = decode($id_usuario);

        $permiso = valida_permiso($id_usuario, 'GREPORTES');

        require_once 'libs/PHPExcel/PHPExcel.php';

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Viaggi")
            ->setLastModifiedBy("Viaggi")
            ->setTitle("REPORTES")
            ->setSubject("REPORTES")
            ->setDescription("REPORTES")
            ->setKeywords("REPORTES")
            ->setCategory("REPORTES");

        $titulosColumnas = array(
            'Nº',
            'FECHA REGISTRO',
            'CODIGO RESERVA',
            'CODIGO VOUCHER',
            'CLIENTE',
            'SERVICIO',
            'PASAJEROS',
            'FECHA IDA/ACTIVIDAD',
            'FECHA REGRESO',
            'ORIGEN',
            'DESTINO',
            'PRECIO NETO',
            'PRECIO VENTA',
            'TKT',
            'PROVEEDOR',
            'ESTADO'
        );

        $cantidad_de_columnas_a_crear = count($titulosColumnas);
        $contador = 0;
        $letra = 'A';
        while ($contador < $cantidad_de_columnas_a_crear) {
            $columnas[$contador] = $letra;
            $contador++;
            $letra++;
        }

        $c = 0;
        foreach ($columnas as $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($value . '1', $titulosColumnas[$c]);
            $c++;
        }

        $reservas = $this->Model_reservas->query_reservas_reporte($tipo_fecha, $desde, $hasta);

        if ($reservas["total_reservas"] != 0) {

            $num = 1;
            $i = 2;
            $cont = 1;

            foreach ($reservas["actividades"] as $info_reserva) {

                $valor_reserva = get_valor_reserva($info_reserva->adultos, $info_reserva->ninos, $info_reserva->infantes, $info_reserva->valor_neto, $info_reserva->valor_venta, $info_reserva->valor_neto_ninos, $info_reserva->valor_venta_ninos, $info_reserva->valor_neto_infantes, $info_reserva->valor_venta_infantes);

                $valor_venta = $valor_reserva["valor_venta"];

                $valor_neto = $valor_reserva["valor_neto"];

                $tkt = $valor_venta - $valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, $info_reserva->servicio)
                    ->setCellValue('G' . $i, $info_reserva->adultos + $info_reserva->ninos + $info_reserva->infantes)
                    ->setCellValue('H' . $i, $info_reserva->fecha_actividad)
                    ->setCellValue('L' . $i, $valor_neto)
                    ->setCellValue('M' . $i, $valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

            foreach ($reservas["paquetes"] as $info_reserva) {

                $tkt = $info_reserva->valor_venta - $info_reserva->valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, $info_reserva->servicio)
                    ->setCellValue('G' . $i, $info_reserva->pasajeros)
                    ->setCellValue('H' . $i, $info_reserva->fecha_ida)
                    ->setCellValue('I' . $i, $info_reserva->fecha_regreso)
                    ->setCellValue('J' . $i, $info_reserva->origen)
                    ->setCellValue('K' . $i, $info_reserva->destino)
                    ->setCellValue('L' . $i, $permiso ? $info_reserva->valor_neto : '')
                    ->setCellValue('M' . $i, $info_reserva->valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('O' . $i, $info_reserva->proveedor)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

            foreach ($reservas["hoteles"] as $info_reserva) {

                $tkt = $info_reserva->valor_venta - $info_reserva->valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, "HOSPEDAJE: " . $info_reserva->servicio)
                    ->setCellValue('G' . $i, $info_reserva->pasajeros)
                    ->setCellValue('H' . $i, $info_reserva->fecha_ida)
                    ->setCellValue('L' . $i, $permiso ? $info_reserva->valor_neto : '')
                    ->setCellValue('M' . $i, $info_reserva->valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

            foreach ($reservas["tiquetes"] as $info_reserva) {

                $tkt = $info_reserva->valor_venta - $info_reserva->valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, $info_reserva->servicio)
                    ->setCellValue('G' . $i, $info_reserva->pasajeros)
                    ->setCellValue('H' . $i, $info_reserva->fecha_ida)
                    ->setCellValue('I' . $i, $info_reserva->fecha_regreso)
                    ->setCellValue('J' . $i, $info_reserva->origen)
                    ->setCellValue('K' . $i, $info_reserva->destino)
                    ->setCellValue('L' . $i, $permiso ? $info_reserva->valor_neto : '')
                    ->setCellValue('M' . $i, $info_reserva->valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('O' . $i, $info_reserva->proveedor)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

            foreach ($reservas["asistencias"] as $info_reserva) {

                $tkt = $info_reserva->valor_venta - $info_reserva->valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, $info_reserva->servicio)
                    ->setCellValue('G' . $i, $info_reserva->pasajeros)
                    ->setCellValue('H' . $i, $info_reserva->fecha_ida)
                    ->setCellValue('I' . $i, $info_reserva->fecha_regreso)
                    ->setCellValue('J' . $i, $info_reserva->origen)
                    ->setCellValue('K' . $i, $info_reserva->destino)
                    ->setCellValue('L' . $i, $permiso ? $info_reserva->valor_neto : '')
                    ->setCellValue('M' . $i, $info_reserva->valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('O' . $i, $info_reserva->proveedor)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

            foreach ($reservas["otros"] as $info_reserva) {

                $tkt = $info_reserva->valor_venta - $info_reserva->valor_neto;

                $estado = Download::estado_reserva($info_reserva->estado_reserva);

                $num++;

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cont)
                    ->setCellValue('B' . $i, $info_reserva->fecha_reg)
                    ->setCellValue('C' . $i, $info_reserva->cod_reserva)
                    ->setCellValue('D' . $i, $info_reserva->cod_voucher)
                    ->setCellValue('E' . $i, $info_reserva->cliente)
                    ->setCellValue('F' . $i, $info_reserva->servicio)
                    ->setCellValue('L' . $i, $info_reserva->valor_neto)
                    ->setCellValue('M' . $i, $info_reserva->valor_venta)
                    ->setCellValue('N' . $i, $tkt)
                    ->setCellValue('P' . $i, $estado);

                $cont++;
                $i++;

            }

        }

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('A')->setWidth(5);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('B')->setWidth(21);

        for ($l = 'C'; $l <= 'D'; $l++) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->getColumnDimension($l)->setWidth(15);
        }

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('E')->setWidth(45);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('F')->setWidth(35);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('G')->setWidth(15);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('H')->setWidth(15);

        for ($l = 'J'; $l <= 'K'; $l++) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->getColumnDimension($l)->setWidth(35);
        }

        for ($l = 'L'; $l <= 'M'; $l++) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->getColumnDimension($l)->setWidth(15);
        }

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('N')->setWidth(35);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('P')->setWidth(10);

        $estiloTituloColumnas = array(
            'font' => array(
                'name' => 'Verdana',
                'bold' => true,
            ),
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '51565c',
                    ),
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '51565c',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(
            array(
                'font' => array(
                    'name' => 'Verdana',
                    'color' => array(
                        'rgb' => '000000',
                    ),
                    'size' => 10,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '51565c',
                        ),
                    ),
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap' => true,
                ),
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('A1:Q2')->applyFromArray($estiloTituloColumnas);

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2:Q" . ($i - 1));

        if (!$permiso) {
            $objPHPExcel->getActiveSheet()->removeColumn('L');
            $objPHPExcel->getActiveSheet()->removeColumn('M');
        }

        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 1);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="REPORTE.xlsx"');
        header('Cache-Control: max-age=0');
        header("Refresh:0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit;

    }

    public function estado_reserva($estado)
    {
        switch ($estado) {
            case 0:
                $estado = "Pendiente";
                break;

            case 1:
                $estado = "Aprobada";
                break;

            case 3:
                $estado = "Anulada";
                break;
        }

        return $estado;
    }

}
