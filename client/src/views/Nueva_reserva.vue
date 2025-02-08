<template>
  <div id="reservas">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Nueva reserva</h4>
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
          <div>
            <h5 class="fw-bolder text-muted">Información básica</h5>
            <div class="card card-body shadow">
              <div class="row">
                <div class="col-lg-6">
                  <div v-if="cliente != null">
                    <small>Cliente</small>
                    <h6 class="fw-bold text-primary">
                      {{ cliente.nombres }} {{ cliente.apellidos }}
                    </h6>
                    <a v-on:click="
                      (crear_cliente = false), (cliente_encontrado = false)
                      " href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#agregar_cliente">
                      <i class="fa fa-edit"></i> Cambiar cliente</a>
                  </div>
                  <button v-else data-bs-toggle="modal" data-bs-target="#agregar_cliente"
                    class="btn btn-primary rounded-pill btn-sm">
                    <i class="fa fa-plus-circle"></i> Agregar cliente
                  </button>
                </div>
              </div>
            </div>
            <h5 class="fw-bolder text-muted mt-3">Servicios</h5>
            <div>
              <div class="text-end mt-2 mb-4">
                <button data-bs-toggle="modal" data-bs-target="#agregar_servicio"
                  class="btn btn-success btn-rounded btn-sm">
                  <i class="fa fa-plus-circle"></i> Agregar servicio
                </button>
              </div>
              <div>
                <div v-for="(servicio, index_servicio) in servicios" v-bind:key="index_servicio"
                  class="card card-body border shadow p-3 mb-2">
                  <div v-if="servicio.tipo == 1">
                    <div class="mb-2">
                      <span class="fw-bold text-muted">
                        <i class="fa-solid fa-map"></i> Actividad
                      </span>
                    </div>
                    <div class="row">
                      <div class="col-lg-3">
                        <small class="text-muted">Actividad</small>
                        <h6 class="text-primary">
                          {{ servicio.servicio.servicio }}
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Pasajeros</small>
                        <h6 class="text-primary" style="font-size: 0.8em;">
                          {{ servicio.adultos }} Adultos - {{ servicio.ninos }} Niños - {{ servicio.infantes }} Infantes
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Fecha</small>
                        <h6 class="text-primary">
                          {{ servicio.fecha_actividad }}
                        </h6>
                      </div>
                      <div v-if="servicio.horario != null" class="col-lg-3">
                        <small class="text-muted">Horario</small>
                        <h6 class="text-primary">
                          {{ servicio.horario.desde }}
                        </h6>
                      </div>
                    </div>
                  </div>
                  <div v-if="servicio.tipo == 2 ||
                    servicio.tipo == 4 ||
                    servicio.tipo == 5
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
                        <i class="fa-solid fa-user-doctor"></i> Asistencia
                        médica
                      </span>
                    </div>
                    <div class="row">
                      <div class="col-lg-3">
                        <small class="text-muted">Origen - Destino</small>
                        <h6 class="text-primary mb-0">
                          {{ servicio.origen.ciudad }} -
                          {{ servicio.destino.ciudad }}
                        </h6>
                        <small class="fw-bold text-primary">{{
                          servicio.proveedor.proveedor
                        }}</small>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Pasajeros</small>
                        <h6 class="text-primary">
                          {{ servicio.num_pasajeros }}
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Fecha ida</small>
                        <h6 class="text-primary">
                          {{ servicio.fecha_ida }}
                        </h6>
                      </div>
                      <div v-if="servicio.tipo == 2 || servicio.tipo_tiquete == 'RT'
                        " class="col-lg-3">
                        <small class="text-muted">Fecha vuelta</small>
                        <h6 class="text-primary">
                          {{ servicio.fecha_regreso }}
                        </h6>
                      </div>
                    </div>
                  </div>
                  <div v-if="servicio.tipo == 3">
                    <div class="mb-2">
                      <span class="fw-bold text-muted">
                        <i class="fa-solid fa-bed"></i> Hotel
                      </span>
                    </div>
                    <div class="row">
                      <div class="col-lg-3">
                        <small class="text-muted">Hotel</small>
                        <h6 v-if="servicio.servicio != null" class="text-primary mb-0">
                          {{ servicio.servicio.servicio }}
                        </h6>
                        <h6 v-if="servicio.nuevo_hotel.nombre != null" class="text-primary mb-0">
                          {{ servicio.nuevo_hotel.nombre }}
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Pasajeros</small>
                        <h6 class="text-primary">
                          {{ servicio.num_pasajeros }}
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Fecha ida</small>
                        <h6 class="text-primary">
                          {{ servicio.fecha_ida }}
                        </h6>
                      </div>
                      <div class="col-lg-3">
                        <small class="text-muted">Fecha vuelta</small>
                        <h6 class="text-primary">
                          {{ servicio.fecha_regreso }}
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
                      <div class="col-lg-12">
                        <small class="text-muted">Descripción</small>
                        <h6 class="text-primary">{{ servicio.descripcion }}</h6>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 text-end">
                      <small class="text-muted">Valor venta</small>
                      <h6 class="text-primary">
                        {{ servicio.valor_venta | currency }}
                      </h6>
                    </div>
                    <div class="col-lg-12 text-end mt-1">
                      <button v-on:click="
                        (servicio_sel = servicio),
                        (index_sel = index_servicio)
                        " data-bs-toggle="modal" data-bs-target="#editar_servicio"
                        class="btn btn-primary btn-sm btn-rounded">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button v-on:click="
                        (servicio_sel = servicio),
                        (index_sel = index_servicio)
                        " data-bs-toggle="modal" data-bs-target="#eliminar_servicio"
                        class="btn btn-danger btn-sm btn-rounded">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-end mt-4 mb-4">
              <button data-bs-toggle="modal" data-bs-target="#agregar_descuento"
                class="btn btn-success btn-rounded btn-sm">
                <i class="fa fa-plus-circle"></i> Agregar/modificar descuento
              </button>
            </div>
            <div class="mt-4 text-end">
              <small>Total reserva</small>
              <h5 class="text-primary fw-bolder">
                {{ subtotal | currency }}
              </h5>
            </div>
            <div class="mt-4 text-end">
              <small>Descuento</small>
              <h5 class="text-info fw-bolder">
                {{ descuento | currency }}
              </h5>
            </div>
            <div class="mt-4 text-end">
              <small>Total a pagar</small>
              <h5 class="text-success fw-bolder">
                {{ total | currency }}
              </h5>
            </div>
            <hr />
            <div class="text-end mb-3">
              <button v-on:click="set_reserva" class="btn btn-success btn-sm"
                :disabled="servicios.length == 0 && cliente != null">
                <i class="fa fa-save"></i> <br />
                Guardar reserva
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="agregar_cliente">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Agregar cliente</h5>
            <button id="close_modal_cliente" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="!crear_cliente">
              <div v-if="!cliente_encontrado">
                <div class="form-group">
                  <label>Número de identificación</label>
                  <input v-on:keyup.enter="search_cliente" v-model="identificacion_cliente" type="text"
                    class="form-control" />
                </div>
                <div class="text-end">
                  <button v-on:click="search_cliente" :disabled="identificacion_cliente == null"
                    class="btn btn-primary btn-sm rounded-pill">
                    Siguiente <i class="fa fa-chevron-right"></i>
                  </button>
                </div>
              </div>
              <div v-else>
                <div class="card card-body mb-4">
                  <div class="mb-3">
                    <h6 class="text-primary fw-bold">Cliente encontrado</h6>
                  </div>
                  <div>
                    <small>Cliente</small>
                    <h6 class="fw-bold">
                      {{ cliente_sel.nombres }} {{ cliente_sel.apellidos }}
                    </h6>
                    <small>Email</small>
                    <h6 class="fw-bold">
                      {{ cliente_sel.email }}
                    </h6>
                  </div>
                </div>
                <div class="float-start">
                  <button v-on:click="cliente_encontrado = false" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fa fa-chevron-left"></i> Atrás
                  </button>
                </div>
                <div class="float-end">
                  <button v-on:click="select_cliente" class="btn btn-success btn-sm rounded-pill">
                    <i class="fa fa-save"></i> Seleccionar cliente
                  </button>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="mb-3">
                <h6 class="text-primary fw-bold">Registar nuevo cliente</h6>
              </div>
              <div class="form-group">
                <label>Tipo de identificación</label>
                <select v-model="nuevo_cliente.tipo_identificacion" class="form-control">
                  <option value="CC">CC</option>
                  <option value="CE">CE</option>
                  <option value="PAS">PAS</option>
                </select>
              </div>
              <div class="form-group">
                <label>Nombres</label>
                <input v-model="nuevo_cliente.nombres" type="text" class="form-control" />
              </div>
              <div class="form-group">
                <label>Apellidos</label>
                <input v-model="nuevo_cliente.apellidos" type="text" class="form-control" />
              </div>
              <div class="form-group">
                <label>Email</label>
                <input v-model="nuevo_cliente.email" type="text" class="form-control" />
              </div>
              <div class="form-group">
                <label>Teléfono</label>
                <input v-model="nuevo_cliente.telefono" type="text" class="form-control" />
              </div>
              <div class="float-start">
                <button v-on:click="crear_cliente = false" class="btn btn-primary btn-sm rounded-pill">
                  <i class="fa fa-chevron-left"></i> Atrás
                </button>
              </div>
              <div class="float-end">
                <button v-on:click="set_cliente" class="btn btn-success btn-sm rounded-pill">
                  <i class="fa fa-save"></i> Guardar
                </button>
                <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-sm rounded-pill ms-1">
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="agregar_servicio">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Agregar servicio</h5>
            <button id="close_modal_agregar_servicio" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Tipo de servicio</label>
              <select v-model="nuevo_servicio.tipo" @change="set_tipo_servicio" class="form-control">
                <option value="null">Selecciona un tipo de servicio</option>
                <option value="1">Actividad</option>
                <option value="2">Paquete</option>
                <option value="3">Hotel</option>
                <option value="4">Tiquete</option>
                <option value="5">Asistencia</option>
                <option value="6">Otros</option>
              </select>
            </div>
            <div v-if="nuevo_servicio.tipo == 1">
              <transition appear name="slide-fade">
                <div>
                  <div class="form-group">
                    <label>Servicio</label>
                    <v-select v-model="nuevo_servicio.servicio" @input="get_disponibilidad(1)" @search="get_actividades"
                      label="servicio" :options="actividades" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Pasajeros</label>
                    <div class="row">
                      <div class="col-lg-4">
                        <small>Adultos</small>
                        <input v-model="nuevo_servicio.adultos" v-on:blur="get_disponibilidad(1)" class="form-control"
                          type="number" min="1" />
                      </div>
                      <div class="col-lg-4">
                        <small>Niños</small>
                        <input v-model="nuevo_servicio.ninos" v-on:blur="get_disponibilidad(1)" class="form-control"
                          type="number" min="o" />
                      </div>
                      <div class="col-lg-4">
                        <small>Infantes</small>
                        <input v-model="nuevo_servicio.infantes" v-on:blur="get_disponibilidad(1)" class="form-control"
                          type="number" min="o" />
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fecha de actividad</label>
                    <input v-model="nuevo_servicio.fecha_actividad" v-on:change="get_disponibilidad(1)"
                      class="form-control" type="date" />
                  </div>
                  <div v-if="nuevo_servicio.servicio != null &&
                    nuevo_servicio.servicio.horarios.length != 0
                    " class="form-group">
                    <label>Horarios</label>
                    <v-select v-model="nuevo_servicio.horario" @input="get_disponibilidad(1)" label="desde"
                      :options="nuevo_servicio.servicio.horarios"></v-select>
                  </div>
                  <div v-if="!disponibilidad" class="alert alert-warning fw-bold">
                    <i class="fa fa-exclamation-circle"></i> No hay la
                    disponibilidad suficiente para tomar el servicio en la fecha
                    y hora indicada. Disponibilidad actual:
                    {{ disponibles }} pasajeros.
                  </div>
                  <div class="text-end">
                    <button v-on:click="validate(nuevo_servicio, 1)" :disabled="nuevo_servicio.num_pasajeros <= 0 ||
                      nuevo_servicio.servicio == null ||
                      nuevo_servicio.fecha_actividad == null ||
                      !disponibilidad ||
                      (nuevo_servicio.servicio.horarios.length != 0 &&
                        nuevo_servicio.horario == null) ||
                      loading_agregar
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Agregar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </transition>
            </div>
            <div v-if="nuevo_servicio.tipo == 2 ||
              nuevo_servicio.tipo == 4 ||
              nuevo_servicio.tipo == 5
              ">
              <transition appear name="slide-fade">
                <div>
                  <div v-if="nuevo_servicio.tipo == 4" class="form-group">
                    <label>Tipo</label>
                    <select v-model="nuevo_servicio.tipo_tiquete" class="form-control">
                      <option value="RT">RT</option>
                      <option value="OW">OW</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Origen</label>
                    <v-select v-model="nuevo_servicio.origen" @input="ciudades = []" @search="get_ciudades"
                      label="ciudad_pais" :options="ciudades" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Destino</label>
                    <v-select v-model="nuevo_servicio.destino" @input="ciudades = []" @search="get_ciudades"
                      label="ciudad_pais" :options="ciudades" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Pasajeros</label>
                    <input v-model="nuevo_servicio.num_pasajeros" class="form-control" type="number" min="1" />
                  </div>
                  <div class="form-group">
                    <label>Fecha ida</label>
                    <input v-model="nuevo_servicio.fecha_ida" type="date" class="form-control" />
                  </div>
                  <div v-if="nuevo_servicio.tipo == 2 ||
                    nuevo_servicio.tipo_tiquete == 'RT'
                    " class="form-group">
                    <label>Fecha vuelta</label>
                    <input v-model="nuevo_servicio.fecha_regreso" :min="nuevo_servicio.fecha_ida" type="date"
                      class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Proveedor</label>
                    <v-select v-model="nuevo_servicio.proveedor" @input="proveedores = []" @search="get_proveedores"
                      label="proveedor" :options="proveedores" :clearable="false"></v-select>
                  </div>
                  <div class="form-group">
                    <label>Precio neto</label>
                    <money v-model="nuevo_servicio.valor_neto" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="form-group">
                    <label>Precio venta</label>
                    <money v-model="nuevo_servicio.valor_venta" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="text-end">
                    <button v-on:click="validate(nuevo_servicio, 1)" :disabled="nuevo_servicio.num_pasajeros <= 0 ||
                      nuevo_servicio.origen == null ||
                      nuevo_servicio.destino == null ||
                      nuevo_servicio.fecha_ida == null ||
                      ((nuevo_servicio.tipo == 2 ||
                        nuevo_servicio.tipo_tiquete == 'RT') &&
                        nuevo_servicio.fecha_regreso == null) ||
                      nuevo_servicio.proveedor == null ||
                      loading_agregar
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Agregar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </transition>
            </div>
            <div v-if="nuevo_servicio.tipo == 3">
              <transition appear name="slide-fade">
                <div>
                  <div v-if="!nuevo_servicio.form_hotel" class="form-group">
                    <label>Hotel</label>
                    <v-select v-model="nuevo_servicio.servicio" @search="get_hoteles" label="servicio" :options="hoteles"
                      :clearable="false"></v-select>
                    <div class="text-end">
                      <a @click="nuevo_servicio.form_hotel = true" class="text-primary" href="javascript:void(0)">
                        <i class="fa fa-plus-circle"></i> Crear nuevo hotel</a>
                    </div>
                  </div>
                  <div v-else class="card card-body shadow border mt-2 mb-2">
                    <h5 class="text-success fw-bold">Nuevo hotel</h5>
                    <div class="form-group">
                      <label>Destino</label>
                      <v-select v-model="nuevo_servicio.nuevo_hotel.destino" @search="get_ciudades" label="ciudad_pais"
                        :options="ciudades" :clearable="false"></v-select>
                    </div>
                    <div class="form-group">
                      <label>Hotel</label>
                      <input v-model="nuevo_servicio.nuevo_hotel.nombre" type="text" class="form-control" />
                    </div>
                    <div class="text-end">
                      <a @click="
                        (nuevo_servicio.form_hotel = false),
                        (nuevo_servicio.nuevo_hotel.nombre = null),
                        (nuevo_servicio.nuevo_hoteldestino = null)
                        " class="text-primary" href="javascript:void(0)">
                        <i class="fa fa-times"></i> Cancelar
                      </a>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Pasajeros</label>
                    <input v-model="nuevo_servicio.num_pasajeros" class="form-control" type="number" min="1" />
                  </div>
                  <div class="form-group">
                    <label>Fecha ida</label>
                    <input v-model="nuevo_servicio.fecha_ida" type="date" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Fecha vuelta</label>
                    <input v-model="nuevo_servicio.fecha_regreso" type="date" :min="nuevo_servicio.fecha_ida"
                      class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Precio neto</label>
                    <money v-model="nuevo_servicio.valor_neto" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="form-group">
                    <label>Precio venta</label>
                    <money v-model="nuevo_servicio.valor_venta" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="text-end">
                    <button v-on:click="validate(nuevo_servicio, 1)" :disabled="(nuevo_servicio.servicio == null &&
                      (nuevo_servicio.nuevo_hotel.nombre == null ||
                        nuevo_servicio.nuevo_hotel.destino == null)) ||
                      nuevo_servicio.num_pasajeros <= 0 ||
                      nuevo_servicio.fecha_ida == null ||
                      nuevo_servicio.fecha_regreso == null ||
                      nuevo_servicio.valor_neto <= 0 ||
                      nuevo_servicio.valor_venta <= 0 ||
                      loading_agregar
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Agregar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </transition>
            </div>
            <div v-if="nuevo_servicio.tipo == 6">
              <transition appear name="slide-fade">
                <div>
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea v-model="nuevo_servicio.descripcion" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label>Precio neto</label>
                    <money v-model="nuevo_servicio.valor_neto" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="form-group">
                    <label>Precio venta</label>
                    <money v-model="nuevo_servicio.valor_venta" class="form-control text-end" v-bind="money" required>
                    </money>
                  </div>
                  <div class="text-end">
                    <button v-on:click="validate(nuevo_servicio, 1)" :disabled="nuevo_servicio.descripcion == null || loading_agregar
                      " class="btn btn-success rounded-pill btn-sm">
                      <i class="fa fa-save"></i> Agregar
                    </button>
                    <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                      Cancelar
                    </button>
                  </div>
                </div>
              </transition>
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
            <button id="close_modal_editar_servicio" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="servicio_sel != null">
              <div v-if="servicio_sel.tipo == 1">
                <div class="form-group">
                  <label>Servicio</label>
                  <v-select v-model="servicio_sel.servicio" @search="get_actividades" @input="get_disponibilidad(2)"
                    label="servicio" :options="actividades" :clearable="false"></v-select>
                </div>
                <div class="form-group">
                  <label>Pasajeros</label>
                  <div class="row">
                    <div class="col-lg-4">
                      <small>Adultos</small>
                      <input v-model="servicio_sel.adultos" v-on:blur="get_disponibilidad(1)" class="form-control"
                        type="number" min="1" />
                    </div>
                    <div class="col-lg-4">
                      <small>Niños</small>
                      <input v-model="servicio_sel.ninos" v-on:blur="get_disponibilidad(1)" class="form-control"
                        type="number" min="o" />
                    </div>
                    <div class="col-lg-4">
                      <small>Infantes</small>
                      <input v-model="servicio_sel.infantes" v-on:blur="get_disponibilidad(1)" class="form-control"
                        type="number" min="o" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Fecha de actividad</label>
                  <input v-model="servicio_sel.fecha_actividad" v-on:change="get_disponibilidad(2)" class="form-control"
                    type="date" />
                </div>
                <div v-if="servicio_sel.servicio != null &&
                  servicio_sel.servicio.horarios.length != 0
                  " class="form-group">
                  <label>Horarios</label>
                  <v-select v-model="servicio_sel.horario" @input="get_disponibilidad(2)" label="desde"
                    :options="servicio_sel.servicio.horarios"></v-select>
                </div>
                <div v-if="!disponibilidad" class="alert alert-warning fw-bold">
                  <i class="fa fa-exclamation-circle"></i> No hay la
                  disponibilidad suficiente para tomar el servicio en la fecha y
                  hora indicada. Disponibilidad actual:
                  {{ disponibles }} pasajeros.
                </div>
                <div class="text-end">
                  <button :disabled="!disponibilidad" v-on:click="validate(servicio_sel, 2)"
                    class="btn btn-success rounded-pill btn-sm">
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
                <transition appear name="slide-fade">
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
                      <input v-model="servicio_sel.fecha_ida" type="date" class="form-control" />
                    </div>
                    <div v-if="servicio_sel.tipo == 2 ||
                      servicio_sel.tipo_tiquete == 'RT'
                      " class="form-group">
                      <label>Fecha vuelta</label>
                      <input v-model="servicio_sel.fecha_regreso" type="date" :min="servicio_sel.fecha_ida"
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
                      <button v-on:click="validate(servicio_sel, 2)" :disabled="servicio_sel.num_pasajeros <= 0 ||
                        servicio_sel.origen == null ||
                        servicio_sel.destino == null ||
                        servicio_sel.fecha_ida == null ||
                        ((servicio_sel.tipo == 2 ||
                          servicio_sel.tipo_tiquete == 'RT') &&
                          servicio_sel.fecha_regreso == null) ||
                        servicio_sel.proveedor == null ||
                        loading_agregar
                        " class="btn btn-success rounded-pill btn-sm">
                        <i class="fa fa-save"></i> Guardar
                      </button>
                      <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                        Cancelar
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
              <div v-if="servicio_sel.tipo == 3">
                <transition appear name="slide-fade">
                  <div>
                    <div v-if="!servicio_sel.form_hotel" class="form-group">
                      <label>Hotel</label>
                      <v-select v-model="servicio_sel.servicio" @search="get_hoteles" label="servicio" :options="hoteles"
                        :clearable="false"></v-select>
                      <div class="text-end">
                        <a @click="servicio_sel.form_hotel = true" class="text-primary" href="javascript:void(0)">
                          <i class="fa fa-plus-circle"></i> Crear nuevo hotel</a>
                      </div>
                    </div>
                    <div v-else class="card card-body shadow border mt-2 mb-2">
                      <h5 class="text-success fw-bold">Nuevo hotel</h5>
                      <div class="form-group">
                        <label>Destino</label>
                        <v-select v-model="servicio_sel.nuevo_hotel.destino" @search="get_ciudades" label="ciudad_pais"
                          :options="ciudades" :clearable="false"></v-select>
                      </div>
                      <div class="form-group">
                        <label>Hotel</label>
                        <input v-model="servicio_sel.nuevo_hotel.nombre" type="text" class="form-control" />
                      </div>
                      <div class="text-end">
                        <a @click="
                          (servicio_sel.form_hotel = false),
                          (servicio_sel.nuevo_hotel.nombre = null),
                          (servicio_sel.nuevo_hotel.destino = null)
                          " class="text-primary" href="javascript:void(0)">
                          <i class="fa fa-times"></i> Cancelar
                        </a>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Pasajeros</label>
                      <input v-model="servicio_sel.num_pasajeros" class="form-control" type="number" min="1" />
                    </div>
                    <div class="form-group">
                      <label>Fecha ida</label>
                      <input v-model="servicio_sel.fecha_ida" type="date" class="form-control" />
                    </div>
                    <div class="form-group">
                      <label>Fecha vuelta</label>
                      <input v-model="servicio_sel.fecha_regreso" type="date" :min="servicio_sel.fecha_ida"
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
                      <button v-on:click="validate(servicio_sel, 2)" :disabled="(servicio_sel.servicio == null &&
                        (servicio_sel.nuevo_hotel.nombre == null ||
                          servicio_sel.nuevo_hotel.destino == null)) ||
                        servicio_sel.num_pasajeros <= 0 ||
                        servicio_sel.fecha_ida == null ||
                        servicio_sel.fecha_regreso == null ||
                        loading_agregar
                        " class="btn btn-success rounded-pill btn-sm">
                        <i class="fa fa-save"></i> Guardar
                      </button>
                      <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                        Cancelar
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
              <div v-if="servicio_sel.tipo == 6">
                <transition appear name="slide-fade">
                  <div>
                    <div class="form-group">
                      <label>Descripción</label>
                      <textarea v-model="servicio_sel.descripcion" class="form-control" />
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
                      <button v-on:click="validate(servicio_sel, 1)" :disabled="servicio_sel.descripcion == null || loading_agregar
                        " class="btn btn-success rounded-pill btn-sm">
                        <i class="fa fa-save"></i> Agregar
                      </button>
                      <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                        Cancelar
                      </button>
                    </div>
                  </div>
                </transition>
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
            <button id="close_modal_eliminar_servicio" type="button" class="close" data-bs-dismiss="modal">
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
    <div class="modal fade" id="agregar_descuento">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Agregar descuento</h5>
            <button id="close_modal_descuento" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Descuento</label>
              <money v-model="descuento" v-bind="money" class="form-control text-end" />
            </div>
            <div class="text-end mt-4">
              <button v-on:click="set_descuento" class="btn btn-primary btn-rounded">
                <i class="fa fa-save"></i> Guardar
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
  name: "Nueva_reserva",
  metaInfo: {
    title: "Nueva reserva - Backoffice",
  },
  data() {
    return {
      loading: true,
      loading_agregar: false,
      id_reserva: null,
      identificacion_cliente: null,
      cliente: null,
      cliente_encontrado: false,
      cliente_sel: null,
      crear_cliente: false,
      nuevo_cliente: {
        tipo_identificacion: null,
        nombres: null,
        apellidos: null,
        email: null,
      },
      servicios: [],
      index_sel: null,
      servicio_sel: null,
      actividades: [],
      ciudades: [],
      proveedores: [],
      nuevo_servicio: {
        tipo: null,
        origen: {
          ciudad: "Santiago De Cali",
          ciudad_pais: "Santiago De Cali (Colombia)",
          id_ciudad: "251589",
        },
        destino: null,
        servicio: null,
        num_pasajeros: 1,
        adultos: 1,
        ninos: 0,
        infantes: 0,
        fecha_actividad: null,
        fecha_ida: null,
        fecha_regreso: null,
        horario: null,
        valor_neto: 0,
        valor_venta: 0,
        proveedor: null,
        tipo_tiquete: "RT",
        descripcion: null,
        form_hotel: false,
        nuevo_hotel: {
          destino: null,
          nombre: null,
        },
      },
      subtotal: 0,
      total: 0,
      disponibilidad: true,
      disponibles: 0,
      descuento: 0,
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        suffix: "",
        precision: 0,
        masked: false,
      },
      hoteles: [],
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    let cliente = localStorage.getItem("cliente");
    if (cliente != undefined) {
      this.cliente = JSON.parse(cliente);
    }
    let servicios = localStorage.getItem("servicios");
    if (servicios != undefined) {
      this.servicios = JSON.parse(servicios);
    }

    let descuento = localStorage.getItem("descuento");
    if (descuento != undefined) {
      this.descuento = descuento;
    }
    this.get_total();
  },
  methods: {
    search_cliente: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          identificacion: this.identificacion_cliente,
        },
      };
      axios
        .get(vm.$base_url + "clientes/cliente_identificacion", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.cliente_sel = response.data.data.cliente;
            vm.cliente_encontrado = true;
          } else {
            vm.crear_cliente = true;
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Nueva reserva",
            text: error,
          });
          vm.loading = false;
        });
    },
    set_cliente: function () {
      const vm = this;
      var valida = true;
      if (this.nuevo_cliente.nombres == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Crear reserva",
          text: "Digite los nombres del cliente",
        });
        valida = false;
      }
      if (this.nuevo_cliente.apellidos == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Crear reserva",
          text: "Digite los nombres del cliente",
        });
        valida = false;
      }
      if (this.nuevo_cliente.email == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Crear reserva",
          text: "Digite el correo electrónico del cliente",
        });
        valida = false;
      }
      if (this.nuevo_cliente.telefono == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Crear reserva",
          text: "Digite el teléfono del cliente",
        });
        valida = false;
      }
      if (!valida) {
        return false;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        tipo_identificacion: this.nuevo_cliente.tipo_identificacion,
        identificacion: this.identificacion_cliente,
        nombres: this.nuevo_cliente.nombres,
        apellidos: this.nuevo_cliente.apellidos,
        email: this.nuevo_cliente.email,
        telefono: this.nuevo_cliente.telefono,
      };
      axios
        .post(this.$base_url + "clientes/set_cliente", params, {
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
              title: "Crear reserva",
              text: response.data.message,
            });
            vm.cliente = response.data.data.cliente;
            localStorage.setItem("cliente", JSON.stringify(vm.cliente));
            document.getElementById("close_modal_cliente").click();
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
    select_cliente: function () {
      localStorage.setItem("cliente", JSON.stringify(this.cliente_sel));
      this.cliente = this.cliente_sel;
      document.getElementById("close_modal_cliente").click();
    },
    set_tipo_servicio: function () {
      switch (Number(this.nuevo_servicio.tipo)) {
        case 1:
          this.get_actividades();
          break;
        case 2:
        case 3:
        case 4:
        case 5:
          this.get_proveedores();
          break;

        default:
          break;
      }
      this.nuevo_servicio.proveedor = null;
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
    get_proveedores: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          tipo: this.nuevo_servicio.tipo,
          estado: 1,
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
    validate: function (servicio, go) {
      switch (Number(servicio.tipo)) {
        case 1:
          if (servicio.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }
          if (servicio.servicio == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione un servicio",
            });
            return false;
          }
          if (servicio.fecha_actividad == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de actividad",
            });
            return false;
          }

          break;
        case 2:
        case 4:
        case 5:
          if (servicio.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }
          if (servicio.origen == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de origen",
            });
            return false;
          }
          if (servicio.destino == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la ciudad de destino",
            });
            return false;
          }
          if (servicio.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (
            (servicio.tipo == 2 || servicio.tipo_tiquete == "RT") &&
            servicio.fecha_regreso == null
          ) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }
          if (servicio.proveedor == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione el proveedor del servicio",
            });
            return false;
          }

          break;
        case 3:
          if (
            servicio.servicio == null &&
            (servicio.nuevo_hotel.destino == null ||
              servicio.nuevo_hotel.nombre == null)
          ) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Selecciona o crea el hotel",
            });
            return false;
          }
          if (servicio.num_pasajeros <= 0) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Indique la cantidad de pasajeros",
            });
            return false;
          }

          if (servicio.fecha_ida == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de salida",
            });
            return false;
          }
          if (servicio.fecha_regreso == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Seleccione la fecha de regreso",
            });
            return false;
          }

          break;
        case 6:
          if (servicio.descripcion == null) {
            this.$notify({
              group: "foo",
              type: "error",
              title: "Nueva reserva",
              text: "Digita la descripción del servicio.",
            });
            return false;
          }
          break;
      }
      if (go == 1) {
        this.set_servicio();
      } else {
        this.update_servicio();
      }
    },
    set_servicio: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };

      switch (Number(vm.nuevo_servicio.tipo)) {
        case 1:
          axios
            .get(
              vm.$base_url + "actividades/tarifa_actividad",
              {
                headers: headers,
                params: {
                  id_servicio: this.nuevo_servicio.servicio.id_servicio,
                  fecha_actividad: this.nuevo_servicio.fecha_actividad,
                },
              },
              {
                headers: headers,
              }
            )
            .then((response) => {
              if (response.data.status) {
                this.nuevo_servicio.valor_neto =
                  (this.nuevo_servicio.adultos *
                    response.data.data.tarifa.valor_neto_adultos) + (this.nuevo_servicio.ninos *
                      response.data.data.tarifa.valor_neto_ninos) + (this.nuevo_servicio.infantes *
                        response.data.data.tarifa.valor_neto_infantes);
                this.nuevo_servicio.valor_venta =
                  (this.nuevo_servicio.adultos *
                    response.data.data.tarifa.valor_venta_adultos) + (this.nuevo_servicio.ninos *
                      response.data.data.tarifa.valor_venta_ninos) + (this.nuevo_servicio.infantes *
                        response.data.data.tarifa.valor_venta_infantes);
                this.servicios.push(this.nuevo_servicio);
                localStorage.setItem(
                  "servicios",
                  JSON.stringify(this.servicios)
                );
                this.clear_nuevo_servicio();
                this.get_total();
                document.getElementById("close_modal_agregar_servicio").click();
              } else {
                vm.$notify({
                  group: "foo",
                  type: "error",
                  title: "Nueva reserva",
                  text: response.data.message,
                });
              }
            })
            .catch((error) => {
              vm.$notify({
                group: "foo",
                type: "error",
                title: "Nueva reserva",
                text: error,
              });
            });
          break;
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
          this.servicios.push(this.nuevo_servicio);
          localStorage.setItem("servicios", JSON.stringify(this.servicios));
          this.clear_nuevo_servicio();
          this.get_total();
          document.getElementById("close_modal_agregar_servicio").click();
          break;
      }
    },
    clear_nuevo_servicio: function () {
      this.nuevo_servicio = {
        origen: {
          ciudad: "Santiago De Cali",
          ciudad_pais: "Santiago De Cali (Colombia)",
          id_ciudad: "251589",
        },
        destino: null,
        servicio: null,
        num_pasajeros: 1,
        fecha_actividad: null,
        fecha_ida: null,
        fecha_regreso: null,
        horario: null,
        valor_neto: 0,
        valor_venta: 0,
        proveedor: null,
        tipo_tiquete: "RT",
        descripcion: null,
      };
    },
    update_servicio: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      switch (Number(vm.servicio_sel.tipo)) {
        case 1:
          axios
            .get(
              vm.$base_url + "actividades/tarifa_actividad",
              {
                headers: headers,
                params: {
                  id_servicio: this.servicio_sel.servicio.id_servicio,
                  fecha_actividad: this.servicio_sel.fecha_actividad,
                },
              },
              {
                headers: headers,
              }
            )
            .then((response) => {
              if (response.data.status) {
                this.servicio_sel.valor_neto =
                  (this.servicio_sel.adultos *
                    response.data.data.tarifa.valor_neto_adultos) + (this.servicio_sel.ninos *
                      response.data.data.tarifa.valor_neto_ninos);
                this.servicio_sel.valor_venta =
                  (this.servicio_sel.adultos *
                    response.data.data.tarifa.valor_venta_adultos) + (this.servicio_sel.ninos *
                      response.data.data.tarifa.valor_venta_ninos) + (this.servicio_sel.infantes *
                        response.data.data.tarifa.valor_venta_infantes);
                this.servicios[this.index_sel] = this.servicio_sel;
                localStorage.setItem(
                  "servicios",
                  JSON.stringify(this.servicios)
                );
                this.servicio_sel = null;
                this.index_sel = null;
                this.get_total();
                document.getElementById("close_modal_editar_servicio").click();
              } else {
                vm.$notify({
                  group: "foo",
                  type: "error",
                  title: "Nueva reserva",
                  text: response.data.message,
                });
              }
            })
            .catch((error) => {
              vm.$notify({
                group: "foo",
                type: "error",
                title: "Nueva reserva",
                text: error,
              });
            });
          break;
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
          this.servicios[this.index_sel] = this.servicio_sel;
          localStorage.setItem("servicios", JSON.stringify(this.servicios));
          this.servicio_sel = null;
          this.index_sel = null;
          this.get_total();
          document.getElementById("close_modal_editar_servicio").click();
          break;
      }
    },
    delete_servicio: function () {
      this.servicios.splice(this.index_sel, 1);
      localStorage.setItem("servicios", JSON.stringify(this.servicios));
      this.servicio_sel = null;
      this.index_sel = null;
      this.get_total();
      document.getElementById("close_modal_eliminar_servicio").click();
    },
    get_total: function () {
      let total = 0;
      if (this.servicios.length != 0) {
        for (let servicio of this.servicios) {
          total = parseFloat(total) + parseFloat(servicio.valor_venta);
        }
      }
      this.subtotal = total;
      this.total = parseFloat(this.subtotal) - parseFloat(this.descuento);
    },
    set_reserva: function () {
      const vm = this;

      if (vm.cliente == null) {
        vm.$notify({
          group: "foo",
          type: "warning",
          title: "Atención",
          text: "Agrega el cliente al que corresponde la reserva.",
        });
        return false;
      }

      let servicios = [];
      if (this.servicios.length != 0) {
        for (let servicio of this.servicios) {
          switch (Number(servicio.tipo)) {
            case 1:
              var id_horario = null;
              if (servicio.horario != null) {
                id_horario = servicio.horario.id_horario;
              }
              servicios.push({
                tipo: 1,
                id_servicio: servicio.servicio.id_servicio,
                adultos: servicio.adultos,
                ninos: servicio.ninos,
                infantes: servicio.infantes,
                fecha_actividad: servicio.fecha_actividad,
                id_horario: id_horario,
              });
              break;
            case 2:
              servicios.push({
                tipo: 2,
                num_pasajeros: servicio.num_pasajeros,
                origen: servicio.origen.id_ciudad,
                destino: servicio.destino.id_ciudad,
                fecha_ida: servicio.fecha_ida,
                fecha_regreso: servicio.fecha_regreso,
                id_proveedor: servicio.proveedor.id_proveedor,
                valor_neto: servicio.valor_neto,
                valor_venta: servicio.valor_venta,
              });
              break;
            case 3:
              var id_servicio = null;
              if (servicio.servicio != null) {
                id_servicio = servicio.servicio.id_servicio;
              }

              var nuevo_hotel = false;
              var hotel = null;

              if (servicio.nuevo_hotel.destino != null) {
                nuevo_hotel = true;
                hotel = {
                  id_ciudad: servicio.nuevo_hotel.destino.id_ciudad,
                  nombre: servicio.nuevo_hotel.nombre,
                };
              }

              servicios.push({
                tipo: 3,
                num_pasajeros: servicio.num_pasajeros,
                fecha_ida: servicio.fecha_ida,
                fecha_regreso: servicio.fecha_regreso,
                id_servicio: id_servicio,
                nuevo_hotel: nuevo_hotel,
                hotel: hotel,
                valor_neto: servicio.valor_neto,
                valor_venta: servicio.valor_venta,
              });
              break;
            case 4:
              servicios.push({
                tipo: 4,
                tipo_tiquete: servicio.tipo_tiquete,
                num_pasajeros: servicio.num_pasajeros,
                origen: servicio.origen.id_ciudad,
                destino: servicio.destino.id_ciudad,
                fecha_ida: servicio.fecha_ida,
                fecha_regreso: servicio.fecha_regreso,
                id_proveedor: servicio.proveedor.id_proveedor,
                valor_neto: servicio.valor_neto,
                valor_venta: servicio.valor_venta,
              });
              break;
            case 5:
              servicios.push({
                tipo: 5,
                num_pasajeros: servicio.num_pasajeros,
                origen: servicio.origen.id_ciudad,
                destino: servicio.destino.id_ciudad,
                fecha_ida: servicio.fecha_ida,
                fecha_regreso: servicio.fecha_regreso,
                id_proveedor: servicio.proveedor.id_proveedor,
                valor_neto: servicio.valor_neto,
                valor_venta: servicio.valor_venta,
              });
              break;
            case 6:
              servicios.push({
                tipo: 6,
                descripcion: servicio.descripcion,
                valor_neto: servicio.valor_neto,
                valor_venta: servicio.valor_venta,
              });
              break;
          }
        }
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_cliente: this.cliente.id_cliente,
        servicios: servicios,
        descuento: this.descuento,
      };
      axios
        .post(this.$base_url + "reservas/set_reserva", params, {
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
              title: "Nueva reserva",
              text: response.data.message,
            });
            vm.$router.push("/reserva/" + response.data.data.id_reserva);
            localStorage.removeItem("descuento");
            localStorage.removeItem("cliente");
            localStorage.removeItem("servicios");
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
    get_disponibilidad: function () {
      this.disponibilidad = true;
      this.loading_agregar = false;
    },
    set_descuento: function () {
      if (this.descuento > this.total) {
        this.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "El descuento no puede superar el total de la reserva.",
        });
        return false;
      } else {
        localStorage.setItem("descuento", this.descuento);
        this.get_total();
        document.getElementById("close_modal_descuento").click();
      }
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
  },
  mounted: function () {
    this.loading = false;
  },
};
</script>
