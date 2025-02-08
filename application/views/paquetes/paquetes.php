<div id="paquetes">
    <!-- breadcrumb start -->
    <section class="breadcrumb-section parallax-img pt-0">
        <img src="<?= $recursos->BANNER_SECUNDARIO->url ?>" class="bg-img img-fluid blur-up lazyload" alt="">
        <div class="breadcrumb-content overlay-black">
            <div>
                <h2>Paquetes</h2>
                <nav aria-label="breadcrumb" class="theme-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Paquetes</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="bird-animation">
            <div class="bird-container bird-container--one">
                <div class="bird bird--one"></div>
            </div>
            <div class="bird-container bird-container--two">
                <div class="bird bird--two"></div>
            </div>
            <div class="bird-container bird-container--three">
                <div class="bird bird--three"></div>
            </div>
            <div class="bird-container bird-container--four">
                <div class="bird bird--four"></div>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->
    <div id="buscador" style="margin-top: 40px;">
        <box-buscador tipo_busqueda="2"></box-buscador>
    </div>

    <!-- tours section start -->
    <section class="category-sec ratio3_2 section-b-space">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h4 class="my-text-primary fw-bold text-center">Planes con todo incluído...</h4>
                    <div v-if="loading_destacadas" class="text-center">
                        <div class="loadingio-spinner-pulse-pc66029qoh">
                            Cargando...
                            <div class="ldio-dt8bwii34sa">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="category-wrapper">
                        <div class="row">
                            <div v-for="(destacada, index_destacada) in paquetes" v-bind:key="index_destacada"
                                class="col-lg-6 col-12">
                                <transition appear name="slide-fade">
                                    <a v-bind:href="base_url+'paquetes/ver/'+destacada.servicio_slug">
                                        <div class="category-wrap">
                                            <div class="category-img">
                                                <img v-if="destacada.img_principal!=null"
                                                    v-bind:src="destacada.img_principal" v-bind:alt="destacada.servicio"
                                                    class="img-fluid blur-up lazyload">
                                                <div class="side-effect"></div>
                                            </div>
                                            <div class="category-content">
                                                <div>
                                                    <div class="top my-text-secondary">
                                                        <h3>{{destacada.servicio}}</h3>
                                                    </div>
                                                    <div>
                                                        <h5 style="display: block;">{{destacada.destino.destino}}</h5>
                                                    </div>
                                                    <div class="bottom text-end">
                                                        <span class="fw-bold text-muted">Desde</span>
                                                        <h3>{{destacada.valor_desde | currency}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </transition>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- tours section end -->

</div>
<script src="<?= base_url() ?>assets/js/app/paquetes/paquetes.min.js"></script>