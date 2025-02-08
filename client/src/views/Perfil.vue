<template>
  <div id="actividades">
    <div class="card">
      <div class="card-body">
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
            <div class="mt-3">
              <div class="row">
                <div class="col-md-6 offset-md-3">
                  <div class="mt-4 mb-4">
                    <span>Nombres y apellidos</span>
                    <h4 class="fw-bold text-primary">
                      {{ session.usuario.usuario.nombres }}
                      {{ session.usuario.usuario.apellidos }}
                    </h4>
                    <span>Correo electrónico</span>
                    <h4 class="fw-bold text-primary">
                      {{ session.usuario.usuario.email }}
                    </h4>
                  </div>

                  <h5 class="fw-bold text-primary">Cambiar contraseña</h5>
                  <div class="card card-body shadow">
                    <div class="form-group">
                      <label>Contraseña actual</label>
                      <input
                        v-model="password"
                        class="form-control"
                        type="password"
                      />
                    </div>
                    <div class="form-group">
                      <label>Nueva contraseña</label>
                      <input
                        v-model="new_password"
                        class="form-control"
                        type="password"
                      />
                    </div>
                    <div class="form-group">
                      <label>Confirma la nueva contraseña</label>
                      <input
                        v-model="confirm_password"
                        class="form-control"
                        type="password"
                      />
                    </div>
                    <div class="text-end">
                      <button
                        v-on:click="update_clave"
                        class="btn btn-success btn-sm rounded-pill"
                      >
                        <i class="fa fa-save"></i> Guardar
                      </button>
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
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
  name: "Perfil",
  metaInfo: {
    title: "Perfil - Backoffice",
  },
  data() {
    return {
      loading: true,
      session: null,
      password: null,
      new_password: null,
      confirm_password: null,
    };
  },
  created: function () {
    var session = localStorage.getItem("sess_vyt_backoffice");
    session = JSON.parse(session);
    this.session = session;
    this.loading = false;
  },
  methods: {
    update_clave: function () {
      if (this.password == null || this.password.trim().length == 0) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "<i class='fa fa-exclamation-circle'></i> Ups!",
          text: "Digita tu contraseña actual",
        });
        return false;
      }
      if (this.new_password == null || this.new_password.trim().length == 0) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "<i class='fa fa-exclamation-circle'></i> Ups!",
          text: "Digita tu nueva contraseña ",
        });
        return false;
      }
      if (this.new_password.trim().length < 6) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "<i class='fa fa-exclamation-circle'></i> Ups!",
          text: "Digita una contraseña de seis o más caracteres.",
        });
        return false;
      }
      if (
        this.confirm_password == null ||
        this.confirm_password.trim().length == 0
      ) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "<i class='fa fa-exclamation-circle'></i> Ups!",
          text: "Confirma tu nueva contraseña",
        });
        return false;
      }
      if (this.confirm_password != this.new_password) {
        this.$notify({
          type: "error",
          group: "foo",
          title: "<i class='fa fa-exclamation-circle'></i> Ups!",
          text: "La contraseña digitada no coincide con la confirmación",
        });
        return false;
      }
      this.loading = true;
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_usuario: vm.session.usuario.usuario.id_usuario,
        password: this.password,
        nuevo_password: this.new_password,
      };
      axios
        .post(this.$base_url + "admin/update_password", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              type: "error",
              group: "foo",
              title: "<i class='fa fa-exclamation-circle'></i> Ups!",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              type: "success",
              group: "foo",
              title: "<i class='fa fa-check-circle'></i> Excelente!",
              text: "Has actualizado tus contraseña.",
            });
          }
          vm.loading = false;
        })
        .catch((error) =>
          vm.$notify({
            type: "error",
            group: "foo",
            title: "<i class='fa fa-exclamation-circle'></i> Ups!",
            text: error,
          })
        )
        .finally();
    },
  },
  watch: {},
};
</script>
