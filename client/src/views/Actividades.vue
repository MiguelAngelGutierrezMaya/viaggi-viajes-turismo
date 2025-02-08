<template>
  <div id="actividades">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Actividades</h4>
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
            <div v-if="permiso" class="text-end">
              <button data-bs-toggle="modal" data-bs-target="#nueva_actividad"
                class="btn btn-success btn-sm btn-rounded btn-icon-text">
                <i class="fa fa-plus-circle"></i> Nueva actividad
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_actividades" type="text" class="form-control"
                placeholder="Buscar actividad" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="actividades.length != 0" class="mt-3">
              <div class="card card-body card-reservas border shadow mb-3"
                v-for="(actividad, index_actividad) in actividades" v-bind:key="index_actividad">
                <div class="text-end">
                  <label v-if="actividad.estado_servicio == 1" class="text-success">Activa</label>
                  <label v-if="actividad.estado_servicio == 0" class="text-danger">Inactiva</label>
                </div>
                <div class="form-group">
                  <small>Actividad</small>
                  <h6>
                    <router-link v-bind:to="'/actividad/' + actividad.id_servicio">
                      <strong>{{ actividad.servicio }}</strong></router-link>
                  </h6>
                  <div>
                    <span class="badge bg-secondary" v-for="(
                        tipo_actividad, index_tipo
                      ) in actividad.tipos_actividad" v-bind:key="index_tipo">
                      {{ tipo_actividad.tipo_actividad }}
                    </span>
                  </div>
                </div>
                <div class="text-end">
                  <router-link v-bind:to="'/actividad/' + actividad.id_servicio"
                    class="btn btn-primary btn-rounded btn-xs" title="Detalles">
                    <i class="fa fa-list"></i>
                  </router-link>
                  <button v-on:click="
                    (actividad_sel = actividad), get_disponibilidad()
                    " data-bs-toggle="modal" data-bs-target="#disponibilidad"
                    class="btn btn-info btn-rounded btn-xs ms-1" title="Disponibilidad">
                    <i class="fa fa-calendar"></i>
                  </button>
                  <button v-if="actividad.estado_servicio == 0 && permiso"
                    v-on:click="update_estado_actividad(index_actividad)" class="btn btn-warning btn-rounded btn-xs ms-1"
                    title="Activar">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button v-if="actividad.estado_servicio == 1 && permiso"
                    v-on:click="update_estado_actividad(index_actividad)"
                    class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Desactivar">
                    <i class="fa fa-eye-slash"></i>
                  </button>
                  <button v-if="actividad.destacada == 1 && permiso"
                    v-on:click="update_destacada_actividad(index_actividad)"
                    class="btn btn-warning btn-rounded btn-xs ms-1" title="No destacar">
                    <i class="fa fa-star"></i>
                  </button>
                  <button v-if="actividad.destacada == 0 && permiso"
                    v-on:click="update_destacada_actividad(index_actividad)"
                    class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Destacar">
                    <i class="fa fa-star"></i>
                  </button>
                  <button v-if="permiso" v-on:click="update_oferta(index_actividad)" class="btn  btn-rounded btn-xs ms-1"
                    :class="{ 'btn-warning': actividad.oferta == 1, 'btn-outline-warning': actividad.oferta == 0 }"
                    title="Oferta">
                    <i class="fa fa-tag"></i>
                  </button>
                  <button v-if="permiso" v-on:click="delete_actividad(index_actividad)"
                    class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>
              <div class="text-center mt-3 mb-2" v-html="paginate"></div>
            </div>
            <div v-else>
              <div class="text-muted mt-4">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                actividades.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nueva_actividad">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Nueva actividad</h5>
            <button id="close_modal_set" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Destino</label>
              <v-select v-model="nueva.destino" @search="get_ciudades" label="ciudad_pais" :options="ciudades"
                :clearable="false"></v-select>
            </div>
            <div class="form-group">
              <label>Nombre de la actividad</label>
              <input v-model="nueva.servicio" type="text" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="set_actividad" class="btn btn-success btn-rounded btn-sm btn-icon-text">
                <i class="fa fa-save"></i> Guardar
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="disponibilidad">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Disponibilidad</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="actividad_sel != null" class="modal-body">
            <small> Actividad</small>
            <h6 class="fw-bold text-primary">
              {{ actividad_sel.servicio }}
            </h6>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li v-if="horarios.length != 0" class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                  role="tab" aria-controls="home" aria-selected="true">
                  Horarios
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" :class="{ active: horarios.length == 0 }" id="profile-tab" data-bs-toggle="tab"
                  data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                  Fechas
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="dias-tab" data-bs-toggle="tab" data-bs-target="#dias" type="button"
                  role="tab" aria-controls="dias" aria-selected="false">
                  Días
                </button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div v-if="horarios.length != 0" class="tab-pane fade show active" id="home" role="tabpanel"
                aria-labelledby="home-tab">
                <div v-if="loading_horarios" class="text-center">
                  <div class="loadingio-spinner-ripple-6armq16mc04">
                    <div class="ldio-2tzb8vdk7pu">
                      <div></div>
                      <div></div>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <div>
                    <small> Fecha</small>
                    <input v-on:change="get_disponibilidad()" v-model="fecha_disponibilidad" type="date"
                      class="form-control" />
                    <hr />
                    <div class="table-responsive mt-2">
                      <table class="table table-hover table-condensed table-bordered my_table_1">
                        <thead>
                          <tr>
                            <th>Horario</th>
                            <th class="text-center">Disponibilidad</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(horario, index_horario) in horarios" v-bind:key="index_horario">
                            <td>{{ horario.desde }}</td>
                            <td class="text-center">
                              <input :disabled="!permiso" v-model="horario.estado" class="form-check-input"
                                type="checkbox" />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <div v-if="permiso" class="text-end mt-3">
                        <button v-on:click="update_disponibilidad"
                          class="btn btn-success btn-rounded btn-sm btn-icon-text">
                          <i class="fa fa-save"></i> Guardar
                        </button>
                        <button data-bs-dismiss="modal"
                          class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                          Cerrar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" :class="{ 'show active': horarios.length == 0 }" id="profile" role="tabpanel"
                aria-labelledby="profile-tab">
                <div v-if="loading_horarios" class="text-center">
                  <div class="loadingio-spinner-ripple-6armq16mc04">
                    <div class="ldio-2tzb8vdk7pu">
                      <div></div>
                      <div></div>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <div v-if="actividad_sel != null">
                    <small> Actividad</small>
                    <h6 class="fw-bold text-primary">
                      {{ actividad_sel.servicio }}
                    </h6>
                    <div class="table-responsive mt-2">
                      <table v-if="inoperaciones != null && inoperaciones.length != 0
                        " class="table table-hover table-condensed table-bordered my_table_1">
                        <thead>
                          <tr>
                            <th class="text-center">Desde</th>
                            <th class="text-center">Hasta</th>
                            <th v-if="permiso" class="text-center">Opciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(fecha, index_fecha) in inoperaciones" v-bind:key="index_fecha">
                            <td class="text-center">{{ fecha.desde }}</td>
                            <td class="text-center">{{ fecha.hasta }}</td>
                            <td v-if="permiso" class="text-center">
                              <a v-on:click="
                                delete_inoperacion(fecha.id_inoperacion)
                                " href="javascript:void(0)" class="text-danger">
                                <i class="fa fa-trash"></i>
                              </a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <div v-else class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No se han agregado
                        inoperaciones.
                      </div>
                      <div v-if="permiso" class="text-end mb-0">
                        <a href="javascript:void(0)" v-on:click="nueva_inoperacion = true">
                          <i class="fa fa-plus-circle"></i> Agregar
                        </a>
                      </div>
                      <div v-if="nueva_inoperacion" class="card card-body border mt-3 mb-3">
                        <div class="form-group">
                          <label>Desde</label>
                          <input v-model="inoperacion.desde" class="form-control" type="date" />
                        </div>
                        <div class="form-group">
                          <label>Hasta</label>
                          <input v-model="inoperacion.hasta" class="form-control" type="date" />
                        </div>
                        <div class="text-end mt-3">
                          <button v-on:click="set_inoperacion"
                            class="btn btn-success btn-rounded btn-sm btn-icon-text ms-1">
                            <i class="fa fa-save"></i> Guardar
                          </button>
                          <button v-on:click="nueva_inoperacion = false"
                            class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                            Cancelar
                          </button>
                        </div>
                      </div>
                      <div class="text-end mt-3">
                        <button data-bs-dismiss="modal"
                          class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                          Cerrar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="dias" role="tabpanel" aria-labelledby="dias-tab">
                <div>
                  <div>
                    <div class="text-muted  mb-3">
                      <i class="fa fa-info-circle"></i> Los días marcados corresponden a <strong>NO operación.</strong>
                    </div>
                    <div class="table-responsive mt-2">
                      <table class="table table-bordered table-hover">
                        <tbody>
                          <tr>
                            <th>Lunes</th>
                            <td class="text-center">
                              <input :disabled="!permiso" v-model="actividad_sel.dias_inoperacion[0].estado"
                                class="form-check-input" type="checkbox" />
                            </td>
                          </tr>
                          <tr>
                            <th>Martes</th>
                            <td class="text-center">
                              <input :disabled="!permiso" v-model="actividad_sel.dias_inoperacion[1].estado"
                                class="form-check-input" type="checkbox" />
                            </td>
                          </tr>
                          <tr>
                            <th>Miércoles</th>
                            <td class="text-center">
                              <input :disabled="!permiso" v-model="actividad_sel.dias_inoperacion[2].estado"
                                class="form-check-input" type="checkbox" />
                            </td>
                          </tr>
                          <tr>
                            <th>Jueves</th>
                            <td class="text-center">
                              <input :disabled="!permiso" v-model="actividad_sel.dias_inoperacion[3].estado"
                                class="form-check-input" type="checkbox" />
                            </td>
                          </tr>
                          <tr>
                            <th>Viernes</th>
                            <td class="text-center"> <input :disabled="!permiso"
                                v-model="actividad_sel.dias_inoperacion[4].estado" class="form-check-input"
                                type="checkbox" /></td>
                          </tr>
                          <tr>
                            <th>Sábado</th>
                            <td class="text-center"> <input :disabled="!permiso"
                                v-model="actividad_sel.dias_inoperacion[5].estado" class="form-check-input"
                                type="checkbox" /></td>
                          </tr>
                          <tr>
                            <th>Domingo</th>
                            <td class="text-center"> <input :disabled="!permiso"
                                v-model="actividad_sel.dias_inoperacion[6].estado" class="form-check-input"
                                type="checkbox" /></td>
                          </tr>
                        </tbody>
                      </table>
                      <div v-if="permiso" class="text-end mt-3 mb-0">
                        <button @click="update_dias_disponibilidad()" :disabled="loading"
                          class="btn btn-success btn-sm rounded-pill">
                          <i class="fa fa-save"></i> Guardar
                        </button>
                      </div>
                      <div class="text-end mt-3">
                        <button data-bs-dismiss="modal"
                          class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                          Cerrar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
  name: "Actividades",
  metaInfo: {
    title: "Actividades - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      actividades: [],
      ciudades: [],
      paginate: null,
      nueva: {
        destino: null,
        servicio: null,
      },
      actividad_sel: null,
      loading_horarios: false,
      horarios: [],
      fecha_disponibilidad: null,
      inoperaciones: [],
      nueva_inoperacion: false,
      inoperacion: {
        desde: null,
        hasta: null,
      },
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_actividades();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_actividades: function () {
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/actividades/1");
        }
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          page: this.page,
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "servicios/actividades", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.actividades = response.data.data.actividades;
            vm.get_paginate(response.data.data.total_pages);
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
    get_paginate: function (tpages) {
      var page = this.page;

      if (page === undefined || page <= 0) {
        this.page = 1;
        page = this.page;
      }

      var adjacents = 4;

      var paginate = null;

      paginate = '<ul class="pagination justify-content-center text-center">';
      // previous label
      if (page == 1) {
        paginate +=
          "<li class='paginate_button page-item previous disabled'><a class='page-link disabled'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else if (page == 2) {
        paginate +=
          "<li class='paginate_button page-item  previous'><a href='#/actividades/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/actividades/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/actividades/1'>1</a></li>";
      }
      // interval
      if (page > parseFloat(adjacents) + 2) {
        paginate += "<li class='paginate_button page-item'> <a> ... </a> </li>";
      }
      // pages
      var pmin =
        page > adjacents ? parseFloat(page) - parseFloat(adjacents) : 1;
      var pmax =
        page < parseFloat(tpages) - parseFloat(adjacents)
          ? parseFloat(page) + parseFloat(adjacents)
          : tpages;

      for (var i = pmin; i <= pmax; i++) {
        if (i == page) {
          paginate +=
            "<li class='paginate_button page-item active'><a href='#/actividades/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/actividades/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/actividades/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        }
      }
      // interval
      if (page < parseFloat(tpages) - parseFloat(adjacents) - 1) {
        paginate += "<li class='paginate_button page-item'> <a> ... </a> </li>";
      }
      // last
      if (page < parseFloat(tpages) - parseFloat(adjacents)) {
        paginate +=
          "<li class='paginate_button page-item'><a  href='#/actividades/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/actividades/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
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
    get_destinos: function (search) {
      if (search.length <= 3) {
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
        .get(vm.$base_url + "servicios/destino_search", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.destinos = response.data.data.destinos;
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
    set_actividad: function () {
      const vm = this;
      var valida = true;

      if (
        this.nueva.servicio == null ||
        this.nueva.servicio.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Digita el nombre de la actividad.",
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
        destino: this.nueva.destino,
        servicio: this.nueva.servicio,
      };
      axios
        .post(this.$base_url + "servicios/set_actividad", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
          vm.nueva = {
            destino: null,
            servicio: null,
          };
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_estado_actividad: function (index_actividad) {
      var estado = 1;
      if (this.actividades[index_actividad].estado_servicio == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.actividades[index_actividad].id_servicio,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_estado_servicio", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_destacada_actividad: function (index_actividad) {
      var estado = 1;
      if (this.actividades[index_actividad].destacada == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: this.actividades[index_actividad].id_actividad,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_destacada_actividad", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_oferta: function (index_actividad) {
      var estado = 1;
      if (this.actividades[index_actividad].oferta == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.actividades[index_actividad].id_servicio,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_oferta", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_actividad: function (index_actividad) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.actividades[index_actividad].id_servicio,
      };
      axios
        .post(this.$base_url + "servicios/delete_servicio", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    get_disponibilidad: function () {
      const vm = this;
      vm.loading_horarios = true;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          fecha: vm.fecha_disponibilidad,
        },
      };
      axios
        .get(
          vm.$base_url +
          "actividades/disponibilidad/" +
          vm.actividad_sel.id_actividad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.fecha_disponibilidad = response.data.data.fecha;
            vm.horarios = response.data.data.horarios;
            vm.inoperaciones = response.data.data.inoperaciones;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.loading_horarios = false;
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
    update_disponibilidad: function () {
      const vm = this;
      let horarios = [];
      for (let horario of vm.horarios) {
        horarios.push({
          id_horario: horario.id_horario,
          estado: horario.estado,
        });
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        fecha: vm.fecha_disponibilidad,
        horarios: horarios,
      };
      axios
        .post(this.$base_url + "actividades/update_disponibilidad", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_disponibilidad();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    set_inoperacion: function () {
      const vm = this;
      var valida = true;
      if (this.inoperacion.desde == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Selecciona la fecha de inicio",
        });
        valida = false;
      }
      if (this.inoperacion.hasta == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Selecciona la fecha de fin",
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
        id_actividad: this.actividad_sel.id_actividad,
        desde: this.inoperacion.desde,
        hasta: this.inoperacion.hasta,
      };
      axios
        .post(this.$base_url + "actividades/set_inoperacion", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_disponibilidad();
          vm.inoperacion = {
            desde: null,
            hasta: null,
          };
          vm.nueva_inoperacion = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_inoperacion: function (id_inoperacion) {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_inoperacion: id_inoperacion,
      };
      axios
        .post(this.$base_url + "actividades/delete_inoperacion", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_disponibilidad();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_dias_disponibilidad: function () {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: vm.actividad_sel.id_actividad,
        dias: vm.actividad_sel.dias_inoperacion
      };
      axios
        .post(this.$base_url + "actividades/update_dias_disponibilidad", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividades",
              text: response.data.message,
            });
          }
          vm.get_actividades();
        })
        .catch((error) => {
          vm.error_manager(error);
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
  },
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_actividades();
    },
  },
};
</script>
