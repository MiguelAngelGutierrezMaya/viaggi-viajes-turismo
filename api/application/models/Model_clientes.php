<?php

class Model_clientes extends CI_Model
{

    public function query_cliente($id_cliente)
    {
        $this->db->from('clientes');
        $this->db->where('clientes.id_cliente', $id_cliente);

        return $this->db->get();

    }

    public function query_cliente_identificacion($identificacion)
    {
        $this->db->from('clientes');
        $this->db->where('clientes.identificacion', $identificacion);

        return $this->db->get();
    }

    public function query_cliente_email($email)
    {
        $this->db->from('clientes');
        $this->db->where('clientes.email', $email);

        return $this->db->get();

    }

    public function query_reservas_cliente($id_cliente = null, $search = null, $offset = null, $per_page = null)
    {
        $this->db->from('reservas');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('reservas.cod_reserva', $search);
            $this->db->group_end();
        }

        if ($id_cliente != null) {
            $this->db->group_start();
            $this->db->where('reservas.id_cliente', $id_cliente);
            $this->db->group_end();
        }

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('reservas.id_reserva', 'DESC');

        return $this->db->get();

    }

    public function query_num_reservas_cliente($id_cliente = null, $search = null)
    {
        $this->db->from('reservas');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('reservas.cod_reserva', $search);
            $this->db->group_end();
        }

        if ($id_cliente != null) {
            $this->db->group_start();
            $this->db->where('reservas.id_cliente', $id_cliente);
            $this->db->group_end();
        }

        return $this->db->count_all_results();

    }

}