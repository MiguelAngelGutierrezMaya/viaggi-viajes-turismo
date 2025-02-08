<div id="paquete">
    <section class="bg-inner">
        <div class="hotel-title-section" style="margin-top:5em">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hotel-name">
                            <div class="left-part">
                                <div class="top">
                                    <h2>
                                        <?= $paquete->servicio ?>
                                    </h2>
                                    <p>
                                        <?= $paquete->resumen ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-end">
                            <span class="fw-bold text-muted">Desde</span>
                            <h2 class="price text-primary">
                                <?= $paquete->valor_desde_c ?><span></span>
                            </h2>
                            <a class="btn btn-primary rounded-pill"
                                v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor"
                                target="_blank">
                                Reservas ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
  if (count($paquete->galeria) != 0) {
    ?>
    <!-- section start -->
    <section class="single-section xs-section bg-inner">
        <div class="container">


            <div class="row ">

                <div class="col-xl-9 col-lg-8">
                    <div class="image_section ">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="slide-1 arrow-dark zoom-gallery ratio_square">
                                    <?php

                    foreach ($paquete->galeria as $img) {
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
                      if (isset($paquete->galeria[1]) && $paquete->galeria[1]->tipo == 0) {

                        ?>
                                        <div class="slide-1 zoom-gallery no-arrow">
                                            <div>
                                                <img src="<?= $paquete->galeria[1]->url ?>"
                                                    class="img-fluid blur-up lazyload bg-img w-100" alt="">
                                            </div>
                                        </div>
                                        <?php
                      }
                      if (isset($paquete->galeria[2]) && $paquete->galeria[1]->tipo == 0) {
                        ?>
                                        <div class="slide-1 zoom-gallery no-arrow m-cls">
                                            <div>
                                                <img src="<?= $paquete->galeria[2]->url ?>"
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

                    <div class="description-section card card-body mt-4">

                        <h4 class="mt-4 text-uppercase">
                            Información del plan
                        </h4>

                        <div class="mt-3 mb-3">
                            <?= $paquete->descripcion ?>
                        </div>

                        <div class="mt-3 mb-3">
                            <?php
              if ($paquete->img != null) {
                ?>
                            <a href="<?= $paquete->img ?>" target="_blank">
                                <img src="<?= $paquete->img ?>" class="img-thumbnail">
                            </a>
                            <?php
              }
              ?>

                        </div>

                    </div>

                </div>
                <div class="col-xl-3 col-lg-4" id="booking">
                    <div class="sticky-cls">

                        <div class="single-sidebar">
                            <h6 class="contact-title text-primary">Contacto</h6>
                            <p> <a v-bind:href="'tel:'+parametros.TELEFONO.valor"> <i class="fas fa-phone-alt"></i>
                                    {{parametros.TELEFONO.valor}} </a></p>
                            <p> <a v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor"> <i
                                        class="fab fa-whatsapp"></i>
                                    {{parametros.WHATSAPP.valor}} </a></p>
                            <a href="#">
                                <p> <a v-bind:href="'mailto:'+parametros.EMAIL.valor"> <i class="fas fa-envelope"></i>
                                        {{parametros.EMAIL.valor}} </a></p>
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
                                            v-bind:href="parametros.YOUTUBE.valor"><i class="fab fa-youtube"></i></a>
                                    </li>
                                </ul>
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

</div>
<script>
let id_servicio = "<?= $paquete->id_servicio ?>";
</script>
<script src="<?= base_url() ?>assets/js/app/paquetes/detalle.min.js?v=<?= date("YmdHis") ?>"></script>
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