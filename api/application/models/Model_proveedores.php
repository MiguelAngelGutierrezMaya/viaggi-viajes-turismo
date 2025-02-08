<?php

class Model_proveedores extends CI_Model
{

    public function query_proveedores($tipo = null, $search = null, $estado = null)
    {

        $this->db->select('proveedores.id_proveedor, proveedores.proveedor, proveedores.estado_proveedor as estado');
        $this->db->from('proveedores');

        if ($search != null) {
            $this->db->like('proveedores.proveedor', $search);
        }

        if ($tipo != null) {
            $this->db->join('proveedores_tipo', 'proveedores.id_proveedor = proveedores_tipo.id_proveedor');
            $this->db->where('proveedores_tipo.id_tipo_servicio', $tipo);
        }

        if ($estado != null) {
            $this->db->where('proveedores.estado_proveedor', $estado);
        }

        $this->db->where('proveedores.deleted_proveedor', 0);

        return $this->db->get();
    }

    public function query_tipos_servicio_proveedor($id_proveedor)
    {

        $this->db->select('proveedores_tipo.id_tipo_servicio, tipos_servicio.tipo_servicio');
        $this->db->from('proveedores_tipo');
        $this->db->join('tipos_servicio', 'tipos_servicio.id_tipo_servicio = proveedores_tipo.id_tipo_servicio');
        $this->db->where('proveedores_tipo.id_proveedor', $id_proveedor);

        return $this->db->get();

    }

}
