<template>
  <div id="tipos_actividad">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Tipos de actividad</h4>
        <div v-if="permiso" class="text-end">
          <button
            data-bs-toggle="modal"
            data-bs-target="#nuevo"
            class="btn btn-primary btn-sm rounded-pill"
          >
            <i class="fa fa-plus-circle"></i> Nuevo
          </button>
        </div>
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
            <div
              v-if="tipos_actividad.length != 0"
              class="table-responsive mt-3"
            >
              <table
                class="table table-hover table-bordered table-condensed my_table_1"
              >
                <thead class="table-light">
                  <tr>
                    <th style="width: 10%">Tipo</th>
                    <th v-if="permiso" class="text-center" style="width: 10%">
                      Opciones
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(tipo, index) in tipos_actividad"
                    v-bind:key="index"
                  >
                    <td>
                      {{ tipo.tipo_actividad }}
                    </td>

                    <td v-if="permiso" class="text-center">
                      <button
                        v-on:click="tipo_select = tipo"
                        data-bs-toggle="modal"
                        data-bs-target="#editar"
                        class="btn btn-info btn-rounded btn-xs"
                      >
                        <i class="fa fa-edit"></i>
                      </button>
                      <button
                        v-on:click="delete_tipo(index)"
                        class="btn btn-danger btn-rounded btn-xs ms-1"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                tipos.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nuevo">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Nuevo</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body pt-2">
            <div class="form-group col-lg-12">
              <label>Tipo</label>
              <input type="text" class="form-control" v-model="nuevo" />
            </div>
            <div class="text-end">
              <button
                v-on:click="set_tipo"
                class="btn btn-success btn-rounded btn-sm btn-icon-text"
              >
                <i class="fa fa-save"></i> Guardar
              </button>
              <button
                data-bs-dismiss="modal"
                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body pt-2">
            <div v-if="tipo_select.length != 0" class="row">
              <div class="form-group col-lg-12">
                <label>Tipo</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="tipo_select.tipo_actividad"
                />
              </div>
            </div>
            <div class="text-end">
              <button
                v-on:click="update_tipo"
                class="btn btn-success btn-rounded btn-sm btn-icon-text"
              >
                <i class="fa fa-save"></i> Guardar
              </button>
              <button
                data-bs-dismiss="modal"
                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1"
              >
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
  name: "Tipos_actividad",
  metaInfo: {
    title: "Tipos de actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      tipos_actividad: [],
      paginate: null,
      tipo_select: [],
      nuevo: null,
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_tipos_actividad();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_tipos_actividad: function () {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
      };
      axios
        .get(vm.$base_url + "actividades/tipos_actividad", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.tipos_actividad = response.data.data.tipos_actividad;
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

    set_tipo: function () {
      const vm = this;
      var valida = true;

      if (this.nuevo == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Digite el tipo de actividad.",
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
        tipo_actividad: this.nuevo,
      };
      axios
        .post(this.$base_url + "actividades/set_tipo_actividad", params, {
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
            vm.nuevo = null;
          }

          vm.get_tipos_actividad();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },

    update_tipo: function () {
      const vm = this;
      var valida = true;

      if (this.tipo_select.tipo_actividad == null) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Digite el tipo de actividad.",
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
        tipo_actividad: this.tipo_select,
      };
      axios
        .post(this.$base_url + "actividades/update_tipo_actividad", params, {
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
          vm.get_tipos_actividad();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },

    delete_tipo: function (index) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_tipo_actividad: this.tipos_actividad[index].id_tipo_actividad,
      };
      axios
        .post(this.$base_url + "actividades/delete_tipo_actividad", params, {
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
          vm.get_tipos_actividad();
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
  watch: {},
};
</script>
