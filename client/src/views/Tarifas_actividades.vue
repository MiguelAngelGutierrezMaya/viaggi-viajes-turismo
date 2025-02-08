<template>
  <div id="tarifas_actividades">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Tarifas de actividades</h4>
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
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_tarifas" type="text" class="form-control"
                placeholder="Buscar actividad" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="tarifas.length != 0" class="table-responsive mt-3">
              <table class="table table-hover table-bordered table-condensed my_table_1">
                <thead class="table-light">
                  <tr>
                    <th rowspan="2" style="width: 25%">Actividad</th>
                    <th rowspan="2" style="width: 10%">Fechas</th>
                    <th colspan="2" class="text-center">Adultos</th>
                    <th colspan="2" class="text-center">Niños</th>
                    <th colspan="2" class="text-center">Infantes</th>
                    <th rowspan="2" v-if="permiso" class="text-center" style="width: 10%">
                      Opciones
                    </th>
                  </tr>
                  <tr>
                    <th class="text-center">Neto</th>
                    <th class="text-center">Venta</th>
                    <th class="text-center">Neto</th>
                    <th class="text-center">Venta</th>
                    <th class="text-center">Neto</th>
                    <th class="text-center">Venta</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(temporada, index_temporada) in tarifas" v-bind:key="index_temporada">
                    <td>
                      {{ temporada.actividad.actividad }}
                    </td>
                    <td>
                      {{ temporada.fecha_desde_f }} a
                      {{ temporada.fecha_hasta_f }}
                    </td>
                    <td class="text-center fw-bold text-primary">
                      {{ temporada.valor_neto_adultos | currency }}
                    </td>
                    <td class="text-center fw-bold text-success">
                      {{ temporada.valor_venta_adultos | currency }}
                    </td>
                    <td class="text-center fw-bold text-primary">
                      {{ temporada.valor_neto_ninos | currency }}
                    </td>
                    <td class="text-center fw-bold text-success">
                      {{ temporada.valor_venta_ninos | currency }}
                    </td>
                    <td class="text-center fw-bold text-primary">
                      {{ temporada.valor_neto_infantes | currency }}
                    </td>
                    <td class="text-center fw-bold text-success">
                      {{ temporada.valor_venta_infantes | currency }}
                    </td>
                    <td v-if="permiso" class="text-center">
                      <button v-on:click="load_editar_tarifa(index_temporada)" data-bs-toggle="modal"
                        data-bs-target="#editar_tarifa" class="btn btn-info btn-rounded btn-xs">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button v-if="temporada.estado_temporada == 0" v-on:click="update_estado_temporada(index_temporada)"
                        class="btn btn-warning btn-rounded btn-xs ms-1" title="Activar">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button v-if="temporada.estado_temporada == 1" v-on:click="update_estado_temporada(index_temporada)"
                        class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Desactivar">
                        <i class="fa fa-eye-slash"></i>
                      </button>
                      <button v-on:click="delete_tarifa(index_temporada)" class="btn btn-danger btn-rounded btn-xs ms-1">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="text-center mt-3 mb-2" v-html="paginate"></div>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                tarifas.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editar_tarifa">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar tarifa</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body pt-2">
            <div v-if="tarifa_selected.length != 0" class="row">
              <div class="form-group col-lg-12">
                <label>Actividad</label>
                <p class="text-primary fw-bold">
                  {{ tarifa_selected.actividad.actividad }}
                </p>
              </div>

              <div class="form-group col-lg-6">
                <label>Desde</label>
                <input v-model="tarifa_selected.fecha_desde" type="date" class="form-control" />
              </div>
              <div class="form-group col-lg-6">
                <label>Hasta</label>
                <input v-model="tarifa_selected.fecha_hasta" type="date" class="form-control" />
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Adultos</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="tarifa_selected.valor_neto_adultos" class="form-control text-end" v-bind="money" required>
                </money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa venta </label>
                <money v-model="tarifa_selected.valor_venta_adultos" class="form-control text-end" v-bind="money"
                  required></money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Niños</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="tarifa_selected.valor_neto_ninos" class="form-control text-end" v-bind="money" required>
                </money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="tarifa_selected.valor_venta_ninos" class="form-control text-end" v-bind="money" required>
                </money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Infantes</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="tarifa_selected.valor_neto_infantes" class="form-control text-end" v-bind="money"
                  required>
                </money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="tarifa_selected.valor_venta_infantes" class="form-control text-end" v-bind="money"
                  required>
                </money>
              </div>
            </div>
            <div class="text-end">
              <button v-on:click="update_tarifa" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
  </div>
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Tarifas_actividades",
  metaInfo: {
    title: "Tarifas actividades - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      tarifas: [],
      paginate: null,
      tarifa_selected: [],
      form_nuevo_proveedor: false,
      nueva: {
        proveedor: null,
        fecha_desde: null,
        fecha_hasta: null,
        valor_neto_adultos: 0,
        valor_neto_ninos: 0,
        valor_neto_infantes: 0,
        valor_venta_adultos: 0,
        valor_venta_ninos: 0,
        valor_venta_infantes: 0,
      },
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        suffix: "",
        precision: 0,
        masked: false,
      },
      proveedores: [],
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_tarifas();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_tarifas: function () {
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/tarifas_actividades/1");
        }
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
        },
      };
      axios
        .get(vm.$base_url + "actividades/tarifas_actividades", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.tarifas = response.data.data.tarifas;
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
    get_proveedores: function (search) {
      if (search.length <= 3) {
        return false;
      }
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
        .get(vm.$base_url + "servicios/proveedores", config, {
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
    load_form_nuevo_proveedor: function () {
      this.tarifa_selected.proveedor = {
        id_proveedor: null,
        proveedor: null,
      };
      this.form_nuevo_proveedor = true;
    },
    load_editar_tarifa: function (index) {
      this.tarifa_selected = this.tarifas[index];
    },
    update_tarifa: function () {
      const vm = this;
      var valida = true;

      if (
        this.tarifa_selected.fecha_desde == null ||
        this.tarifa_selected.fecha_hasta == null
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Selecciona el rango de fechas de la temporada.",
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
        id_modalidad: this.tarifa_selected.modalidad.id_modalidad,
        temporada: this.tarifa_selected,
      };
      axios
        .post(this.$base_url + "actividades/update_temporada_tarifa", params, {
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
          vm.get_tarifas();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_estado_temporada: function (index) {
      var estado = 1;
      var estado_actual = this.tarifas[index].estado_temporada;
      if (estado_actual == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_temporada_modalidad: this.tarifas[index].id_temporada_modalidad,
        estado: estado,
      };
      axios
        .post(this.$base_url + "actividades/update_estado_temporada", params, {
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
          vm.get_tarifas();
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
        })
        .finally();
    },
    delete_tarifa: function (index) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_temporada_modalidad: this.tarifas[index].id_temporada_modalidad,
      };
      axios
        .post(this.$base_url + "actividades/delete_temporada_tarifa", params, {
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
          vm.get_tarifas();
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
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
  },
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_tarifas();
    },
  },
};
</script>
