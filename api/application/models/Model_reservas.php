<?php

class Model_reservas extends CI_Model
{
    public function query_reservas($search = null, $estado = null, $id_servicio = null, $desde = null, $hasta = null, $offset = null, $per_page = null)
    {
        $this->db->select('reservas.*');
        $this->db->from('reservas');

        if ($search != null) {
            $this->db->join('clientes', 'clientes.id_cliente = reservas.id_cliente');
            $this->db->join('pasajeros', 'pasajeros.id_reserva = reservas.id_reserva', 'left');
            $this->db->group_start();
            $this->db->like('clientes.nombres', $search);
            $this->db->or_like('clientes.apellidos', $search);
            $this->db->or_where('reservas.cod_reserva', $search);
            $this->db->or_like('pasajeros.nombres', $search);
            $this->db->or_like('pasajeros.apellidos', $search);
            $this->db->group_end();
        }

        if ($estado != null) {
            $this->db->group_start();
            $this->db->where('reservas.estado_reserva', $estado);
            $this->db->group_end();
        }

        $join_servicios = false;
        if ($id_servicio != null) {
            $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
            $this->db->group_start();
            $this->db->where('servicios_reserva.id_servicio', $id_servicio);
            $this->db->group_end();
            $join_servicios = true;
        }

        if ($desde != null) {
            if (!$join_servicios) {
                $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
                $join_servicios = true;
            }
            $this->db->group_start();
            $this->db->where('servicios_reserva.fecha_actividad >= ', $desde);
            $this->db->group_end();
        }

        if ($hasta != null) {
            if (!$join_servicios) {
                $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
            }
            $this->db->group_start();
            $this->db->where('servicios_reserva.fecha_actividad <= ', $hasta);
            $this->db->group_end();
        }

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('reservas.id_reserva', 'DESC');

        return $this->db->get();

    }

    public function query_num_reservas($search = null, $estado = null, $id_servicio = null, $desde = null, $hasta = null)
    {
        $this->db->from('reservas');

        if ($search != null) {
            $this->db->join('clientes', 'clientes.id_cliente = reservas.id_cliente');
            $this->db->group_start();
            $this->db->like('clientes.nombres', $search);
            $this->db->or_like('clientes.apellidos', $search);
            $this->db->group_end();
        }

        if ($estado != null) {
            $this->db->group_start();
            $this->db->where('reservas.estado_reserva', $estado);
            $this->db->group_end();
        }

        $join_servicios = false;
        if ($id_servicio != null) {
            $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
            $this->db->group_start();
            $this->db->where('servicios_reserva.id_servicio', $id_servicio);
            $this->db->group_end();
            $join_servicios = true;
        }

        if ($desde != null) {
            if (!$join_servicios) {
                $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
                $join_servicios = true;
            }
            $this->db->group_start();
            $this->db->where('servicios_reserva.fecha_actividad >= ', $desde);
            $this->db->group_end();
        }

        if ($hasta != null) {
            if (!$join_servicios) {
                $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');
            }
            $this->db->group_start();
            $this->db->where('servicios_reserva.fecha_actividad <= ', $hasta);
            $this->db->group_end();
        }

        return $this->db->count_all_results();

    }

    public function query_cliente($id_cliente)
    {

        $this->db->from('clientes');
        $this->db->where('clientes.id_cliente', $id_cliente);

        return $this->db->get();
    }

    public function query_servicios_reserva($id_reserva)
    {
        $this->db->from('servicios_reserva');
        $this->db->where('servicios_reserva.id_reserva', $id_reserva);

        return $this->db->get();

    }

    public function query_servicio_reserva($id_servicio_reserva)
    {
        $this->db->from('servicios_reserva');
        $this->db->join('servicios', 'servicios.id_servicio = servicios_reserva.id_servicio', 'left');
        $this->db->where('servicios_reserva.id_servicio_reserva', $id_servicio_reserva);

        return $this->db->get();

    }

    public function query_reserva($id_reserva)
    {
        $this->db->from('reservas');
        $this->db->where('reservas.id_reserva', $id_reserva);

        return $this->db->get();
    }

    public function query_pagos_reserva($id_reserva)
    {
        $this->db->from('pagos');
        $this->db->where('pagos.id_reserva', $id_reserva);

        return $this->db->get();
    }

    public function query_pago($id_pago)
    {
        $this->db->from('pagos');
        $this->db->where('pagos.id_pago', $id_pago);

        return $this->db->get();
    }

