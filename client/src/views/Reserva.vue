<template>
  <div id="reservas">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">
          Detalle de reserva
          <span v-if="reserva != null" class="fw-bold text-info">{{
            reserva.cod_reserva
          }}</span>
        </h4>

        <div class="text-right"></div>
        <div v-if="loading" class="text-center">
          <div class="loadingio-spinner-ripple-6armq16mc04">
            <div class="ldio-2tzb8vdk7pu">
              <div></div>
              <div></div>
            </div>
          </div>
        </div>
        <div v-else>
          <div v-if="reserva != null">
            <div v-if="reserva.estado == 3" class="alert alert-danger fw-bold">
              <i class="fa fa-exclamation-circle"></i> Reserva anulada.
            </div>
            <div class="mb-3">
              <button v-if="reserva.estado != 1 && reserva.estado != 3 && permiso" data-bs-toggle="modal"
                data-bs-target="#aprobar_reserva" class="btn btn-success btn-sm" :disabled="reserva.saldo > 0">
                <i class="fa fa-check-circle"></i> <br />
                Aprobar reserva
              </button>
              <button v-if="reserva.estado != 3" data-bs-toggle="modal" data-bs-target="#enviar_email"
                class="btn btn-info btn-sm ms-1">
                <i class="fa-solid fa-envelope"></i> <br />
                Enviar email
              </button>
              <button v-if="permiso && reserva.estado != 3" data-bs-toggle="modal" data-bs-target="#anular"
                class="btn btn-outline-danger btn-sm ms-1">
                <i class="fa-solid fa-trash"></i> <br />
                Anular
              </button>
            </div>
            <h5 class="fw-bolder text-muted">Información básica</h5>
            <div class="card card-body shadow">
              <div class="row">
                <div class="col-lg-3">
                  <small>Fecha de reserva</small>
                  <h6 class="text-primary fw-bolder">
                    {{ reserva.fecha_reg }}
                  </h6>
                </div>
                <div class="col-lg-6">
                  <small>Cliente</small>
                  <h6 class="text-primary text-uppercase fw-bolder mb-0">
                    {{ reserva.cliente.nombres }}
                    {{ reserva.cliente.apellidos }}
                  </h6>
                  <span>{{ reserva.cliente.email }}</span><br />
                  <span>{{ reserva.cliente.telefono }}</span>
                </div>
                <div class="col-lg-3">
                  <small>Estado</small>
                  <h6 class="fw-bolder">
                    <span v-if="reserva.estado == 0" class="text-warning">Pendiente</span>
                    <span v-if="reserva.estado == 1" class="text-success">Aprobada</span>
                    <span v-if="reserva.estado == 2" class="text-danger">Anulada</span>
                  </h6>
                </div>
              </div>
            </div>
            <h5 class="fw-bolder text-muted mt-3">Servicios</h5>
            <div class="card card-body shadow mt-3" v-for="(servicio, index_servicio) in reserva.servicios"
              v-bind:key="index_servicio">
              <div class="mb-2">
                <span class="text-muted fw-bold">
                  <small>ID: {{ servicio.id_servicio_reserva }}</small>
                </span>
              </div>
              <div v-if="servicio.tipo == 1">
                <div class="mb-2">
                  <span class="fw-bold text-muted">
                    <i class="fa-solid fa-map"></i> Actividad
                  </span>
                </div>
                <div class="row">
                  <div class="col-lg-4">
                    <small class="text-muted">Actividad</small>
                    <h6 class="text-primary">
                      {{ servicio.servicio.servicio }}
                    </h6>
                  </div>
                  <div class="col-lg-2">
                    <small class="text-muted">Pasajeros</small>
                    <h6 class="text-primary" style="font-size: 0.8em;">
                      {{ servicio.adultos }} adultos - {{ servicio.ninos }} niños - {{ servicio.infantes }} infantes
                    </h6>
                  </div>
                  <div class="col-lg-2">
                    <small class="text-muted">Fecha</small>
                    <h6 class="text-primary">
                      {{ servicio.fecha_actividad.f }}
                    </h6>
                  </div>
                  <div v-if="servicio.horario != null" class="col-lg-2">
                    <small class="text-muted">Horario</small>
                    <h6 class="text-primary">
                      {{ servicio.horario.desde }}
                    </h6>
                  </div>
                  <div class="col-lg-2 text-end">
                    <small class="text-muted">Valor venta</small>
                    <h6 class="text-primary">
                      {{ servicio.valor_venta | currency }}
                    </h6>
                  </div>
                </div>
              </div>
              <div v-if="servicio.tipo == 2 || servicio.tipo == 4 || servicio.tipo == 5
                ">
                <div class="mb-2">
                  <span v-if="servicio.tipo == 2" class="fw-bold text-muted">
                    <i class="fa-solid fa-box"></i> Paquete
                  </span>
                  <span v-if="servicio.tipo == 4" class="fw-bold text-muted">
                    <i class="fa-solid fa-plane-departure"></i> Tiquete
                    {{ servicio.tipo_tiquete }}
                  </span>
                  <span v-if="servicio.tipo == 5" class="fw-bold text-muted">
                    <i class="fa-solid fa-user-doctor"></i> Asistencia médica
                  </span>
                </div>
                <div class="row">
                  <div class="col-lg-4">
                    <small class="text-muted">Origen - Destino</small>
                    <h6 class="text-primary mb-0">
                      {{ servicio.origen.ciudad_pais }} -
                      {{ servicio.destino.ciudad_pais }}
                    </h6>
                    <small class="fw-bold text-primary">{{
                      servicio.proveedor.proveedor
                    }}</small>
                  </div>
                  <div class="col-lg-2 text-center">
                    <small class="text-muted">Pasajeros</small>
                    <h6 class="text-primary">
                      {{ servicio.num_pasajeros }}
                    </h6>
                  </div>
                  <div class="col-lg-2">
                    <small class="text-muted">Fecha ida</small>
                    <h6 class="text-primary">
                      {{ servicio.fecha_ida.f }}
                    </h6>
                  </div>
                  <div v-if="servicio.tipo == 2 ||
                    servicio.tipo == 5 ||
                    servicio.tipo_tiquete == 'RT'
                    " class="col-lg-2">
                    <small class="text-muted">Fecha regreso</small>
                    <h6 class="text-primary">
                      {{ servicio.fecha_regreso.f }}
                    </h6>
                  </div>
                  <div class="col-lg-2 text-end">
                    <small class="text-muted">Valor venta</small>
                    <h6 class="text-primary">
                      {{ servicio.valor_venta | currency }}
                    </h6>
                  </div>
                </div>
              </div>
              <div v-if="servicio.tipo == 3">
                <div class="mb-2">
                  <span class="fw-bold text-muted">
                    <i class="fa-solid fa-bed"></i> Hospedaje
                  </span>
                </div>
                <div class="row">
                  <div class="col-lg-4">
                    <small class="text-muted">Hotel</small>
                    <h6 class="text-primary">
                      {{ servicio.servicio.servicio }}
                    </h6>
                  </div>
                  <div class="col-lg-2 text-center">
                    <small class="text-muted">Pasajeros</small>
                    <h6 class="text-primary">
                      {{ servicio.num_pasajeros }}
                    </h6>
                  </div>
                  <div class="col-lg-2">
                    <small class="text-muted">Fecha ida</small>
                    <h6 class="text-primary">
                      {{ servicio.fecha_ida.f }}
                    </h6>
                  </div>
                  <div class="col-lg-2">
                    <small class="text-muted">Fecha regreso</small>
                    <h6 class="text-primary">
                      {{ servicio.fecha_regreso.f }}
                    </h6>
                  </div>
                  <div class="col-lg-2 text-end">
                    <small class="text-muted">Valor venta</small>
                    <h6 class="text-primary">
                      {{ servicio.valor_venta | currency }}
                    </h6>
                  </div>
                </div>
              </div>
              <div v-if="servicio.tipo == 6">
                <div class="mb-2">
                  <span class="fw-bold text-muted">
                    <i class="fa-solid fa-file"></i> Otros servicios
                  </span>
                </div>
                <div class="row">
                  <div class="col-lg-8">
                    <small class="text-muted">Descripción</small>
                    <h6 class="text-primary">
                      {{ servicio.descripcion }}
                    </h6>
                  </div>

                  <div class="col-lg-2 text-end">
                    <small class="text-muted">Valor venta</small>
                    <h6 class="text-primary">
                      {{ servicio.valor_venta | currency }}
                    </h6>
                  </div>
                </div>
              </div>
              <div class="text-end mt-1">
                <button v-if="permiso && reserva.estado != 3" v-on:click="load_edicion(index_servicio)"
                  data-bs-toggle="modal" data-bs-target="#editar_servicio" class="btn btn-primary btn-sm btn-rounded">
                  <i class="fa fa-edit"></i>
                </button>
                <button v-if="reserva.estado != 3" @click="index_servicio_sel = index_servicio" data-bs-toggle="modal"
                  data-bs-target="#vouchers" class="btn btn-success btn-sm btn-rounded ms-1">
                  <i class="fa-solid fa-file"></i>
                </button>
                <button v-if="reserva.servicios.length > 1 && permiso" v-on:click="servicio_sel = servicio"
                  data-bs-toggle="modal" data-bs-target="#eliminar_servicio" class="btn btn-danger btn-sm btn-rounded">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
            <h5 class="fw-bolder text-muted mt-3">Pasajeros</h5>
            <div class="card card-body shadow">
              <div class="text-end">
                <button data-bs-toggle="modal" data-bs-target="#pasajeros" class="btn btn-primary btn-sm rounded-pill">
                  <i class="fa fa-users"></i> Pasajeros
                </button>
              </div>
              <div v-if="reserva.pasajeros.length != 0">
                <table class="table table-hover table-condensed my_table_2">
                  <thead>
                    <tr>
                      <th>Nombres y apellidos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(pasajero, index_pasajero) in reserva.pasajeros" v-bind:key="index_pasajero">
                      <td class="fw-bolder text-primary">
                        {{ pasajero.nombres }} {{ pasajero.apellidos }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <h5 class="fw-bolder text-muted mt-3">Pagos</h5>
            <div class="card card-body shadow">
              <div class="text-end mt-2 mb-4">
                <button v-if="reserva.estado != 3" data-bs-toggle="modal" data-bs-target="#agregar_pago"
                  class="btn btn-success btn-rounded btn-sm" :disabled="reserva.saldo <= 0">
                  <i class="fa fa-plus-circle"></i> Agregar pago
                </button>
              </div>
              <div v-if="reserva.pagos != null" class="table-responsive">
                <table class="table table-hover table-condensed my_table_2">
                  <thead>
                    <tr>
                      <th>Fecha de pago</th>
                      <th>Medio de pago</th>
                      <th class="text-end">Valor venta</th>
                      <th class="text-center">Opciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(pago, index_pago) in reserva.pagos" v-bind:key="index_pago">
                      <td class="fw-bolder text-primary">
                        {{ pago.fecha_pago }}
                      </td>
                      <td class="fw-bolder text-primary">
                        {{ pago.medio.medio }}
                      </td>
                      <td class="text-end">
                        {{ pago.valor | currency }}
                      </td>
                      <td class="text-center">
                        <button v-on:click="pago_sel = pago" data-bs-toggle="modal" data-bs-target="#eliminar_pago"
                          class="btn btn-danger btn-sm btn-rounded">
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="p-3">
                <div class="text-muted">
                  <i class="fa fa-info-circle"></i> No se han registrado pagos
                  para esta reserva.
                </div>
              </div>
            </div>
            <div class="card card-body shadow mt-3">
              <div class="mt-4 text-end">
                <small>Total reserva</small>
                <h5 class="text-info fw-bolder">
                  {{ reserva.valor | currency }}
                </h5>
                <small>Descuento</small>
                <h5 class="text-info fw-bolder">
                  {{ reserva.descuento | currency }}
                  <br />
                  <small v-if="reserva.descuento != 0 && permiso"><a v-on:click="delete_descuento()" class="text-danger"
                      href="javascript:void(0)">
                      <i class="fa fa-trash"></i> Quitar descuento</a></small>
                </h5>

                <small>Valor total</small>
                <h5 class="text-info fw-bolder">
                  {{ reserva.total | currency }}
                </h5>
                <small>Total pagos</small>
                <h5 class="text-primary fw-bolder">
                  {{ reserva.total_pagos | currency }}
                </h5>
                <small>Saldo</small>
                <h5 class="text-success fw-bolder">
                  {{ reserva.saldo | currency }}
                </h5>
              </div>
            </div>
            <h5 class="fw-bolder text-muted mt-3">Notas</h5>
            <div class="card card-body shadow mt-3">
              <div class="text-end">
                <button data-bs-toggle="modal" data-bs-target="#nota" class="btn btn-primary btn-sm rounded-pill">
                  <i class="fa fa-plus-circle"></i> Agregar nota
                </button>
              </div>
              <div v-if="reserva.notas.length != 0">
                <div v-for="(nota, index_nota) in reserva.notas" v-bind:key="index_nota" class="mb-2">
                  <div v-if="(nota.tipo == 2 && permiso) || nota.tipo == 1 || nota.tipo == 0">
                    <small class="text-muted fst-italic">{{ nota.fecha_reg }} - {{ nota.usuario }} <strong
                        v-if="nota.tipo == 1"> - Nota para cliente</strong></small>

                    <div class=" bg-light p-2">
                      {{ nota.nota }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <div class="alert alert-danger">
              <i class="fa fa-exclamation-circle"></i> No se encontró al reserva
              que intenta consultar.
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editar_servicio">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar servicio</h5>
            <button id="close_modal" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="servicio_sel != null">
              <div v-if="servicio_sel.tipo == 1">
                <div class="form-group">
                  <label>Servicio</label>
                  <v-select v-model="servicio_sel.servicio" @search="get_actividades" label="servicio"
                    :options="actividades" :clearable="false"></v-select>
                </div>
                <div class="form-group">
                  <label>Adultos</label>
                  <input v-model="servicio_sel.adultos" class="form-control" type="number" />
                </div>
                <div class="form-group">
                  <label>Niños</label>
                  <input v-model="servicio_sel.ninos" class="form-control" type="number" />
                </div>
                <div class="form-group">
                  <label>Infantes</label>
                  <input v-model="servicio_sel.infantes" class="form-control" type="number" />
                </div>
                <div class="form-group">
                  <label>Fecha de actividad</label>
                  <input v-model="servicio_sel.fecha_actividad.sf" class="form-control" type="date" />
                </div>
                <div v-if="servicio_sel != null" class="form-group">
                  <label>Horario</label>
                  <v-select v-model="servicio_sel.horario" label="desde" :options="horarios"></v-select>
                </div>
                <div class="text-end">
                  <button v-on:click="update_servicio" class="btn btn-success rounded-pill btn-sm">
                    <i class="fa fa-save"></i> Guardar
                  </button>
                  <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                    Cancelar
                  </button>
                </div>
              </div>
              <div v-if="servicio_sel.tipo == 2 ||
                servicio_sel.tipo == 4 ||
                servicio_sel.tipo == 5
                ">
                <div>
                  <div v-if="servicio_sel.tipo == 4" class="form-group">
                    <label>Tipo</label>
                    <select v-model="servicio_sel.tipo_tiquete" class="form-control">
                      <option value="RT">RT</option>
                      <option value="OW">OW</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Origen</label>
                    <v-select v-model="servicio_sel.origen" @input="ciudades = []" @search="get_ciudades"
                      label="ciudad_pais" :options="ciudades" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Destino</label>
                    <v-select v-model="servicio_sel.destino" @input="ciudades = []" @search="get_ciudades"
                      label="ciudad_pais" :options="ciudades" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Pasajeros</label>
                    <input v-model="servicio_sel.num_pasajeros" class="form-control" type="number" min="1" />
                  </div>
                  <div class="form-group">
                    <label>Fecha ida</label>
                    <input v-model="servicio_sel.fecha_ida.sf" type="date" class="form-control" />
                  </div>
                  <div v-if="servicio_sel.tipo == 2 ||
                    servicio_sel.tipo == 5 ||
                    servicio_sel.tipo_tiquete == 'RT'
                    " class="form-group">
                    <label>Fecha regreso</label>
                    <input v-model="servicio_sel.fecha_regreso.sf" :min="servicio_sel.fecha_ida.sf" type="date"
                      class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Proveedor</label>
                    <v-select v-model="servicio_sel.proveedor" @input="proveedores = []" @search="get_proveedores"
                      label="proveedor" :options="proveedores" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Precio neto</label>
                    <money v-model="servicio_sel.valor_neto" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="form-group">
                    <label>Precio venta</label>
                    <money v-model="servicio_sel.valor_venta" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="text-end">
                    <button v-on:click="update_servicio()" :disabled="servicio_sel.num_pasajeros <= 0 ||
                      servicio_sel.origen == null ||
                      servicio_sel.destino == null ||
                      servicio_sel.fecha_ida == null ||
                      ((servicio_sel.tipo_tiquete == 'RT' ||
                        servicio_sel.tipo == 2 ||
                        servicio_sel == 5) &&
                        servicio_sel.fecha_regreso == null) ||
                      servicio_sel.proveedor == null
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Guardar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="servicio_sel.tipo == 3">
                <div>
                  <div class="form-group">
                    <label>Hotel</label>
                    <v-select v-model="servicio_sel.servicio" @search="get_hoteles" label="servicio" :options="hoteles"
                      :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Pasajeros</label>
                    <input v-model="servicio_sel.num_pasajeros" class="form-control" type="number" min="1" />
                  </div>
                  <div class="form-group">
                    <label>Fecha ida</label>
                    <input v-model="servicio_sel.fecha_ida.sf" type="date" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Fecha regreso</label>
                    <input v-model="servicio_sel.fecha_regreso.sf" type="date" :min="servicio_sel.fecha_ida.sf"
                      class="form-control" />
                  </div>

                  <div class="form-group">
                    <label>Precio neto</label>
                    <money v-model="servicio_sel.valor_neto" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="form-group">
                    <label>Precio venta</label>
                    <money v-model="servicio_sel.valor_venta" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="text-end">
                    <button v-on:click="update_servicio()" :disabled="servicio_sel.servicio == null ||
                      servicio_sel.num_pasajeros <= 0 ||
                      servicio_sel.fecha_ida == null ||
                      servicio_sel.fecha_regreso == null
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Guardar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="servicio_sel.tipo == 6">
                <div class="form-group">
                  <label>Descripción</label>
                  <textarea v-model="servicio_sel.descripcion" class="form-control" />
                </div>
                <div class="form-group">
                  <label>Precio neto</label>
                  <money v-model="servicio_sel.valor_neto" class="form-control text-end" v-bind="money" required></money>
                </div>
                <div class="form-group">
                  <label>Precio venta</label>
                  <money v-model="servicio_sel.valor_venta" class="form-control text-end" v-bind="money" required></money>
                </div>
                <div class="text-end">
                  <button v-on:click="update_servicio" class="btn btn-success rounded-pill btn-sm">
                    <i class="fa fa-save"></i> Guardar
                  </button>
                  <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                    Cancelar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="eliminar_servicio">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-danger">
            <h5 class="modal-title fw-bolder">Eliminar servicio</h5>
            <button id="close_modal_eliminar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="servicio_sel != null">
              <span class="text-danger">
                ¿Desea eliminar el servicio de esta reserva?
              </span>
              <div class="text-end">
                <button v-on:click="delete_servicio" class="btn btn-danger rounded-pill btn-sm">
                  <i class="fa fa-trash"></i> Sí
                </button>
                <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                  No
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="agregar_pago">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Agregar pago</h5>
            <button id="close_modal_pago" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Fecha de pago</label>
              <input v-model="nuevo_pago.fecha" class="form-control" type="date" />
            </div>
            <div class="form-group">
              <label>Valor del pago</label>
              <money v-model="nuevo_pago.valor" class="form-control text-end" v-bind="money" required></money>
            </div>
            <div class="form-group">
              <label>Medio de pago</label>
              <select v-model="nuevo_pago.medio" class="form-control">
                <option value="0">Pasarela de pago</option>
                <option value="1">Consignación o transferencia</option>
                <option value="2">Efectivo</option>
                <option value="3">Tarjetas de crédito</option>
                <option value="4">Pago en destino</option>
              </select>
            </div>
            <div class="text-end">
              <button v-on:click="set_pago" class="btn btn-success rounded-pill btn-sm">
                <i class="fa fa-save"></i> Guardar
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="eliminar_pago">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-danger">
            <h5 class="modal-title fw-bolder">Eliminar pago</h5>
            <button id="close_modal_eliminar_pago" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="pago_sel != null" class="modal-body">
            <div class="text-danger">
              <div class="mb-3">
                <i class="fa fa-question-circle"></i> ¿Desea eliminar este pago?
              </div>
              <div>
                <ul>
                  <li>
                    Fecha de pago: <strong>{{ pago_sel.fecha_pago }}</strong>
                  </li>
                  <li>
                    Medio: <strong>{{ pago_sel.medio.medio }}</strong>
                  </li>
                  <li>
                    Valor: <strong>{{ pago_sel.valor | currency }}</strong>
                  </li>
                </ul>
              </div>
            </div>
            <div class="text-end">
              <button v-on:click="delete_pago" class="btn btn-danger rounded-pill btn-sm">
                <i class="fa fa-trash"></i> Sí
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                No
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="aprobar_reserva">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Aprobar reserva</h5>
            <button id="close_modal_aprobar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="text-primary">
              <div class="mb-3">
                <i class="fa fa-question-circle"></i> ¿Desea aprobar esta
                reserva?
              </div>
            </div>
            <div class="text-end">
              <button v-on:click="aprobar_reserva" class="btn btn-success rounded-pill btn-sm">
                <i class="fa fa-check-circle"></i> Sí
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                No
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="enviar_email">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Enviar email</h5>
            <button id="close_modal_enviar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form v-on:submit.prevent="enviar_email">
              <div v-if="reserva != null" class="form-group">
                <label>Email</label>
                <input v-model="reserva.cliente.email" type="email" class="form-control" required />
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success rounded-pill btn-sm">
                  <i class="fa-solid fa-paper-plane"></i> Enviar
                </button>
                <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="vouchers">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Vouchers</h5>
            <button id="close_modal" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card card-body border shadow">
              <div class="form-group">
                <label>Subir archivo</label>
                <input ref="input_file" @change="handle_images" type="file" class="form-control">
              </div>
              <div class="form-group">
                <label>Título o descripción</label>
                <input v-model="titulo_archivo" type="text" class="form-control">
              </div>
              <div class="text-end">
                <button @click="upload_voucher" :disabled="!hay_archivo" class="btn btn-primary btn-sm rounded-pill">
                  <i class="fa fa-upload"></i> Guardar
                </button>
              </div>
            </div>
            <div v-if="index_servicio_sel != null" class="mt-4">
              <div v-if="reserva.servicios[index_servicio_sel].vouchers.length != 0">
                <div v-for="(voucher, index_voucher) in reserva.servicios[index_servicio_sel].vouchers"
                  v-bind:key="index_voucher" class="card card-body shadow mb-2">
                  <div>
                    <a :href="voucher.url" target="_blank" class="fw-bold">
                      <i class="fa fa-file"></i> {{ voucher.titulo }}
                    </a>
                    <div class="text-end">
                      <small>
                        <a @click="delete_voucher(voucher.id_voucher)" href="javascript:void(0)" class="text-danger">
                          <i class="fa fa-trash"></i> Eliminar
                        </a>
                      </small>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <span class="text-muted">
                  <i class="fa fa-info-circle"></i> No se han agregado vouchers para este servicio.
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nota">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Nueva nota</h5>
            <button id="close_modal" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Tipo</label>
              <select v-model="nueva_nota.tipo" class="form-control">
                <option value="2">Admin</option>
                <option value="0">Agentes y administrativos </option>
                <option value="1">Todos</option>
              </select>
            </div>
            <div class="form-group">
              <label>Nota</label>
              <textarea v-model="nueva_nota.nota" class="form-control" rows="4"></textarea>
            </div>
            <div class="text-end">
              <button @click="set_nota" :disabled="nueva_nota.nota == null || nueva_nota.nota.trim().length == 0"
                class="btn btn-primary btn-sm rounded-pill">
                <i class="fa fa-save"></i> Guardar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="pasajeros">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Pasajeros</h5>
            <button id="close_modal_pasajeros" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="reserva != null" class="modal-body">
            <div v-if="reserva.pasajeros.length != 0">
              <div v-for="(pasajero, index_pasajero) in reserva.pasajeros" v-bind:key="index_pasajero"
                class="card card-body border shadow mb-3">
                <span class="text-muted fw-bold">Pasajero {{ index_pasajero + 1 }}</span>
                <div class="row">
                  <div class="col-lg-6 form-group">
                    <label>Nombres</label>
                    <input v-model="pasajero.nombres" type="text" class="form-control">
                  </div>
                  <div class="col-lg-6 form-group">
                    <label>Apellidos</label>
                    <input v-model="pasajero.apellidos" type="text" class="form-control">
                  </div>
                </div>
                <div class="text-end">
                  <small>
                    <a @click="eliminar_pasajero(index_pasajero)" href="javascript:void(0)" class="text-danger">
                      <i class="fa fa-trash"></i> Eliminar
                    </a>
                  </small>
                </div>
              </div>
            </div>
            <div class="text-end">
              <button v-if="reserva.estado != 3" @click="set_pasajero" class="btn btn-primary btn-sm rounded-pill">
                <i class="fa fa-plus-circle"></i> Agregar pasajero
              </button>
            </div>
            <hr>
            <div class="text-end mt-4">
              <button v-if="reserva.estado != 3" @click="update_pasajeros" class="btn btn-success btn-sm rounded-pill">
                <i class="fa fa-save"></i> Guardar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="anular">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Anular reserva</h5>
            <button id="close_modal_anular" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="text-danger">
              <i class="fa fa-info-question"></i> ¿Desea anular esta reserva?
            </div>
            <div class="text-end">
              <button v-on:click="anular_reserva" class="btn btn-danger rounded-pill btn-sm">
                <i class="fa fa-trash"></i> Si, anular
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Reserva",
  metaInfo: {
    title: "Reserva - Backoffice",
  },
  data() {
    return {
      loading: true,
      base_url: this.$base_url,
      id_reserva: null,
      reserva: null,
      servicio_sel: null,
      actividades: [],
      nuevo_pago: {
        medio: 1,
        fecha: new Date().toISOString().slice(0, 10),
        valor: 0,
      },
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        suffix: "",
        precision: 0,
        masked: false,
      },
      pago_sel: null,
      horarios: [],
      email: null,
      proveedores: [],
      ciudades: [],
      hoteles: [],
      index_servicio_sel: null,
      file: [],
      hay_archivo: false,
      files: [],
      titulo_archivo: null,
      permiso: false,
      nueva_nota: {
        tipo: 0,
        nota: null
      }
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.id_reserva = this.$route.params.id_reserva;
    this.get_reserva();
    this.permiso = this.session.usuario.usuario.permisos.GRESERVAS;
  },
  methods: {
    get_reserva: function () {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {},
      };
      axios
        .get(vm.$base_url + "reservas/reserva/" + this.id_reserva, config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.reserva = response.data.data;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
          vm.loading = false;
        });
    },
    get_actividades: function (search = null) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "servicios/actividades_list", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.actividades = response.data.data.actividades;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
          vm.loading = false;
        });
    },
    update_servicio: function () {
      const vm = this;
      var valida = true;
      var servicio = null;
      switch (Number(this.servicio_sel.tipo)) {
        case 1:
          if (this.servicio_sel.servicio == null) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Editar reserva",
              text: "Seleccione una actividad.",
            });
            valida = false;
          }
          if (this.servicio_sel.num_pasajeros <= 0) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Editar reserva",
              text: "Indique la cantidad de pasajeros",
            });
            valida = false;
          }
          if (!valida) {
            return false;
          }
          var id_horario = null;
          if (this.servicio_sel.horario != null) {
            id_horario = this.servicio_sel.horario.id_horario;
          }

          servicio = {
            tipo: 1,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            id_servicio: this.servicio_sel.servicio.id_servicio,
            adultos: this.servicio_sel.adultos,
            ninos: this.servicio_sel.ninos,
            infantes: this.servicio_sel.infantes,
            fecha_actividad: this.servicio_sel.fecha_actividad.sf,
            id_horario: id_horario,
          };
          break;

        case 2:
          if (this.servicio_sel.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }
          if (this.servicio_sel.origen == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de origen",
            });
            return false;
          }
          if (this.servicio_sel.destino == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de destino",
            });
            return false;
          }
          if (this.servicio_sel.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (this.servicio_sel.fecha_regreso == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }
          if (this.servicio_sel.proveedor == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione el proveedor del servicio",
            });
            return false;
          }
          servicio = {
            tipo: 2,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            num_pasajeros: this.servicio_sel.num_pasajeros,
            origen: this.servicio_sel.origen.id_ciudad,
            destino: this.servicio_sel.destino.id_ciudad,
            fecha_ida: this.servicio_sel.fecha_ida.sf,
            fecha_regreso: this.servicio_sel.fecha_regreso.sf,
            id_proveedor: this.servicio_sel.proveedor.id_proveedor,
            valor_neto: this.servicio_sel.valor_neto,
            valor_venta: this.servicio_sel.valor_venta,
          };
          break;
        case 3:
          if (this.servicio_sel.servicio == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Selecciona el hotel.",
            });
            return false;
          }
          if (this.servicio_sel.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }

          if (this.servicio_sel.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (this.servicio_sel.fecha_regreso == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }

          servicio = {
            tipo: 3,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            id_servicio: this.servicio_sel.servicio.id_servicio,
            num_pasajeros: this.servicio_sel.num_pasajeros,
            fecha_ida: this.servicio_sel.fecha_ida.sf,
            fecha_regreso: this.servicio_sel.fecha_regreso.sf,
            valor_neto: this.servicio_sel.valor_neto,
            valor_venta: this.servicio_sel.valor_venta,
          };
          break;
        case 4:
          if (this.servicio_sel.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }
          if (this.servicio_sel.origen == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de origen",
            });
            return false;
          }
          if (this.servicio_sel.destino == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de destino",
            });
            return false;
          }
          if (this.servicio_sel.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (this.servicio_sel.fecha_regreso == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }
          if (this.servicio_sel.proveedor == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione el proveedor del servicio",
            });
            return false;
          }
          servicio = {
            tipo: this.servicio_sel.tipo,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            tipo_tiquete: this.servicio_sel.tipo_tiquete,
            num_pasajeros: this.servicio_sel.num_pasajeros,
            origen: this.servicio_sel.origen.id_ciudad,
            destino: this.servicio_sel.destino.id_ciudad,
            fecha_ida: this.servicio_sel.fecha_ida.sf,
            fecha_regreso: this.servicio_sel.fecha_regreso.sf,
            id_proveedor: this.servicio_sel.proveedor.id_proveedor,
            valor_neto: this.servicio_sel.valor_neto,
            valor_venta: this.servicio_sel.valor_venta,
          };
          break;
        case 5:
          if (this.servicio_sel.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }
          if (this.servicio_sel.origen == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de origen",
            });
            return false;
          }
          if (this.servicio_sel.destino == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de destino",
            });
            return false;
          }
          if (this.servicio_sel.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (this.servicio_sel.fecha_regreso == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }
          if (this.servicio_sel.proveedor == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione el proveedor del servicio",
            });
            return false;
          }
          servicio = {
            tipo: 5,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            num_pasajeros: this.servicio_sel.num_pasajeros,
            origen: this.servicio_sel.origen.id_ciudad,
            destino: this.servicio_sel.destino.id_ciudad,
            fecha_ida: this.servicio_sel.fecha_ida.sf,
            fecha_regreso: this.servicio_sel.fecha_regreso.sf,
            id_proveedor: this.servicio_sel.proveedor.id_proveedor,
            valor_neto: this.servicio_sel.valor_neto,
            valor_venta: this.servicio_sel.valor_venta,
          };
          break;
        case 6:
          if (this.servicio_sel.descripcion == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Digita la descripción del servicio",
            });
            return false;
          }

          servicio = {
            tipo: this.servicio_sel.tipo,
            id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
            descripcion: this.servicio_sel.descripcion,
            valor_neto: this.servicio_sel.valor_neto,
            valor_venta: this.servicio_sel.valor_venta,
          };
          break;
      }
      if (servicio == null) {
        return false;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        servicio: servicio,
      };
      axios
        .post(this.$base_url + "reservas/update_servicio", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Editar reserva",
              text: response.data.message,
            });
            document.getElementById("close_modal").click();
            vm.servicio_sel = null;
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_servicio: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio_reserva: this.servicio_sel.id_servicio_reserva,
      };
      axios
        .post(this.$base_url + "reservas/delete_servicio", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Editar reserva",
              text: response.data.message,
            });
            document.getElementById("close_modal_eliminar").click();
            vm.servicio_sel = null;
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    load_edicion: function (index) {
      this.servicio_sel = this.reserva.servicios[index];
      switch (Number(this.servicio_sel.tipo)) {
        case 1:
          this.get_horarios(this.servicio_sel.servicio.id_servicio);
          this.get_actividades();
          break;
      }
    },
    get_horarios: function (id_servicio) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
      };
      axios
        .get(
          vm.$base_url + "servicios/horarios_servicio/" + id_servicio,
          config, {
          headers: headers,
        }
        )
        .then((response) => {
          if (response.data.status) {
            vm.horarios = response.data.data.horarios;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Horarios",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Horarios",
            text: error,
          });
          vm.loading = false;
        });
    },
    get_proveedores: function (search) {
      if (search.length < 3) {
        return false;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          search: search,
          tipo: this.servicio_sel.tipo,
        },
      };
      axios
        .get(vm.$base_url + "proveedores/proveedores", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.proveedores = response.data.data.proveedores;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
          vm.loading = false;
        });
    },
    get_ciudades: function (search) {
      if (search.length < 3) {
        return false;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "destinos/ciudades", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.ciudades = response.data.data.ciudades;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
          vm.loading = false;
        });
    },
    set_pago: function () {
      const vm = this;

      if (this.nuevo_pago.valor == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Agregar pago",
          text: "El valor debe ser mayor a cero.",
        });
        return false;
      }

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
        pago: this.nuevo_pago,
      };
      axios
        .post(this.$base_url + "reservas/set_pago", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Agregar pago",
              text: response.data.message,
            });
            document.getElementById("close_modal_pago").click();
            vm.servicio_sel = null;
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    delete_pago: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_pago: this.pago_sel.id_pago,
      };
      axios
        .post(this.$base_url + "reservas/delete_pago", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Eliminar pago",
              text: response.data.message,
            });
            document.getElementById("close_modal_eliminar_pago").click();
            vm.pago_sel = null;
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    aprobar_reserva: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
      };
      axios
        .post(this.$base_url + "reservas/aprobar_reserva", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Aprobar reserva",
              text: response.data.message,
            });
            document.getElementById("close_modal_aprobar").click();

            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    enviar_email: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
        email: this.reserva.cliente.email,
      };
      axios
        .post(this.$base_url + "reservas/enviar_email", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Enviar",
              text: response.data.message,
            });
            document.getElementById("close_modal_enviar").click();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    cambiar_estado_review: function (review) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_review: review.id_review,
        estado: review.estado,
      };
      axios
        .post(this.$base_url + "reservas/update_review", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Reviews",
              text: response.data.message,
            });
            document.getElementById("close_modal_enviar").click();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    delete_descuento: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
      };
      axios
        .post(this.$base_url + "reservas/quitar_descuento", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Enviar",
              text: response.data.message,
            });
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    get_hoteles: function (search) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "servicios/hoteles_list", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.hoteles = response.data.data.hoteles;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
          vm.loading = false;
        });
    },
    handle_images(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        this.hay_archivo = false;
        return;
      }
      this.file = files;
      this.hay_archivo = true;
    },
    upload_voucher: function () {
      this.hay_archivo = false;
      this.loading = true;
      var formData = new FormData();
      formData.append("files", this.file[0]);
      formData.append("token", this.session.usuario.token_session);
      formData.append("id_servicio_reserva", this.reserva.servicios[this.index_servicio_sel].id_servicio_reserva);
      formData.append("titulo", this.titulo_archivo);
      const vm = this;
      axios
        .post(this.$base_url + "upload/voucher", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          if (response.data.status) {
            vm.hay_archivo = false;
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Excelente!",
              text: "Se ha subido el archivo.",
            });
            vm.titulo_archivo = null;
            vm.$refs.input_file.value = null;
            vm.loading = false;
            vm.get_reserva();
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Ups!",
              text: response.data.message,
            });
            vm.loading = false;
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Ups",
            text: error,
          });
          console.log(error);
          vm.loading = false;
        });
    },
    delete_voucher: function (id_voucher) {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_voucher: id_voucher
      };
      axios
        .post(this.$base_url + "reservas/delete_voucher", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Eliminar pago",
              text: response.data.message,
            });
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    set_nota: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
        nota: this.nueva_nota,
      };
      axios
        .post(this.$base_url + "reservas/set_nota", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Agregar pago",
              text: response.data.message,
            });
            vm.nueva_nota = {
              nota: null,
              tipo: 0
            };
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    set_pasajero: function () {
      this.reserva.pasajeros.push(
        {
          id_pasajero: null,
          nombres: null,
          apellidos: null
        }
      );
    },
    eliminar_pasajero: function (index) {
      this.reserva.pasajeros.splice(index, 1);
    },
    update_pasajeros: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
        pasajeros: this.reserva.pasajeros,
      };
      axios
        .post(this.$base_url + "reservas/update_pasajeros", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Pasajeros",
              text: response.data.message,
            });
            document.getElementById("close_modal_pasajeros").click();
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    },
    error_manager: function (error) {
      let vm = this;
      let message = error;
      if (error.response.status != undefined && error.response.status == 401) {
        message = error.response.data.message;
        localStorage.removeItem("sess_vyt_backoffice");
        location.reload();
      }
      vm.$notify({
        group: "foo",
        type: "error",
        title: "Error",
        text: message,
      });
    },
    anular_reserva: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_reserva: this.id_reserva,
      };
      axios
        .post(this.$base_url + "reservas/anular_reserva", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Agregar pago",
              text: response.data.message,
            });
            vm.nueva_nota = null;
            document.getElementById("close_modal_anular").click();
            vm.get_reserva();
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
        })
        .finally();
    }
  },
  watch: {
    $route() {
      this.id_reserva = this.$route.params.id_reserva;
      this.get_reserva();
    },
  },
};
</script>
