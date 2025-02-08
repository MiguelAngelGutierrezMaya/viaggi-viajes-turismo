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
                        <h5 class="text-center fw-bolder text-muted mt-2">BACKOFFICE</h5>
                      </div>
  
                      <h5 class="fw-bold text-center">Crea una nueva contraseña para ingresar</h5>
                      <transition appear name="slide-fade">
                        <div v-if="msg_auth" class="alert alert-danger mt-3 mb-3">
                          <i class="ti-alert"></i> {{ msg_auth }}
                        </div>
                      </transition>
                      <form v-on:submit.prevent="set_auth" class="pt-3">
                        <div class="form-group">
                            <input
                            v-model="password"
                            type="password" ref="password"
                            class="form-control form-control-lg"
                            id="password"
                            placeholder="Contraseña"
                          />
                         
                        </div>
                        <div class="form-group">
                          <input
                            v-model="passwordConfirm"
                            type="password" ref="passwordConfirm"
                            class="form-control form-control-lg"
                            id="passwordConfirm"
                            placeholder="Confirma la nueva contraseña"
                          />
                        </div>
                        <div class="mt-3 d-grid gap-2">
                          <button
                            class="btn rounded-pill btn-primary btn-lg font-weight-medium auth-form-btn"
                          >
                            Continuar
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
        password: null,
        passwordConfirm:null,
        msg_auth: false,
      };
    },
    created: function () {
        this.validate();
    },
    methods: {
        validate:function(){
            this.id_user = localStorage.getItem("vyt_backoffice_restore_user");
            if (this.id_user == undefined) {
                this.$router.push("/auth");
            }
            this.loading = false;
        },
        set_auth: function () {

            const vm = this;
        
            if (this.password == null || this.password.trim().length == 0) {
                this.$refs.password.focus();
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Digita tu nueva contraseña"
                });
                return false;
            }

            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])([A-Za-z\d$@$!%*?&]|[^ ]){6,15}$/;
            if (!this.password.match(regex)) {
                this.$refs.password.focus();
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Tu contraseña debe tener entre 6 y 15 caracteres, con al menos una letra mayúscula, una minúscula y un caracter especial."
                });
                return false;
            }

            if (this.passwordConfirm == null || this.passwordConfirm.trim().length == 0) {
                this.$refs.passwordConfirm.focus();
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Confirma tu nueva contraseña."
                });
                return false;
            }

            if (this.password != this.passwordConfirm) {
                this.$refs.password.focus();
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Las contraseñas digitadas no coinciden.."
                });
                return false;
            }

            const headers = {
            "Content-Type": "application/json",
            "x-api-key": this.$api_key,
            };
            let params = {
                id_usuario: this.id_user,
                password: this.password,
            };
           
            axios
            .post(this.$base_url + "auth/restore", params, {
                headers: headers,
            })
            .then((response) => {
                if(response.data.status){
                    vm.$notify({
                        group: "foo",
                        type: "success",
                        title: "Contraseña restablecida",
                        text: response.data.message
                    });
                    localStorage.clear();
                    vm.$router.push('/auth');
                }else{
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Error",
                        text: response.data.message
                    });
                }
            })
            .catch((error) => {
                if (error == "Error: Network Error") {
                    vm.msg_auth = "No se ha logrado conexión con el servidor.";
                } else {
                    vm.msg_auth = error;
                }
            })
            .finally();
        }
    },
    watch: {
    $route() {
        this.validate();
    },
  },
  };
  </script>
  