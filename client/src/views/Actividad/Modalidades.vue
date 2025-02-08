<template>
  <div id="modalidades_actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Modalidades</h4>
      <div class="text-end">
        <button
          data-bs-toggle="modal"
          data-bs-target="#nueva_modalidad"
          class="btn btn-success btn-sm btn-rounded btn-icon-text"
        >
          <i class="fa fa-plus-circle"></i> Nueva modalidad
        </button>
      </div>
      <div class="input-group mt-2 mb-2 mr-sm-2">
        <input
          v-model="search"
          v-on:keyup="get_modalidades"
          type="text"
          class="form-control"
          placeholder="Buscar modalidad"
        />
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fa fa-search"></i>
          </div>
        </div>
      </div>

      <div v-if="modalidades.length != 0" class="table-responsive mt-3">
        <table class="table table-hover table-condensed my_table_1">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 3%">#</th>
              <th style="width: 30%">Modalidad</th>
              <th class="text-center" style="width: 5%">Estado</th>
              <th class="text-center" style="width: 10%">Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(modalidad, index_modalidad) in modalidades"
              v-bind:key="index_modalidad"
            >
              <td class="text-center">{{ modalidad.num }}</td>
              <td>
                {{ modalidad.modalidad }}
              </td>

              <td class="text-center">
                <label
                  v-if="modalidad.estado_modalidad == 1"
                  class="badge badge-success"
                  >Activa</label
                >
                <label
                  v-if="modalidad.estado_modalidad == 0"
                  class="badge badge-danger"
                  >Inactiva</label
                >
              </td>
              <td class="text-center">
                <router-link
                  v-bind:to="
                    '/modalidad_actividad/' +
                    id_servicio +
                    '/' +
                    modalidad.id_modalidad
                  "
                  class="btn btn-primary btn-rounded btn-xs"
                  title="Detalles"
                >
                  <i class="fa fa-list"></i>
                </router-link>
                <button
                  v-if="modalidad.estado_modalidad == 0"
                  v-on:click="update_estado_modalidad(index_modalidad)"
                  class="btn btn-warning btn-rounded btn-xs ms-1"
                  title="Activar"
                >
                  <i class="fa fa-eye"></i>
                </button>
                <button
                  v-if="modalidad.estado_modalidad == 1"
                  v-on:click="update_estado_modalidad(index_modalidad)"
                  class="btn btn-outline-warning btn-rounded btn-xs ms-1"
                  title="Desactivar"
                >
                  <i class="fa fa-eye-slash"></i>
                </button>
                <button
                  v-on:click="delete_modalidad(index_modalidad)"
                  class="btn btn-danger btn-rounded btn-xs ms-1"
                  title="Eliminar"
                >
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" id="nueva_modalidad">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Nueva modalidad</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre de la modalidad</label>
              <input
                v-model="nueva.modalidad"
                type="text"
                class="form-control"
              />
            </div>
            <div class="text-end">
              <button
                v-on:click="set_modalidad"
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
  name: "Modalidades_actividad",
  metaInfo: {
    title: "Modalidades de actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      editor_api_key: this.$editor_api_key,
      id_servicio: null,
      id_actividad: null,
      search: null,
      modalidades: [],
      nueva: {
        modalidad: null,
      },
    };
  },
  props: {
    session: Object,
    actividad: Object,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.id_actividad = this.actividad.id_actividad;
    this.get_modalidades();
  },
  methods: {
    get_modalidades: function () {
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
          vm.$base_url +
            "actividades/modalidades_actividad/" +
            this.id_actividad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.modalidades = response.data.data.modalidades;
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
    set_modalidad: function () {
      const vm = this;
      var valida = true;
      if (
        this.nueva.modalidad == null ||
        this.nueva.modalidad.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Actividades",
          text: "Digita el nombre de la modalidad.",
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
        id_actividad: this.id_actividad,
        modalidad: this.nueva.modalidad,
      };
      axios
        .post(this.$base_url + "actividades/set_modalidad", params, {
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
          vm.get_modalidades();
          vm.nueva = {
            modalidad: null,
          };
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
    update_estado_modalidad: function (index_modalidad) {
      var estado = 1;
      var estado_actual = this.modalidades[index_modalidad].estado_modalidad;
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
        id_modalidad: this.modalidades[index_modalidad].id_modalidad,
        estado: estado,
      };
      axios
        .post(this.$base_url + "actividades/update_estado_modalidad", params, {
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
          vm.get_modalidades();
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
    delete_modalidad: function (index_modalidad) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_modalidad: this.modalidades[index_modalidad].id_modalidad,
      };
      axios
        .post(this.$base_url + "actividades/delete_modalidad", params, {
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
          vm.get_modalidades();
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
  },
};
</script>