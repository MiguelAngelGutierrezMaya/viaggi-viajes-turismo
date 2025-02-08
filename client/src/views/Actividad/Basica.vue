<template>
  <div id="basica_actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <div v-if="actividad.estado_servicio == 0" class="text-warning fw-bold mt-2 mb-4">
        <i class="fa fa-info-circle"></i> Este servicio se encuentra inactivo.
      </div>
      <h4 class="mb-4 fw-bold text-info">Información básica</h4>
      <div v-if="permiso">
        <div class="row">
          <div class="col-lg-12 col-12 form-group">
            <label>Nombre de la actividad</label>
            <input v-model="actividad.servicio" type="text" class="form-control" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Tipos de actividad</label>
            <v-select multiple v-model="actividad.tipos_actividad" label="tipo_actividad"
              :options="tipos_actividad"></v-select>
          </div>
          <div class="col-lg-12 form-group">
            <label>Resumen</label>
            <editor v-model="actividad.resumen" :api-key="editor.api_key" :init="editor.init" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Descripción</label>
            <editor v-model="actividad.descripcion" :api-key="editor.api_key" :init="{
              height: 400,
              menubar: false,
              toolbar:
                'undo redo  | bold italic | \ alignleft aligncenter alignright alignjustify | \ bullist numlist  | ',
            }" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Incluye</label>
            <editor v-model="actividad.incluye" :api-key="editor.api_key" :init="{
              height: 400,
              menubar: false,
              toolbar:
                'undo redo  | bold italic | \ alignleft aligncenter alignright alignjustify | \ bullist numlist  | ',
            }" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Datos para el tour</label>
            <editor v-model="actividad.datos_tour" :api-key="editor.api_key" :init="editor.init" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Importante</label>
            <editor v-model="actividad.cancelaciones" :api-key="editor.api_key" :init="editor.init" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Punto de encuentro</label>
            <editor v-model="actividad.punto_encuentro" :api-key="editor.api_key" :init="editor.init" />
          </div>
          <div class="col-lg-4 form-group">
            <label>Duración</label>
            <input class="form-control" type="text" v-model="actividad.duracion" />
          </div>
          <div class="col-lg-4 form-group">
            <label>Tiempo mínimo reserva (en horas)</label>
            <input class="form-control" type="text" v-model="actividad.tiempo_min_reserva" />
          </div>
          <div class="col-lg-4 form-group">
            <label>Estado</label>
            <select v-model="actividad.estado_servicio" class="form-control">
              <option value="0">Inactivo</option>
              <option value="1">Activo</option>
            </select>
          </div>
          <div class="col-lg-12 form-group">
            <label>Meta descripción</label>
            <textarea class="form-control" rows="4" v-model="actividad.meta_descripcion" />
          </div>
          <div class="col-lg-12 form-group">
            <label>Palabras clave</label>
            <input type="text" class="form-control" v-model="actividad.tags" />
          </div>
        </div>
        <div class="text-end">
          <button v-on:click="update_actividad" class="btn btn-success btn-sm">
            <i class="fa fa-save"></i> Guardar cambios
          </button>
          <button v-on:click="delete_actividad" class="btn btn-outline-danger btn-sm ms-1">
            <i class="fa fa-trash"></i> Eliminar servicio
          </button>
        </div>
      </div>
      <div v-else class="row">
        <div class="col-lg-12 col-12 form-group">
          <label>Nombre de la actividad</label>
          <h6>{{ actividad.servicio }}</h6>
        </div>
        <div class="col-lg-12 form-group">
          <label>Tipos de actividad</label>
          <div>
            <span class="badge bg-secondary" v-for="(tipo_actividad, index_tipo) in actividad.tipos_actividad"
              v-bind:key="index_tipo">
              {{ tipo_actividad.tipo_actividad }}
            </span>
          </div>
        </div>
        <div class="col-lg-12 form-group">
          <label>Resumen</label>
          <p v-html="actividad.resumen"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Descripción</label>
          <p v-html="actividad.descripcion"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Incluye</label>
          <p v-html="actividad.incluye"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Datos para el tour</label>
          <p v-html="actividad.datos_tour"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Cancelaciones</label>
          <p v-html="actividad.cancelaciones"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Punto de encuentro</label>
          <p v-html="actividad.punto_encuentro"></p>
        </div>
        <div class="col-lg-4 form-group">
          <label>Duración</label>
          <p v-html="actividad.duracion"></p>
        </div>
        <div class="col-lg-4 form-group">
          <label>Tiempo mínimo reserva (en horas)</label>
          <p v-html="actividad.tiempo_min_reserva"></p>
        </div>
        <div class="col-lg-4 form-group">
          <label>Estado</label>
          <p>
            <span v-if="actividad.estado_servicio == 0">Inactiva</span>
            <span v-if="actividad.estado_servicio == 1">Activa</span>
          </p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Meta descripción</label>
          <p v-html="actividad.meta_descripcion"></p>
        </div>
        <div class="col-lg-12 form-group">
          <label>Palabras clave</label>
          <p v-html="actividad.tags"></p>
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
  name: "Basica_actividad",
  metaInfo: {
    title: "Información básica de actividad - Backoffice",
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
      tipos_actividad: [],
    };
  },
  props: {
    session: Object,
    actividad: Object,
    permiso: Boolean,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_tipos_actividad();
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
              title: "Actividad",
              text: response.data.message,
            });
          }
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
    update_actividad: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.id_servicio,
        actividad: this.actividad,
      };
      axios
        .post(this.$base_url + "servicios/update_actividad", params, {
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
        })
        .catch((error) => {
          var msg = "Ha ocurrido un error.";
          if (
            error.response.status != undefined &&
            error.response.status == 401
          ) {
            localStorage.removeItem("sess_vyt_backoffice");
            location.reload();
            msg = "Acceso no autorizado.";
          }
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: msg,
          });
        })
        .finally();
    },
    delete_actividad: function () {
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
          this.$router.push("/actividades/1");
        })
        .catch((error) => {
          var msg = "Ha ocurrido un error.";
          if (
            error.response.status != undefined &&
            error.response.status == 401
          ) {
            localStorage.removeItem("sess_vyt_backoffice");
            location.reload();
            msg = "Acceso no autorizado.";
          }
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: msg,
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
