<?php
$html = '
<!doctype html>
   <html lang="en">
      <head>
         <meta charset="utf-8">
         <title></title>
         <link href="' . base_url() . 'assets/css/csspdf.css" type="text/css" rel="stylesheet"/>
      </head>
      <body style="font-size: 12px">';

$html .= '
      <div style="text-align:center">
         <img src="' . base_url() . 'media/header_voucher.jpeg" style="width:400px">
      </div>

      <div style="text-align:right">
         <span>
            <span style="font-size:12px">Voucher No.</span> <br>
            <span style="font-size:14px; color: #1464C5">
               <strong> ' . $cod_voucher . '</strong>
            </span>
         </span>
      </div>

      <div>
         <table class="table table-bordered" style="margin-top: 2px;">
            <tr>
               <td> Nombre del pasajero </td>
            </tr>
            <tr>
               <td>
                  <span style="font-size:12px; color: #1464C5">
                     <strong>' . $cliente["nombres"] . ' ' . $cliente["apellidos"] . '</strong>
                  </span>
               </td>
            </tr>
         </table>

         <h5 style="font-size:12px;margin-bottom: 2px"><strong>Detalle de la reserva</strong></h5>
         <table class="table table-bordered" style="margin-top:2px; margin-bottom:2px">
            <tr>
               <td style="width:10%"> Tour </td>
               <td colspan="3"> <span style="font-size:14px; color: #1464C5"> <strong>' . $servicio["servicio"] . '</strong></span> </td>
            </tr>
            <tr>
               <td style="width:15%"> Fecha del tour </td>
               <td> <span style="font-size:14px; color: #1464C5">  <strong> ' . $fecha_actividad["f"] . '</strong></span> </td>
               <td>Salida </td>
               <td> <span style="font-size:14px; color: #1464C5">  <strong> ' . $horario["desde"] . '</strong></span> </td>
            </tr>
            <tr>
               <td style="width:10%"> Estar </td>
               <td> <span style="font-size:14px; color: #1464C5">  <strong>' . $servicio["estar"] . '</strong> </span> </td>
               <td style="width:20%"> Punto de encuentro </td>
               <td> <span style="font-size:14px; color: #1464C5"> <strong>' . $punto_salida["punto_salida"] . '</strong></span> </td>
            </tr>

            <tr>
               <td style="width:10%"> Total Pax </td>
               <td colspan="3"> <span style="font-size:14px; color: #1464C5"> <strong>' . $num_pasajeros . '</strong></span> </td>
            </tr>
         </table>

         <table class="table table-bordered" style="margin-top:5px; margin-bottom:2px">
            <tr>
               <td> Total </td>
               <td style="text-align: right"> <span style="font-size:16px; color: #1464C5"
"><strong>' . $valor_servicio . '</strong></span> </td>
            </tr>
         </table>

         <h5 style="font-size:12px; color: #1464C5; margin-top:2px; margin-bottom:2px; text-align:center "><strong>Instrucciones</strong></h5>
         <table class="table table-bordered" style="margin-top:2px">
            <tr>
              <td style="text-align:center; color: #1464C5;
"><strong>' . $servicio["servicio"] . '</strong></td>
            </tr>
            <tr>
               <td style="text-align:center">
                  <div>
                     Estar puntual en el lugar de recogida acordado. Clic en el enlace para ver en Google Maps.<br>
                     <a href="' . $punto_salida["link_mapa"] . '" target="_blank">' . $punto_salida["link_mapa"] . '</a>
                  </div>  ';

if ($punto_salida["foto"] != null) {
    $html .= '
                     <div style="text-align:center; margin-top:30px">
                        <img style="width:300px" src="' . $punto_salida["foto"] . '">
                     </div>
                            ';
}

$html .= '

                </td>
            </tr>
            <tr>
               <td>
                  <div style="margin-top:2px; text-align:justify">
                     ' . $servicio["datos_tour"] . '
                  </div>
               </td>
            </tr>
         </table>

      </div>

<div>
<h4>Política De Cancelación</h4>
<p>
El Tren turistico Blanco, tiene políticas de devolución de dinero hasta 3 días antes de la fecha de
tour.
</p>
<p>
Si por motivos técnicos no se puede prestar el servicio en el transporte pagado, se podrá ubicar en
un vehículo similar o reprogramar.
</p>
<p>
Sujeto a cupo limitado, si por alguna razón no puede llegar al horario de su reserva está sujeto al
cupo disponible.
</p>
</div>


      ';

$html .= '
     <div style="text-align:center; margin-top:10px">
         <img src="' . base_url() . 'media/footer_voucher.jpeg" style="width:400px">
      </div>
      ';

$html .= '
      </body>
   </html>';

echo $html;
exit;

require_once 'libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

//$html = ob_get_clean();

$options = new Options();

$options->set(array(
    'pdfBackend' => 'PDFLib',
    'defaultMediaType' => 'print',
    'defaultPaperSize' => 'A4',
    'defaultPaperOrientation ' => 'landscape',
    'defaultFont' => 'arial',
    'enable_html5_parser' => true,
    'enable_font_subsetting' => true,
    'isRemoteEnabled' => true,
));

// instantiate and use the dompdf class
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Voucher_" . $cod_voucher . ".pdf");
