<?php

function send_mail($data)
{
    //Incluimos la clase de PHPMailer
    require_once 'libs/phpmailer/class.phpmailer.php';

    $correo = new PHPMailer(); //Creamos una instancia en lugar usar mail()
    //Usamos el SetFrom para decirle al script quien envia el correo
    $correo->SMTPDebug = 2;
    $correo->Helo = "www.arboledadev.com"; //Muy importante para que llegue a hotmail y otros
    $correo->Host = HOST_EMAIL;
    $correo->Port = 465;
    $correo->SMTPAuth = true;
    $correo->Username = EMAIL;
    $correo->Password = PASS_EMAIL;
    $correo->SMTPSecure = 'ssl';
    $correo->IsHTML(true);
    $correo->SetFrom(EMAIL, "Notificaciones Viaggi");

    //Ponemos el asunto del mensaje
    $correo->Subject = $data["asunto"];

    $correo->AddAddress($data["to"], $data["nombre_cliente"]);

    $correo->MsgHTML($data["html"]);

    $correo->CharSet = 'UTF-8';

    if ($correo->Send()) {
        return true;
    } else {
        return false;
    }
}
