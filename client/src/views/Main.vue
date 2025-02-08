<template>
  <div class="main">
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <router-link class="navbar-brand brand-logo" to="/">
              <img src="assets/images/logo.png" alt="logo" />
            </router-link>

            <router-link class="navbar-brand brand-logo-mini" to="/">
              <img src="assets/images/logo.png" alt="logo" />
            </router-link>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold d-none d-lg-block ms-0"></li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="javascript:void(0)" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img class="img-xs rounded-circle" src="assets/images/user.png" alt="Imagen de perfil" />
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="assets/images/user.png" alt="Imagen de perfil" />
                  <p class="mb-1 mt-3 font-weight-semibold">
                    {{ session.usuario.usuario.nombres }}
                  </p>
                  <p class="fw-light text-muted mb-0">
                    {{ session.usuario.usuario.email }}
                  </p>
                </div>
                <router-link to="/perfil" class="dropdown-item"><i
                    class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
                  Mi perfil
                </router-link>
                <a v-on:click="set_logout" href="javascript:void(0)" class="dropdown-item"><i
                    class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Salir</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <router-link class="nav-link" to="/">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Inicio</span>
              </router-link>
            </li>
            <li class="nav-item nav-category">Servicios</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false"
                aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Actividades</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <router-link to="/actividades" class="nav-link">Actividades</router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/tarifas_actividades" class="nav-link">Tarifas</router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/tipos_actividad" class="nav-link">Tipos de actividad</router-link>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#item-paquetes" aria-expanded="false"
                aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Paquetes</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="item-paquetes">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <router-link to="/paquetes" class="nav-link">Paquetes</router-link>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <router-link to="/cupones" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Cupones</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/proveedores" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Proveedores</span>
              </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/agenda" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Agenda</span>
              </router-link>
            </li>
            <li class="nav-item nav-category">Reservas</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false"
                aria-controls="form-elements">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Reservas</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <router-link to="/nueva_reserva" class="nav-link">Nueva reserva</router-link>
                  </li>
                  <li class="nav-item">
                    <router-link class="nav-link" to="/reservas">Reservas</router-link>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <router-link to="/cotizaciones" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Cotizaciones</span>
              </router-link>
            </li>
            <li class="nav-item">
              <a v-on:click="get_actividades()" class="nav-link" data-bs-toggle="modal" data-bs-target="#reportes"
                href="javascript:void(0)">
                <i class="menu-icon mdi mdi-chart-line"></i>
                <span class="menu-title">Reporte</span>
              </a>
            </li>
            <li v-if="permiso_usuarios" class="nav-item nav-category">
              Usuarios
            </li>
            <li v-if="permiso_usuarios" class="nav-item">
              <router-link to="/usuarios" class="nav-link">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Usuarios</span>
              </router-link>
            </li>
            <li class="nav-item nav-category">
              Herramientas
            </li>
            <li class="nav-item">
              <router-link to="/links" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Directorio</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/documentos" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Administrativo</span>
              </router-link>
            </li>
            <li v-if="permiso_parametros || permiso_recursos" class="nav-item nav-category">
              Ajustes
            </li>
            <li v-if="permiso_parametros" class="nav-item">
              <router-link to="/parametros" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Parámetros</span>
              </router-link>
            </li>
            <li v-if="permiso_recursos" class="nav-item">
              <router-link to="/recursos" class="nav-link">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Recursos</span>
              </router-link>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <router-view :session="session" />
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <small>
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Premium
                  <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a>
                  from BootstrapDash.</span>
              </small>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2024. Todos los
                derechos reservados. Un desarrollo
                <a href="https://arboledadev.com" target="_blank">Arboleda Dev</a></span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
      <div class="modal fade" id="reportes">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header text-success">
              <h5 class="modal-title fw-bolder">Reporte</h5>
              <button type="button" class="close" data-bs-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loading_reporte" class="text-center">
                <div class="loadingio-spinner-ripple-6armq16mc04">
                  <div class="ldio-2tzb8vdk7pu">
                    <div></div>
                    <div></div>
                  </div>
                </div>
              </div>
              <div v-else>
                <div class="ps-4 pe-4">
                  <div class="form-check">
                    <input v-model="reporte.tipo_fecha" class="form-check-input" type="radio" name="flexRadioDefault"
                      id="flexRadioDefault1" value="1" />
                    <label class="form-check-label" for="flexRadioDefault1">
                      Por fechas de registro
                    </label>
                  </div>
                  <div class="form-check">
                    <input v-model="reporte.tipo_fecha" class="form-check-input" type="radio" name="flexRadioDefault"
                      id="flexRadioDefault2" value="2" />
                    <label class="form-check-label" for="flexRadioDefault2">
                      Por fechas de reserva
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label>Desde</label>
                  <input v-on:change="result_reporte = null" v-model="reporte.desde" class="form-control" type="date" />
                </div>
                <div class="form-group">
                  <label>Hasta</label>
                  <input v-on:change="result_reporte = null" :min="reporte.desde" v-model="reporte.hasta"
                    class="form-control" type="date" />
                </div>
                <div class="text-end">
                  <button v-on:click="get_reporte" :disabled="reporte.desde == null || reporte.hasta == null"
                    class="btn btn-primary rounded-pill btn-sm">
                    <i class="fa fa-search"></i> Consultar
                  </button>
                  <button data-bs-dismiss="modal" class="btn btn-outline-dark rounded-pill btn-sm ms-1">
                    Cancelar
                  </button>
                </div>
                <div v-if="result_reporte != null" class="mt-4">
                  <div v-if="result_reporte == 0" class="alert alert-warning">
                    <i class="fa fa-info-circle"></i> No se encontraron
                    reservas.
                  </div>
                  <div v-else class="text-center">
                    <hr />
                    <span><i class="fa fa-info-circle"></i> Se encontraron
                      {{ result_reporte }} reservas.</span>
                    <div class="mt-3">
                      <a v-bind:href="url_reporte" target="_blank" class="btn btn-success rounded-pill">
                        <i class="fa fa-download"></i> Descargar
                      </a>
                    </div>
                  </div>
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
import $ from "jquery";
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Main",
  data() {
    return {
      session: null,
      actividades: [],
      reporte: {
        tipo_fecha: 1,
        desde: null,
        hasta: null,
      },
      loading_reporte: false,
      result_reporte: null,
      url_reporte: null,
      permiso_usuarios: false,
      permiso_parametros: false,
      permiso_recursos: false,
    };
  },
  created: function () {
    var session = localStorage.getItem("sess_vyt_backoffice");
    session = JSON.parse(session);
    this.session = session;
    this.permiso_usuarios = this.session.usuario.usuario.permisos.GUSUARIOS;
    this.permiso_parametros = this.session.usuario.usuario.permisos.GPARAMETROS;
    this.permiso_recursos = this.session.usuario.usuario.permisos.GRECURSOS;
  },
  methods: {
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
    get_reporte: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": vm.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let config = {
        headers: headers,
        params: {
          tipo_fecha: this.reporte.tipo_fecha,
          desde: this.reporte.desde,
          hasta: this.reporte.hasta,
        },
      };
      axios
        .get(vm.$base_url + "reservas/reporte", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.result_reporte = response.data.results;
            vm.url_reporte = response.data.url;
          } else {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Reporte",
              text: response.data.message,
            });
          }
          vm.loading_reporte = false;
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Reporte",
            text: error,
          });
          vm.loading_reporte = false;
        });
    },
    set_logout: function () {
      localStorage.removeItem("sess_vyt_backoffice");
      location.reload();
    },
  },
  mounted: function () {
    var body = $("body");
    $('[data-bs-toggle="minimize"]').on("click", function () {
      if (
        body.hasClass("sidebar-toggle-display") ||
        body.hasClass("sidebar-absolute")
      ) {
        body.toggleClass("sidebar-hidden");
      } else {
        body.toggleClass("sidebar-icon-only");
      }
    });
    $('[data-bs-toggle="offcanvas"]').on("click", function () {
      $(".sidebar-offcanvas").toggleClass("active");
    });
  },
};
</script>
