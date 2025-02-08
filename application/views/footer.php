<!-- footer start -->
<div id="footer">
    <div v-if="!loading">
        <footer>
            <div class="footer section-b-space section-t-space">
                <div class="container">
                    <div class="row order-row">
                        <div class="col-xl-6 col-md-12 order-cls">
                            <div class="footer-title mobile-title">
                                <h5>Contáctanos</h5>
                            </div>
                            <div class="footer-content">
                                <div class="contact-detail">
                                    <div class="footer-logo">
                                        <img src="<?= base_url() ?>assets/images/icon/footer-logo-v.png" alt=""
                                            class="img-fluid blur-up lazyload">
                                    </div>

                                    <ul v-if="parametros.length!=0" class="contact-list ">
                                        <li class="text-white"><i class="fas fa-phone-alt"></i>
                                            {{parametros.TELEFONO.valor}}</li>
                                        <li class="text-white"><i class="fab fa-whatsapp"></i>
                                            {{parametros.WHATSAPP.valor}}</li>
                                        <li class="text-white"><i class="fas fa-file"></i> RNT {{parametros.RNT.valor}}
                                        </li>
                                        <li class="text-white"><i class="fas fa-envelope"></i>
                                            {{parametros.EMAIL.valor}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-12" style="margin-top: 4em">
                            <div class="footer-space">
                                <div class="footer-title">
                                    <h5>Viaggi - Viajes y Turismo</h5>
                                </div>
                                <div class="footer-content">
                                    <div class="footer-links">
                                        <ul>


                                            <li><a href="#" data-bs-toggle="modal" data-bs-target="#login_home"
                                                    class="text-white">Iniciar sesión</a></li>

                                            <li><a href="#" data-bs-toggle="modal" data-bs-target="#login_home"
                                                    class="text-white">Registrarme</a></li>

                                            <li><a href="<?= base_url() ?>home/politica_privacidad_datos"
                                                    class="text-white">Politica de privacidad
                                                    y protección de
                                                    datos </a></li>
                                            <li class="mt-4"><a href="<?= base_url() ?>backoffice" target="_blank"
                                                    class="text-white">Backoffice </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-12">
                            <!-- <div class="footer-space">
                        <div class="footer-title">
                           <h5>Medios de pago</h5>
                        </div>
                        <div class="footer-content">
                           <ul>
                              <li><img class="b-lazy b-loaded bloaded" border="0" alt="pse"
                                    src="<?= base_url() ?>assets/images/icon/pse.png"></li>
                              <li><img class="b-lazy b-loaded bloaded" border="0" alt="visa"
                                    src="<?= base_url() ?>assets/images/icon/visa-card.png"></li>
                              <li><img class="b-lazy b-loaded bloaded" border="0" alt="master card"
                                    src="<?= base_url() ?>assets/images/icon/mc-card.png"></li>
                              <li><img class="b-lazy b-loaded bloaded" border="0" alt="diner club"
                                    src="<?= base_url() ?>assets/images/icon/diner-club-card.png"></li>

                           </ul>

                        </div>
                     </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="sub-footer">
                <div class="container">
                    <div class="row ">
                        <div class="col-xl-6 col-md-6 col-sm-12">
                            <div class="footer-social">
                                <ul v-if="parametros.length!=0">
                                    <li><a :href="parametros.FACEBOOK.valor"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a :href="parametros.INSTAGRAM.valor"><i class="fab fa-instagram"></i></a></li>
                                    <li><a v-if="parametros.YOUTUBE.valor!=null" :href="parametros.YOUTUBE.valor"><i
                                                class="fab fa-youtube"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 col-sm-12">
                            <div class="copy-right">
                                <p>copyright 2024 Template by pixelstrap - Un desarrollo <a
                                        href="https://arboledadev.com" target="_blank">ArboledaDev</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <div class="tap-top">
            <div>
                <i class="fas fa-angle-up"></i>
            </div>
        </div>
    </div>
    <div v-else class="text-center text-primary">
        Cargando...
    </div>
</div>
<!-- tap to top end -->
<script src="<?= base_url() ?>assets/js/app/footer.min.js?v=1"></script>

<!-- portfolio js -->
<script src="<?= base_url() ?>assets/js/jquery.magnific-popup.js"></script>
<script src="<?= base_url() ?>assets/js/zoom-gallery.js"></script>

<!-- slick js-->
<script src="<?= base_url() ?>assets/js/slick.js"></script>

<!-- ripple effect js -->
<script src="<?= base_url() ?>assets/js/jquery.ripples.js"></script>

<!-- Bootstrap js-->
<script src="<?= base_url() ?>assets/js/bootstrap.bundle.min.js"></script>

<!-- lazyload js-->
<script src="<?= base_url() ?>assets/js/lazysizes.min.js"></script>

<!-- Theme js-->
<script src="<?= base_url() ?>assets/js/script.js"></script>


</body>

</html>