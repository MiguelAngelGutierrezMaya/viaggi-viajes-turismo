<template>
  <div id="reservas">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Reservas</h4>
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
          <div class="text-end mb-4">
            <button v-on:click="get_actividades()" data-bs-toggle="modal" data-bs-target="#filtrar"
              class="btn btn-primary btn-sm btn-rounded">
              <i class="fa fa-filter"></i> Filtrar
            </button>
          </div>
          <div>
            <input v-model="search" v-on:keyup="get_reservas" class="form-control" type="text"
              placeholder="Buscar reserva..." />
          </div>
          <div v-if="filtrado" class="card card-body border mt-3 mb-3">
            <span v-if="filtros.estado != null">
              <span>Estado: </span><strong v-if="filtros.estado == 0">Pendiente</strong>
              <strong v-if="filtros.estado == 1">Aprobada</strong>
            </span>
            <span v-if="filtros.servicio != null">
              <span>Servicio: </span><strong>{{ filtros.servicio.servicio }}</strong>
            </span>
            <span v-if="filtros.desde != null">
              <span>Desde: </span><strong>{{ filtros.desde }}</strong>
            </span>
            <span v-if="filtros.hasta != null">
              <span>Hasta: </span><strong>{{ filtros.hasta }}</strong>
            </span>
            <div class="text-end">
              <a v-on:click="quitar_filtros" href="javascript:void(0)" class="text-primary">
                <i class="fa-solid fa-filter-circle-xmark"></i> Quitar filtros
              </a>
            </div>
          </div>
          <div v-if="reservas.length != 0">
            <div class="card card-body card-reservas shadow border mt-3" v-for="(reserva, index) in reservas"
              v-bind:key="index">
              <small class="text-muted">{{ reserva.fecha_reg }}</small>
              <h6 class="text-primary">{{ reserva.cod_reserva }}</h6>
              <div class="row">
                <div class="col-lg-3">
                  <label>Cliente:</label>
                  <div class="fw-bold mb-0">
                    {{ reserva.cliente.nombres }}
                    {{ reserva.cliente.apellidos }}
                  </div>
                  <div class="mt-0">
                    <small>
                      {{ reserva.cliente.telefono }}<br />
                      {{ reserva.cliente.email }}
                    </small>
                  </div>
                </div>
                <div class="col-lg-3">
                  <label>Servicios:</label>
                  <div v-for="(servicio, index_servicio) in reserva.servicios" v-bind:key="index_servicio">
                    <i class="fa fa-check-circle me-1"></i>
                    <span v-if="servicio.tipo == 1 || servicio.tipo == 3">{{
                      servicio.servicio
                    }}</span>
                    <span v-if="servicio.tipo == 2">Paquete</span>
                    <span v-if="servicio.tipo == 4">Tiquete <span>{{ servicio.tipo_tiquete }}</span></span>
                    <span v-if="servicio.tipo == 5">Asistencia médica</span>
                    <span v-if="servicio.tipo == 6">Otros servicios</span>
                  </div>
                </div>
                <div class="col-lg-2 text-end">
                  <label>Valor:</label>
                  <div class="fw-bold">{{ reserva.total | currency }}</div>
                </div>
                <div class="col-lg-2 text-center fw-bold">
                  <span v-if="reserva.estado == 0" class="text-warning">Pendiente</span>
                  <span v-if="reserva.estado == 1" class="text-success">Aprobada</span>
                  <span v-if="reserva.estado == 3" class="text-danger">Anulada</span>
                </div>
              </div>
              <div class="text-end">
                <router-link v-bind:to="'/reserva/' + reserva.id_reserva" class="btn btn-primary btn-sm btn-rounded">
                  <i class="fa fa-list"></i>
                </router-link>
              </div>
            </div>

            <div class="text-center mt-3 mb-2" v-html="paginate"></div>
          </div>
          <div v-else>
            <div class="text-muted mt-4">
              <i class="fa fa-info-circle"></i> No se encontraron reservas.
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="filtrar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Fitrar reservas</h5>
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
                  <option value="1">Aprobada</option>
                </select>
              </div>
              <div class="form-group">
                <label>Servicio</label>
                <v-select v-model="filtros.servicio" @search="get_actividades" label="servicio" :options="actividades"
                  :clearable="false"></v-select>
              </div>

              <div class="form-group">
                <label>Desde</label>
                <input v-model="filtros.desde" class="form-control" type="date" />
              </div>
              <div class="form-group">
                <label>Hasta</label>
                <input v-model="filtros.hasta" class="form-control" type="date" />
              </div>
              <div class="text-end">
                <button v-on:click="get_reservas" class="btn btn-primary rounded-pill btn-sm">
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
  name: "Reservas",
  metaInfo: {
    title: "Reservas - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      reservas: [],
      paginate: null,
      index_sel: null,
      filtros: {
        estado: null,
        servicio: null,
        desde: null,
        hasta: null,
      },
      filtrado: false,
      actividades: [],
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_reservas();
  },
  methods: {
    get_reservas: function () {
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/reservas/1");
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
        .get(vm.$base_url + "reservas/reservas", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.reservas = response.data.data;
            vm.get_paginate(response.data.total_pages);
            if (
              this.filtros.estado != null ||
              this.filtros.id_servicio != null ||
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
          "<li class='paginate_button page-item  previous'><a href='#/reservas/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/reservas/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/reservas/1'>1</a></li>";
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
            "<li class='paginate_button page-item active'><a href='#/reservas/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/reservas/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/reservas/" +
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
          "<li class='paginate_button page-item'><a  href='#/reservas/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/reservas/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
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
    quitar_filtros: function () {
      this.filtros = {
        estado: null,
        servicio: null,
        desde: null,
        hasta: null,
      };
      this.filtrado = false;
      this.loading = true;
      this.get_reservas();
    },
  },
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_reservas();
    },
  },
};
</script>
