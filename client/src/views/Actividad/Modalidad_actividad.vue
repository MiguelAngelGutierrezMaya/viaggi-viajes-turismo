<template>
  <div id="modalidad_actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Información de la modalidad</h4>
      <div
        v-if="modalidad.estado_servicio == 0"
        class="text-warning fw-bold mt-2 mb-4"
      >
        <i class="fa fa-info-circle"></i> Esta modalidad se encuentra inactiva.
      </div>
      <div class="row">
        <div class="col-lg-6 col-12 form-group">
          <label>Nombre de la modalidad</label>
          <input
            v-model="modalidad.modalidad"
            type="text"
            class="form-control"
          />
        </div>
        <div class="col-lg-12 form-group">
          <label>Descripción</label>
          <editor
            v-model="modalidad.descripcion"
            api-key="editor_api_key"
            :init="{
              height: 200,
              menubar: false,
              toolbar:
                'undo redo  | bold italic | \ alignleft aligncenter alignright alignjustify | \ bullist numlist  | ',
            }"
          />
        </div>
        <div class="col-lg-12 form-group">
          <label>Indicaciones</label>
          <editor
            v-model="modalidad.indicaciones"
            api-key="editor_api_key"
            :init="{
              height: 200,
              menubar: false,
              toolbar:
                'undo redo  | bold italic | \ alignleft aligncenter alignright alignjustify | \ bullist numlist  | ',
            }"
          />
        </div>
        <div class="col-lg-4 col-12 form-group">
          <label>Edad mínima de niños</label>
          <input
            v-model="modalidad.edad_min_ninos"
            type="text"
            class="form-control"
          />
        </div>
        <div class="col-lg-4 col-12 form-group">
          <label>Edad máxima de niños</label>
          <input
            v-model="modalidad.edad_max_ninos"
            type="text"
            class="form-control"
          />
        </div>
        <div class="col-lg-4 form-group">
          <label>Estado</label>
          <select v-model="modalidad.estado_modalidad" class="form-control">
            <option value="0">Inactivo</option>
            <option value="1">Activo</option>
          </select>
        </div>
      </div>
      <div class="text-end">
        <button v-on:click="update_modalidad" class="btn btn-success btn-sm">
          <i class="fa fa-save"></i> Guardar cambios
        </button>
        <button
          v-on:click="delete_modalidad"
          class="btn btn-outline-danger btn-sm ms-1"
        >
          <i class="fa fa-trash"></i> Eliminar modalidad
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
import Editor from "@tinymce/tinymce-vue";
export default {
  name: "Modalidad_actividad",
  metaInfo: {
    title: "Modalidad - Backoffice",
  },
  components: {
    editor: Editor,
  },
  data() {
    return {
      loading: true,
      editor_api_key: this.$editor_api_key,
      id_modalidad: null,
      modalidad: [],
    };
  },
  props: {
    session: Object,
    actividad: Object,
  },
  created: function () {
    this.id_modalidad = this.$route.params.id_modalidad;
    this.get_modalidad();
  },
  methods: {
    get_modalidad: function () {
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
        .get(
          vm.$base_url + "actividades/modalidad/" + this.id_modalidad,
          config,
          {
            headers: headers,
          }
        )
        .then((response) => {
          if (response.data.status) {
            vm.modalidad = response.data.data.modalidad;
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
    update_modalidad: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_modalidad: this.id_modalidad,
        modalidad: this.modalidad,
      };
      axios
        .post(this.$base_url + "actividades/update_modalidad", params, {
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
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Actividades",
            text: error,
          });
        })
        .finally();
    },
    delete_modalidad: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_modalidad: this.id_modalidad,
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
          this.$router.push("/actividades/1");
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