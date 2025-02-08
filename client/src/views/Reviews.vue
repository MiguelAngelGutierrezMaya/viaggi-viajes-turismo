<template>
  <div id="reviews">
    <div class="card">
      <div class="card-body">
        <h4 class="fw-bolder mb-4">Reviews</h4>
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
          <div class="text-end mb-4">
            <button
              v-on:click="get_actividades()"
              data-bs-toggle="modal"
              data-bs-target="#filtrar"
              class="btn btn-primary btn-sm btn-rounded"
            >
              <i class="fa fa-filter"></i> Filtrar
            </button>
          </div>

          <div v-if="filtrado" class="card card-body border mt-3 mb-3">
            <span v-if="filtros.estado != null">
              <span>Estado: </span
              ><strong v-if="filtros.estado == 0">Pendiente</strong>
              <strong v-if="filtros.estado == 1">Aprobada</strong>
              <strong v-if="filtros.estado == 2">Archivada</strong>
            </span>
            <span v-if="filtros.servicio != null">
              <span>Servicio: </span
              ><strong>{{ filtros.servicio.servicio }}</strong>
            </span>

            <div class="text-end">
              <a
                v-on:click="quitar_filtros"
                href="javascript:void(0)"
                class="text-primary"
              >
                <i class="fa-solid fa-filter-circle-xmark"></i> Quitar filtros
              </a>
            </div>
          </div>
          <div v-if="reviews.length != 0">
            <div class="table-responsive mt-3">
              <table class="table table-hover table-condensed my_table_2">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 5%">#</th>
                    <th style="width: 20%">Cliente</th>
                    <th style="width: 10%">Reserva</th>
                    <th style="width: 20%">Servicio</th>
                    <th class="text-center" style="width: 5%">Valor</th>
                    <th class="text-left" style="width: 30%">Comentarios</th>
                    <th class="text-center" style="width: 5%">Estado</th>
                    <th class="text-center" style="width: 5%">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(review, index) in reviews" v-bind:key="index">
                    <td class="text-right">{{ review.num }}</td>
                    <td class="text-uppercase">
                      {{ review.cliente.nombres }}
                      {{ review.cliente.apellidos }}
                    </td>
                    <td>
                      <router-link
                        v-bind:to="'/reserva/' + review.id_reserva"
                        class="fw-bold"
                        >{{ review.cod_reserva }}</router-link
                      >
                    </td>
                    <td>
                      {{ review.servicio }}
                    </td>
                    <td class="text-center">
                      {{ review.valor }}
                    </td>
                    <td>
                      {{ review.resumen_comentarios }}
                    </td>
                    <td class="text-center fw-bolder">
                      <span v-if="review.estado == 0" class="text-warning"
                        >Nueva</span
                      >
                      <span v-if="review.estado == 1" class="text-success"
                        >Aprobada</span
                      >
                      <span v-if="review.estado == 2" class="text-danger"
                        >Archivada</span
                      >
                    </td>
                    <td class="text-center">
                      <button
                        v-on:click="review_sel = review"
                        data-bs-toggle="modal"
                        data-bs-target="#estado"
                        class="btn btn-info btn-rounded btn-xs ms-1"
                        title="Estado"
                      >
                        <i class="fa fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="text-center mt-3 mb-2" v-html="paginate"></div>
          </div>
          <div v-else>
            <div class="text-muted mt-4">
              <i class="fa fa-info-circle"></i> No se encontraron reseñas.
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="filtrar">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Fitrar reseñas</h5>
            <button
              id="close_modal"
              type="button"
              class="close"
              data-bs-dismiss="modal"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div>
              <div class="form-group">
                <label>Estado</label>
                <select v-model="filtros.estado" class="form-control">
                  <option value="0">Nueva</option>
                  <option value="1">Aprobada</option>
                  <option value="2">Archivada</option>
                </select>
              </div>
              <div class="form-group">
                <label>Servicio</label>
                <v-select
                  v-model="filtros.servicio"
                  @search="get_actividades"
                  label="servicio"
                  :options="actividades"
                  :clearable="false"
                ></v-select>
              </div>
              <div class="text-end">
                <button
                  v-on:click="get_reviews"
                  class="btn btn-primary rounded-pill btn-sm"
                >
                  <i class="fa fa-filter"></i> Filtrar
                </button>
                <button
                  data-bs-dismiss="modal"
                  class="btn btn-outline-dark rounded-pill btn-sm ms-1"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="estado">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-primary">
            <h5 class="modal-title fw-bolder">Reseña</h5>
            <button
              v-on:click="review_sel = null"
              id="close_modal_estado"
              type="button"
              class="close"
              data-bs-dismiss="modal"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="review_sel != null">
              <div>
                <h6>Reserva:</h6>
                <h5 class="fw-bold">
                  <router-link
                    v-bind:to="'/reserva/' + review_sel.id_reserva"
                    class="fw-bold"
                    data-bs-dismiss="modal"
                    >{{ review_sel.cod_reserva }}</router-link
                  >
                </h5>
              </div>
              <div class="mt-3">
                <h6>Servicio:</h6>
                <h5 class="fw-bold">{{ review_sel.servicio }}</h5>
              </div>
              <div class="mt-3">
                <h6>Valoración:</h6>
                <h5 class="fw-bold">{{ review_sel.valor }}</h5>
              </div>
              <div class="mt-3">
                <h6>Comentarios:</h6>
                <div
                  class="card card-body p-2 bg-light"
                  style="font-size: 0.8em"
                >
                  {{ review_sel.comentarios }}
                </div>
              </div>
              <div class="form-group mt-3">
                <label>Estado</label>
                <select v-model="review_sel.estado" class="form-control">
                  <option value="0">Nueva</option>
                  <option value="1">Aprobada</option>
                  <option value="2">Archivada</option>
                </select>
              </div>

              <div class="text-end">
                <button
                  v-on:click="update_review"
                  class="btn btn-success rounded-pill btn-sm"
                >
                  <i class="fa fa-save"></i> Guardar
                </button>
                <button
                  v-on:click="review_sel = null"
                  data-bs-dismiss="modal"
                  class="btn btn-outline-dark rounded-pill btn-sm ms-1"
                >
                  Cancelar
                </button>
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
  name: "Reviews",
  metaInfo: {
    title: "Reviews - Backoffice",
  },
  data() {
    return {
      loading: true,
      page: 1,
      search: null,
      reviews: [],
      paginate: null,
      index_sel: null,
      filtros: {
        estado: null,
        servicio: null,
      },
      filtrado: false,
      actividades: [],
      review_sel: null,
    };
  },
  props: {
    session: Object,
  },
  created: function () {
    this.page = this.$route.params.page;
    this.get_reviews();
  },
  methods: {
    get_reviews: function () {
      const vm = this;

      var id_servicio = null;
      if (this.filtros.servicio != null) {
        id_servicio = this.filtros.servicio.id_servicio;
      }

      if (this.filtros.servicio != null || this.filtros.estado != null) {
        this.filtrado = true;
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
          estado: this.filtros.estado,
          id_servicio: id_servicio,
        },
      };
      axios
        .get(vm.$base_url + "reservas/reviews", config, {
          headers: headers,
        })
        .then((response) => {
          if (response.data.status) {
            vm.reviews = response.data.data;
            vm.get_paginate(response.data.total_pages);
            if (
              this.filtros.estado != null ||
              this.filtros.id_servicio != null
            ) {
              this.filtrado = true;
            }
            document.getElementById("close_modal").click();
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
          "<li class='paginate_button page-item  previous'><a href='#/reservas/1' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      } else {
        var page_ant = parseFloat(page) - 1;
        paginate +=
          "<li class='paginate_button page-item'><a href='#/reservas/" +
          page_ant +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-left'></i> </a></li>";
      }
      // first label
      if (page > parseFloat(adjacents) + 1) {
        paginate +=
          "<li class='paginate_button page-item'><a class='page-link' href='#/reservas/1'>1</a></li>";
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
            "<li class='paginate_button page-item active'><a href='#/reservas/" +
            i +
            "' class='page-link'>" +
            i +
            "</a></li>";
        } else if (i == 1) {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/reservas/" +
            i +
            "' class='page-link' href='javascript:void(0);'>" +
            i +
            "</a></li>";
        } else {
          paginate +=
            "<li class='paginate_button page-item'><a href='#/reservas/" +
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
          "<li class='paginate_button page-item'><a  href='#/reservas/" +
          tpages +
          "' class='page-link' href='javascript:void(0);'>" +
          tpages +
          "</a></li>";
      }
      // next
      if (page < tpages) {
        var page_next = parseFloat(page) + 1;
        paginate +=
          "<li class='paginate_button page-item next'><a  href='#/reservas/" +
          page_next +
          "' class='page-link' href='javascript:void(0);'> <i class='fa fa-chevron-right'></i> </a></li>";
      } else {
        paginate +=
          "<li class='paginate_button page-item next disabled'><a class='page-link disabled'> <i class='fa fa-chevron-right'></i> </a></li>";
      }
      paginate += "</ul>";
      this.paginate = paginate;
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
    quitar_filtros: function () {
      this.filtros = {
        estado: null,
        servicio: null,
      };
      this.filtrado = false;
      this.loading = true;
      this.get_reviews();
    },
    update_review: function () {
      const vm = this;
      const headers = {
        "Content-Type": "application/json",
        "x-api-key": this.$api_key,
        Token: vm.session.usuario.token_session,
      };
      let params = {
        id_review: this.review_sel.id_review,
        estado: this.review_sel.estado,
      };
      axios
        .post(this.$base_url + "reservas/update_review", params, {
          headers: headers,
        })
        .then((response) => {
          if (!response.data.status) {
            vm.$notify({
              group: "foo",
              type: "error",
              title: "Reviews",
              text: response.data.message,
            });
          } else {
            vm.$notify({
              group: "foo",
              type: "success",
              title: "Reviews",
              text: response.data.message,
            });
          }
          document.getElementById("close_modal_estado").click();
          vm.review_sel = null;
          vm.get_reviews();
        })
        .catch((error) => {
          vm.$notify({
            group: "foo",
            type: "error",
            title: "Reviews",
            text: error,
          });
        })
        .finally();
    },
  },
  watch: {
    $route() {
      this.page = this.$route.params.page;
      this.get_reviews();
    },
  },
};
</script>
