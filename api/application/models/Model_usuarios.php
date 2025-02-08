<?php

class Model_usuarios extends CI_Model
{

    public function query_usuario($id_usuario)
    {

        $this->db->from('usuarios');
        $this->db->where('usuarios.id_usuario', $id_usuario);

        return $this->db->get();
    }

    public function query_usuario_email($email)
    {

        $this->db->from('usuarios');
        $this->db->where('usuarios.email', $email);

        $this->db->group_start();
        $this->db->where_in('usuarios.estado_usuario', [1, 2]);
        $this->db->where('usuarios.deleted_usuario', 0);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_token_session($token)
    {

        $this->db->from('tokens_session');
        $this->db->where('tokens_session.token', $token);
        $this->db->where('tokens_session.token_status', 1);

        return $this->db->get();
    }

    public function query_agencia_usuarios($id_agency, $search = null, $offset = null, $per_page = null)
    {
        $this->db->from('usuarios');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('usuarios.nombres', $search);
            $this->db->or_like('usuarios.apellidos', $search);
            $this->db->group_end();
        }

        $this->db->where('usuarios.id_agencia', $id_agency);

        $this->db->group_start();
        $this->db->where('usuarios.deleted_usuario', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        return $this->db->get();

    }

    public function query_num_agencia_usuarios($id_agency, $search = null)
    {
        $this->db->from('usuarios');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('usuarios.nombres', $search);
            $this->db->or_like('usuarios.apellidos', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('usuarios.id_agencia', $id_agency);
        $this->db->where('usuarios.deleted_usuario', 0);
        $this->db->group_end();

        return $this->db->count_all_results();

    }

    public function query_permisos()
    {
        $sql = 'SELECT *
        FROM permisos ';

        return $this->db->query($sql);
    }

    public function query_permiso_usuario($id_usuario, $permiso)
    {

        $sql = 'SELECT *
        FROM permisos_usuario pu
        INNER JOIN permisos p ON pu.id_permiso = p.id_permiso
        WHERE pu.id_usuario = ?
        AND p.permiso = ?';

        return $this->db->query($sql, [$id_usuario, $permiso]);
    }

    public function query_permisos_usuario($id_usuario)
    {

        $sql = 'SELECT p.id_permiso, p.permiso, p.descripcion, pu.id_permiso_usuario, pu.id_usuario
        FROM permisos p
        LEFT JOIN permisos_usuario pu ON pu.id_usuario = ? AND p.id_permiso = pu.id_permiso
        ORDER BY p.id_permiso ASC';

        return $this->db->query($sql, [$id_usuario]);
    }

    public function query_permisos_carpeta_usuario($id_usuario)
    {

        $sql = 'SELECT c.id_documento, c.nombre, pu.id_permiso_carpeta, pu.id_usuario
        FROM documentos c
        LEFT JOIN permisos_carpeta pu ON pu.id_usuario = ? AND c.id_documento = pu.id_carpeta
        WHERE c.tipo = 0
        ORDER BY c.id_documento ASC';

        return $this->db->query($sql, [$id_usuario]);
    }

    public function query_permiso_carpeta($id_usuario, $id_carpeta)
    {

        $sql = 'SELECT *
        FROM permisos_carpeta pu
        INNER JOIN documentos d ON pu.id_carpeta = d.id_documento
        WHERE pu.id_usuario = ?
        AND d.id_documento = ?';

        return $this->db->query($sql, [$id_usuario, $id_carpeta]);
    }

}