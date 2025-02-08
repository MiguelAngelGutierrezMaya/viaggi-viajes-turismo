<?php

class Model_servicios extends CI_Model
{

    public function query_actividades($id_agencia, $id_destino = null, $search = null, $disponible = null, $slug_tipo = null, $offset = null, $per_page = null)
    {

        $this->db->select('s.*, a.id_actividad, a.dias_inoperacion, a.descripcion, a.destacada, a.duracion');
        $this->db->from('servicios s');
        $this->db->join('actividades a', 's.id_servicio = a.id_servicio', 'left');
        $this->db->join('destinos d', 'd.id_destino = s.id_destino');

        $this->db->group_start();
        $this->db->where('s.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($id_destino != null) {
            $this->db->group_start();
            $this->db->where('s.id_destino', $id_destino);
            $this->db->group_end();
        }

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('s.servicio', $search);
            $this->db->group_end();
        }

        if ($disponible != null) {
            if ($disponible) {
                $this->db->group_start();
                $this->db->where('s.estado_servicio', 1);
                $this->db->group_end();
            }
        }

        if ($slug_tipo != null) {
            $this->db->join('rel_tipos_actividad', 'rel_tipos_actividad.id_actividad = a.id_actividad');
            $this->db->join('tipos_actividad', 'tipos_actividad.id_tipo_actividad = rel_tipos_actividad.id_tipo_actividad');
            $this->db->group_start();
            $this->db->where('tipos_actividad.slug', $slug_tipo);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('s.deleted_servicio', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('s.id_destino', 'ASC');
        $this->db->order_by('s.servicio', 'ASC');

        return $this->db->get();
    }

    public function query_actividades_list($id_agencia, $actividades)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        if (count($actividades) != 0) {
            $this->db->group_start();
            $this->db->where_in('servicios.id_servicio', $actividades);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('servicios.estado_servicio', 1);
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        $this->db->order_by('servicios.id_destino', 'ASC');
        $this->db->order_by('servicios.servicio', 'ASC');

        return $this->db->get();
    }

    public function query_num_actividades($id_agencia, $id_destino = null, $search = null, $disponible = null, $slug_tipo = null)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($id_destino != null) {
            $this->db->group_start();
            $this->db->like('servicios.id_destino', $id_destino);
            $this->db->group_end();
        }

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('servicios.servicio', $search);
            $this->db->group_end();
        }

        if ($disponible != null) {
            if ($disponible) {
                $this->db->group_start();
                $this->db->where('servicios.estado_servicio', 1);
                $this->db->group_end();
            }
        }

        if ($slug_tipo != null) {
            $this->db->join('rel_tipos_actividad', 'rel_tipos_actividad.id_actividad = actividades.id_actividad');
            $this->db->join('tipos_actividad', 'tipos_actividad.id_tipo_actividad = rel_tipos_actividad.id_actividad');
            $this->db->where('tipos_actividad.slug', $slug_tipo);
        }

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    public function query_actividad_servicio($id_servicio)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio', 'left');

        $this->db->group_start();
        $this->db->where('servicios.id_servicio', $id_servicio);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_actividad_slug($slug)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.servicio_slug', $slug);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_actividad($id_actividad)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('actividades.id_actividad', $id_actividad);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_destino($id_destino)
    {

        $this->db->from('destinos');
        $this->db->where('destinos.id_destino', $id_destino);

        return $this->db->get();

    }

    public function query_destino_geoname($geoname_id)
    {

        $this->db->from('destinos');
        $this->db->where('destinos.geoname_id', $geoname_id);

        return $this->db->get();

    }

    public function query_servicio($id_servicio)
    {
        $this->db->from('servicios');
        $this->db->where('servicios.id_servicio', $id_servicio);

        return $this->db->get();
    }

    public function query_servicio_slug($slug)
    {
        $this->db->from('servicios');
        $this->db->where('servicios.servicio_slug', $slug);

        return $this->db->get();
    }

    public function query_proveedor($id_proveedor)
    {

        $this->db->from('proveedores');
        $this->db->where('proveedores.id_proveedor', $id_proveedor);

        return $this->db->get();
    }

