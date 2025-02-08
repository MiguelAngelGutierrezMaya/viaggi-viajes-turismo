<?php

class Model_actividades extends CI_Model
{

    public function query_modalidades_actividad($id_actividad, $search = null, $offset = null, $per_page = null)
    {

        $this->db->from('modalidades_actividades');

        $this->db->group_start();
        $this->db->where('modalidades_actividades.id_actividad', $id_actividad);
        $this->db->group_end();

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('modalidades_actividades.modalidad', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('modalidades_actividades.deleted_modalidad', 0);
        $this->db->group_end();

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('modalidades_actividades.modalidad', 'ASC');

        return $this->db->get();
    }

    public function query_num_modalidades_actividad($id_actividad, $search = null)
    {

        $this->db->from('modalidades_actividades');

        $this->db->group_start();
        $this->db->where('modalidades_actividades.id_actividad', $id_actividad);
        $this->db->group_end();

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('modalidades_actividades.modalidad', $search);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('modalidades_actividades.deleted_modalidad', 0);
        $this->db->group_end();

        return $this->db->count_all_results();
    }

    /*  public function query_modalidad_actividad($id_modalidad)
    {
    $this->db->from('modalidades_actividades');
    $this->db->where('modalidades_actividades.id_modalidad_actividad', $id_modalidad);

    return $this->db->get();
    }
     */

    public function query_modalidad($id_modalidad)
    {
        $this->db->from('modalidades_actividades');
        $this->db->where('modalidades_actividades.id_modalidad_actividad', $id_modalidad);

        return $this->db->get();
    }

    public function query_modalidad_actividad($id_actividad)
    {
        $this->db->from('modalidades_actividades');
        $this->db->where('modalidades_actividades.id_actividad', $id_actividad);

        return $this->db->get();
    }

    public function query_temporadas_modalidad($id_modalidad)
    {
        $this->db->from('temporadas_modalidades');
        $this->db->where('temporadas_modalidades.id_modalidad', $id_modalidad);

        return $this->db->get();
    }

    public function query_temporada_actividad($id_modalidad, $fecha)
    {
        $this->db->from('temporadas_modalidades');
        $this->db->where('temporadas_modalidades.id_modalidad', $id_modalidad);
        $this->db->where('temporadas_modalidades.fecha_desde <= ', $fecha);
        $this->db->where('temporadas_modalidades.fecha_hasta >= ', $fecha);

        return $this->db->get();
    }

    public function query_tarifas_actividades($id_agencia, $search = null, $offset = null, $per_page = null)
    {
        $this->db->from('temporadas_modalidades');

        $this->db->join('modalidades_actividades', 'temporadas_modalidades.id_modalidad = modalidades_actividades.id_modalidad_actividad');
        $this->db->join('actividades', 'modalidades_actividades.id_actividad = actividades.id_actividad');
        $this->db->join('servicios', 'actividades.id_servicio = servicios.id_servicio');

        $this->db->where('servicios.id_agencia', $id_agencia);

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('modalidades_actividades.modalidad', $search);
            $this->db->or_like('servicios.servicio', $search);
            $this->db->group_end();
        }

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        return $this->db->get();
    }

