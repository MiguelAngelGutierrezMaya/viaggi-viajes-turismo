<template>
  <div id="proveedores">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Proveedores</h4>
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
              <button
                v-on:click="(nuevo_success = false), (nuevo_proveedor = null)"
                data-bs-toggle="modal"
                data-bs-target="#nuevo"
                class="btn btn-success btn-sm btn-rounded btn-icon-text"
              >
                <i class="fa fa-plus-circle"></i> Nuevo proveedor
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input
                v-model="search"
                v-on:keyup="get_proveedores"
                type="text"
                class="form-control"
                placeholder="Buscar proveedor"
              />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="proveedores.length != 0" class="table-responsive mt-3">
              <table class="table table-hover table-condensed my_table_1">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 3%">#</th>
                    <th style="width: 20%">Proveedor</th>
                    <th style="width: 20%">Servicios</th>
                    <th class="text-center" style="width: 5%">Estado</th>
                    <th v-if="permiso" class="text-center" style="width: 10%">
                      Opciones
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(proveedor, index) in proveedores"
                    v-bind:key="index"
                  >
                    <td class="text-center">{{ index + 1 }}</td>
                    <td>
                      <strong>{{ proveedor.proveedor }}</strong>
                    </td>
                    <td>
                      <span
                        class="badge bg-secondary"
                        v-for="(tipo, index_tipo) in proveedor.tipos_servicio"
                        v-bind:key="index_tipo"
                      >
                        {{ tipo.tipo_servicio }}
                      </span>
                    </td>
                    <td class="text-center">
                      <label v-if="proveedor.estado == 0" class="text-danger"
                        >Inactivo</label
                      >
                      <label v-if="proveedor.estado == 1" class="text-success"
                        >Activo</label
                      >
                    </td>
                    <td v-if="permiso" class="text-center">
                      <button
                        v-on:click="index_sel = index"
                        data-bs-toggle="modal"
                        data-bs-target="#editar"
                        class="btn btn-info btn-rounded btn-xs"
                        title="Detalles"
                      >
                        <i class="fa fa-edit"></i>
                      </button>

                      <button
                        v-on:click="index_sel = index"
                        data-bs-toggle="modal"
                        data-bs-target="#eliminar"
                        class="btn btn-danger btn-rounded btn-xs ms-1"
                        title="Eliminar"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="text-center mt-3 mb-2" v-html="paginate"></div>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                proveedores.
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
            <h5 class="modal-title fw-bolder">Nuevo proveedor</h5>
            <button
              id="close_nuevo"
              type="button"
              class="close"
              data-bs-dismiss="modal"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Proveedor </label>
              <input
                v-model="nuevo.proveedor"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-lg-12 form-group">
              <label>Tipos de actividad</label>
              <v-select
                multiple
                v-model="nuevo.tipos_servicio"
                label="tipo_servicio"
                :options="tipos_servicio"
              ></v-select>
            </div>
            <div class="col-lg-12 form-group">
              <label>Estado</label>
              <select v-model="nuevo.estado" class="form-control">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
            <div class="text-end">
              <button
                v-on:click="set_proveedor"
                class="btn btn-success btn-rounded btn-sm btn-icon-text"
              >
                <i class="fa fa-save"></i> Guardar
              </button>
              <button
                data-bs-dismiss="modal"
                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1"
              >
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
            <h5 class="modal-title fw-bolder">Editar proveedor</h5>
            <button
              id="close_editar"
              type="button"
              class="close"
              data-bs-dismiss="modal"
            >
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="form-group">
              <label>Proveedor </label>
              <input
                v-model="proveedores[index_sel].proveedor"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-lg-12 form-group">
              <label>Tipos de actividad</label>
              <v-select
                multiple
                v-model="proveedores[index_sel].tipos_servicio"
                label="tipo_servicio"
                :options="tipos_servicio"
              ></v-select>
            </div>
            <div class="col-lg-12 form-group">
              <label>Estado</label>
              <select
                v-model="proveedores[index_sel].estado"
                class="form-control"
              >
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
            <div class="text-end">
              <button
                v-on:click="update_proveedor"
                class="btn btn-success btn-rounded btn-sm btn-icon-text"
              >
                <i class="fa fa-save"></i> Guardar
              </button>
              <button
                data-bs-dismiss="modal"
                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1"
              >
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
            <h5 class="modal-title fw-bolder text-danger">
              Eliminar proveedor
            </h5>
            <button
              id="close_eliminar"
              type="button"
              class="close"
              data-bs-dismiss="modal"
            >
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="text-danger">
              ¿Desea eliminar el proveedor
              <strong>
                {{ proveedores[index_sel].proveedor }}
              </strong>
              ?
            </div>
            <div class="text-end mt-4">
              <button
                v-on:click="delete_proveedor"
                class="btn btn-danger btn-rounded btn-sm btn-icon-text"
              >
                Sí
              </button>
              <button
                data-bs-dismiss="modal"
                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1"
              >
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
  name: "Proveedores",
  metaInfo: {
    title: "Proveedores - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      proveedores: [],
      paginate: null,
      nuevo: {
        proveedor: null,
        tipos_servicio: [],
        estado: 1,
      },
      nuevo_success: false,
      index_sel: null,
      tipos_servicio: [
        {
          id_tipo_servicio: 1,
          tipo_servicio: "Actividades",
        },
        {
          id_tipo_servicio: 2,
          tipo_servicio: "Paquetes",
        },
        {
          id_tipo_servicio: 3,
          tipo_servicio: "Hoteles",
        },
        {
          id_tipo_servicio: 4,
          tipo_servicio: "Tiquetes",
        },
        {
          id_tipo_servicio: 5,
          tipo_servicio: "Asistencias",
        },
        {
          id_tipo_servicio: 6,
          tipo_servicio: "Otros",
        },
      ],
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_proveedores();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_proveedores: function () {
      const vm = this;

      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/proveedores/1");
        }
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
        .get(vm.$base_url + "proveedores/proveedores", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.proveedores = response.data.data.proveedores;
            //vm.get_paginate(response.data.data.total_pages);
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Proveedores",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Proveedores",
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
          "<li class='paginate_button page-item  previous'><a href='#/usuarios/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/usuarios/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/usuarios/1'>1</a></li>";
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
            "<li class='paginate_button page-item active'><a href='#/usuarios/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/usuarios/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/usuarios/" +
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
          "<li class='paginate_button page-item'><a  href='#/usuarios/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/usuarios/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
    },
    set_proveedor: function () {
      const vm = this;
      vm.nuevo_success = false;
      
      if (
        this.nuevo.proveedor == null ||
        this.nuevo.proveedor.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el proveedor.",
        });
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
        .post(this.$base_url + "proveedores/set_proveedor", params, {
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
              title: "Proveedor creado",
              text: response.data.message,
            });
            vm.nuevo = {
              proveedor: null,
              tipos_servicio: [],
              estado: 1,  
            };
            vm.get_proveedores();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_proveedor: function () {
      const vm = this;
      
      if (
        this.proveedores[this.index_sel].proveedor == null ||
        this.proveedores[this.index_sel].proveedor.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el nombre del proveedor.",
        });
        return false;
      }

      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      var params = {
        proveedor: this.proveedores[this.index_sel],
      };
      axios
        .post(this.$base_url + "proveedores/update_proveedor", params, {
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
            vm.get_proveedores();
            document.getElementById("close_editar").click();
          }

          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_proveedor: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_proveedor: this.proveedores[this.index_sel].id_proveedor,
      };
      axios
        .post(this.$base_url + "proveedores/delete_proveedor", params, {
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
              title: "Proveedor eliminado",
              text: response.data.message,
            });
          }
          vm.get_proveedores();
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