    public function query_proveedores($id_agencia, $search = null, $offset = null, $per_page = null)
    {

        $this->db->from('proveedores');

        $this->db->group_start();
        $this->db->where('proveedores.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('proveedores.proveedor', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('proveedores.estado_proveedor', 1);
        $this->db->where('proveedores.deleted_proveedor', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('proveedores.proveedor', 'ASC');

        return $this->db->get();
    }

    public function query_num_proveedores($id_agencia, $search = null)
    {

        $this->db->from('proveedores');

        $this->db->group_start();
        $this->db->where('proveedores.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('proveedores.proveedor', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('proveedores.estado_proveedor', 1);
        $this->db->where('proveedores.deleted_proveedor', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    public function query_galeria($id_servicio, $tipo = null)
    {
        $this->db->from('galeria');

        $this->db->group_start();
        $this->db->where('galeria.id_servicio', $id_servicio);
        $this->db->group_end();

        if ($tipo != null) {
            $this->db->group_start();
            $this->db->where('galeria.tipo', $tipo);
            $this->db->group_end();
        }

        $this->db->order_by('galeria.orden', 'ASC');

        return $this->db->get();
    }

    public function query_img_galeria($id_galeria)
    {
        $this->db->from('galeria');
        $this->db->where('galeria.id_galeria', $id_galeria);

        return $this->db->get();
    }

    public function query_actividades_destacadas($id_agencia, $tipo_destino = null, $offset = null, $per_page = null)
    {

        $this->db->select('s.id_servicio, s.id_tipo_servicio, s.id_destino, s.servicio, s.servicio_slug, s.resumen, s.estado_servicio, a.duracion, p.valor_desde, a.id_actividad');
        $this->db->from('servicios s');
        $this->db->join('actividades a', 's.id_servicio = a.id_servicio', 'left');
        $this->db->join('paquetes p', 's.id_servicio = p.id_servicio', 'left');

        $this->db->group_start();
        $this->db->where('s.id_agencia', $id_agencia);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('a.destacada', 1);
        $this->db->or_where('p.destacado', 1);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('s.estado_servicio', 1);
        $this->db->where('s.deleted_servicio', 0);
        $this->db->group_end();

        if ($tipo_destino != null) {
            $this->db->group_start();
            $this->db->join('destinos', 'destinos.id_destino = s.id_destino');
            $this->db->join('ciudades', 'ciudades.id_ciudad = destinos.id_ciudad');
            $this->db->join('paises', 'paises.codigo = ciudades.codigo');

            if ($tipo_destino == 0) {
                $this->db->where('ciudades.codigo', 'CO');
            } else {
                $this->db->where('ciudades.codigo <>', 'CO');
            }

            $this->db->group_end();
        }

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('s.id_destino', 'ASC');
        $this->db->order_by('s.servicio', 'ASC');

        return $this->db->get();
    }

    public function query_num_actividades_destacadas($id_agencia, $offset = null, $per_page = null)
    {

        $this->db->from('servicios');
        $this->db->join('actividades', 'servicios.id_servicio = actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('actividades.destacada', 1);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    public function query_imagen_galeria($id_galeria)
    {
        $this->db->from('galeria');

        $this->db->group_start();
        $this->db->where('galeria.id_galeria', $id_galeria);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_img_principal($id_servicio)
    {
        $this->db->from('galeria');

        $this->db->group_start();
        $this->db->where('galeria.id_servicio', $id_servicio);
        $this->db->where('galeria.principal', 1);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_paquetes_destacados($id_agencia, $offset = null, $per_page = null)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('paquetes.destacado', 1);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('servicios.id_destino', 'ASC');
        $this->db->order_by('servicios.servicio', 'ASC');

        return $this->db->get();
    }

    public function query_num_paquetes_destacados($id_agencia, $offset = null, $per_page = null)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('paquetes.destacado', 1);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    public function query_cupones($search = null, $offset = null, $per_page = null)
    {
        $this->db->from('cupones');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('cupones.codigo', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->like('cupones.deleted_cupon', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        return $this->db->get();

    }

    public function query_num_cupones($search = null)
    {
        $this->db->from('cupones');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('cupones.codigo', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->like('cupones.deleted_cupon', 0);
        $this->db->group_end();

        return $this->db->count_all_results();

    }

    public function query_cupon_codigo($codigo)
    {

        $this->db->from('cupones');

        $this->db->group_start();
        $this->db->where('cupones.codigo', $codigo);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('cupones.deleted_cupon', 0);
        $this->db->group_end();

        return $this->db->get();

    }

    public function query_actividades_cupon($id_cupon)
    {

        $this->db->from('servicios');
        $this->db->join('cupones_actividades', 'servicios.id_servicio = cupones_actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('cupones_actividades.id_cupon', $id_cupon);
        $this->db->group_end();

        return $this->db->get();

    }

    public function query_actividad_cupon($id_cupon, $id_servicio)
    {

        $this->db->from('servicios');
        $this->db->join('cupones_actividades', 'servicios.id_servicio = cupones_actividades.id_servicio');

        $this->db->group_start();
        $this->db->where('cupones_actividades.id_cupon', $id_cupon);
        $this->db->where('cupones_actividades.id_servicio', $id_servicio);
        $this->db->group_end();

        return $this->db->get();

    }

    public function query_popup($vigente = null)
    {
        $this->db->from('popups');

        if ($vigente != null) {
            switch ($vigente) {
                case true:
                    $this->db->where('popups.desde <= ', date("Y-m-d"));
                    $this->db->where('popups.hasta >= ', date("Y-m-d"));
                    break;
            }
        }

        $this->db->limit(1);

        return $this->db->get();
    }

    public function query_review_count($id_servicio)
    {
        $this->db->select('reviews.id_review');
        $this->db->from('reviews');
        $this->db->join('reservas', 'reviews.id_reserva = reservas.id_reserva');
        $this->db->join('servicios', 'reviews.id_servicio = servicios.id_servicio');
        $this->db->where('reviews.estado', 1);
        $this->db->where('reviews.id_servicio', $id_servicio);

        return $this->db->count_all_results();
    }

    public function query_reviews_rating($id_servicio)
    {
        $this->db->select_avg('reviews.valor');
        $this->db->from('reviews');
        $this->db->join('reservas', 'reviews.id_reserva = reservas.id_reserva');
        $this->db->join('servicios', 'reviews.id_servicio = servicios.id_servicio');
        $this->db->where('reviews.estado', 1);
        $this->db->where('reviews.id_servicio', $id_servicio);

        return $this->db->get();
    }

    public function query_reviews_servicio($id_servicio)
    {

        $this->db->from('reviews');
        $this->db->join('reservas', 'reviews.id_reserva = reservas.id_reserva');
        $this->db->join('servicios', 'reviews.id_servicio = servicios.id_servicio');
        $this->db->join('clientes', 'reservas.id_cliente = clientes.id_cliente');
        $this->db->where('reviews.estado', 1);
        $this->db->where('reviews.id_servicio', $id_servicio);

        return $this->db->get();

    }

    public function query_paquetes($id_agencia, $id_destino = null, $search = null, $disponible = null, $offset = null, $per_page = null)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');
        $this->db->join('destinos', 'destinos.id_destino = servicios.id_destino');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($id_destino != null) {
            $this->db->group_start();
            $this->db->like('servicios.id_destino', $id_destino);
            $this->db->group_end();
        }

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('servicios.servicio', $search);
            $this->db->group_end();
        }

        if ($disponible != null) {
            if ($disponible) {
                $this->db->group_start();
                $this->db->where('servicios.estado_servicio', 1);
                $this->db->group_end();
            }
        }

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('servicios.id_destino', 'ASC');
        $this->db->order_by('servicios.servicio', 'ASC');

        return $this->db->get();
    }

    public function query_num_paquetes($id_agencia, $id_destino = null, $search = null, $disponible = null)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');
        $this->db->join('destinos', 'destinos.id_destino = servicios.id_destino');

        $this->db->group_start();
        $this->db->where('servicios.id_agencia', $id_agencia);
        $this->db->group_end();

        if ($id_destino != null) {
            $this->db->group_start();
            $this->db->like('servicios.id_destino', $id_destino);
            $this->db->group_end();
        }

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('servicios.servicio', $search);
            $this->db->group_end();
        }

        if ($disponible != null) {
            if ($disponible) {
                $this->db->group_start();
                $this->db->where('servicios.estado_servicio', 1);
                $this->db->group_end();
            }
        }

        $this->db->group_start();
        $this->db->where('servicios.deleted_servicio', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    public function query_paquete_servicio($id_servicio)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.id_servicio', $id_servicio);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_paquete_slug($slug)
    {

        $this->db->from('servicios');
        $this->db->join('paquetes', 'servicios.id_servicio = paquetes.id_servicio');

        $this->db->group_start();
        $this->db->where('servicios.servicio_slug', $slug);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_hoteles($search = null)
    {

        $this->db->select('s.id_servicio, CONCAT(s.servicio," - ",d.destino) as servicio');
        $this->db->from('servicios s');
        $this->db->join('destinos d', 's.id_destino = d.id_destino');
        $this->db->where('s.id_tipo_servicio', 3);

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('s.servicio', $search);
            $this->db->group_end();
        }

        return $this->db->get();

    }

    public function query_links($search = null)
    {
        $this->db->from('links');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('links.nombre', $search);
            $this->db->or_like('links.descripcion', $search);
            $this->db->group_end();
        }

        return $this->db->get();

    }

    public function query_ofertas($id_agencia)
    {

        $this->db->select('s.id_servicio, s.id_tipo_servicio, s.id_destino, s.servicio, s.servicio_slug, s.resumen, s.estado_servicio, a.duracion, p.valor_desde, a.id_actividad');
        $this->db->from('servicios s');
        $this->db->join('actividades a', 's.id_servicio = a.id_servicio', 'left');
        $this->db->join('paquetes p', 's.id_servicio = p.id_servicio', 'left');

        $this->db->group_start();
        $this->db->where('s.id_agencia', $id_agencia);
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('s.oferta', 1);
        $this->db->where('s.estado_servicio', 1);
        $this->db->where('s.deleted_servicio', 0);
        $this->db->group_end();

        $this->db->order_by('s.id_destino', 'ASC');
        $this->db->order_by('s.servicio', 'ASC');

        return $this->db->get();
    }
}