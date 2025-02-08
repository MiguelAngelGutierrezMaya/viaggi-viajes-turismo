<template>
  <div id="auth">
    <div class="container-scroller">
      <transition appear name="slide-fade">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
          <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
              <div class="col-lg-4 mx-auto">
                <div v-if="loading" class="text-center">
                  <div class="loadingio-spinner-ripple-6armq16mc04">
                    <div class="ldio-2tzb8vdk7pu">
                      <div></div>
                      <div></div>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                    <div class="brand-logo text-center">
                      <img
                        src="assets/images/logo.png"
                        class="mx-auto"
                        alt="logo"
                      />
                      <h5 class="text-center fw-bold text-muted">BACKOFFICE</h5>
                    </div>

                    <h6 class="fw-light">Inicia sesión para continuar</h6>
                    <transition appear name="slide-fade">
                      <div v-if="msg_auth" class="alert alert-danger mt-3 mb-3">
                        <i class="ti-alert"></i> {{ msg_auth }}
                      </div>
                    </transition>
                    <form v-on:submit.prevent="set_auth" class="pt-3">
                      <div class="form-group">
                        <input
                          v-model="email"
                          type="email"
                          class="form-control form-control-lg"
                          id="exampleInputEmail1"
                          placeholder="Correo electrónico"
                        />
                        <span v-if="validate.email" class="text-danger">
                          <i class="ti-alert"></i> Digita tu correo electrónico
                          registrado
                        </span>
                      </div>
                      <div class="form-group">
                        <input
                          v-model="password"
                          type="password"
                          class="form-control form-control-lg"
                          id="exampleInputPassword1"
                          placeholder="Contraseña"
                        />
                        <span v-if="validate.password" class="text-danger">
                          <i class="ti-alert"></i> Digita tu contraseña
                        </span>
                      </div>
                      <div class="mt-3 d-grid gap-2">
                        <button
                          class="btn rounded-pill btn-primary btn-lg font-weight-medium auth-form-btn"
                        >
                          Ingresar
                        </button>
                      </div>
                    
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
      </transition>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "Auth",
  metaInfo: {
    title: "Inicia sesión - Backoffice",
  },
  data() {
    return {
      loading: true,
      email: null,
      password: null,
      validate: {
        email: false,
        password: false,
      },
      msg_auth: false,
    };
  },
  created: function () {
    this.get_validate_auth();
  },
  methods: {
    get_validate_auth: function () {
      var session = localStorage.getItem("sess_vyt_backoffice");
      if (session == null) {
        this.loading = false;
        return false;
      }
      this.$router.push("/dashboard");
    },
    set_auth: function () {
      this.validate.email = false;
      this.validate.password = false;
      this.msg_auth = false;
      if (this.email == null || this.email == "") {
        this.validate.email = true;
      }
      if (this.password == null || this.password == "") {
        this.validate.password = true;
      }
      if (this.validate.email || this.validate.password) {
        return false;
      }
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
      };
      let params = {
        email: this.email,
        password: this.password,
      };
      const vm = this;
      axios
        .post(this.$base_url + "auth/login", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            if(response.data.restore != undefined && response.data.restore){
              localStorage.setItem("vyt_backoffice_restore_user", response.data.id_usuario);
              vm.$router.push("/restablecer");
              return false;
            }
            vm.msg_auth = response.data.message;
            return false;
          }
          vm.msg_auth = false;
          var session = {
            Token: response.data.token,
            usuario: response.data.data,
          };
          localStorage.clear();
          localStorage.setItem("sess_vyt_backoffice", JSON.stringify(session));
          location.reload();
        })
        .catch((error) => {
          if (error == "Error: Network Error") {
            vm.msg_auth = "No se ha logrado conexión con el servidor.";
          } else {
            vm.msg_auth = error;
          }
        })
        .finally();
    },
  },
};
</script>
