<?php

class Model_documentos extends CI_Model
{

    public function query_documentos($id_carpeta = null)
    {

        $this->db->from('documentos d');
        $this->db->where('d.id_carpeta', $id_carpeta);

        $this->db->order_by('d.tipo', 'ASC');

        return $this->db->get();

    }

    public function query_carpeta($id_carpeta)
    {
        $this->db->from('documentos d');
        $this->db->where('d.id_documento', $id_carpeta);

        return $this->db->get();
    }

    public function query_documento($id_documento)
    {

        $this->db->from('documentos d');
        $this->db->where('d.id_documento', $id_documento);

        return $this->db->get();

    }

}