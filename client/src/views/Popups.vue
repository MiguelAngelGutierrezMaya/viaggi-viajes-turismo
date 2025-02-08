<template>
  <div id="galeria_actividad">
    <div v-if="loading" class="text-center">
      <div class="loadingio-spinner-ripple-6armq16mc04">
        <div class="ldio-2tzb8vdk7pu">
          <div></div>
          <div></div>
        </div>
      </div>
    </div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Popup</h4>
      <div class="card ms-2">
        <div class="card-body">
          <div>
            <UploadImages
              @changed="handle_images"
              :max="1"
              uploadMsg="Actualiza la imagen del popup"
              maxError="Solo se permite subir una imagen."
              fileError="Solo se permiten imágenes en formato jpg, jpeg o png"
              clearAll="Quitar"
            />
            <div v-if="popup != null" class="row mb-3 mt-3">
              <div class="col-5">
                <img v-bind:src="base_url + img" class="img-thumbnail" />
              </div>
            </div>
            <div class="mt-4 mb-3 row">
              <div class="col-3 form-group">
                <label>Desde</label>
                <input v-model="desde" class="form-control" type="date" />
              </div>
              <div class="col-3 form-group">
                <label>Hasta</label>
                <input v-model="hasta" class="form-control" type="date" />
              </div>
              <div class="col-6 form-group">
                <label>Url</label>
                <input v-model="url" class="form-control" type="text" />
              </div>
            </div>
            <div class="text-end mt-2">
              <button v-on:click="upload" class="btn btn-success btn-sm">
                <i class="fa fa-save"></i> Actualizar
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
import UploadImages from "vue-upload-drop-images";
import "vue-select/dist/vue-select.css";
export default {
  name: "Popup",
  metaInfo: {
    title: "Popup - Backoffice",
  },
  components: {
    UploadImages,
  },
  data() {
    return {
      loading: true,
      loading_upload: false,
      base_url: this.$base_url,
      files: [],
      img: null,
      url: null,
      desde: null,
      hasta: null,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.get_popup();
  },
  methods: {
    get_popup: function () {
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
        .get(vm.$base_url + "servicios/popup", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.popup = response.data.data.popup;
            if (vm.popup != null) {
              vm.desde = vm.popup.desde;
              vm.hasta = vm.popup.hasta;
              vm.url = vm.popup.url;
              vm.img = vm.popup.img;
            }
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Popup",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Popup",
            text: error,
          });
        });
    },
    handle_images(files) {
      this.files = files;
    },
    upload: function () {
      const vm = this;
      vm.loading_upload = true;
      var formData = new FormData();
      for (let file of this.files) {
        formData.append("files[]", file);
      }
      formData.append("token", vm.session.usuario.token_session);
      formData.append("desde", vm.desde);
      formData.append("hasta", vm.hasta);
      formData.append("url", vm.url);
      axios
        .post(this.$base_url + "upload/popup", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          if (response.data.status) {
            location.reload();
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Popup",
              text: response.data.message,
            });
            vm.loading_upload = false;
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Popup",
            text: error,
          });
          console.log(error);
          vm.loading_upload = false;
        });
    },
    delete_popup: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {};
      axios
        .post(this.$base_url + "servicios/delete_popup", params, {
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
              text: "Se ha eliminado la imagen de la galería.",
            });
          }
          vm.get_galeria();
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
