<div id="review">
  <!-- breadcrumb start -->
  <section class="hotel-single-section pt-0">
    <img src="<?=base_url()?>assets/images/bg-tour.png" class="bg-img bg-bottom img-fluid blur-up lazyload" alt=""
      data-pag="1">
    <div class="hotel-title-section">
      <div class="container ">
        <div class="row">
          <div class="col-12">
            <div class="hotel-name">
              <div class="left-part">
                <div class="top">
                  <h2>Cuéntanos tu experiencia</h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- breadcrumb end -->
  <section class="single-section xs-section bg-inner">
    <div class="container">
      <div class="row">
        <div class="col-xl-12 col-lg-12">
          <div>
            <div>
              <div class="row">
                <div class="col-md-6 offset-md-3">
                  <div v-if="loading" class="text-center">Cargando...</div>
                  <div v-else>
                    <div v-if="review!=null">
                      <div class="card card-body shadow mt-3 mb-3">

                        <h5 class="fw-bold mb-4 text-uppercase">HOLA {{review.cliente.nombres}}</h5>
                        <p>
                          Cuéntenos sobre tu última reserva.
                        </p>
                        <h6 class="text-warning">
                          ☆ ☆ ☆ ☆ ☆
                        </h6>
                        <p> Envíanos una reseña. Es muy importante para nosotros.</p>
                        <p>
                          ¡Gracias por confiar en nuestros servicios!
                        </p>
                      </div>
                      <h5 class="text-primary fw-bold">Servicios de tu reserva</h5>
                      <div v-if="review.servicios.length!=0">
                        <div v-for="(servicio, index_servicio) in review.servicios" v-bind:key="index_servicio"
                          class="card card-body shadow mb-4">
                          <h5 class="fw-bold text-primary">
                            <i class="fas fa-map-marked-alt"></i>
                            {{servicio.servicio.servicio}}
                          </h5>
                          <h5 class="fw-bold mt-3">¿Cómo fue tu experiencia en este servicio?</h5>
                          <div>
                            <div class="text-center row">
                              <div class="col">
                                <a v-on:click="servicio.review.valor = 1" v-bind:class="{
        'rating-select': servicio.review.valor == 1,
        rating: servicio.review.valor != 1,
      }" href="javascript:void(0)">
                                  😠
                                </a>
                              </div>
                              <div class="col">
                                <a v-on:click="servicio.review.valor = 2" v-bind:class="{
        'rating-select': servicio.review.valor == 2,
        rating: servicio.review.valor != 2,
      }" href="javascript:void(0)">
                                  😞
                                </a>
                              </div>
                              <div class="col">
                                <a v-on:click="servicio.review.valor = 3" v-bind:class="{
        'rating-select': servicio.review.valor == 3,
        rating: servicio.review.valor != 3,
      }" href="javascript:void(0)">
                                  😑
                                </a>
                              </div>
                              <div class="col">
                                <a v-on:click="servicio.review.valor = 4" v-bind:class="{
        'rating-select': servicio.review.valor == 4,
        rating: servicio.review.valor != 4,
      }" href="javascript:void(0)">
                                  😃
                                </a>
                              </div>
                              <div class="col">
                                <a v-on:click="servicio.review.valor = 5" v-bind:class="{
        'rating-select': servicio.review.valor == 5,
        rating: servicio.review.valor != 5,
      }" href="javascript:void(0)">
                                  🤩
                                </a>
                              </div>
                            </div>
                            <span class="fw-bold text-muted">Cuéntanos un poco más sobre tu opinión</span>
                            <textarea v-model="servicio.review.comentarios" class="form-control my-textarea mt-2"
                              rows="5"></textarea>
                          </div>
                        </div>
                        <div v-if="alert" class="alert alert-danger">
                          <i class="fa fa-exclamation-circle"></i> Por favor selecciona una valoración por cada
                          servicio.
                        </div>
                        <div v-if="success" class="alert alert-success">
                          <i class="fa fa-check-circle"></i> Gracias por compartir tu experiencia con nosotros.
                          Fue un gusto atenderte.
                        </div>
                        <div class="mt-4 mb-4">
                          <div class="text-center d-grid gap-2 mt-4">
                            <button v-on:click="enviar" class="btn btn-primary rounded-pill" :disabled="sending">
                              Enviar </button>
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
      </div>
    </div>
  </section>
</div>
<script>
let id_reserva = "<?=$id_reserva?>";
</script>
<script src="<?=base_url()?>assets/js/app/clientes/review.js?v=<?=date("YmdHis")?>"></script>