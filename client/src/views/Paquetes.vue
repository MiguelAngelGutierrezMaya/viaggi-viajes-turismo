<template>
  <div id="paquetes">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Paquetes</h4>
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
              <button data-bs-toggle="modal" data-bs-target="#nueva_paquete"
                class="btn btn-success btn-sm btn-rounded btn-icon-text">
                <i class="fa fa-plus-circle"></i> Nuevo
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_paquetes" type="text" class="form-control"
                placeholder="Buscar paquete" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="paquetes.length != 0" class="mt-3">
              <div class="card card-body card-reservas shadow border mb-3" v-for="(paquete, index_paquete) in paquetes"
                v-bind:key="index_paquete">
                <div class="text-end">
                  <label v-if="paquete.estado_servicio == 1" class="text-success">Activa</label>
                  <label v-if="paquete.estado_servicio == 0" class="text-danger">Inactiva</label>
                </div>
                <div class="form-group">
                  <small>Paquete</small>
                  <h6>
                    <router-link v-bind:to="'/paquete/' + paquete.id_servicio">
                      <strong>{{ paquete.servicio }}</strong></router-link>
                  </h6>
                </div>
                <div class="text-end">
                  <router-link v-bind:to="'/paquete/' + paquete.id_servicio" class="btn btn-primary btn-rounded btn-xs"
                    title="Detalles">
                    <i class="fa fa-list"></i>
                  </router-link>

                  <button v-if="paquete.estado_servicio == 0 && permiso" v-on:click="update_estado_paquete(index_paquete)"
                    class="btn btn-warning btn-rounded btn-xs ms-1" title="Activar">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button v-if="paquete.estado_servicio == 1 && permiso" v-on:click="update_estado_paquete(index_paquete)"
                    class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Desactivar">
                    <i class="fa fa-eye-slash"></i>
                  </button>
                  <button v-if="paquete.destacado == 1 && permiso" v-on:click="update_destacada_paquete(index_paquete)"
                    class="btn btn-warning btn-rounded btn-xs ms-1" title="No destacar">
                    <i class="fa fa-star"></i>
                  </button>
                  <button v-if="paquete.destacado == 0 && permiso" v-on:click="update_destacada_paquete(index_paquete)"
                    class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Destacar">
                    <i class="fa fa-star"></i>
                  </button>
                  <button v-if="permiso" v-on:click="update_oferta(index_paquete)" class="btn  btn-rounded btn-xs ms-1"
                    :class="{ 'btn-warning': paquete.oferta == 1, 'btn-outline-warning': paquete.oferta == 0 }"
                    title="Oferta">
                    <i class="fa fa-tag"></i>
                  </button>
                  <button v-if="permiso" v-on:click="delete_paquete(index_paquete)"
                    class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>

              <div class="text-center mt-3 mb-2" v-html="paginate"></div>
            </div>
            <div v-else>
              <div class="text-muted mt-4">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                paquetes.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="nueva_paquete">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Nueva paquete</h5>
            <button id="close_modal_set" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Destino</label>
              <v-select v-model="nueva.destino" @search="get_ciudades" label="ciudad_pais" :options="ciudades"
                :clearable="false"></v-select>
            </div>
            <div class="form-group">
              <label>Nombre de la paquete</label>
              <input v-model="nueva.servicio" type="text" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="set_paquete" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
  name: "Paquetes",
  metaInfo: {
    title: "Paquetes - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      paquetes: [],
      ciudades: [],
      paginate: null,
      nueva: {
        destino: null,
        servicio: null,
      },
      paquete_sel: null,
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_paquetes();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_paquetes: function () {
      const vm = this;
      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/paquetes/1");
        }
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          page: this.page,
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "servicios/paquetes", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.paquetes = response.data.data.paquetes;
            vm.get_paginate(response.data.data.total_pages);
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
    get_paginate: function (tpages) {
      var page = this.page;

      if (page === undefined || page <= 0) {
        this.page = 1;
        page = this.page;
      }

      var adjacents = 4;

      var paginate = null;

      paginate = '<ul class="pagination justify-content-center text-center">';
      // previous label
      if (page == 1) {
        paginate +=
          "<li class='paginate_button page-item previous disabled'><a class='page-link disabled'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else if (page == 2) {
        paginate +=
          "<li class='paginate_button page-item  previous'><a href='#/paquetes/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/paquetes/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/paquetes/1'>1</a></li>";
      }
      // interval
      if (page > parseFloat(adjacents) + 2) {
        paginate += "<li class='paginate_button page-item'> <a> ... </a> </li>";
      }
      // pages
      var pmin =
        page > adjacents ? parseFloat(page) - parseFloat(adjacents) : 1;
      var pmax =
        page < parseFloat(tpages) - parseFloat(adjacents)
          ? parseFloat(page) + parseFloat(adjacents)
          : tpages;

      for (var i = pmin; i <= pmax; i++) {
        if (i == page) {
          paginate +=
            "<li class='paginate_button page-item active'><a href='#/paquetes/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/paquetes/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/paquetes/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        }
      }
      // interval
      if (page < parseFloat(tpages) - parseFloat(adjacents) - 1) {
        paginate += "<li class='paginate_button page-item'> <a> ... </a> </li>";
      }
      // last
      if (page < parseFloat(tpages) - parseFloat(adjacents)) {
        paginate +=
          "<li class='paginate_button page-item'><a  href='#/paquetes/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/paquetes/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
    },
    get_ciudades: function (search) {
      if (search.length < 3) {
        return false;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
      };
      let config = {
        headers: headers,
        params: {
          search: search,
        },
      };
      axios
        .get(vm.$base_url + "destinos/ciudades", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.ciudades = response.data.data.ciudades;
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
    get_destinos: function (search) {
      if (search.length <= 3) {
        return false;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
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
          if (response.data.status) {
            vm.destinos = response.data.data.destinos;
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
    set_paquete: function () {
      const vm = this;
      var valida = true;

      if (
        this.nueva.servicio == null ||
        this.nueva.servicio.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Paquetes",
          text: "Digita el nombre de la paquete.",
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
        destino: this.nueva.destino,
        servicio: this.nueva.servicio,
      };
      axios
        .post(this.$base_url + "servicios/set_paquete", params, {
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
          vm.get_paquetes();
          vm.nueva = {
            destino: null,
            servicio: null,
          };
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_estado_paquete: function (index_paquete) {
      var estado = 1;
      if (this.paquetes[index_paquete].estado_servicio == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.paquetes[index_paquete].id_servicio,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_estado_servicio", params, {
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
          }
          vm.get_paquetes();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_destacada_paquete: function (index_paquete) {
      var estado = 1;
      if (this.paquetes[index_paquete].destacada == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_paquete: this.paquetes[index_paquete].id_paquete,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_destacada_paquete", params, {
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
          }
          vm.get_paquetes();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_oferta: function (index_paquete) {
      var estado = 1;
      if (this.paquetes[index_paquete].oferta == 1) {
        estado = 0;
      }
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.paquetes[index_paquete].id_servicio,
        estado: estado,
      };
      axios
        .post(this.$base_url + "servicios/update_oferta", params, {
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
          }
          vm.get_paquetes();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_paquete: function (index_paquete) {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_servicio: this.paquetes[index_paquete].id_servicio,
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
          vm.get_paquetes();
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
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_paquetes();
    },
  },
};
</script>
