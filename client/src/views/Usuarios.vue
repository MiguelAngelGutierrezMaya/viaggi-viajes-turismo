<template>
  <div id="actividades">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Usuarios</h4>
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
            <div class="text-end">
              <button v-on:click="
                (nuevo_success = false),
                (nuevo_usuario = null),
                (nuevo_clave = null)
                " data-bs-toggle="modal" data-bs-target="#nuevo"
                class="btn btn-success btn-sm btn-rounded btn-icon-text">
                <i class="fa fa-plus-circle"></i> Nuevo usuario
              </button>
            </div>
            <div class="input-group mt-2 mb-2 mr-sm-2">
              <input v-model="search" v-on:keyup="get_usuarios" type="text" class="form-control"
                placeholder="Buscar usuario" />
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fa fa-search"></i>
                </div>
              </div>
            </div>
            <div v-if="usuarios.length != 0" class="mt-3">
              <div class="row">
                <div v-for="(usuario, index) in usuarios" v-bind:key="index" class="col-lg-6">
                  <div class="card card-body card-reservas shadow border mt-3">
                    <div class="text-end">
                      <label v-if="usuario.estado == 1" class="text-success">Activo</label>
                      <label v-if="usuario.estado == 0" class="text-danger">Inactivo</label>
                      <label v-if="usuario.estado == 2" class="text-warning">Pendiente</label>
                    </div>
                    <div class="row">
                      <div class="form-group col-lg-6 mt-0 mb-0">
                        <small>Nombres y apellidos</small>
                        <h6 class="text-primary">
                          {{ usuario.nombres }} {{ usuario.apellidos }}
                        </h6>
                      </div>
                      <div class="form-group col-lg-6 mt-0 mb-0">
                        <small>Email</small>
                        <h6 class="text-primary">
                          {{ usuario.email }}
                        </h6>
                      </div>
                      <div class="form-group col-lg-6 mt-0 mb-0">
                        <small>Teléfono</small>
                        <h6 class="text-primary">
                          {{ usuario.telefono }}
                        </h6>
                      </div>
                      <div class="form-group col-lg-6 mt-0 mb-0">
                        <small>Perfil</small>
                        <h6 class="text-primary">
                          <span v-if="usuario.perfil == 1">Admin</span>
                          <span v-if="usuario.perfil == 2">Básico</span>
                        </h6>
                      </div>
                    </div>
                    <div class="text-end">
                      <button v-on:click="index_sel = index" data-bs-toggle="modal" data-bs-target="#editar"
                        class="btn btn-info btn-rounded btn-xs" title="Detalles">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button v-if="usuario.perfil != 1" v-on:click="index_sel = index" data-bs-toggle="modal"
                        data-bs-target="#permisos" class="btn btn-primary btn-rounded btn-xs ms-1" title="Permisos">
                        <i class="fa fa-user-lock"></i>
                      </button>
                      <button  v-on:click="index_sel = index" data-bs-toggle="modal"
                        data-bs-target="#restablecer" class="btn btn-primary btn-rounded btn-xs ms-1" title="Restablecer">
                        <i class="fa fa-key"></i>
                      </button>
                      <button v-if="usuario.estado == 0" v-on:click="update_estado_usuario(index)"
                        class="btn btn-warning btn-rounded btn-xs ms-1" title="Activar">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button v-if="usuario.estado == 1" v-on:click="update_estado_usuario(index)"
                        class="btn btn-outline-warning btn-rounded btn-xs ms-1" title="Desactivar">
                        <i class="fa fa-eye-slash"></i>
                      </button>
                      <button v-on:click="index_sel = index" data-bs-toggle="modal" data-bs-target="#eliminar"
                        class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center mt-3 mb-2" v-html="paginate"></div>
            </div>
            <div v-else>
              <div class="text-muted">
                <i class="fa-solid fa-circle-info"></i> No se han agregado
                usuarios.
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
            <h5 class="modal-title fw-bolder">Nuevo usuario</h5>
            <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <transition appear name="slide-fade">
              <div v-if="nuevo_success" class="alert alert-success">
                <ul>
                  <li>
                    Usuario: <strong>{{ nuevo_usuario }}</strong>
                  </li>
                  <li>
                    Clave: <strong>{{ nuevo_clave }}</strong>
                  </li>
                </ul>
              </div>
            </transition>
            <div class="form-group">
              <label>Nombres </label>
              <input v-model="nuevo.nombres" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Apellidos </label>
              <input v-model="nuevo.apellidos" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input v-model="nuevo.email" type="email" class="form-control" />
            </div>
            <div class="form-group">
              <label>Teléfono </label>
              <input v-model="nuevo.telefono" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Perfil</label>
              <select v-model="nuevo.perfil" class="form-control">
                <option value="1">Admin</option>
                <option value="2">Básico</option>
              </select>
            </div>
            <div class="text-end">
              <button v-on:click="set_usuario" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
            <h5 class="modal-title fw-bolder">Editar usuario</h5>
            <button id="close_editar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="form-group">
              <label>Nombres </label>
              <input v-model="usuarios[index_sel].nombres" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Apellidos </label>
              <input v-model="usuarios[index_sel].apellidos" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input v-model="usuarios[index_sel].email" type="email" class="form-control" />
            </div>
            <div class="form-group">
              <label>Teléfono </label>
              <input v-model="usuarios[index_sel].telefono" type="text" class="form-control" />
            </div>
            <div class="form-group">
              <label>Perfil</label>
              <select v-model="usuarios[index_sel].perfil" class="form-control">
                <option value="1">Admin</option>
                <option value="2">Básico</option>
              </select>
            </div>
            <div class="text-end">
              <button v-on:click="update_usuario" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
    <div class="modal fade" id="restablecer">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Restablecer usuario</h5>
            <button id="close_restablecer" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="text-primary">
              Se restablecerá la contraseña del usuario <strong>   {{ usuarios[index_sel].nombres }}
                {{ usuarios[index_sel].apellidos }}. </strong> La contraseña para su ingreso será: <pre class="bg-light mt-3">Viagg1**</pre> El usuario deberá crear una nueva contraseña tras iniciar sesión.
            </div>
            <div class="text-end mt-4">
              <button v-on:click="restablecer" class="btn btn-success btn-rounded btn-sm btn-icon-text">
                <i class="fa fa-check-circle"></i> Restablecer
              </button>
              <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="permisos">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-success">
            <h5 class="modal-title fw-bolder">Permisos usuario</h5>
            <button id="close_permisos" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <small class="fw-bold">Usuario</small>
            <h6 class="text-primary">
              {{ usuarios[index_sel].nombres }}
              {{ usuarios[index_sel].apellidos }}
            </h6>
            <div class="mt-4">
              <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link fw-bold active" id="nav-home-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                    aria-selected="true">Permisos</button>
                  <button class="nav-link fw-bold" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Carpetas</button>
                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                  <small class="fw-bold">Permisos</small>
                  <div class="ps-4 mb-3">
                    <div v-for="(permiso, index_permiso) in usuarios[index_sel]
                      .permisos" :key="index_permiso" class="form-check">
                      <input v-model="permiso.estado" class="form-check-input" type="checkbox" :id="index_permiso" />
                      <label class="form-check-label" :for="index_permiso">
                        {{ permiso.descripcion }}
                      </label>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                  <small class="fw-bold">Carpetas</small>
                  <div class="ps-4 mb-3">
                    <div v-for="(permiso, index_permiso) in usuarios[index_sel]
                      .permisos_carpetas" :key="index_permiso" class="form-check">
                      <input v-model="permiso.estado" class="form-check-input" type="checkbox" :id="index_permiso" />
                      <label class="form-check-label" :for="index_permiso">
                        {{ permiso.nombre }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-end mt-4">
              <button v-on:click="update_permisos" class="btn btn-success btn-rounded btn-sm btn-icon-text">
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
            <h5 class="modal-title fw-bolder text-danger">Eliminar usuario</h5>
            <button id="close_eliminar" type="button" class="close" data-bs-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div v-if="index_sel != null" class="modal-body">
            <div class="text-danger">
              ¿Desea eliminar al usuario
              <strong>
                {{ usuarios[index_sel].nombres }}
                {{ usuarios[index_sel].apellidos }}
              </strong>
              ?
            </div>
            <div class="text-end mt-4">
              <button v-on:click="delete_usuario" class="btn btn-danger btn-rounded btn-sm btn-icon-text">
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
  name: "Usuarios",
  metaInfo: {
    title: "Usuarios - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      usuarios: [],
      paginate: null,
      nuevo: {
        nombres: null,
        apellidos: null,
        email: null,
        telefono: null,
        perfil: 1,
      },
      nuevo_success: false,
      nuevo_usuario: null,
      nuevo_clave: null,
      index_sel: null,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_usuarios();
  },
  methods: {
    get_usuarios: function () {
      const vm = this;

      var search = null;
      if (this.search != null && this.search.length > 3) {
        search = this.search;
        if (this.page != 1) {
          this.page = 1;
          this.$router.push("/usuarios/1");
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
        .get(vm.$base_url + "admin/usuarios", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.usuarios = response.data.data.usuarios;
            vm.get_paginate(response.data.data.total_pages);
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Usuarios",
              text: response.data.message,
            });
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Usuarios",
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
    set_usuario: function () {
      const vm = this;
      vm.nuevo_success = false;
      vm.nuevo_usuario = null;
      vm.nuevo_clave = null;
      var valida = true;
      if (this.nuevo.nombres == null || this.nuevo.nombres.trim().length == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el nombre del usuario.",
        });
        valida = false;
      }
      if (
        this.nuevo.apellidos == null ||
        this.nuevo.apellidos.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita los apellidos del usuario.",
        });
        valida = false;
      }
      if (this.nuevo.email == null || this.nuevo.email.trim().length == 0) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el correo electrónico del usuario.",
        });
        valida = false;
      }
      var re = /\S+@\S+\.\S+/;
      var valida_email = re.test(this.nuevo.email);
      if (!valida_email) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "Error",
          text: "Digita una dirección de correo electrónico válida.",
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
        .post(this.$base_url + "admin/set_usuario", params, {
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
              title: "Usuario creado",
              text: response.data.message,
            });
          }
          vm.get_usuarios();
          vm.nuevo = {
            nombres: null,
            apellidos: null,
            email: null,
            telefono: null,
            perfil: 1,
          };
          vm.nuevo_success = true;
          vm.nuevo_usuario = response.data.data.usuario;
          vm.nuevo_clave = response.data.data.password;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_usuario: function () {
      const vm = this;
      var valida = true;
      if (
        this.usuarios[this.index_sel].nombres == null ||
        this.usuarios[this.index_sel].nombres.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el nombre del usuario.",
        });
        valida = false;
      }
      if (
        this.usuarios[this.index_sel].apellidos == null ||
        this.usuarios[this.index_sel].apellidos.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita los apellidos del usuario.",
        });
        valida = false;
      }
      if (
        this.usuarios[this.index_sel].email == null ||
        this.usuarios[this.index_sel].email.trim().length == 0
      ) {
        vm.$notify({
          group: "foo",
          type: "error",
          title: "Error",
          text: "Digita el correo electrónico del usuario.",
        });
        valida = false;
      }
      var re = /\S+@\S+\.\S+/;
      var valida_email = re.test(this.usuarios[this.index_sel].email);
      if (!valida_email) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "Error",
          text: "Digita una dirección de correo electrónico válida.",
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
        data: this.usuarios[this.index_sel],
      };
      axios
        .post(this.$base_url + "admin/update_usuario", params, {
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
            vm.loading = false;
            return false;
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "Bien!",
              text: response.data.message,
            });
            vm.get_usuarios();
            document.getElementById("close_editar").click();
            vm.loading = false;
          }

          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_estado_usuario: function (index) {
      const vm = this;
      vm.loading = true;
      var estado = 1;
      if (this.usuarios[index].estado == 1) {
        estado = 0;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      var params = {
        id_usuario: this.usuarios[index].id_usuario,
        estado: estado,
      };
      axios
        .post(this.$base_url + "admin/update_estado_usuario", params, {
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
            vm.loading = false;
            return false;
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "Bien!",
              text: response.data.message,
            });
            vm.get_usuarios();
            vm.loading = false;
          }
          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    delete_usuario: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_usuario: this.usuarios[this.index_sel].id_usuario,
      };
      axios
        .post(this.$base_url + "admin/delete_usuario", params, {
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
              title: "Usuario eliminado",
              text: response.data.message,
            });
          }
          vm.get_usuarios();
          vm.index_sel = null;
          vm.loading = false;
          document.getElementById("close_eliminar").click();
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    update_permisos: function () {
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      var params = {
        id_usuario: this.usuarios[this.index_sel].id_usuario,
        permisos: this.usuarios[this.index_sel].permisos,
        permisos_carpetas: this.usuarios[this.index_sel].permisos_carpetas
      };
      axios
        .post(this.$base_url + "admin/update_permisos", params, {
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
            vm.loading = false;
            return false;
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "Bien!",
              text: response.data.message,
            });
            vm.get_usuarios();
            vm.loading = false;
          }
          document.getElementById("close_permisos").click();
          vm.loading = false;
        })
        .catch((error) => {
          vm.error_manager(error);
        })
        .finally();
    },
    restablecer:function(){
      const vm = this;
      vm.loading = true;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      var params = {
        id_usuario: this.usuarios[this.index_sel].id_usuario,
      };
      axios
        .post(this.$base_url + "admin/restablecer_usuario", params, {
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
            vm.loading = false;
            return false;
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "Bien!",
              text: response.data.message,
            });
            vm.get_usuarios();
            document.getElementById("close_restablecer").click();
            vm.loading = false;
          }

          vm.loading = false;
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
      this.get_usuarios();
    },
  },
};
</script>
