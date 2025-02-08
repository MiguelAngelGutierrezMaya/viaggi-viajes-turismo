<template>
  <div id="parametros">
    <div class="card">
      <div class="card-body">
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
            <div class="mt-3">
              <div class="row">
                <div class="col-md-6 offset-md-3">
                  <h5 class="fw-bold text-primary">Ajustes generales</h5>
                  <div class="card card-body shadow">
                    <div v-for="(parametro, index) in parametros" :key="index" class="form-group">
                      <label>{{ parametro.descripcion }}</label>
                      <select v-if="index == 'APROBACION_RESERVAS'" v-model="parametro.valor" class="form-control">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                      </select>
                      <input v-else v-model="parametro.valor" class="form-control" />
                    </div>

                    <div class="text-end">
                      <button v-on:click="update_parametros" class="btn btn-success btn-sm rounded-pill">
                        <i class="fa fa-save"></i> Guardar
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
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Ajustes",
  metaInfo: {
    title: "Ajustes - Backoffice",
  },
  data() {
    return {
      loading: true,
      session: null,
      parametros: null,
    };
  },
  created: function () {
    var session = localStorage.getItem("sess_vyt_backoffice");
    session = JSON.parse(session);
    this.session = session;
    this.get_parametros();
  },
  methods: {
    get_parametros: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
      };
      axios
        .get(vm.$base_url + "admin/parametros", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.parametros = response.data.data.parametros;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Ajustes",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Ajustes",
            text: error,
          });
          vm.loading = false;
        });
    },
    update_parametros: function () {
      this.loading = true;
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        parametros: vm.parametros,
      };
      axios
        .post(this.$base_url + "admin/update_parametros", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              type: "error",
              group: "foo",
              title: "<i class='fa fa-exclamation-circle'></i> Ups!",
              text: response.data.message,
            });
            vm.get_parametros();
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "<i class='fa fa-check-circle'></i> Excelente!",
              text: "Se han actualizado los parámetros.",
            });
          }
          vm.loading = false;
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
