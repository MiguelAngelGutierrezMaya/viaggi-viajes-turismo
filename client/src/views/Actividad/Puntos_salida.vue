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
      <h4 class="mb-4 fw-bold text-info">Puntos de salida</h4>
      <div class="text-end">
        <button
          v-on:click="$modal.show('modal_nuevo')"
          class="btn btn-success btn-sm rounded-pill"
        >
          <i class="fa fa-plus-circle"></i> Nuevo punto
        </button>
      </div>
      <div v-if="puntos_salida.length != 0" class="mt-3">
        <table
          class="table table-hover table-bordered table-condensed my_table_1"
        >
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 2%">#</th>
              <th class="textc-center" style="width: 30%">Punto de salida</th>
              <th class="text-center" style="width: 10%">Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(punto, index) in puntos_salida" v-bind:key="index">
              <td class="text-center">{{ punto.num }}</td>
              <td>{{ punto.punto_salida }}</td>
              <td class="text-center">
                <button
                  v-on:click="
                    (index_selected = index), $modal.show('modal_editar')
                  "
                  class="btn btn-primary btn-rounded btn-xs ms-1"
                  title="Editar"
                >
                  <i class="fa fa-edit"></i>
                </button>
                <button
                  v-on:click="
                    (index_selected = index), $modal.show('modal_image')
                  "
                  class="btn btn-info btn-rounded btn-xs ms-1"
                  title="Editar"
                >
                  <i class="fa fa-image"></i>
                </button>
                <button
                  v-on:click="delete_punto(index)"
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
          <i class="fa fa-info-circle"></i> No se han creado puntos de salida
          para esta actividad.
        </div>
      </div>
    </div>
    <modal name="modal_editar" :adaptive="true" height="450">
      <div class="m-4">
        <h4 class="fw-bold text-primary mb-4">Editar punto</h4>
        <div v-if="index_selected != null">
          <div class="form-group">
            <label>Punto de salida</label>
            <input
              v-model="puntos_salida[index_selected].punto_salida"
              class="form-control"
              type="text"
            />
          </div>
          <div class="form-group">
            <label>Ubicación</label>
            <textarea
              v-model="puntos_salida[index_selected].ubicacion"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label>Link ubicación</label>
            <textarea
              v-model="puntos_salida[index_selected].link_mapa"
              class="form-control"
              rows="2"
            />
          </div>
          <div class="text-end">
            <button
              @click="update_punto"
              class="btn btn-success btn-sm rounded-pill"
            >
              <i class="fa fa-save"></i> Guardar
            </button>
            <button
              @click="$modal.hide('modal_editar')"
              class="btn btn-outline-secondary btn-sm ms-1 rounded-pill"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </modal>
    <modal name="modal_nuevo" :adaptive="true" height="450">
      <div class="card m-4">
        <div class="card-header">
          <h4 class="fw-bold text-primary">Nuevo punto</h4>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Punto de salida</label>
            <input v-model="nuevo.punto" class="form-control" type="text" />
          </div>
          <div class="form-group">
            <label>Ubicación</label>
            <textarea v-model="nuevo.ubicacion" class="form-control" rows="8" />
          </div>
          <div class="form-group">
            <label>Link ubicación</label>
            <textarea v-model="nuevo.link_mapa" class="form-control" rows="8" />
          </div>
          <div class="text-end">
            <button
              @click="set_punto"
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
    <modal name="modal_image" :adaptive="true" height="600">
      <div class="m-4">
        <h4 class="fw-bold text-primary mb-4">Fotografía</h4>
        <div v-if="index_selected != null">
          <div class="form-group">
            <h4>{{ puntos_salida[index_selected].punto_salida }}</h4>
          </div>
          <div
            v-if="puntos_salida[index_selected].foto != null"
            class="mt-3 mb-3 text-center"
          >
            <img
              v-bind:src="puntos_salida[index_selected].foto"
              class="img-thumbnail"
              style="max-width: 200px"
            />
          </div>
          <div class="form-group">
            <label>Fotografía</label>
            <input @change="handle_images" class="form-control" type="file" />
          </div>

          <div class="text-end">
            <button @click="upload" class="btn btn-success btn-sm rounded-pill">
              <i class="fa fa-upload"></i> Subir
            </button>
            <button
              @click="$modal.hide('modal_image')"
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
  name: "Puntos_salida",
  metaInfo: {
    title: "Puntos de salida de la actividad - Backoffice",
  },
  data() {
    return {
      loading: true,
      loading_upload: false,
      id_servicio: null,
      id_actividad: null,
      search: null,
      puntos_salida: [],
      index_selected: null,
      nuevo: {
        punto: null,
        ubicacion: null,
        link_mapa: null,
      },
      file: [],
      hay_archivo: false,
      files: [],
    };
  },
  props: {
    session: Object,
    actividad: Object,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.get_puntos_salida();
  },
  methods: {
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
    set_punto: function () {
      const vm = this;

      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_actividad: this.id_actividad,
        punto: this.nuevo.punto,
        ubicacion: this.nuevo.ubicacion,
        link_mapa: this.nuevo.link_mapa,
      };
      axios
        .post(this.$base_url + "actividades/set_punto_salida", params, {
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
          vm.get_puntos_salida();
          vm.nuevo = {
            desde: null,
            hasta: null,
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
    update_punto: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        punto: this.puntos_salida[this.index_selected],
      };
      axios
        .post(this.$base_url + "actividades/update_punto_salida", params, {
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
          vm.$modal.hide("modal_editar");
          vm.index_selected = null;
          vm.get_puntos_salida();
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
    delete_punto: function (index) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_punto: this.puntos_salida[index].id_punto_salida,
      };
      axios
        .post(this.$base_url + "actividades/delete_punto_salida", params, {
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
          vm.get_puntos_salida();
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
    handle_images(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        this.hay_archivo = false;
        return;
      }
      this.file = files;
      this.hay_archivo = true;
    },
    upload: function () {
      this.hay_archivo = false;
      this.loading_upload = true;
      var formData = new FormData();
      formData.append("files", this.file[0]);
      formData.append("token", this.session.usuario.token_session);
      formData.append("id_servicio", this.id_servicio);
      formData.append(
        "id_punto_salida",
        this.puntos_salida[this.index_selected].id_punto_salida
      );
      const vm = this;
      axios
        .post(this.$base_url + "upload/foto_ubicacion", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          if (response.data.status) {
            vm.hay_archivo = false;
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Excelente!",
              text: "Se ha subido la fotografía.",
            });
            vm.loading_upload = false;
            vm.get_puntos_salida();
            vm.$modal.hide("modal_image");
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Ups!",
              text: response.data.message,
            });
            vm.loading_upload = false;
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Ups",
            text: error,
          });
          console.log(error);
          vm.loading_upload = false;
        });
    },
  },
};
</script>