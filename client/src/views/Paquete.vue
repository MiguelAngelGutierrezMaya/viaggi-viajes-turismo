<template>
  <div id="paquete">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h3 class="fw-bold text-primary">{{ paquete.servicio }}</h3>
      <hr />
      <div class="row">
        <div class="col-12 col-md-3">
          <div class="card">
            <div class="card-body">
              <ul class="list-arrow">
                <li>
                  <router-link
                    v-bind:to="'/paquete/' + id_servicio + '/basica'"
                    href="#doc-intro"
                    >Información básica</router-link
                  >
                </li>
                <li>
                  <router-link
                    v-bind:to="'/paquete/' + id_servicio + '/imagen'"
                    href="javascript:void(0)"
                    >Imagen</router-link
                  >
                </li>
                <li>
                  <router-link
                    v-bind:to="'/paquete/' + id_servicio + '/galeria'"
                    href="javascript:void(0)"
                    >Galería</router-link
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-9">
          <div class="col-12">
            <div class="card ms-2">
              <div class="card-body">
                <router-view
                  :session="session"
                  :paquete="paquete"
                  :permiso="permiso"
                />
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
  name: "Paquete",
  metaInfo: {
    title: "Paquete - Backoffice",
  },
  data() {
    return {
      loading: true,
      id_servicio: null,
      paquete: [],
      destinos: [],
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_paquete();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_paquete: function () {
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
        .get(vm.$base_url + "servicios/paquete/" + this.id_servicio, config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.paquete = response.data.data.paquete;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Paquetes",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquetes",
            text: error,
          });
          vm.loading = false;
        });
    },
  },
};
</script>
