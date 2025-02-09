<?php

class Model_comentarios_reservas extends CI_Model
{
    public function query_comentarios_reservas_reporte($desde, $hasta, $estado)
    {
        $fdesde = $desde . " 00:00:00";
        $fhasta = $hasta . " 23:59:59";

        #Actividades
        $sql = 'SELECT
        nr.id_reserva,
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
        nr.nota,
        CONCAT(c.nombres," ", c.apellidos) as cliente
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
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
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_actividades = $this->db->query($sql);

        #Paquetes
        $sql = 'SELECT
        nr.id_reserva,
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
        p.proveedor,
        nr.nota
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
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
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_paquetes = $this->db->query($sql);

        #Hoteles
        $sql = 'SELECT
        nr.id_reserva,
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
        CONCAT(c.nombres," ", c.apellidos) as cliente,
        nr.nota
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicios s
        ON s.id_servicio = sr.id_servicio
        INNER JOIN servicio_reserva_hoteles srh
        ON srh.id_servicio_reserva = sr.id_servicio_reserva
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_hoteles = $this->db->query($sql);

        #Tiquetes
        $sql = 'SELECT
        nr.id_reserva,
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
        p.proveedor,
        nr.nota
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
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
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_tiquetes = $this->db->query($sql);

        #Asistencias
        $sql = 'SELECT
        nr.id_reserva,
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
        p.proveedor,
        nr.nota
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
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
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_asistencias = $this->db->query($sql);

        #Otros
        $sql = 'SELECT
        nr.id_reserva,
        r.cod_reserva,
        r.fecha_reg,
        r.estado_reserva,
        r.descuento,
        sr.adultos as pasajeros,
        sr.valor_neto,
        sr.valor_venta,
        sro.descripcion as servicio,
        sr.cod_voucher,
        CONCAT(c.nombres," ", c.apellidos) as cliente,
        nr.nota
        FROM notas_reserva nr
        INNER JOIN reservas r
        ON r.id_reserva = nr.id_reserva
        INNER JOIN clientes c
        ON c.id_cliente = r.id_cliente
        INNER JOIN servicios_reserva sr
        ON sr.id_reserva = r.id_reserva
        INNER JOIN servicio_reserva_otros sro
        ON sro.id_servicio_reserva = sr.id_servicio_reserva
        WHERE  r.estado_reserva = "' . $estado . '" AND nr.fecha_reg >= "' . $fdesde . '" AND nr.fecha_reg <= "' . $fhasta . '"
        ';

        $query_otros = $this->db->query($sql);

        // Recolectar todos los id_reserva
        $id_reservas = [];
        
        foreach ($query_actividades->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }
        foreach ($query_paquetes->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }
        foreach ($query_hoteles->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }
        foreach ($query_tiquetes->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }
        foreach ($query_asistencias->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }
        foreach ($query_otros->result() as $row) {
            $id_reservas[] = $row->id_reserva;
        }

        // Eliminar duplicados y contar
        $total_reservas = count(array_unique($id_reservas));

        return [
            'total_reservas' => $total_reservas,
            'actividades' => $query_actividades->result(),
            'paquetes' => $query_paquetes->result(),
            'hoteles' => $query_hoteles->result(),
            'tiquetes' => $query_tiquetes->result(),
            'asistencias' => $query_asistencias->result(),
            'otros' => $query_otros->result(),
        ];

    }
}