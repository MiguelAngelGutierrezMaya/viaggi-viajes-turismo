<template>
  <div id="basica_paquete">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <div
        v-if="paquete.estado_servicio == 0"
        class="text-warning fw-bold mt-2 mb-4"
      >
        <i class="fa fa-info-circle"></i> Este servicio se encuentra inactivo.
      </div>
      <h4 class="mb-4 fw-bold text-info">Información básica</h4>
      <div v-if="permiso">
        <div class="row">
          <div class="col-lg-12 col-12 form-group">
            <label>Nombre del paquete</label>
            <input
              v-model="paquete.servicio"
              type="text"
              class="form-control"
            />
          </div>

          <div class="col-lg-12 form-group">
            <label>Resumen</label>
            <editor
              v-model="paquete.resumen"
              :api-key="editor.api_key"
              :init="editor.init"
            />
          </div>
          <div class="col-lg-12 form-group">
            <label>Descripción</label>
            <editor
              v-model="paquete.descripcion"
              :api-key="editor.api_key"
              :init="{
                height: 400,
                menubar: false,
                toolbar:
                  'undo redo  | bold italic | \ alignleft aligncenter alignright alignjustify | \ bullist numlist  | ',
              }"
            />
          </div>
          <div class="col-lg-4 form-group">
            <label>Estado</label>
            <select v-model="paquete.estado_servicio" class="form-control">
              <option value="0">Inactivo</option>
              <option value="1">Activo</option>
            </select>
          </div>
          <div class="col-lg-4 form-group">
            <label>Desde</label>
            <money
              v-model="paquete.valor_desde"
              class="form-control text-end"
              v-bind="money"
              required
            ></money>
          </div>
          <div class="col-lg-12 form-group">
            <label>Meta descripción</label>
            <textarea
              class="form-control"
              rows="4"
              v-model="paquete.meta_descripcion"
            />
          </div>
          <div class="col-lg-12 form-group">
            <label>Palabras clave</label>
            <input type="text" class="form-control" v-model="paquete.tags" />
          </div>
        </div>

        <div class="text-end">
          <button v-on:click="update_paquete" class="btn btn-success btn-sm">
            <i class="fa fa-save"></i> Guardar cambios
          </button>
          <button
            v-on:click="delete_paquete"
            class="btn btn-outline-danger btn-sm ms-1"
          >
            <i class="fa fa-trash"></i> Eliminar servicio
          </button>
        </div>
      </div>
      <div v-else>
        <div class="row">
          <div class="col-lg-12 col-12 form-group">
            <label>Nombre del paquete</label>
            <h6>{{ paquete.servicio }}</h6>
          </div>
          <div class="col-lg-12 form-group">
            <label>Resumen</label>
            <p v-html="paquete.resumen"></p>
          </div>
          <div class="col-lg-12 form-group">
            <label>Descripción</label>
            <p v-html="paquete.descripcion"></p>
          </div>
          <div class="col-lg-4 form-group">
            <label>Estado</label>
            <p>
              <span v-if="paquete.estado_servicio == 0">Inactivo</span>
              <span v-if="paquete.estado_servicio == 1">Activo</span>
            </p>
          </div>
          <div class="col-lg-12 form-group">
            <label>Meta descripción</label>
            <p v-html="paquete.meta_descripcion"></p>
          </div>
          <div class="col-lg-12 form-group">
            <label>Palabras clave</label>
            <p v-html="paquete.tags"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
import Editor from "@tinymce/tinymce-vue";
export default {
  name: "Basica_paquete",
  metaInfo: {
    title: "Información básica de paquete - Backoffice",
  },
  components: {
    editor: Editor,
  },
  data() {
    return {
      loading: true,
      editor: {
        api_key: this.$editor_api_key,
        init: {
          height: 200,
          menubar: false,
          toolbar: "undo redo  | bold italic  |  bullist numlist  | ",
          plugins: "lists advlist",
        },
      },
      id_servicio: null,
      destinos: [],
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        suffix: "",
        precision: 0,
        masked: false,
      },
    };
  },
  props: {
    session: Object,
    paquete: Object,
    permiso: Boolean,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
  },
  methods: {
    get_destinos: function (search) {
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
        .get(vm.$base_url + "servicios/destino_search", config, {
          headers: headers,
        })
        .then((response) => {
          console.log(response);
          if (response.data.status) {
            vm.destinos = response.data.data.destinos;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Paquete",
              text: response.data.message,
            });
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquete",
            text: error,
          });
        });
    },
    update_paquete: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.id_servicio,
        paquete: this.paquete,
      };
      axios
        .post(this.$base_url + "servicios/update_paquete", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Paquetes",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Paquetes",
              text: response.data.message,
            });
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquetes",
            text: error,
          });
        })
        .finally();
    },
    delete_paquete: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.id_servicio,
      };
      axios
        .post(this.$base_url + "servicios/delete_servicio", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Paquetes",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Paquetes",
              text: response.data.message,
            });
          }
          this.$router.push("/paquetes/1");
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquetes",
            text: error,
          });
        })
        .finally();
    },
  },
  mounted: function () {
    this.loading = false;
  },
};
</script>