    public function query_reservas_horario($id_servicio = null, $fecha, $id_horario = null)
    {

        $this->db->from('servicios_reserva');

        if ($id_servicio != null) {
            $this->db->where('servicios_reserva.id_servicio', $id_servicio);
        }

        $this->db->where('servicios_reserva.fecha_actividad', $fecha);

        if ($id_horario != null) {
            $this->db->where('servicios_reserva.id_horario_actividad', $id_horario);
        }

        return $this->db->get();

    }

    public function query_ultimo_voucher()
    {
        $this->db->from('servicios_reserva');
        $this->db->order_by('servicios_reserva.cod_voucher', 'DESC');
        $this->db->limit(1);

        return $this->db->get();
    }

    public function query_reservas_fecha($fecha, $estado)
    {
        $this->db->from('reservas');

        if ($estado != null) {
            $this->db->group_start();
            $this->db->where('reservas.estado_reserva', $estado);
            $this->db->group_end();
        }

        $this->db->join('servicios_reserva', 'servicios_reserva.id_reserva = reservas.id_reserva');

        $this->db->group_start();
        $this->db->where('servicios_reserva.fecha_actividad', $fecha);
        $this->db->group_end();

        return $this->db->get();

    }

    public function query_review($id_reserva, $id_servicio)
    {
        $this->db->from('reviews');
        $this->db->where('reviews.id_reserva', $id_reserva);
        $this->db->where('reviews.id_servicio', $id_servicio);

        return $this->db->get();
    }

    public function query_reviews($estado = null, $id_servicio = null, $offset = null, $per_page = null)
    {
        $this->db->select('reviews.id_review, reservas.id_cliente, reservas.id_reserva, reservas.cod_reserva, reviews.fecha_reg, servicios.id_servicio, servicios.servicio, reviews.valor, reviews.comentarios, reviews.estado');
        $this->db->from('reviews');
        $this->db->join('reservas', 'reviews.id_reserva = reservas.id_reserva');
        $this->db->join('servicios', 'reviews.id_servicio = servicios.id_servicio');

        if ($estado != null) {
            $this->db->where('reviews.estado', $estado);
        }

        if ($id_servicio != null) {
            $this->db->where('reviews.id_servicio', $id_servicio);
        }

        if (($offset != null or $offset == 0) and $per_page != null) {
            $this->db->limit($per_page, $offset);
        }

        $this->db->order_by('reviews.id_review', 'DESC');

        return $this->db->get();

    }

    public function query_num_reviews($estado = null, $id_servicio = null, $offset = null, $per_page = null)
    {

        $this->db->from('reviews');
        $this->db->join('reservas', 'reviews.id_reserva = reservas.id_reserva');
        $this->db->join('servicios', 'reviews.id_servicio = servicios.id_servicio');

        if ($estado != null) {
            $this->db->where('reviews.estado', $estado);
        }

        if ($id_servicio != null) {
            $this->db->where('reviews.id_servicio', $id_servicio);
        }

        return $this->db->count_all_results();

    }

    public function query_cotizaciones($search = null, $estado = null, $offset = null, $per_page = null)
    {

        $sql = 'SELECT
        c.id_cotizacion,
        c.nombre,
        c.email,
        c.cod_pais,
        c.telefono,
        c.servicio,
        date_format(c.fecha_ida, "%d-%m-%Y") as fecha_ida,
        date_format(c.fecha_regreso, "%d-%m-%Y") as fecha_regreso,
        cs.ciudad as origen,
        csd.ciudad as destino,
        c.origen_busqueda,
        c.destino_busqueda,
        c.estado
        FROM cotizaciones c
        LEFT JOIN ciudades cs ON c.id_ciudad_origen = cs.id_ciudad
        LEFT JOIN ciudades csd ON c.id_ciudad_destino = csd.id_ciudad';

        $onSearch = false;
        if ($search != null) {
            $onSearch = true;
            $sql .= ' WHERE (c.nombre LIKE "%' . $search . '%" OR c.telefono = "' . $search . '" OR c.email = "' . $search . '")';
        }

        if ($estado != null) {
            if (!$onSearch) {
                $sql .= ' WHERE c.estado = ' . $estado;
            } else {
                $sql .= ' AND c.estado = ' . $estado;
            }
        }

        $sql .= ' ORDER BY c.id_cotizacion DESC';

        return $this->db->query($sql);

    }

