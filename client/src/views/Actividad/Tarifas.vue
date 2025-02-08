<template>
  <div id="tarifas_actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Tarifas</h4>
      <div v-if="permiso" class="text-end">
        <button v-on:click="load_nueva_tarifa(0)" data-bs-toggle="modal" data-bs-target="#nueva_tarifa"
          class="btn btn-success btn-sm btn-rounded btn-icon-text">
          <i class="fa fa-plus-circle"></i> Nueva tarifa
        </button>
      </div>
      <div v-if="tarifas.length != 0" class="mt-3">
        <div v-if="tarifas[0].temporadas.length != 0" class="table-responsive mt-3">
          <table class="table table-hover table-bordered table-condensed my_table_1">
            <thead class="table-light">
              <tr>
                <th rowspan="2" style="width: 10%">Fechas</th>
                <th class="text-center" colspan="2">Adultos</th>
                <th class="text-center" colspan="2">Niños</th>
                <th class="text-center" colspan="2">Infantes</th>
                <th rowspan="2" v-if="permiso" class="text-center" style="width: 10%">
                  Opciones
                </th>
              </tr>
              <tr>
                <th style="width: 10%" class="text-center">
                  Tarifa neta <br />
                </th>
                <th style="width: 10%" class="text-center">
                  Tarifa de venta <br />
                </th>
                <th style="width: 10%" class="text-center">
                  Tarifa neta <br />
                </th>
                <th style="width: 10%" class="text-center">
                  Tarifa de venta <br />
                </th>
                <th style="width: 10%" class="text-center">
                  Tarifa neta <br />
                </th>
                <th style="width: 10%" class="text-center">
                  Tarifa de venta <br />
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(temporada, index_temporada) in tarifas[0].temporadas" v-bind:key="index_temporada">
                <td>
                  Del {{ temporada.fecha_desde_f }}
                  al
                  {{ temporada.fecha_hasta_f }}
                </td>
                <td class="text-center">
                  {{ temporada.valor_neto_adultos | currency }}
                </td>

                <td class="text-center">
                  {{ temporada.valor_venta_adultos | currency }}
                </td>
                <td class="text-center">
                  {{ temporada.valor_neto_ninos | currency }}
                </td>
                <td class="text-center">
                  {{ temporada.valor_venta_ninos | currency }}
                </td>
                <td class="text-center">
                  {{ temporada.valor_neto_infantes | currency }}
                </td>
                <td class="text-center">
                  {{ temporada.valor_venta_infantes | currency }}
                </td>
                <td v-if="permiso" class="text-center">
                  <button v-on:click="load_editar_tarifa(0, index_temporada)" data-bs-toggle="modal"
                    data-bs-target="#editar_tarifa" class="btn btn-info btn-rounded btn-xs">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button v-if="temporada.estado_temporada == 0" v-on:click="update_estado_temporada(0, index_temporada)"
                    class="btn btn-warning btn-rounded btn-xs ms-1" title="Activar">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button v-if="temporada.estado_temporada == 1" v-on:click="update_estado_temporada(0, index_temporada)"
                    class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Desactivar">
                    <i class="fa fa-eye-slash"></i>
                  </button>
                  <button v-on:click="delete_tarifa(0, index_temporada)" class="btn btn-danger btn-rounded btn-xs ms-1">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nueva_tarifa">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Nueva tarifa</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="form-group col-lg-6">
                <label>Desde</label>
                <input v-model="nueva.fecha_desde" type="date" class="form-control" />
              </div>
              <div class="form-group col-lg-6">
                <label>Hasta</label>
                <input v-model="nueva.fecha_hasta" type="date" class="form-control" />
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Adultos</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="nueva.valor_neto_adultos" class="form-control text-end" v-bind="money" required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="nueva.valor_venta_adultos" class="form-control text-end" v-bind="money" required></money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Niños</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="nueva.valor_neto_ninos" class="form-control text-end" v-bind="money" required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="nueva.valor_venta_ninos" class="form-control text-end" v-bind="money" required></money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Infantes</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="nueva.valor_neto_infantes" class="form-control text-end" v-bind="money" required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="nueva.valor_venta_infantes" class="form-control text-end" v-bind="money" required></money>
              </div>
            </div>
            <div class="text-end">
              <button v-on:click="set_tarifa" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
    <div class="modal fade" id="editar_tarifa">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar tarifa</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="temporada_selected.length != 0" class="row">
              <div class="form-group col-lg-6">
                <label>Desde</label>
                <input v-model="temporada_selected.fecha_desde" type="date" class="form-control" />
              </div>
              <div class="form-group col-lg-6">
                <label>Hasta</label>
                <input v-model="temporada_selected.fecha_hasta" type="date" class="form-control" />
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Adultos</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="temporada_selected.valor_neto_adultos" class="form-control text-end" v-bind="money"
                  required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="temporada_selected.valor_venta_adultos" class="form-control text-end" v-bind="money"
                  required></money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Niños</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="temporada_selected.valor_neto_ninos" class="form-control text-end" v-bind="money"
                  required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="temporada_selected.valor_venta_ninos" class="form-control text-end" v-bind="money"
                  required></money>
              </div>
              <div class="col-lg-12 mb-2">
                <span class="fw-bold">Infantes</span>
              </div>
              <div class="form-group col-lg-6">
                <label>Tarifa neta </label>

                <money v-model="temporada_selected.valor_neto_infantes" class="form-control text-end" v-bind="money"
                  required></money>
              </div>

              <div class="form-group col-lg-6">
                <label>Tarifa de venta </label>
                <money v-model="temporada_selected.valor_venta_infantes" class="form-control text-end" v-bind="money"
                  required></money>
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
import { Money } from "v-money";
export default {
  name: "Tarifas_actividad",
  components: { Money },
  metaInfo: {
    title: "Tarifas de actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      editor_api_key: this.$editor_api_key,
      id_servicio: null,
      id_actividad: null,
      search: null,
      tarifas: [],
      modalidad_selected: [],
      proveedores: [],
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
      temporada_selected: [],
    };
  },
  props: {
    session: Object,
    actividad: Object,
    permiso: Boolean,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_tarifas();
  },
  methods: {
    get_tarifas: function () {
      this.id_actividad = this.actividad.id_actividad;
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
      }
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
        .get(
          vm.$base_url + "actividades/tarifas_actividad/" + this.id_actividad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.tarifas = response.data.data.tarifas;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Actividad",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividad",
            text: error,
          });
        });
    },
    load_nueva_tarifa: function (index) {
      this.modalidad_selected = this.tarifas[index];
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
      this.temporada_selected.proveedor = {
        id_proveedor: null,
        proveedor: null,
      };
      this.form_nuevo_proveedor = true;
    },
    set_tarifa: function () {
      const vm = this;
      var valida = true;

      if (this.nueva.fecha_desde == null || this.nueva.fecha_hasta == null) {
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
      var nuevo_proveedor = 0;
      if (this.form_nuevo_proveedor) {
        nuevo_proveedor = 1;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_modalidad: this.modalidad_selected.id_modalidad,
        nuevo_proveedor: nuevo_proveedor,
        temporada: this.nueva,
      };
      axios
        .post(this.$base_url + "actividades/set_temporada_tarifa", params, {
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
          vm.nueva = {
            proveedor: null,
            fecha_desde: null,
            fecha_hasta: null,
            valor_neto_adultos: 0,
            valor_neto_ninos: 0,
            valor_neto_infantes: 0,
            valor_venta_adultos: 0,
            valor_venta_ninos: 0,
            valor_venta_infantes: 0,
          };
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    load_editar_tarifa: function (index_modalidad, index_temporada) {
      this.temporada_selected =
        this.tarifas[index_modalidad].temporadas[index_temporada];
    },
    update_tarifa: function () {
      const vm = this;
      var valida = true;
      if (this.temporada_selected.proveedor == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Selecciona o crea el proveedor de la modalidad.",
        });
        valida = false;
      }
      if (
        this.temporada_selected.fecha_desde == null ||
        this.temporada_selected.fecha_hasta == null
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
      var nuevo_proveedor = 0;
      if (this.form_nuevo_proveedor) {
        nuevo_proveedor = 1;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_modalidad: this.modalidad_selected.id_modalidad,
        nuevo_proveedor: nuevo_proveedor,
        temporada: this.temporada_selected,
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
    update_estado_temporada: function (index_modalidad, index_temporada) {
      var estado = 1;
      var estado_actual =
        this.tarifas[index_modalidad].temporadas[index_temporada]
          .estado_temporada;
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
        id_temporada_modalidad:
          this.tarifas[index_modalidad].temporadas[index_temporada]
            .id_temporada_modalidad,
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
          vm.error_manager(error);
        })
        .finally();
    },
    delete_tarifa: function (index_modalidad, index_temporada) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_temporada_modalidad:
          this.tarifas[index_modalidad].temporadas[index_temporada]
            .id_temporada_modalidad,
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
};
</script>
