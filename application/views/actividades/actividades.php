<div id="actividades">
    <!-- breadcrumb start -->
    <section class="breadcrumb-section parallax-img pt-0">
        <img src="<?= $recursos->BANNER_SECUNDARIO->url ?>" class="bg-img img-fluid blur-up lazyload" alt="">
        <div class="breadcrumb-content overlay-black">
            <div>
                <h2>Actividades</h2>
                <nav aria-label="breadcrumb" class="theme-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Actividades</li>
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
        <box-buscador tipo_busqueda="1"></box-buscador>
    </div>

    <!-- tours section start -->
    <section class="category-sec ratio3_2 section-b-space">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2 class="my-text-primary fw-bold text-center">
                        <span v-if="tipo_actividad == null">Tours, pasadías y experiencias encantadoras...</span>
                        <span v-else>{{tipo_actividad}}</span>
                    </h2>

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
                        <div v-if="actividades.length!=0" class="row">
                            <div v-for="(destacada, index_destacada) in actividades" v-bind:key="index_destacada"
                                class="col-lg-6 col-12">
                                <transition appear name="slide-fade">
                                    <a v-bind:href="base_url+'actividades/ver/'+destacada.servicio_slug">
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
                                                        <h3>{{destacada.valor_desde | currency}}</h3>
                                                    </div>
                                                    <h5 class="text-muted mt-2"><i class="fa fa-clock"></i>
                                                        {{destacada.duracion }}
                                                    </h5>
                                                    <div v-if="destacada.review_count!=0" class="me-2 text-warning"
                                                        style="font-size: 1.5em">
                                                        <i
                                                            v-bind:class="{'fas fa-star': destacada.rating_value >= 1, 'fas fa-star-half-alt': destacada.rating_value <1 && destacada.rating_value >0, 'far fa-star':destacada.rating_value<1 && destacada.rating_value<=0}"></i>
                                                        <i
                                                            v-bind:class="{'fas fa-star': destacada.rating_value >= 2, 'fas fa-star-half-alt': destacada.rating_value <2 && destacada.rating_value >1, 'far fa-star':destacada.rating_value<2 && destacada.rating_value<=1}"></i>
                                                        <i
                                                            v-bind:class="{'fas fa-star': destacada.rating_value >= 3, 'fas fa-star-half-alt': destacada.rating_value <3 && destacada.rating_value >2, 'far fa-star':destacada.rating_value<3 && destacada.rating_value<=2}"></i>
                                                        <i
                                                            v-bind:class="{'fas fa-star': destacada.rating_value >= 4, 'fas fa-star-half-alt': destacada.rating_value <4 && destacada.rating_value >3, 'far fa-star':destacada.rating_value<4 && destacada.rating_value<=3}"></i>
                                                        <i
                                                            v-bind:class="{'fas fa-star': destacada.rating_value == 5, 'fas fa-star-half-alt': destacada.rating_value <5 && destacada.rating_value >4, 'far fa-star':destacada.rating_value<5 && destacada.rating_value<=4}"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </transition>
                            </div>
                        </div>
                        <div v-else>
                            <div class="card card-body border p-4 mt-4">
                                <div class="text-center">
                                    <h5>😕</h5>
                                    <h6 class="text-muted"> No encontramos en este momento las actividades que buscas,
                                        pero quizá aún
                                        podemos ayudarte. </h6>
                                    <div class="mt-4">
                                        <a v-if="parametros.length!=0"
                                            v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor"
                                            target="_blank" class="btn btn-success rounded-pill">
                                            <i class="fab fa-whatsapp"></i>
                                            Escríbenos ahora
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- tours section end -->

</div>

<script>
var slug = '<?= isset($slug) ? $slug : null ?>';
</script>
<script src="<?= base_url() ?>assets/js/app/actividades/actividades.min.js?v=1"></script>