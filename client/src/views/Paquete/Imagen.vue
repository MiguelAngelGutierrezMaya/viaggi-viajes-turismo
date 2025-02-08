<template>
  <div id="imagen_paquete">
    <div v-if="loading"  id="loading" class="loading_full">Loading&#8230;</div>
    <div v-else>
      <h4 class="mb-4 fw-bold text-info">Imagen</h4>

      <hr />
      <div>
        <div v-if="permiso">
          <UploadImages
            @changed="handle_images"
            :max="1"
            uploadMsg="Sube la imagen o flyer del paquete"
            maxError="Solo se permite subir una imágenes del paquete."
            fileError="Solo se permiten imágenes en formato jpg, jpeg o png"
            clearAll="Quitar todas"
          />
          <div class="text-end mt-2">
            <button
              v-on:click="upload"
              class="btn btn-success btn-sm"
              :disabled="files.length == 0"
            >
              <i class="fa fa-upload"></i> Subir imagen
            </button>
          </div>
        </div>

        <div class="mt-4 mb-3">
          <h4 class="fw-bold text-primary">Imagen o flyer del paquete</h4>
          <div v-if="imagen != null" class="row">
            <div class="col-lg-3 text-center">
              <a :href="imagen" target="_blank">
                <img class="img-thumbnail" v-bind:src="imagen" />
              </a>
            </div>
          </div>
          <div v-else class="text-muted mt-4">
            <i class="fa fa-info-circle"></i> No se han agregado imagen o flyer
            del paquete.
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
  name: "Imagen_paquete",
  metaInfo: {
    title: "Imagen del paquete - Backoffice",
  },
  components: {
    UploadImages,
  },
  data() {
    return {
      loading: true,
      loading_upload: false,
      id_servicio: null,
      id_paquete: null,
      imagen: null,
      files: [],
    };
  },
  props: {
    session: Object,
    paquete: Object,
    permiso: Boolean,
  },
  created: function () {
    this.id_servicio = this.$route.params.id_servicio;
    this.id_paquete = this.paquete.id_paquete;
    this.imagen = this.paquete.img;
    this.loading = false;
  },
  methods: {
    handle_images(files) {
      this.files = files;
    },
    upload: function () {
      const vm = this;
      vm.loading = true;
      var formData = new FormData();
      for (let file of this.files) {
        formData.append("files[]", file);
      }
      formData.append("token", vm.session.usuario.token_session);
      formData.append("id_servicio", vm.id_servicio);
      axios
        .post(this.$base_url + "upload/imagen_paquete", formData, {
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
              title: "Paquetes",
              text: response.data.message,
            });
            vm.loading = false;
          }
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquetees",
            text: error,
          });
          console.log(error);
          vm.loading = false;
        });
    },
    delete_img: function (index_galeria) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_galeria: this.galeria[index_galeria].id_galeria,
      };
      axios
        .post(this.$base_url + "servicios/delete_imagen", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Paquetees",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Paquetees",
              text: "Se ha eliminado la imagen de la galería.",
            });
          }
          vm.get_galeria();
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Paquetees",
            text: error,
          });
        })
        .finally();
    },
  },
};
</script>
