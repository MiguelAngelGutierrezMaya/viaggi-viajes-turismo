<template>
  <div id="cupones">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Cupones</h4>
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
              <button v-on:click="(nuevo_success = false), (nuevo_cupon = null)" data-bs-toggle="modal"
                data-bs-target="#nuevo" class="btn btn-success btn-sm btn-rounded btn-icon-text">
                <i class="fa fa-plus-circle"></i> Nuevo cupón
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_cupones" type="text" class="form-control"
                placeholder="Buscar cupón" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="cupones.length != 0" class="table-responsive mt-3">
              <table class="table table-hover table-condensed my_table_1">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 3%">#</th>
                    <th style="width: 20%">Código</th>
                    <th style="width: 10%" class="text-center">
                      Por actividad
                    </th>
                    <th style="width: 10%" class="text-center">Tipo</th>
                    <th style="width: 5%" class="text-center">Descuento</th>
                    <th class="text-center" style="width: 5%">Vencimiento</th>
                    <th class="text-center" style="width: 5%">Estado</th>
                    <th v-if="permiso" class="text-center" style="width: 10%">
                      Opciones
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(cupon, index) in cupones" v-bind:key="index">
                    <td class="text-center">{{ cupon.num }}</td>
                    <td>
                      <strong>{{ cupon.codigo }}</strong>
                    </td>
                    <td class="text-center">
                      <span v-if="cupon.por_actividad == 0">No</span>
                      <span v-if="cupon.por_actividad == 1">Sí</span>
                    </td>
                    <td class="text-center">
                      <span v-if="cupon.tipo == 1">Un solo uso</span>
                      <span v-if="cupon.tipo == 2">Múltiples usos</span>
                    </td>
                    <td class="text-center">
                      {{ cupon.descuento | currency }}
                    </td>
                    <td class="text-center">
                      {{ cupon.fecha_vence }}
                    </td>
                    <td class="text-center">
                      <label v-if="cupon.estado == 0" class="text-success">Sin uso</label>
                      <label v-if="cupon.estado == 1" class="text-danger">Usado</label>
                    </td>
                    <td v-if="permiso" class="text-center">
                      <button v-on:click="index_sel = index" data-bs-toggle="modal" data-bs-target="#editar"
                        class="btn btn-info btn-rounded btn-xs" title="Detalles">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button v-if="cupon.por_actividad == 1" v-on:click="(index_sel = index), get_actividades()"
                        data-bs-toggle="modal" data-bs-target="#actividades" class="btn btn-primary btn-rounded btn-xs"
                        title="Detalles">
                        <i class="fa fa-list"></i>
                      </button>
                      <button v-on:click="index_sel = index" data-bs-toggle="modal" data-bs-target="#eliminar"
                        class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
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
                cupones.
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
            <h5 class="modal-title fw-bolder">Nuevo cupón</h5>
            <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Código del cupón </label>
              <input v-model="nuevo.codigo" type="text" class="form-control" />
            </div>

            <div class="form-group">
              <label>Tipo</label>
              <select v-model="nuevo.tipo" class="form-control">
                <option value="1">Un solo uso</option>
                <option value="2">Uso múltiple</option>
              </select>
            </div>
            <div class="form-group">
              <label>Valor descuento por persona </label>
              <money v-model="nuevo.descuento" v-bind="money" class="form-control text-end"></money>
            </div>
            <div class="form-group">
              <label>Fecha de vencimiento </label>
              <input v-model="nuevo.fecha_vence" type="datetime-local" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="set_cupon" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
            <h5 class="modal-title fw-bolder">Editar cupón</h5>
            <button id="close_editar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="form-group">
              <label>Código del cupón </label>
              <input v-model="cupones[index_sel].codigo" type="text" class="form-control" />
            </div>

            <div class="form-group">
              <label>Tipo</label>
              <select v-model="cupones[index_sel].tipo" class="form-control">
                <option value="1">Un solo uso</option>
                <option value="2">Uso múltiple</option>
              </select>
            </div>
            <div class="form-group">
              <label>Valor descuento por persona </label>

              <money v-model="cupones[index_sel].descuento" v-bind="money" class="form-control text-end"></money>
            </div>
            <div class="form-group">
              <label>Fecha de vencimiento </label>
              <input v-model="cupones[index_sel].fecha_vence" type="datetime-local" class="form-control" />
            </div>
            <div class="text-end">
              <button v-on:click="update_cupon" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
            <h5 class="modal-title fw-bolder text-danger">Eliminar cupón</h5>
            <button id="close_eliminar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="text-danger">
              ¿Desea eliminar el cupón
              <strong>
                {{ cupones[index_sel].codigo }}
              </strong>
              ?
            </div>
            <div class="text-end mt-4">
              <button v-on:click="delete_cupon" class="btn btn-danger btn-rounded btn-sm btn-icon-text">
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
    <div class="modal fade" id="actividades">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Actividades</h5>
            <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="index_sel != null">
              <div class="card card-body">
                <h5 class="text-primary">Agregar actividad</h5>
                <div class="form-group">
                  <label>Actividad</label>
                  <v-select v-model="actividad_cupon" @search="get_actividades" @input="set_actividad_cupon"
                    label="servicio" :options="actividades" :clearable="false"></v-select>
                </div>
              </div>
              <div class="card card-body mt-3">
                <h5 class="text-success">Actividades agregadas</h5>
                <table v-if="cupones[index_sel].actividades != null"
                  class="table table-bordered table-condensed table-hover my_table_1">
                  <thead>
                    <tr>
                      <th>Actividad</th>
                      <th class="text-center">Quitar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(actividad, index_actividad) in cupones[index_sel]
                      .actividades" v-bind:key="index_actividad">
                      <td>{{ actividad.actividad }}</td>
                      <td class="text-center">
                        <button v-on:click="quitar_actividad(index_actividad)" class="btn btn-danger btn-sm rounded-pill">
                          <i class="fa fa-times"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else class="text-muted">
                  <i class="fa fa-info-circle"></i> No se han agregado
                  actividades.
                </div>
              </div>
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
  name: "Cupones",
  metaInfo: {
    title: "Cupones - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      cupones: [],
      paginate: null,
      nuevo: {
        codigo: null,
        tipo: null,
        por_actividad: 0,
        descuento: 0,
        fecha_vence: null,
      },
      nuevo_success: false,
      index_sel: null,
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        suffix: "",
        precision: 0,
        masked: false,
      },
      actividades: [],
      actividad_cupon: [],
      permiso: false,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_cupones();
    this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
  },
  methods: {
    get_cupones: function () {
      const vm = this;

      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/cupones/1");
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
        .get(vm.$base_url + "admin/cupones", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.cupones = response.data.data.cupones;
            vm.get_paginate(response.data.data.total_pages);
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Cupones",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Cupones",
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
    set_cupon: function () {
      const vm = this;
      vm.nuevo_success = false;
      var valida = true;
      if (this.nuevo.codigo == null || this.nuevo.codigo.trim().length == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el código del cupón.",
        });
        valida = false;
      }

      if (this.nuevo.descuento <= 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "El descuento debe ser mayor a cero.",
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
        .post(this.$base_url + "admin/set_cupon", params, {
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
              title: "Cupón creado",
              text: response.data.message,
            });
            vm.nuevo = {
              codigo: null,
              tipo: null,
              descuento: 0,
              fecha_vence: null,
            };
            vm.get_cupones();
          }
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_cupon: function () {
      const vm = this;
      var valida = true;
      if (
        this.cupones[this.index_sel].codigo == null ||
        this.cupones[this.index_sel].codigo.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el código del cupón.",
        });
        valida = false;
      }

      if (this.cupones[this.index_sel].descuento <= 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "El descuento debe ser mayor a cero.",
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
        data: this.cupones[this.index_sel],
      };
      axios
        .post(this.$base_url + "admin/update_cupon", params, {
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
            vm.get_cupones();
            document.getElementById("close_editar").click();
          }

          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_cupon: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_cupon: this.cupones[this.index_sel].id_cupon,
      };
      axios
        .post(this.$base_url + "admin/delete_cupon", params, {
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
              title: "Cupón eliminado",
              text: response.data.message,
            });
          }
          vm.get_cupones();
          vm.index_sel = null;
          vm.loading = false;
          document.getElementById("close_eliminar").click();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    quitar_actividad: function (index_actividad) {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_cupon: this.cupones[this.index_sel].id_cupon,
        id_servicio:
          this.cupones[this.index_sel].actividades[index_actividad].id_servicio,
      };
      axios
        .post(this.$base_url + "admin/delete_actividad_cupon", params, {
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
              title: "Actividad removida",
              text: response.data.message,
            });
          }
          vm.get_cupones();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    get_actividades: function (search = null) {
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
        .get(vm.$base_url + "servicios/actividades_list", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.actividades = response.data.data.actividades;
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
    set_actividad_cupon: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_cupon: this.cupones[this.index_sel].id_cupon,
        id_servicio: this.actividad_cupon.id_servicio,
      };
      axios
        .post(this.$base_url + "admin/set_actividad_cupon", params, {
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
              title: "Actividad agregada",
              text: response.data.message,
            });
          }
          vm.get_cupones();
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
      this.get_cupones();
    },
  },
};
</script>