    public function query_num_tarifas_actividades($id_agencia, $search = null)
    {
        $this->db->from('temporadas_modalidades');
        $this->db->join('modalidades_actividades', 'temporadas_modalidades.id_modalidad = modalidades_actividades.id_modalidad_actividad');
        $this->db->join('actividades', 'modalidades_actividades.id_actividad = actividades.id_actividad');
        $this->db->join('servicios', 'actividades.id_servicio = servicios.id_servicio');
        $this->db->where('servicios.id_agencia', $id_agencia);

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('modalidades_actividades.modalidad', $search);
            $this->db->or_like('servicios.servicio', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function query_tarifa_desde($id_actividad, $fecha = null)
    {
        $this->db->from('temporadas_modalidades');

        $this->db->join('modalidades_actividades', 'temporadas_modalidades.id_modalidad = modalidades_actividades.id_modalidad_actividad');

        $this->db->group_start();
        $this->db->where('modalidades_actividades.id_actividad', $id_actividad);
        $this->db->group_end();

        if ($fecha != null) {
            $this->db->group_start();
            $this->db->where('temporadas_modalidades.fecha_desde <= ', $fecha);
            $this->db->where('temporadas_modalidades.fecha_hasta >= ', $fecha);
            $this->db->group_end();
        }

        $this->db->order_by('temporadas_modalidades.valor_venta_adultos', 'ASC');

        $this->db->limit(1);

        return $this->db->get();
    }

    public function query_tarifa_fecha($id_actividad, $fecha = null)
    {
        $this->db->from('temporadas_modalidades');

        $this->db->join('modalidades_actividades', 'temporadas_modalidades.id_modalidad = modalidades_actividades.id_modalidad_actividad');

        $this->db->group_start();
        $this->db->where('modalidades_actividades.id_actividad', $id_actividad);
        $this->db->group_end();

        if ($fecha != null) {
            $this->db->group_start();
            $this->db->where('temporadas_modalidades.fecha_desde <= ', $fecha);
            $this->db->where('temporadas_modalidades.fecha_hasta >= ', $fecha);
            $this->db->group_end();
        }

        $this->db->group_start();
        $this->db->where('temporadas_modalidades.estado_temporada', 1);
        $this->db->group_end();

        return $this->db->get();
    }

    public function query_tipos_actividad()
    {
        $this->db->from('tipos_actividad');
        $this->db->order_by('tipos_actividad.tipo_actividad', 'ASC');

        return $this->db->get();
    }

    public function query_horarios($id_actividad)
    {
        $this->db->from('horarios_actividad');
        $this->db->where('horarios_actividad.id_actividad', $id_actividad);
        $this->db->where('horarios_actividad.deleted', 0);

        return $this->db->get();

    }

    public function query_horario($id_horario)
    {
        $this->db->from('horarios_actividad');
        $this->db->where('horarios_actividad.id_horario', $id_horario);

        return $this->db->get();

    }

    public function query_puntos_salida($id_actividad)
    {
        $this->db->from('puntos_salida');
        $this->db->where('puntos_salida.id_actividad', $id_actividad);
        $this->db->where('puntos_salida.deleted', 0);

        return $this->db->get();

    }

    public function query_punto_salida($id_punto_salida)
    {
        $this->db->from('puntos_salida');
        $this->db->where('puntos_salida.id_punto_salida', $id_punto_salida);

        return $this->db->get();

    }

    public function query_cierre_horario($id_horario = null, $fecha)
    {

        $this->db->from('cierres_actividad');

        if ($id_horario != null) {
            $this->db->where('cierres_actividad.id_horario', $id_horario);
        }

        $this->db->where('cierres_actividad.desde <= ', $fecha);
        $this->db->where('cierres_actividad.hasta >= ', $fecha);

        return $this->db->get();

    }

    public function query_actividades_recomendadas($id_actividad)
    {
        $this->db->from('actividades_recomendadas');
        $this->db->join('actividades', 'actividades_recomendadas.id_actividad = actividades.id_actividad');
        $this->db->where('actividades_recomendadas.id_actividad', $id_actividad);

        return $this->db->get();

    }

    public function query_actividad_recomendada($id_actividad_recomendada, $id_actividad)
    {
        $this->db->from('actividades_recomendadas');
        $this->db->where('actividades_recomendadas.id_actividad', $id_actividad);
        $this->db->where('actividades_recomendadas.id_actividad_recomendada', $id_actividad_recomendada);

        return $this->db->get();

    }

    public function query_inoperaciones($id_actividad)
    {
        $this->db->from('inoperaciones');
        $this->db->where('inoperaciones.id_actividad', $id_actividad);

        return $this->db->get();

    }

    public function query_horario_estar($id_punto_salida, $id_horario)
    {
        $this->db->from('horarios_estar');
        $this->db->where('horarios_estar.id_punto_salida', $id_punto_salida);
        $this->db->where('horarios_estar.id_horario', $id_horario);

        return $this->db->get();
    }

    public function query_tipos_actividad_actividad($id_actividad)
    {
        $this->db->select('tipos_actividad.id_tipo_actividad, tipos_actividad.tipo_actividad');
        $this->db->from('tipos_actividad');
        $this->db->join('rel_tipos_actividad', 'tipos_actividad.id_tipo_actividad = rel_tipos_actividad.id_tipo_actividad');
        $this->db->where('rel_tipos_actividad.id_actividad', $id_actividad);

        return $this->db->get();
    }

    public function update_tipos_actividad($id_actividad, $tipos_actividad)
    {

        $this->db->where('rel_tipos_actividad.id_actividad', $id_actividad);
        $this->db->delete('rel_tipos_actividad');

        if (count($tipos_actividad) != 0) {
            foreach ($tipos_actividad as $tipo) {
                $this->db->insert('rel_tipos_actividad', [
                    'id_actividad' => $id_actividad,
                    'id_tipo_actividad' => $tipo->id_tipo_actividad,
                ]);
            }
        }

    }

    public function query_tipo_actividad_slug($slug)
    {
        $this->db->from('tipos_actividad');
        $this->db->where('tipos_actividad.slug', $slug);

        return $this->db->get();
    }

}
