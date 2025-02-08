<div id="actividad">
    <section class="bg-inner">
        <div class="hotel-title-section" style="margin-top:5em">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hotel-name">
                            <div class="left-part">
                                <div class="top">
                                    <h2>
                                        <?= $actividad->servicio ?>
                                    </h2>
                                    <span class="text-muted"><i class="fa fa-clock"></i>
                                        <?= $actividad->duracion ?>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-end">
                            <h2 class="price text-primary">
                                <?= $actividad->valor_desde_c ?><span></span>
                            </h2>
                            <a v-if="actividad.estado_servicio==1"
                                class="btn btn-rounded btn-text-white color1 rounded-pill" href="javascript:void(0)"
                                data-bs-target="#disponibilidad" data-bs-toggle="modal">
                                Ver disponibilidad
                            </a>
                            <div v-else class="text-danger fw-bold">
                                <i class="fa fa-exclamation-circle"></i> No disponible
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
  if (count($actividad->galeria) != 0) {
    ?>
    <!-- section start -->
    <section class="single-section xs-section bg-inner">
        <div class="container">

            <div class="row">
                <div class="col-lg-9">


                </div>

            </div>
            <div class="row ">

                <div class="col-xl-9 col-lg-8">
                    <div class="image_section ">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="slide-1 arrow-dark zoom-gallery ratio_square">
                                    <?php

                    foreach ($actividad->galeria as $img) {
                      if ($img->tipo == 0) {
                        ?>
                                    <div>
                                        <img src="<?= $img->url ?>" class="img-fluid blur-up lazyload bg-img w-100"
                                            alt="">
                                    </div>
                                    <?php
                      }
                      if ($img->tipo == 1) {
                        ?>
                                    <div class="mx-auto">
                                        <iframe class="iframe-video" src="<?= $img->url ?>" frameborder="0"
                                            allowfullscreen></iframe>
                                    </div>

                                    <?php
                      }

                    }
                    ?>
                                </div>
                            </div>
                            <div class="col-sm-4 d-none d-sm-block">
                                <div class="row">
                                    <div class="col-12 ratio_square">
                                        <?php
                      if (isset($actividad->galeria[1]) && $actividad->galeria[1]->tipo == 0) {

                        ?>
                                        <div class="slide-1 zoom-gallery no-arrow">
                                            <div>
                                                <img src="<?= $actividad->galeria[1]->url ?>"
                                                    class="img-fluid blur-up lazyload bg-img w-100" alt="">
                                            </div>
                                        </div>
                                        <?php
                      }
                      if (isset($actividad->galeria[2]) && $actividad->galeria[1]->tipo == 0) {
                        ?>
                                        <div class="slide-1 zoom-gallery no-arrow m-cls">
                                            <div>
                                                <img src="<?= $actividad->galeria[2]->url ?>"
                                                    class="img-fluid blur-up lazyload bg-img w-100" alt="">
                                            </div>
                                        </div>
                                        <?php
                      }
                      ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
  }
  ?>

                    <div class="description-section card card-body">

                        <div class="mt-3 mb-3">
                            <?= $actividad->resumen ?>
                        </div>

                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <?php
              if ($actividad->descripcion != "") {
                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingOne">
                                    <button class="accordion-button fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                        Actividad en detalle
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingOne">
                                    <div class="accordion-body">
                                        <?= $actividad->descripcion ?>
                                    </div>
                                </div>
                            </div>
                            <?php
              }

              if (count($actividad->horarios) != 0) {
                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingTwo">
                                    <button
                                        class="accordion-button fw-bold text-primary fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                        aria-expanded="false" aria-controls="flush-collapseTwo">
                                        Horarios
                                    </button>
                                </h2>
                                <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingTwo">
                                    <div class="accordion-body">
                                        <?php
                      foreach ($actividad->horarios as $horario) {
                        ?>
                                        <h5> <i class="far fa-clock"></i>
                                            <?= "De " . $horario->desde . " a " . $horario->hasta ?>
                                        </h5>
                                        <?php
                      }
                      ?>

                                        <div class="mt-4">
                                            <h5>Duración:
                                                <?= $actividad->duracion ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
              }
              ?>
                            <?php
              if ($actividad->incluye != "") {
                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingThree">
                                    <button class="accordion-button fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree"
                                        aria-expanded="false" aria-controls="flush-collapseThree">
                                        Incluye
                                    </button>
                                </h2>
                                <div id="flush-collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingThree">
                                    <div class="accordion-body">
                                        <?= $actividad->incluye ?>
                                    </div>
                                </div>
                            </div>
                            <?php
              }

              if ($actividad->datos_tour != "") {
                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingFour">
                                    <button class="accordion-button fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour"
                                        aria-expanded="false" aria-controls="flush-collapseFour">
                                        Prepárate para el tour
                                    </button>
                                </h2>
                                <div id="flush-collapseFour" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingFour">
                                    <div class="accordion-body">
                                        <?= $actividad->datos_tour ?>
                                    </div>
                                </div>
                            </div>
                            <?php
              }
              if ($actividad->cancelaciones != "") {
                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingFive">
                                    <button class="accordion-button fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive"
                                        aria-expanded="false" aria-controls="flush-collapseFour">
                                        Importante
                                    </button>
                                </h2>
                                <div id="flush-collapseFive" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingFive">
                                    <div class="accordion-body">
                                        <?= $actividad->cancelaciones ?>
                                    </div>
                                </div>
                            </div>
                            <?php
              }
              if ($actividad->punto_encuentro != "") {

                ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header text-uppercase" id="flush-headingSix">
                                    <button class="accordion-button fw-bold text-primary text-uppercase collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix"
                                        aria-expanded="false" aria-controls="flush-collapseSix">
                                        Punto de encuentro
                                    </button>
                                </h2>
                                <div id="flush-collapseSix" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingSix">
                                    <div class="accordion-body">
                                        <?= $actividad->punto_encuentro ?>

                                    </div>
                                </div>
                            </div>
                            <?php
              }
              ?>
                        </div>

                    </div>

                </div>
                <div class="col-xl-3 col-lg-4" id="booking">
                    <div class="sticky-cls">
                        <div class="single-sidebar">
                            <h6 class="contact-title text-primary">Reserva ahora</h6>
                            <vuejs-datepicker :disabled="actividad.estado_servicio!=1" :language="es"
                                v-model="booking.fecha" @input="get_disponibilidad" input-class="form-control"
                                :disabled-dates="disabled_dates" calendar-class="my-datetimepicker" placeholder="Fecha">
                            </vuejs-datepicker>
                            <span v-if="valida_fecha" class="text-danger"><small> <i
                                        class="fa fa-exclamation-circle"></i> Selecciona
                                    la fecha de tu reserva</small></span>
                            <div v-if="loading" class="text-center mt-4">
                                <span>Cargando...</span><br>
                            </div>
                            <div v-else class="mt-4">
                                <div v-if="actividad!=null && horarios.length!=0" class="form-group">
                                    <label class="text-primary">Hora</label>
                                    <select v-model="booking.id_horario" v-on:change="get_disponibilidad"
                                        class="form-control" ref="horario"
                                        :disabled="loading_disponibilidad || actividad.estado_servicio!=1">
                                        <option value="null">Selecciona el horario</option>
                                        <option v-for="(hora, index_hora) in horarios" v-bind:key="index_hora"
                                            :disabled="!hora.disponible" v-bind:value="hora.id_horario">{{hora.desde}} a
                                            {{hora.hasta}} </option>
                                    </select>
                                    <span v-if="valida_hora" class="text-danger"><small> <i
                                                class="fa fa-exclamation-circle"></i>
                                            Selecciona el horario</small></span>
                                </div>
                                <div class="form-group">
                                    <label class="text-primary">Pasajeros</label>
                                    <div>
                                        <small style="display:block">Adultos</small>
                                        <vue-number-input v-model="booking.adultos" v-on:change="get_disponibilidad"
                                            :disabled="loading_disponibilidad || actividad.estado_servicio!=1"
                                            class="text-center" :min="1" inline controls>
                                        </vue-number-input>
                                    </div>
                                    <div>
                                        <small style="display:block">Niños</small>
                                        <vue-number-input v-model="booking.ninos" v-on:change="get_disponibilidad"
                                            :disabled="loading_disponibilidad || actividad.estado_servicio!=1"
                                            class="text-center" :min="0" inline controls>
                                        </vue-number-input>
                                    </div>
                                    <div>
                                        <small style="display:block">Infantes</small>
                                        <vue-number-input v-model="booking.infantes" v-on:change="get_disponibilidad"
                                            :disabled="loading_disponibilidad || actividad.estado_servicio!=1"
                                            class="text-center" :min="0" inline controls>
                                        </vue-number-input>
                                    </div>
                                </div>


                                <hr>

                                <div v-if="loading_disponibilidad" class="text-center">
                                    <div> Estamos revisando la disponibilidad...</div><br>
                                </div>
                                <div v-if="loading_set" class="text-center">
                                    <div> Agregando servicio al carrito...</div><br>
                                </div>
                                <div v-if="!disponibilidad">
                                    <transition appear name="slide-fade">
                                        <div class="alert alert-warning">
                                            😕 ¡Ups!
                                            No hay la disponibilidad suficiente para la fecha, horario y/o pasajeros
                                            indicados. Por
                                            favor intenta con una nueva fecha u horario.
                                        </div>
                                    </transition>
                                </div>
                                <div v-if="valor!=0" class="text-end mt-3 mb-3">
                                    <small> Total </small>
                                    <h4 class="my-text-secondary fw-bold">{{ valor | currency}}</h4>
                                </div>
                                <div class="text-center">
                                    <button v-on:click="set_servicio" class="btn btn-primary rounded-pill"
                                        :disabled="loading_disponibilidad || !disponibilidad || actividad.estado_servicio!=1">
                                        <i class="fas fa-cart-plus"></i> Agregar al carrito
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="single-sidebar">
                            <div v-if="loading_parametros">Cargando...</div>
                            <div v-else>
                                <h6 class="contact-title text-primary">Contacto</h6>
                                <p> <a v-bind:href="'tel:'+parametros.TELEFONO.valor"> <i class="fas fa-phone-alt"></i>
                                        {{parametros.TELEFONO.valor}} </a></p>
                                <p> <a v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor">
                                        <i class="fab fa-whatsapp"></i>
                                        {{parametros.WHATSAPP.valor}} </a></p>
                                <a href="#">
                                    <p> <a v-bind:href="'mailto:'+parametros.EMAIL.valor"> <i
                                                class="fas fa-envelope"></i>
                                            {{parametros.EMAIL.valor}} </a> </p>
                                </a>
                                <div class="social-box text-center">
                                    <ul>
                                        <li v-if="parametros.FACEBOOK.valor!=null"><a
                                                v-bind:href="parametros.FACEBOOK.valor" target="_blank"><i
                                                    class="fab fa-facebook-f"></i></a>
                                        </li>
                                        <li v-if="parametros.INSTAGRAM.valor!=null"><a
                                                v-bind:href="parametros.INSTAGRAM.valor" target="_blank"><i
                                                    class="fab fa-instagram"></i></a>
                                        </li>
                                        <li v-if="parametros.YOUTUBE.valor!=null"><a
                                                v-bind:href="parametros.YOUTUBE.valor"><i
                                                    class="fab fa-youtube"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- section end -->

    <div class="modal fade" id="modal_cart" tabindex="-1" role="dialog" aria-labelledby="cart_success"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-body">

            </div>
        </div>
    </div>

    <div class="modal fade" id="disponibilidad" tabindex="-1" role="dialog" aria-labelledby="cart_success"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"> Selecciona fechas, horario y pasajeros </h5>
                    <a id="disponibilidad_close" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <vuejs-datepicker :language="es" v-model="booking.fecha" @input="get_disponibilidad"
                        input-class="form-control" :disabled-dates="disabled_dates" calendar-class="my-datetimepicker"
                        placeholder="Fecha">
                    </vuejs-datepicker>
                    <span v-if="valida_fecha" class="text-danger"><small> <i class="fa fa-exclamation-circle"></i>
                            Selecciona la
                            fecha de tu reserva</small></span>
                    <div v-if="loading" class="text-center mt-4">
                        <span>Cargando...</span><br>

                    </div>
                    <div v-else class="mt-4">
                        <div v-if="actividad!=null && horarios.length!=0" class="form-group">
                            <label class="text-primary">Hora</label>
                            <select v-model="booking.id_horario" v-on:change="get_disponibilidad" class="form-control"
                                ref="horario" :disabled="loading_disponibilidad">
                                <option value="null">Selecciona el horario</option>
                                <option v-for="(hora, index_hora) in horarios" v-bind:key="index_hora"
                                    v-bind:value="hora.id_horario" :disabled="!hora.disponible">
                                    {{hora.desde}} </option>
                            </select>
                            <span v-if="valida_hora" class="text-danger"><small> <i
                                        class="fa fa-exclamation-circle"></i> Selecciona
                                    el horario</small></span>
                        </div>
                        <div class="form-group">
                            <label class="text-primary">Pasajeros</label>
                            <div>
                                <vue-number-input v-model="booking.pasajeros" v-on:change="get_disponibilidad"
                                    :disabled="loading_disponibilidad" class="text-center" :min="1" inline controls>
                                </vue-number-input>
                            </div>
                        </div>
                        <div v-if="actividad.puntos_salida.length > 1">
                            <div class="form-group">
                                <label class="text-primary">Punto de salida</label>
                                <div v-if="booking.punto_salida!=null">
                                    <div style="font-size:1.2em">
                                        {{booking.punto_salida.punto_salida}}
                                    </div>
                                    <div class="mt-2 text-end">
                                        <a href="javascript:void(0)" class="text-blue" data-bs-toggle="modal"
                                            data-bs-target="#sel_punto_salida"> <i class="fas fa-map-marked-alt"></i>
                                            Cambia
                                            el
                                            punto de
                                            salida</a>
                                    </div>
                                </div>
                                <div v-else style="font-size:1.2em">
                                    <a href="javascript:void(0)" class="text-blue" data-bs-toggle="modal"
                                        data-bs-target="#sel_punto_salida"> <i class="fas fa-map-marked-alt"></i>
                                        Selecciona
                                        el
                                        punto de
                                        salida</a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div v-if="loading_disponibilidad" class="text-center">
                            <div> Estamos revisando la disponibilidad...</div><br>
                        </div>
                        <div v-if="loading_set" class="text-center">
                            <div> Agregando servicio al carrito...</div><br>
                        </div>
                        <div v-if="!disponibilidad">
                            <transition appear name="slide-fade">
                                <div class="alert alert-warning">
                                    😕 ¡Ups!
                                    No hay la disponibilidad suficiente para la fecha, horario y/o pasajeros indicados.
                                    Por
                                    favor intenta con una nueva fecha u horario.
                                </div>
                            </transition>
                        </div>
                        <div v-if="valor!=0" class="text-end mt-3 mb-3">
                            <small> Total </small>
                            <h4 class="my-text-secondary fw-bold">{{ valor | currency}}</h4>
                        </div>
                        <div class="text-center">
                            <button v-on:click="set_servicio" class="btn btn-primary rounded-pill"
                                :disabled="loading_disponibilidad || !disponibilidad">
                                <i class="fas fa-cart-plus"></i> Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
<script>
let id_servicio = "<?= $actividad->id_servicio ?>";
</script>
<script src="<?= base_url() ?>assets/js/app/actividades/detalle.min.js?v=<?= date("YmdHis") ?>"></script>
<script>
$(window).scroll(function() {
    posicionarMenu();
});

function posicionarMenu() {
    var altura_del_header = $('.image_section').outerHeight(true);
    var altura_del_menu = $('.menu-top').outerHeight(true);

    if ($(window).scrollTop() >= altura_del_header) {
        $('.menu-top').addClass('fixed');
        $('.wrapper').css('margin-top', (altura_del_menu) + 'px');
    } else {
        $('.menu-top').removeClass('fixed');
        $('.wrapper').css('margin-top', '0');
    }
}
</script>