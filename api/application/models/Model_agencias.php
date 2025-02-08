<?php

class Model_agencias extends CI_Model
{

    public function query_agencia($id_agencia)
    {

        $this->db->from('agencias');
        $this->db->where('agencias.id_agencia', $id_agencia);

        return $this->db->get();
    }

    public function query_parametros()
    {
        $this->db->from('parametros');

        return $this->db->get();
    }

    public function query_parametro($parametro)
    {
        $this->db->from('parametros');
        $this->db->where('parametros.parametro', $parametro);

        return $this->db->get();
    }

    public function query_recursos($estado = null)
    {
        $this->db->from('recursos');

        if ($estado != null) {
            $this->db->where('recursos.estado', $estado);
        }

        return $this->db->get();
    }

}
