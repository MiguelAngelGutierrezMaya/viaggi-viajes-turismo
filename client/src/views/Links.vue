<template>
  <div id="links">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Directorio</h4>
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
            <div v-if="permiso" class="text-end">
              <button v-on:click="(nuevo_success = false), (nuevo_link = null)" data-bs-toggle="modal"
                data-bs-target="#nuevo" class="btn btn-success btn-sm btn-rounded btn-icon-text">
                <i class="fa fa-plus-circle"></i> Nuevo link
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_links" type="text" class="form-control" placeholder="Buscar link" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="links.length != 0" class="mt-3">
              <div class="card card-body border shadow mb-3" v-for="(link, index) in links" v-bind:key="index">
                <div @click="go(link.link)" class="row card-link mb-0">
                  <div class="col-lg-1">
                    <i class="fa fa-link fw-bold text-primary" style="font-size: 1.2em"></i>
                  </div>
                  <div class="col-lg-11">
                    <h6 class="text-primary mb-0">{{ link.nombre }}</h6>
                    <small style="font-size: 0.8em">
                      {{ link.descripcion }}
                    </small>
                  </div>
                </div>
                <div class="text-end mt-0">
                  <button v-if="permiso" @click="index_sel = index" data-bs-toggle="modal" data-bs-target="#editar"
                    class="btn btn-primary btn-rounded btn-xs ms-1" title="Editar">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button v-if="permiso" @click="index_sel = index" data-bs-toggle="modal" data-bs-target="#eliminar"
                    class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                links.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nuevo">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Nuevo link</h5>
            <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre </label>
              <input v-model="nuevo.nombre" type="text" class="form-control" />
            </div>

            <div class="form-group">
              <label>Descripción</label>
              <textarea v-model="nuevo.descripcion" class="form-control" />
            </div>
            <div class="form-group">
              <label>Link </label>
              <input v-model="nuevo.link" type="text" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="set_link" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
    <div class="modal fade" id="editar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Editar link</h5>
            <button id="close_editar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="form-group">
              <label>Nombre </label>
              <input v-model="links[index_sel].nombre" type="text" class="form-control" />
            </div>

            <div class="form-group">
              <label>Descripción</label>
              <textarea v-model="links[index_sel].descripcion" class="form-control" />
            </div>
            <div class="form-group">
              <label>Link </label>
              <input v-model="links[index_sel].link" type="text" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="update_link" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
    <div class="modal fade" id="eliminar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder text-danger">Eliminar link</h5>
            <button id="close_eliminar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="text-danger">
              ¿Desea eliminar el link de
              <strong>
                {{ links[index_sel].nombre }}
              </strong>
              ?
            </div>
            <div class="text-end mt-4">
              <button v-on:click="delete_link" class="btn btn-danger btn-rounded btn-sm btn-icon-text">
                Sí
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                No
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
  name: "Links",
  metaInfo: {
    title: "Links - Backoffice",
  },
  data() {
    return {
      loading: true,
      links: [],
      search: null,
      nuevo: {
        link: null,
        nombre: null,
        descripcion: null,
      },
      nuevo_success: false,
      index_sel: null,
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.get_links();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_links: function () {
      const vm = this;

      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          page: this.page,
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "admin/links", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.links = response.data.data.links;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Error",
            text: error,
          });
          vm.loading = false;
        });
    },
    go(link) {
      window.open(link, "_blank");
    },
    set_link: function () {
      const vm = this;
      vm.nuevo_success = false;
      var valida = true;
      if (this.nuevo.nombre == null || this.nuevo.nombre.trim().length == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita un nombre para el link.",
        });
        valida = false;
      }
      if (this.nuevo.link == null || this.nuevo.link.trim().length == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita la URL.",
        });
        valida = false;
      }

      if (!valida) {
        return false;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        data: this.nuevo,
      };
      axios
        .post(this.$base_url + "admin/set_link", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Directorio",
              text: response.data.message,
            });
            vm.nuevo = {
              titulo: null,
              descripcion: null,
              link: null,
            };
            vm.get_links();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_link: function () {
      const vm = this;
      var valida = true;

      if (
        this.links[this.index_sel].nombre == null ||
        this.links[this.index_sel].nombre.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita un nombre para el link.",
        });
        valida = false;
      }
      if (
        this.links[this.index_sel].link == null ||
        this.links[this.index_sel].link.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita la URL.",
        });
        valida = false;
      }

      if (!valida) {
        return false;
      }
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      var params = {
        data: this.links[this.index_sel],
      };
      axios
        .post(this.$base_url + "admin/update_link", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              type: "error",
              group: "foo",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "Bien!",
              text: response.data.message,
            });
            vm.get_links();
            document.getElementById("close_editar").click();
          }

          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_link: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_link: this.links[this.index_sel].id_link,
      };
      axios
        .post(this.$base_url + "admin/delete_link", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Error",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Directorio",
              text: response.data.message,
            });
          }
          vm.get_links();
          vm.index_sel = null;
          vm.loading = false;
          document.getElementById("close_eliminar").click();
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
};
</script>