    public function query_num_cotizaciones($search = null, $estado = null)
    {
        $this->db->from('cotizaciones');

        if ($search != null) {
            $this->db->group_start();
            $this->db->like('cotizaciones.nombre', $search);
            $this->db->or_like('cotizaciones.email', $search);
            $this->db->or_where('cotizaciones.telefono', $search);
            $this->db->group_end();

        }

        if ($estado != null) {
            $this->db->group_start();
            $this->db->where('cotizaciones.estado', $estado);
            $this->db->group_end();
        }

        return $this->db->count_all_results();

    }

    public function query_reserva_actividad($id_servicio_reserva)
    {
        $this->db->from('servicio_reserva_actividad');
        $this->db->where('servicio_reserva_actividad.id_servicio_reserva', $id_servicio_reserva);

        return $this->db->get();
    }

    public function query_reserva_tiquete($id_servicio_reserva)
    {

        $sql = 'SELECT
        rt.tipo_tiquete,
        rt.fecha_ida,
        rt.fecha_regreso,
        co.id_ciudad as id_ciudad_origen,
        CONCAT(co.ciudad, " (", po.pais,")") as ciudad_origen,
        cd.id_ciudad as id_ciudad_destino,
        CONCAT(cd.ciudad, " (", pd.pais,")") as ciudad_destino,
        p.id_proveedor,
        p.proveedor
        FROM servicio_reserva_tiquete rt
        INNER JOIN ciudades co ON rt.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON rt.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON rt.id_proveedor = p.id_proveedor
        WHERE rt.id_servicio_reserva = ' . $id_servicio_reserva;

        return $this->db->query($sql);

    }

    public function query_reserva_paquete($id_servicio_reserva)
    {

        $sql = 'SELECT
        rt.fecha_ida,
        rt.fecha_regreso,
        co.id_ciudad as id_ciudad_origen,
        CONCAT(co.ciudad, " (", po.pais,")") as ciudad_origen,
        cd.id_ciudad as id_ciudad_destino,
        CONCAT(cd.ciudad, " (", pd.pais,")") as ciudad_destino,
        p.id_proveedor,
        p.proveedor
        FROM servicio_reserva_paquete rt
        INNER JOIN ciudades co ON rt.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON rt.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON rt.id_proveedor = p.id_proveedor
        WHERE rt.id_servicio_reserva = ' . $id_servicio_reserva;

        return $this->db->query($sql);

    }

    public function query_reserva_hotel($id_servicio_reserva)
    {

        $sql = 'SELECT
        rh.fecha_ida,
        rh.fecha_regreso
        FROM servicio_reserva_hoteles rh
        WHERE rh.id_servicio_reserva = ' . $id_servicio_reserva;

        return $this->db->query($sql);

    }

    public function query_reserva_otros($id_servicio_reserva)
    {

        $sql = 'SELECT
        *
        FROM servicio_reserva_otros rt
        WHERE rt.id_servicio_reserva = ' . $id_servicio_reserva;

        return $this->db->query($sql);

    }

