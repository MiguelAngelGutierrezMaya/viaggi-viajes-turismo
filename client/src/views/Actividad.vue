<template>
  <div id="actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h3 class="fw-bold text-primary">{{ actividad.servicio }}</h3>
      <hr />
      <div class="row">
        <div class="col-12 col-md-2">
          <div class="card">
            <div class="card-body fw-bold p-2" style="font-size: 0.6em;">
              <ul class="list-arrow">
                <li>
                  <router-link v-bind:to="'/actividad/' + id_servicio + '/basica'" href="#doc-intro">Información
                    básica</router-link>
                </li>
                <li>
                  <router-link v-bind:to="'/actividad/' + id_servicio + '/horarios'"
                    href="javascript:void(0)">Horarios</router-link>
                </li>

                <li>
                  <router-link v-bind:to="'/actividad/' + id_servicio + '/tarifas'"
                    href="javascript:void(0)">Tarifas</router-link>
                </li>
                <li>
                  <router-link v-bind:to="'/actividad/' + id_servicio + '/galeria'"
                    href="javascript:void(0)">Galería</router-link>
                </li>
                <li>
                  <router-link v-bind:to="'/actividad/' + id_servicio + '/recomendadas'"
                    href="javascript:void(0)">Recomendadas</router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-10">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <router-view :session="session" :actividad="actividad" :permiso="permiso" />
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
  name: "Actividad",
  metaInfo: {
    title: "Actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      id_servicio: null,
      actividad: [],
      destinos: [],
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_actividad();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_actividad: function () {
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
        .get(vm.$base_url + "servicios/actividad/" + this.id_servicio, config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.actividad = response.data.data.actividad;
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
  },
};
</script>
