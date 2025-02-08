<template>
  <div id="horarios">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Horarios</h4>
      <div v-if="permiso" class="text-end">
        <button
          v-on:click="$modal.show('modal_nuevo')"
          class="btn btn-success btn-sm rounded-pill"
        >
          <i class="fa fa-plus-circle"></i> Nuevo horario
        </button>
      </div>
      <div v-if="horarios.length != 0" class="mt-3">
        <table
          class="table table-hover table-bordered table-condensed my_table_1"
        >
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 5%">#</th>
              <th class="textc-center" style="width: 30%">Desde</th>
              <th class="text-center" style="width: 30%">Hasta</th>
              <th v-if="permiso" class="text-center" style="width: 5%">
                Opciones
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(horario, index_horario) in horarios"
              v-bind:key="index_horario"
            >
              <td class="text-center">{{ horario.num }}</td>
              <td>{{ horario.desde }}</td>
              <td>{{ horario.hasta }}</td>

              <td v-if="permiso" class="text-center">
                <button
                  v-on:click="index_selected = index_horario"
                  data-bs-target="#editar"
                  data-bs-toggle="modal"
                  class="btn btn-primary btn-rounded btn-xs ms-1"
                  title="Editar"
                >
                  <i class="fa fa-edit"></i>
                </button>
                <button
                  v-on:click="delete_horario(index_horario)"
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
      <div v-else>
        <div class="mt-3 mb-3 text-muted">
          <i class="fa fa-info-circle"></i> No se han creado horarios para esta
          actividad.
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="editar"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
      data-bs-keyboard="false"
      data-bs-backdrop="static"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title text-primary font-weight-bold"
              id="exampleModalLabel"
            >
              Editar
            </h5>
            <button
              id="close_modal_edit"
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div v-if="index_selected != null">
              <div class="form-group">
                <label>Desde</label>
                <input
                  v-model="horarios[index_selected].desde"
                  class="form-control"
                  type="text"
                />
              </div>
              <div class="form-group">
                <label>Hasta</label>
                <input
                  v-model="horarios[index_selected].hasta"
                  class="form-control"
                  type="text"
                />
              </div>

              <div class="text-end">
                <button
                  @click="update_horario"
                  class="btn btn-success btn-sm rounded-pill"
                >
                  <i class="fa fa-save"></i> Guardar
                </button>
                <button
                  data-bs-dismiss="modal"
                  class="btn btn-outline-secondary btn-sm ms-1 rounded-pill"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <modal name="modal_nuevo" :adaptive="true" height="450">
      <div class="card m-4">
        <div class="card-header">
          <h4 class="fw-bold text-primary">Nuevo horario</h4>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Desde</label>
            <input v-model="nuevo.desde" class="form-control" type="text" />
          </div>
          <div class="form-group">
            <label>Hasta</label>
            <input v-model="nuevo.hasta" class="form-control" type="text" />
          </div>

          <div class="text-end">
            <button
              @click="set_horario"
              class="btn btn-success btn-sm rounded-pill"
            >
              <i class="fa fa-save"></i> Guardar
            </button>
            <button
              @click="$modal.hide('modal_nuevo')"
              class="btn btn-outline-secondary btn-sm ms-1 rounded-pill"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </modal>
  </div>
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Horarios",
  metaInfo: {
    title: "Horarios de actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      id_servicio: null,
      id_actividad: null,
      search: null,
      horarios: [],
      index_selected: null,
      nuevo: {
        desde: null,
        hasta: null,
        estar: null,
      },
      puntos_salida: [],
    };
  },
  props: {
    session: Object,
    actividad: Object,
    permiso: Boolean,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_horarios();
    this.get_puntos_salida();
  },
  methods: {
    get_horarios: function () {
      this.id_actividad = this.actividad.id_actividad;
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
        .get(
          vm.$base_url + "actividades/horarios/" + this.id_actividad,
          config,
          {
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
    get_puntos_salida: function () {
      this.id_actividad = this.actividad.id_actividad;
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
        .get(
          vm.$base_url + "actividades/puntos_salida/" + this.id_actividad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.puntos_salida = response.data.data.puntos_salida;
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
    set_horario: function () {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: this.id_actividad,
        horario: this.nuevo,
      };
      axios
        .post(this.$base_url + "actividades/set_horario", params, {
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
          vm.get_horarios();
          vm.nuevo = {
            desde: null,
            hasta: null,
          };
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_horario: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        horario: this.horarios[this.index_selected],
      };
      axios
        .post(this.$base_url + "actividades/update_horario", params, {
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
          vm.get_horarios();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_horario: function (index_horario) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_horario: this.horarios[index_horario].id_horario,
      };
      axios
        .post(this.$base_url + "actividades/delete_horario", params, {
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
          vm.get_horarios();
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