    public function query_reservas_reporte($tipo_fecha, $desde, $hasta)
    {

        #Actividades
        $sql = 'SELECT
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        s.servicio,
        d.destino,
        sra.fecha_actividad,
        sr.adultos as adultos,
        sr.ninos as ninos,
        sr.infantes as infantes,
        sr.valor_neto,
        sr.valor_venta,
        sr.valor_neto_ninos,
        sr.valor_venta_ninos,
        sr.valor_neto_infantes,
        sr.valor_venta_infantes,
        sr.cod_voucher,
        CONCAT(c.nombres," ", c.apellidos) as cliente
        FROM reservas r
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicio_reserva_actividad sra
        ON sra.id_servicio_reserva = sr.id_servicio_reserva
        INNER JOIN servicios s
        ON s.id_servicio = sr.id_servicio
        INNER JOIN destinos d
        ON d.id_destino = s.id_destino
        ';

        switch ($tipo_fecha) {
            case 1:
                $fdesde = $desde . " 00:00:00";
                $fhasta = $hasta . " 23:59:59";
                $sql .= ' WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"';
                break;

            case 2:
                $sql .= ' WHERE  sra.fecha_actividad >= "' . $desde . '" AND sra.fecha_actividad <= "' . $hasta . '"';
        }

        $query_actividades = $this->db->query($sql);

        #Paquetes
        $sql = 'SELECT
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        CONCAT(co.ciudad, " (", po.pais,")") as origen,
        CONCAT(cd.ciudad, " (", pd.pais,")") as destino,
        srp.fecha_ida,
        srp.fecha_regreso,
        sr.adultos as pasajeros,
        sr.valor_neto,
        sr.valor_venta,
        sr.cod_voucher,
        "Paquete" as servicio,
        CONCAT(c.nombres," ", c.apellidos) as cliente,
        p.proveedor
        FROM reservas r
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicio_reserva_paquete srp
        ON srp.id_servicio_reserva = sr.id_servicio_reserva
        INNER JOIN ciudades co ON srp.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON srp.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON srp.id_proveedor = p.id_proveedor
        ';

        switch ($tipo_fecha) {
            case 1:
                $fdesde = $desde . " 00:00:00";
                $fhasta = $hasta . " 23:59:59";
                $sql .= ' WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"';
                break;

            case 2:
                $sql .= ' WHERE  srp.fecha_ida >= "' . $desde . '" AND srp.fecha_ida <= "' . $hasta . '"';
        }

        $query_paquetes = $this->db->query($sql);

        #Hoteles
        $sql = 'SELECT
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        srh.fecha_ida,
        srh.fecha_regreso,
        sr.adultos as pasajeros,
        sr.valor_neto,
        sr.valor_venta,
        sr.cod_voucher,
        s.servicio,
        CONCAT(c.nombres," ", c.apellidos) as cliente
        FROM reservas r
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicios s
        ON s.id_servicio = sr.id_servicio
        INNER JOIN servicio_reserva_hoteles srh
        ON srh.id_servicio_reserva = sr.id_servicio_reserva

        ';

        switch ($tipo_fecha) {
            case 1:
                $fdesde = $desde . " 00:00:00";
                $fhasta = $hasta . " 23:59:59";
                $sql .= ' WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"';
                break;

            case 2:
                $sql .= ' WHERE  srh.fecha_ida >= "' . $desde . '" AND srh.fecha_ida <= "' . $hasta . '"';
        }

        $query_hoteles = $this->db->query($sql);

        #Tiquetes
        $sql = 'SELECT
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        CONCAT(co.ciudad, " (", po.pais,")") as origen,
        CONCAT(cd.ciudad, " (", pd.pais,")") as destino,
        srt.fecha_ida,
        srt.fecha_regreso,
        sr.adultos as pasajeros,
        sr.valor_neto,
        sr.valor_venta,
        sr.cod_voucher,
        CONCAT("Tiquete ",srt.tipo_tiquete) as servicio,
        CONCAT(c.nombres," ",c.apellidos) as cliente,
        p.proveedor
        FROM reservas r
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicio_reserva_tiquete srt
        ON srt.id_servicio_reserva = sr.id_servicio_reserva
        INNER JOIN ciudades co ON srt.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON srt.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON srt.id_proveedor = p.id_proveedor
        ';

        switch ($tipo_fecha) {
            case 1:
                $fdesde = $desde . " 00:00:00";
                $fhasta = $hasta . " 23:59:59";
                $sql .= ' WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"';
                break;

            case 2:
                $sql .= ' WHERE  (srt.fecha_ida >= "' . $desde . '" AND srt.fecha_ida <= "' . $hasta . '") OR (srt.fecha_regreso >= "' . $desde . '" AND srt.fecha_regreso <= "' . $hasta . '") ';
        }

        $query_tiquetes = $this->db->query($sql);

        #Asistencias
        $sql = 'SELECT
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        CONCAT(co.ciudad, " (", po.pais,")") as origen,
        CONCAT(cd.ciudad, " (", pd.pais,")") as destino,
        sra.fecha_ida,
        sra.fecha_regreso,
        sr.adultos as pasajeros,
        sr.valor_neto,
        sr.valor_venta,
        sr.cod_voucher,
        "Asistencia médica" as servicio,
        CONCAT(c.nombres," ", c.apellidos) as cliente,
        p.proveedor
        FROM reservas r
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicio_reserva_asistencia sra
        ON sra.id_servicio_reserva = sr.id_servicio_reserva
        INNER JOIN ciudades co ON sra.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON sra.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON sra.id_proveedor = p.id_proveedor
        ';

        switch ($tipo_fecha) {
            case 1:
                $fdesde = $desde . " 00:00:00";
                $fhasta = $hasta . " 23:59:59";
                $sql .= ' WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"';
                break;

            case 2:
                $sql .= ' WHERE  sra.fecha_ida >= "' . $desde . '" AND sra.fecha_ida <= "' . $hasta . '"';
        }

        $query_asistencias = $this->db->query($sql);

        #Otros
        $otros = [];
        $num_otros = 0;
        if ($tipo_fecha == 1) {
            $sql = 'SELECT
            r.cod_reserva,
            r.fecha_reg,
            r.estado_reserva,
            r.descuento,
            sr.adultos as pasajeros,
            sr.valor_neto,
            sr.valor_venta,
            sro.descripcion as servicio,
            sr.cod_voucher,
            CONCAT(c.nombres," ", c.apellidos) as cliente
            FROM reservas r
            INNER JOIN clientes c
            ON c.id_cliente = r.id_cliente
            INNER JOIN servicios_reserva sr
            ON sr.id_reserva = r.id_reserva
            INNER JOIN servicio_reserva_otros sro
            ON sro.id_servicio_reserva = sr.id_servicio_reserva
            WHERE  r.fecha_reg >= "' . $fdesde . '" AND r.fecha_reg <= "' . $fhasta . '"
            ';

            $query_otros = $this->db->query($sql);

            $otros = $query_otros->result();

            $num_otros = $query_otros->num_rows();
        }

        $total_reservas = $query_actividades->num_rows() + $query_paquetes->num_rows() + $query_hoteles->num_rows() + $query_tiquetes->num_rows() + $query_asistencias->num_rows() + $num_otros;

        return [
            'total_reservas' => $total_reservas,
            'actividades' => $query_actividades->result(),
            'paquetes' => $query_paquetes->result(),
            'hoteles' => $query_hoteles->result(),
            'tiquetes' => $query_tiquetes->result(),
            'asistencias' => $query_asistencias->result(),
            'otros' => $otros,
        ];

    }

