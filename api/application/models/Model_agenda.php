<?php

class Model_agenda extends CI_Model
{

    public function query_agenda($fecha = null, $id_usuario = null)
    {

        $this->db->select('*, date_format(agenda.fecha, "%d-%m-%Y") as fecha_format, concat(clientes.nombres, " ", clientes.apellidos) as cliente, concat(usuarios.nombres, " ", usuarios.apellidos) as usuario');
        $this->db->from('agenda');
        $this->db->join('clientes', 'clientes.id_cliente = agenda.id_cliente');
        $this->db->join('usuarios', 'usuarios.id_usuario = agenda.id_usuario');

        if ($fecha != null) {
            $this->db->group_start();
            $this->db->where('agenda.fecha', $fecha);
            $this->db->group_end();
        }

        if ($id_usuario != null) {
            $this->db->group_start();
            $this->db->where('agenda.id_usuario', $id_usuario);
            $this->db->group_end();
        }

        $this->db->order_by('agenda.hora', 'ASC');

        return $this->db->get();
    }

    public function query_reunion_cliente($id_cliente, $limit = null)
    {

        $this->db->select('*, date_format(agenda.fecha, "%d-%m-%Y") as fecha_format, concat(clientes.nombres, " ", clientes.apellidos) as cliente');
        $this->db->from('agenda');
        $this->db->join('clientes', 'clientes.id_cliente = agenda.id_cliente');

        $this->db->group_start();
        $this->db->where('agenda.id_cliente', $id_cliente);
        $this->db->where('agenda.fecha >=', date("Y-m-d"));
        $this->db->group_end();

        $this->db->order_by('agenda.fecha', 'ASC');
        $this->db->order_by('agenda.hora', 'ASC');

        if ($limit != null) {
            $this->db->where('agenda.hora >=', date("H:i"));
            $this->db->limit($limit);
        }

        return $this->db->get();

    }
}