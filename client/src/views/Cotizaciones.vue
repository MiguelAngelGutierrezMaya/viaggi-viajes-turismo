<template>
  <div id="cotizaciones">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Cotizaciones</h4>
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
            <input v-model="search" v-on:keyup="get_cotizaciones" class="form-control" type="text"
              placeholder="Buscar cotizacion..." />
          </div>
          <div v-if="filtrado" class="card card-body border mt-3 mb-3">
            <span v-if="filtros.estado != null">
              <span>Estado: </span><strong v-if="filtros.estado == 0">Pendiente</strong>
              <strong v-if="filtros.estado == 1">Gestionada</strong>
            </span>
            <div class="text-end">
              <a v-on:click="quitar_filtros" href="javascript:void(0)" class="text-primary">
                <i class="fa-solid fa-filter-circle-xmark"></i> Quitar filtros
              </a>
            </div>
          </div>
          <div v-if="cotizaciones.length != 0">
            <div class="table-responsive mt-3">
              <table class="table table-hover table-condensed my_table_2">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 5%">#</th>
                    <th style="width: 20%">Cliente</th>
                    <th style="width: 15%">Servicio</th>
                    <th style="width: 15%">Datos</th>
                    <th class="text-center" style="width: 5%">Estado</th>
                    <th class="text-center" style="width: 10%">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(cotizacion, index) in cotizaciones" v-bind:key="index">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td class="text-uppercase">
                      <div class="fw-bold">
                        {{ cotizacion.nombre }}
                      </div>
                      <div>
                        <small>
                          <a v-bind:href="'https://api.whatsapp.com/send?phone=' +
                            cotizacion.cod_pais +
                            cotizacion.telefono
                            " target="_blank">{{ cotizacion.cod_pais }}
                            {{ cotizacion.telefono }}</a>
                          <br />
                          {{ cotizacion.email }}
                        </small>
                      </div>
                    </td>
                    <td>
                      <span v-if="cotizacion.servicio == 1">Actividad</span>
                      <span v-if="cotizacion.servicio == 2">Paquete</span>
                      <span v-if="cotizacion.servicio == 3">Hotel</span>
                      <span v-if="cotizacion.servicio == 4">Vuelos</span>
                      <span v-if="cotizacion.servicio == 5">Asistencia</span>
                    </td>
                    <td>
                      <div v-if="cotizacion.servicio == 2 || cotizacion.servicio == 4
                        ">
                        Origen:<span v-if="cotizacion.origen != null"> {{ cotizacion.origen }} </span> <span> {{
                          cotizacion.origen_busqueda }} </span>
                      </div>
                      <div>Destino: <span v-if="cotizacion.destino != null"> {{ cotizacion.destino }} </span> <span> {{
                        cotizacion.destino_busqueda }} </span> </div>
                      <div>Fecha in: {{ cotizacion.fecha_ida }}</div>
                      <div>Fecha out: {{ cotizacion.fecha_regreso }}</div>
                    </td>
                    <td class="text-center fw-bolder">
                      <span v-if="cotizacion.estado == 0" class="text-warning">Pendiente</span>
                      <span v-if="cotizacion.estado == 1" class="text-success">Gestionada</span>
                      <span v-if="cotizacion.estado == 2" class="text-danger">Anulada</span>
                    </td>
                    <td class="text-center">
                      <a @click="cotizacion_sel = cotizacion" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#estado" class="btn btn-primary btn-sm rounded-pill">
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="text-center mt-3 mb-2" v-html="paginate"></div>
          </div>
          <div v-else>
            <div class="text-muted mt-4">
              <i class="fa fa-info-circle"></i> No se encontraron cotizaciones.
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="estado">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Detalle de cotización</h5>
            <button id="close_modal" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Cliente</label>
              <h6>{{ cotizacion_sel.nombre }}</h6>
              <h6>
                <a v-bind:href="'https://api.whatsapp.com/send?phone=' +
                  cotizacion_sel.cod_pais +
                  cotizacion_sel.telefono
                  " target="_blank">{{ cotizacion_sel.cod_pais }}
                  {{ cotizacion_sel.telefono }}</a>
              </h6>
            </div>
            <div class="form-group">
              <label>Servicio</label>
              <h6 v-if="cotizacion_sel.servicio == 2">Paquete</h6>
              <h6 v-if="cotizacion_sel.servicio == 3">Hotel</h6>
              <h6 v-if="cotizacion_sel.servicio == 4">Vuelos</h6>
            </div>
            <div class="form-group">
              <label>Datos</label>
              <h6 v-if="cotizacion_sel.servicio == 2 || cotizacion_sel.servicio == 4
                ">
                Origen: {{ cotizacion_sel.origen }}
              </h6>
              <h6>Destino: {{ cotizacion_sel.destino }}</h6>
              <h6>Fecha in: {{ cotizacion_sel.fecha_ida }}</h6>
              <h6>Fecha out: {{ cotizacion_sel.fecha_regreso }}</h6>
            </div>
            <div class="form-group">
              <label>Estado</label>
              <select v-model="cotizacion_sel.estado" class="form-control">
                <option value="0">Pendiente</option>
                <option value="1">Gestionada</option>
                <option value="2">Anulada</option>
              </select>
            </div>
            <div class="text-end">
              <button @click="update_estado" class="btn btn-success btn-sm rounded-pill">
                <i class="fa fa-save"></i> Guardar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="filtrar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Fitrar cotizaciones</h5>
            <button id="close_modal" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div>
              <div class="form-group">
                <label>Estado</label>
                <select v-model="filtros.estado" class="form-control">
                  <option value="0">Pendiente</option>
                  <option value="1">Gestionada</option>
                  <option value="1">Anulada</option>
                </select>
              </div>
              <div class="form-group">
                <label>Servicio</label>
              </div>

              <div class="text-end">
                <button v-on:click="get_cotizaciones" class="btn btn-primary rounded-pill btn-sm">
                  <i class="fa fa-filter"></i> Filtrar
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
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Cotizaciones",
  metaInfo: {
    title: "Cotizaciones - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      cotizaciones: [],
      paginate: null,
      index_sel: null,
      filtros: {
        estado: null,
        servicio: null,
        desde: null,
        hasta: null,
      },
      filtrado: false,
      cotizacion_sel: [],
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_cotizaciones();
  },
  methods: {
    get_cotizaciones: function () {
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/cotizaciones/1");
        }
      }
      var id_servicio = null;
      if (this.filtros.servicio != null) {
        id_servicio = this.filtros.servicio.id_servicio;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          page: this.page,
          search: search,
          estado: this.filtros.estado,
          id_servicio: id_servicio,
          desde: this.filtros.desde,
          hasta: this.filtros.hasta,
        },
      };
      axios
        .get(vm.$base_url + "reservas/cotizaciones", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.cotizaciones = response.data.data;
            vm.get_paginate(response.data.total_pages);
            if (
              this.filtros.estado != null ||
              this.filtros.desde != null ||
              this.filtros.hasta != null
            ) {
              this.filtrado = true;
            }
            document.getElementById("close_modal").click();
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
          "<li class='paginate_button page-item  previous'><a href='#/cotizaciones/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/cotizaciones/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/cotizaciones/1'>1</a></li>";
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
            "<li class='paginate_button page-item active'><a href='#/cotizaciones/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/cotizaciones/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/cotizaciones/" +
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
          "<li class='paginate_button page-item'><a  href='#/cotizaciones/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/cotizaciones/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
    },
    quitar_filtros: function () {
      this.filtros = {
        estado: null,
        servicio: null,
        desde: null,
        hasta: null,
      };
      this.filtrado = false;
      this.loading = true;
      this.get_cotizaciones();
    },
    update_estado: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_cotizacion: this.cotizacion_sel.id_cotizacion,
        estado: this.cotizacion_sel.estado,
      };
      axios
        .post(this.$base_url + "clientes/update_cotizacion", params, {
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

            vm.get_cotizaciones();
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
  },
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_cotizaciones();
    },
  },
};
</script>