    public function query_reserva_asistencia($id_servicio_reserva)
    {

        $sql = 'SELECT
        ra.fecha_ida,
        ra.fecha_regreso,
        co.id_ciudad as id_ciudad_origen,
        CONCAT(co.ciudad, " (", po.pais,")") as ciudad_origen,
        cd.id_ciudad as id_ciudad_destino,
        CONCAT(cd.ciudad, " (", pd.pais,")") as ciudad_destino,
        p.id_proveedor,
        p.proveedor
        FROM servicio_reserva_asistencia ra
        INNER JOIN ciudades co ON ra.id_ciudad_origen = co.id_ciudad
        INNER JOIN paises po  ON po.codigo = co.codigo
        INNER JOIN ciudades cd ON ra.id_ciudad_destino = cd.id_ciudad
        INNER JOIN paises pd  ON pd.codigo = cd.codigo
        INNER JOIN proveedores p ON ra.id_proveedor = p.id_proveedor
        WHERE ra.id_servicio_reserva = ' . $id_servicio_reserva;

        return $this->db->query($sql);

    }

    public function query_vouchers($id_servicio_reserva)
    {
        $this->db->from('vouchers');
        $this->db->where('vouchers.id_servicio_reserva', $id_servicio_reserva);

        return $this->db->get();
    }

    public function query_voucher($id_voucher)
    {
        $this->db->from('vouchers');
        $this->db->where('vouchers.id_voucher', $id_voucher);

        return $this->db->get();
    }

    public function query_notas($id_reserva, $tipo = null)
    {
        $this->db->select('notas_reserva.id_nota_reserva, date_format(notas_reserva.fecha_reg, "%d-%m-%Y %H:%i:%s") as fecha_reg, notas_reserva.id_usuario, notas_reserva.nota, concat(usuarios.nombres, " ",usuarios.apellidos) as usuario, notas_reserva.tipo');
        $this->db->from('notas_reserva');
        $this->db->join('usuarios', 'usuarios.id_usuario = notas_reserva.id_usuario');
        $this->db->where('notas_reserva.id_reserva', $id_reserva);

        if ($tipo != null) {
            $this->db->where('notas_reserva.tipo', $tipo);
        }

        $this->db->order_by('notas_reserva.id_nota_reserva', 'DESC');

        return $this->db->get();
    }

    public function query_pasajeros($id_reserva)
    {

        $this->db->from('pasajeros');
        $this->db->where('pasajeros.id_reserva', $id_reserva);

        return $this->db->get();
    }

}