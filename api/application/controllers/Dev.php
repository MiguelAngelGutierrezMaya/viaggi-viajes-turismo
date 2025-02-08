<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dev extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_id($id)
    {
        echo encode($id);
    }

    public function tarea()
    {

        $data = file_get_contents("media/paises.json");
        $json = json_decode($data, true);

        $paises = [];
        foreach ($json as $paises) {

            foreach ($paises as $item) {

                $newJson[] = [
                    'name' => "(" . $item["dial_code"] . ") " . $item["flag"] . " " . $item["name"],
                    'code' => $item["dial_code"],
                ];
            }

        }

        header("Content-Type: text/plain");
        echo json_encode(($newJson), JSON_PRETTY_PRINT);

    }
}
