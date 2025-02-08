<div id="actividades_destino">

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
    <box-buscador></box-buscador>
    <!-- section start -->
    <section class="xs-section bg-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="left-sidebar sticky-cls-top border-rounded">
                        <div class="back-btn">
                            back
                        </div>
                        <div class="search-bar">
                            <input type="text" placeholder="Buscar...">
                            <i class="fas fa-search"></i>
                        </div>
                        <!-- <div class="middle-part collection-collapse-block open">
                      <a href="javascript:void(0)" class="section-title collapse-block-title">
                         <h5>Filtrar</h5>
                         <img src="<?= base_url() ?>assets/images/icon/adjust.png" class="img-fluid blur-up lazyload"
                            alt="">
                      </a>
                      <div class="collection-collapse-block-content ">
                         <div class="filter-block">
                            <div class="collection-collapse-block open">
                               <h6 class="collapse-block-title">Tipo de actividad</h6>
                               <div class="collection-collapse-block-content">
                                  <div class="collection-brand-filter">
                                     <div v-for="(tipo_actividad, index_tipo_actividad) in tipos_actividad"
                                        v-bind:key="index_tipo_actividad" class="form-check collection-filter-checkbox">
                                        <input type="checkbox" class="form-check-input"
                                           v-bind:id="'tipo_actividad_'+tipo_actividad.id_tipo_actividad">
                                        <label class="form-check-label"
                                           v-bind:for="'tipo_actividad_'+tipo_actividad.id_tipo_actividad">{{tipo_actividad.tipo_actividad}}</label>
                                     </div>

                                  </div>
                               </div>
                            </div>
                         </div>

                      </div>
                   </div> -->
                        <div class="bottom-info">
                            <h5><span>i</span> ¿Necesitas asesoría?</h5>
                            <h4>+57 3152217055</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 ratio3_2">
                    <div class="container">
                        <div class="list-view row content grid">
                            <div v-if="actividades.length != 0">
                                <div v-for="(actividad, index_actividad) in actividades" v-bind:key="index_actividad"
                                    class="list-box col-12 popular grid-item wow fadeInUp border-rounded">
                                    <div class="list-img">
                                        <a v-bind:href="base_url+'actividades/ver/'+actividad.slug">
                                            <img v-bind:src="actividad.img_principal"
                                                class="img-fluid blur-up border-rounded lazyload" alt="">
                                        </a>
                                    </div>
                                    <div class="list-content">
                                        <div>
                                            <a v-bind:href="base_url+'actividades/ver/'+actividad.slug">
                                                <h5 class="my-text-primary">{{actividad.servicio}}</h5>
                                            </a>
                                            <p v-html="actividad.resumen"></p>
                                            <small>Desde</small>
                                            <div class="price mt-0">
                                                {{actividad.valor_desde | currency}} <span>/ por persona</span>
                                            </div>
                                            <a v-bind:href="base_url+'actividades/ver/'+actividad.slug"
                                                class="btn btn-solid btn-rounded color1 book-now">Ver
                                                más</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="card card-body shadow text-center">
                                    <h5 class="text-primary"> 😕 No tenemos en este momento actividades en este destino,
                                        pero
                                        seguramente aún podemos ayudarte. 💪 </h5>
                                    <div class="mt-3">
                                        <a v-if="parametros.length!=0"
                                            v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor"
                                            target="_blank" class="btn btn-success rounded-pill">
                                            <i class="fab fa-whatsapp"></i>
                                            Escríbenos ahora!
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <nav aria-label="Page navigation example" class="pagination-section">
                            <div class="text-center mt-3 mb-2" v-html="paginate"></div>
                            <!-- <ul class="pagination">
                         <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Previous">
                               <span aria-hidden="true">&laquo;</span>
                               <span class="sr-only">Previous</span>
                            </a>
                         </li>
                         <li class="page-item active"><a class="page-link" href="javascript:void(0)">1</a></li>
                         <li class="page-item"><a class="page-link" href="javascript:void(0)">2</a></li>
                         <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                         <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                               <span aria-hidden="true">&raquo;</span>
                               <span class="sr-only">Next</span>
                            </a>
                         </li>
                      </ul> -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- section End -->
</div>
<script>
const id_ciudad = '<?= $id_ciudad ?>';
</script>
<script src="<?= base_url() ?>assets/js/app/actividades/destino.min.js"></script>