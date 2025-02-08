<template>
  <div id="recursos">
    <div v-if="loading"  id="loading" class="loading_full">Loading&#8230;</div>
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Recursos</h4>
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
            <div v-if="recursos.length != 0" class="table-responsive mt-3">
              <table class="table table-hover table-condensed my_table_1">
                <thead class="table-light">
                  <tr>
                    <th style="width: 20%">Recurso</th>
                    <th style="width: 20%">Enlace</th>
                    <th class="text-center" style="width: 5%">Estado</th>
                    <th class="text-center" style="width: 10%">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(recurso, index) in recursos" v-bind:key="index">
                    <td>
                      <strong>{{ recurso.descripcion }}</strong>
                    </td>
                    <td>
                      <strong>{{ recurso.enlace }}</strong>
                    </td>
                    <td class="text-center">
                      <label v-if="recurso.estado == 0" class="text-danger">Inactivo</label>
                      <label v-if="recurso.estado == 1" class="text-success">Activo</label>
                    </td>
                    <td class="text-center">
                      <button v-on:click="index_sel = index" data-bs-toggle="modal" data-bs-target="#editar"
                        class="btn btn-info btn-rounded btn-xs" title="Detalles">
                        <i class="fa fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                recursos.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar</h5>
            <button id="close_editar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="row">
              <div v-if="recursos[index_sel].url != null" class="col-lg-12 mb-4 text-central">
                <img v-bind:src="recursos[index_sel].url" class="img-thumbnail mx-auto" style="max-width: 400px" />
              </div>
              <div v-else class="col-lg-12 mb-4">
                <div class="text-muted">
                  <i class="fa fa-info-circle"></i> No se ha agregado archivo
                  para este recurso.
                </div>
              </div>
              <div class="form-group col-lg-12">
                <label>Fotografía</label>
                <input @change="handle_images" class="form-control" type="file" />
              </div>
              <div v-if="recursos[index_sel].tipo == 1" class="form-group col-lg-12">
                <label>Botón</label>
                <input v-model="recursos[index_sel].boton" type="text" class="form-control">
              </div>
              <div v-if="recursos[index_sel].tipo == 1" class="form-group col-lg-12">
                <label>Enlace</label>
                <input v-model="recursos[index_sel].enlace" type="text" class="form-control">
              </div>

              <div class="col-lg-12 form-group">
                <label>Estado</label>
                <select v-model="recursos[index_sel].estado" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            </div>
            <div class="text-end">
              <button @click="update_recurso" class="btn btn-success btn-rounded btn-sm btn-icon-text">
                <i class="fa fa-save"></i> Guardar
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                Cancelar
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
import "vue-select/dist/vue-select.css";
export default {
  name: "Recursos",
  metaInfo: {
    title: "Recursos - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      index_sel: null,
      recursos: [],
      file: [],
      hay_archivo: false,
      files: [],
      loading_upload: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.get_recursos();
  },
  methods: {
    get_recursos: function () {
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
        .get(vm.$base_url + "admin/recursos", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.recursos = response.data.data.recursos;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Recursos",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Recursos",
            text: error,
          });
          vm.loading = false;
        });
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
    update_recurso: function () {
      this.hay_archivo = false;
      this.loading = true;
      var formData = new FormData();
      formData.append("files", this.file[0]);
      formData.append("token", this.session.usuario.token_session);
      formData.append("id_recurso", this.recursos[this.index_sel].id_recurso);
      formData.append("enlace", this.recursos[this.index_sel].enlace);
      formData.append("boton", this.recursos[this.index_sel].boton);
      formData.append("estado", this.recursos[this.index_sel].estado);
      const vm = this;
      axios
        .post(this.$base_url + "upload/recurso", formData, {
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
              text: "Se ha actualizado el recurso.",
            });
            vm.loading = false;
            vm.get_recursos();
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Ups!",
              text: response.data.message,
            });
            vm.loading = false;
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
          vm.loading = false;
        });
    },
  },
};
</script>
