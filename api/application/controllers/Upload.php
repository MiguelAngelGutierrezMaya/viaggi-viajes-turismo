<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_servicios');
    }

    public function galeria_actividad()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');

        $data_post = $this->input->post(null, true);

        // Obtenemos el array de ficheros enviados
        $ficheros = $_FILES['files'];

        // Obtenemos los nombres de los ficheros
        $nombres_ficheros = $ficheros['name'];

        $config = array(
            'upload_path' => "./media/",
            'allowed_types' => 'jpg|png|jpeg|svg',
            'overwrite' => false,
            'max_size' => "3048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
        );

        /*  for load library for upload file start */
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $info_servicio = $this->Model_servicios->query_servicio($data_post["id_servicio"])->row();
        $slug = $info_servicio->servicio_slug;
        $error = null;

        for ($i = 0; $i < count($nombres_ficheros); $i++) {

            $_FILES['userFile']['name'] = $_FILES['files']['name'][$i];
            $_FILES['userFile']['type'] = $_FILES['files']['type'][$i];
            $_FILES['userFile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userFile']['error'] = $_FILES['files']['error'][$i];
            $_FILES['userFile']['size'] = $_FILES['files']['size'][$i];

            $ext = explode('.', $_FILES['userFile']['name']);

            $_FILES['userFile']['name'] = $slug . "." . array_pop($ext);

            if ($this->upload->do_upload('userFile')) {

                $estado_proceso = true;

                $updata = $this->upload->data();
                $url = "media/" . $updata['raw_name'] . $updata['file_ext'];

                #Convertir a webp
                $newUrl = "media/" . $updata['raw_name'] . '.webp';

                $data_update = [
                    'id_servicio' => $data_post["id_servicio"],
                    'img_galeria' => $newUrl,
                ];

                $this->db->insert('galeria', $data_update);

                $im = new Imagick($url);
                $im->writeImage($newUrl);

                unlink($url);

            } else {
                // Activamos el indicador de proceso erroneo
                $estado_proceso = false;

                $error = $this->upload->display_errors();

                // Rompemos el bucle para que no continue procesando ficheros
                break;
            }

        }

        if (!$estado_proceso) {
            $response = [
                'status' => false,
                'message' => "Ocurrió un error subiendo los archivos: " . $error,
            ];

            echo json_encode($response);
            exit();
        }

        $response = [
            'status' => true,
            'message' => "Se subieron las imágenes a la galería satisfactoriamente",
        ];

        echo json_encode($response);
        exit();

    }

    public function imagen_paquete()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');

        $data_post = $this->input->post(null, true);

        // Obtenemos el array de ficheros enviados
        $ficheros = $_FILES['files'];

        // Obtenemos los nombres de los ficheros
        $nombres_ficheros = $ficheros['name'];

        $config = array(
            'upload_path' => "./media/",
            'allowed_types' => 'jpg|png|jpeg',
            'overwrite' => false,
            'max_size' => "3048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
        );

        /*  for load library for upload file start */
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $info_servicio = $this->Model_servicios->query_servicio($data_post["id_servicio"])->row();
        $slug = $info_servicio->servicio_slug;
        $error = null;

        for ($i = 0; $i < count($nombres_ficheros); $i++) {

            $_FILES['userFile']['name'] = $_FILES['files']['name'][$i];
            $_FILES['userFile']['type'] = $_FILES['files']['type'][$i];
            $_FILES['userFile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userFile']['error'] = $_FILES['files']['error'][$i];
            $_FILES['userFile']['size'] = $_FILES['files']['size'][$i];

            $ext = explode('.', $_FILES['userFile']['name']);

            $_FILES['userFile']['name'] = $slug . "." . array_pop($ext);

            if ($this->upload->do_upload('userFile')) {

                $estado_proceso = true;

                $updata = $this->upload->data();
                $url = "media/" . $updata['raw_name'] . $updata['file_ext'];

                #Convertir a webp
                $newUrl = "media/" . $updata['raw_name'] . '.webp';

                $data_update = [
                    'img' => $newUrl,
                ];

                $this->db->where('servicios.id_servicio', $data_post["id_servicio"]);
                $this->db->update('servicios', $data_update);

                $im = new Imagick($url);
                $im->writeImage($newUrl);

                unlink($url);

            } else {
                // Activamos el indicador de proceso erroneo
                $estado_proceso = false;

                $error = $this->upload->display_errors();

                // Rompemos el bucle para que no continue procesando ficheros
                break;
            }

        }

        if (!$estado_proceso) {
            $response = [
                'status' => false,
                'message' => "Ocurrió un error subiendo los archivos: " . $error,
            ];

            echo json_encode($response);
            exit();
        }

        $response = [
            'status' => true,
            'message' => "Se subieron las imágenes a la galería satisfactoriamente",
        ];

        echo json_encode($response);
        exit();

    }

    public function recurso()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');

        $data_post = $this->input->post(null, true);

        if (isset($_FILES['files'])) {

            $file = 'files';

            $config = array(
                'upload_path' => "./media/",
                'allowed_types' => '*',
                'overwrite' => false,
                'max_size' => "2048000",
                'file_name' => uniqid(),
            );

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload($file)) {
                $response = [
                    'status' => false,
                    'message' => 'Ocurrió un error subiendo el archivo. Error: ' . $this->upload->display_errors(),
                ];
                echo json_encode($response);
                exit();
            }

            $updata = $this->upload->data();
            $url = "media/" . $updata['raw_name'] . $updata['file_ext'];

            #Convertir a webp
            $newUrl = "media/" . $updata['raw_name'] . '.webp';

            $data_update = [
                'url' => $newUrl,
                'estado' => $data_post["estado"],
                'boton' => $data_post["boton"],
                'enlace' => $data_post["enlace"]
            ];

            $this->db->where('recursos.id_recurso', $data_post["id_recurso"]);
            $this->db->update('recursos', $data_update);

            $im = new Imagick($url);
            $im->writeImage($newUrl);

            unlink($url);

        } else {

            $this->db->where('recursos.id_recurso', $data_post["id_recurso"]);
            $this->db->update('recursos', [
                'estado' => $data_post["estado"],
                'boton' => $data_post["boton"],
                'enlace' => $data_post["enlace"]
            ]);

        }

        $response = [
            'status' => true,
            'message' => "Se actualizó el recurso.",
        ];

        echo json_encode($response);
        exit();

    }

    public function voucher()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');

        $data_post = $this->input->post(null, true);

        $file = 'files';

        $config = array(
            'upload_path' => "./media/",
            'allowed_types' => '*',
            'overwrite' => false,
            'max_size' => "2048000",
            'file_name' => uniqid(),
        );

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($file)) {
            $response = [
                'status' => false,
                'message' => 'Ocurrió un error subiendo el archivo. Error: ' . $this->upload->display_errors(),
            ];
            echo json_encode($response);
            exit();
        }

        $updata = $this->upload->data();
        $url = "media/" . $updata['raw_name'] . $updata['file_ext'];

        $data_insert = [
            'id_servicio_reserva' => $data_post["id_servicio_reserva"],
            'titulo' => $data_post["titulo"],
            'url' => $url,
            'estado' => 1,
        ];

        $this->db->insert('vouchers', $data_insert);

        $response = [
            'status' => true,
            'message' => "Se subió el archivo.",
        ];

        echo json_encode($response);
        exit();

    }

    public function documento()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');

        $data_post = $this->input->post(null, true);

        if (isset($_FILES['files'])) {

            $file = 'files';

            $config = array(
                'upload_path' => "./media/",
                'allowed_types' => '*',
                'overwrite' => false,
                'max_size' => "2048000",
                'file_name' => uniqid(),
            );

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload($file)) {
                $response = [
                    'status' => false,
                    'message' => 'Ocurrió un error subiendo el archivo. Error: ' . $this->upload->display_errors(),
                ];
                echo json_encode($response);
                exit();
            }

            $updata = $this->upload->data();
            $url = "media/" . $updata['raw_name'] . $updata['file_ext'];

            $id_carpeta = (isset($data_post["id_carpeta"]) && $data_post["id_carpeta"] != null && $data_post["id_carpeta"] != 'null') ? $data_post["id_carpeta"] : null;

            $data_insert = [
                'url' => $url,
                'nombre' => $data_post["nombre"],
                'descripcion' => $data_post["descripcion"],
                'tipo' => $data_post["tipo"],
                'id_carpeta' => $id_carpeta,
            ];

            $this->db->insert('documentos', $data_insert);

        } else {

            $id_carpeta = (isset($data_post["id_carpeta"]) && $data_post["id_carpeta"] != null && $data_post["id_carpeta"] != 'null') ? $data_post["id_carpeta"] : null;

            $data_insert = [
                'nombre' => $data_post["nombre"],
                'descripcion' => $data_post["descripcion"],
                'tipo' => $data_post["tipo"],
                'id_carpeta' => $id_carpeta,
            ];

            $this->db->insert('documentos', $data_insert);

        }

        $response = [
            'status' => true,
            'message' => "Se creado el recurso.",
        ];

        echo json_encode($response);
        exit();

    }

}
