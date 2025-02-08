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
      <h4 class="mb-4 fw-bold text-info">Recomendadas</h4>
      <div v-if="permiso" class="text-end">
        <button
          v-on:click="$modal.show('modal_nuevo')"
          class="btn btn-success btn-sm rounded-pill"
        >
          <i class="fa fa-plus-circle"></i> Agregar
        </button>
      </div>
      <div v-if="recomendadas.length != 0" class="mt-3">
        <table
          class="table table-hover table-bordered table-condensed my_table_1"
        >
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 5%">#</th>
              <th class="textc-center" style="width: 30%">Actividad</th>
              <th v-if="permiso" class="text-center" style="width: 10%">
                Opciones
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(actividad, index) in recomendadas" v-bind:key="index">
              <td class="text-center">{{ index + 1 }}</td>
              <td>{{ actividad.servicio }}</td>
              <td v-if="permiso" class="text-center">
                <button
                  v-on:click="delete_actividad(index)"
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
          <i class="fa fa-info-circle"></i> No se han agregado actividades
          recomendadas para esta actividad.
        </div>
      </div>
    </div>

    <modal name="modal_nuevo" :adaptive="true" height="350">
      <div class="card m-4">
        <div class="card-header">
          <h4 class="fw-bold text-primary">Nueva recomendada</h4>
        </div>
        <div class="card-body">
          <div v-if="loading_actividades" class="text-center">
            <div class="loadingio-spinner-ripple-6armq16mc04">
              <div class="ldio-2tzb8vdk7pu">
                <div></div>
                <div></div>
              </div>
            </div>
          </div>
          <div v-else>
            <div class="form-group">
              <label>Actividad</label>
              <v-select
                v-model="nueva"
                @search="get_actividades"
                label="servicio"
                :options="actividades"
                :clearable="false"
              ></v-select>
            </div>
            <div class="text-end">
              <button
                @click="set_recomendada"
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
      loading_actividades: false,
      id_servicio: null,
      id_actividad: null,
      search: null,
      recomendadas: [],
      actividades: [],
      index_selected: null,
      nueva: null,
    };
  },
  props: {
    session: Object,
    actividad: Object,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_recomendadas();
    this.get_actividades();
  },
  methods: {
    get_recomendadas: function () {
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
          vm.$base_url + "actividades/recomendadas/" + this.id_actividad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.recomendadas = response.data.data.recomendadas;
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
    get_actividades: function (search = null) {
      const vm = this;
      vm.loading_actividades = false;
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
        .get(vm.$base_url + "servicios/actividades", config, {
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
          vm.loading_actividades = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
          vm.loading_actividades = false;
        });
    },
    set_recomendada: function () {
      const vm = this;
      if (this.id_actividad == this.nueva.id_actividad) {
        vm.$notify({
          group: "foo",
          type: "info",
          title: "Nueva recomendada",
          text: "Selecciona una actividad diferente a la actual.",
        });
        return false;
      }

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: this.id_actividad,
        id_actividad_recomendada: this.nueva.id_actividad,
      };
      axios
        .post(this.$base_url + "actividades/set_recomendada", params, {
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
              title: "Nueva recomendada",
              text: response.data.message,
            });
          }
          vm.nueva = null;
          vm.get_recomendadas();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_actividad: function (index) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: this.id_actividad,
        id_actividad_recomendada: this.recomendadas[index].id_actividad,
      };
      axios
        .post(this.$base_url + "actividades/delete_recomendada", params, {
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
              title: "Nueva recomendada",
              text: response.data.message,
            });
          }
          vm.get_recomendadas();
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
