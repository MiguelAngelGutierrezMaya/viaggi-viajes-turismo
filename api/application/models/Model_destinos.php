<?php

class Model_destinos extends CI_Model
{

    public function query_ciudades(string $search = null)
    {

        $search = "%" . $search . "%";

        $sql = 'SELECT c.id_ciudad, CONCAT(c.ciudad, " (", p.pais,")") as ciudad_pais, c.ciudad
                FROM ciudades c
                INNER JOIN paises p
                ON p.codigo = c.codigo
                WHERE c.ciudad LIKE ?
                ORDER BY c.orden DESC';

        return $this->db->query($sql, [$search]);

    }

    public function query_destino_ciudad(int $id_ciudad)
    {
        $sql = 'SELECT *
                FROM destinos d
                WHERE d.id_ciudad = ?';

        return $this->db->query($sql, [$id_ciudad]);
    }

    public function query_destino_slug(string $slug)
    {

        $slug = "%" . $slug . "%";

        $sql = 'SELECT *
                FROM destinos d
                WHERE d.destino_slug LIKE ?';

        return $this->db->query($sql, [$slug]);
    }

    public function query_ciudad(int $id_ciudad)
    {
        $sql = 'SELECT *
                FROM ciudades c
                WHERE c.id_ciudad = ?';

        return $this->db->query($sql, [$id_ciudad]);
    }

    public function query_destinos(string $search = null)
    {

        $search = "%" . $search . "%";

        $sql = 'SELECT c.id_ciudad, CONCAT(c.ciudad, " (", p.pais,")") as ciudad_pais, c.ciudad
                FROM ciudades c
                INNER JOIN destinos d
                ON c.id_ciudad = d.id_ciudad
                INNER JOIN paises p
                ON p.codigo = c.codigo
                WHERE c.ciudad LIKE ?
                ORDER BY c.orden DESC';

        return $this->db->query($sql, [$search]);

    }

}
