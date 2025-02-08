<div id="home">
    <!-- pre-loader start -->
    <div class="loader-wrapper img-gif">
        <img src="<?= base_url() ?>assets/images/loader.gif" alt="">
    </div>
    <!-- pre-loader end -->

    <!-- header start -->
    <header class="overlay-black">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div v-if="parametros.length!=0" class="row">
                        <div class="col text-end text-primary fw-bold p-0 m-0">
                            <span><i class="fas fa-phone-alt"></i> +{{parametros.TELEFONO.valor}}</span>
                            <span class="span-email" style="margin-left: 2.2em;"><i class="fas fa-envelope"></i>
                                {{parametros.EMAIL.valor}}</span>
                        </div>
                    </div>
                    <div class="menu">
                        <div class="brand-logo">
                            <a href="<?= base_url() ?>">
                                <img src="<?= base_url() ?>assets/images/icon/logo-sm.png" alt=""
                                    class="img-fluid blur-up lazyload">
                            </a>
                        </div>
                        <nav>
                            <div class="main-navbar">
                                <div id="mainnav">
                                    <div class="toggle-nav"><i class="fa fa-bars sidebar-bar"></i></div>
                                    <div class="menu-overlay"></div>
                                    <ul class="nav-menu">
                                        <li class="back-btn">
                                            <div class="mobile-back text-end">
                                                <span class="text-muted">
                                                    Cerrar
                                                </span>
                                                <i aria-hidden="true" class="fa fa-angle-right ps-2"></i>
                                            </div>
                                        </li>
                                        <li class="dropdown">
                                            <a href="<?= base_url() ?>" class="nav-link menu-title">Inicio</a>
                                        </li>
                                        <li class="dropdown">
                                            <a @click="tipo_busqueda=4" href="#buscador"
                                                class="nav-link menu-title">Vuelos y Hoteles</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="<?= base_url() ?>paquetes" class="nav-link menu-title">Paquetes</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="nav-link menu-title">Tours y Excursiones</a>
                                            <ul class="nav-submenu menu-content">
                                                <li v-for="(tipo, index_tipo) in tipos_actividad" :key="index_tipo">
                                                    <a :href="'<?= base_url() ?>actividades/tipo/'+tipo.slug"
                                                        class="text-uppercase">{{tipo.tipo_actividad}}</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#asistencia" class="nav-link menu-title">Seguros de
                                                viaje</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                        <ul class="header-right">
                            <li class="user user-light bg_dark">
                                <a v-if="sess==null" data-bs-toggle="modal" data-bs-target="#login_home"
                                    data-bs-backdrop="false" href="javascript:void(0)">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a v-else data-bs-toggle="modal" href="<?= base_url() ?>clientes/index">
                                    <i class="fas fa-user"></i> <span class="text-white">{{sess.cliente.nombres}}</span>
                                </a>
                            </li>
                            <li class="user user-light bg_dark">
                                <a href="<?= base_url() ?>cart/index">
                                    <i class="fas fa-shopping-cart"></i>
                                    <sup v-if="cart!=0">
                                        <span class="badge bg-danger rounded-pill">{{cart}}</span>
                                    </sup>
                                </a>
                            </li>
                            <li class="user user-light bg_dark">
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#videollamada"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <i class="fas fa-video"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--  header end -->

    <!-- home section start -->
    <section class="home_section  p-0">
        <div class="classic-slider no-arrow">
            <?php
      if (isset($recursos->SLIDE1) && $recursos->SLIDE1->url != null) {
        ?>
            <div>
                <div class="home">
                    <?php
            if ($recursos->SLIDE1->enlace != null) {
              ?>

                    <img src="<?= $recursos->SLIDE1->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">

                    <?php
            } else {
              ?>
                    <img src="<?= $recursos->SLIDE1->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">
                    <?php
            }
            ?>
                </div>
            </div>
            <?php
      }

      if (isset($recursos->SLIDE2) && $recursos->SLIDE2->url != null) {
        ?>
            <div>
                <div class="home">
                    <?php
            if ($recursos->SLIDE2->enlace != null) {
              ?>
                    <a href="<?= $recursos->SLIDE2->enlace ?>">
                        <img src="<?= $recursos->SLIDE2->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">
                    </a>
                    <?php
            } else {
              ?>
                    <img src="<?= $recursos->SLIDE2->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">
                    <?php
            }
            ?>
                </div>
            </div>
            <?php
      }

      if (isset($recursos->SLIDE3) && $recursos->SLIDE3->url != null) {
        ?>
            <div>
                <div class="home">
                    <?php
            if ($recursos->SLIDE2->enlace != null) {
              ?>
                    <a href="<?= $recursos->SLIDE3->enlace ?>">
                        <img src="<?= $recursos->SLIDE3->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">
                    </a>
                    <?php
            } else {
              ?>
                    <img src="<?= $recursos->SLIDE3->url ?>" class="img-fluid blur-up lazyload bg-img" alt="">
                    <?php
            }
            ?>
                </div>
            </div>
            <?php
      }
      ?>
        </div>

    </section>
    <!-- home section end -->

    <div id="buscador">
        <box-buscador :tipo_busqueda="tipo_busqueda"></box-buscador>
    </div>

    <?php

  if (isset($recursos->ZONAMEDIA) && $recursos->ZONAMEDIA->url != null) {
    ?>
    <section class="category-sec ratio3_2 section-b-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php
            if ($recursos->ZONAMEDIA->enlace != null) {
              ?>
                    <a href="<?= $recursos->ZONAMEDIA->enlace ?>">
                        <img src="<?= $recursos->ZONAMEDIA->url ?>" class="img-fluid blur-up lazyload" alt="">
                    </a>
                    <?php
            } else {
              ?>
                    <img src="<?= $recursos->ZONAMEDIA->url ?>" class="img-fluid blur-up lazyload" alt="">
                    <?php
            }
            ?>
                    </a>
                    <?php
            if ($recursos->ZONAMEDIA->enlace != null && $recursos->ZONAMEDIA->boton != null) {
              ?>
                    <div class="text-center mt-3 mb-3">
                        <a class="btn btn-rounded btn-text-white btn-lg color1"
                            href="<?= $recursos->ZONAMEDIA->enlace ?>">
                            <?= $recursos->ZONAMEDIA->boton ?>
                        </a>
                    </div>
                    <?php
            }
            ?>
                </div>
            </div>
        </div>
    </section>
    <?php
  }
  ?>

    <!-- tours section start -->
    <section class="category-sec ratio3_2 section-b-space">
        <div class="container">
            <div class="title-1 title-5">
                <h2 class="my-text-primary mt-0 mb-3">Destinos más buscados</h2>
            </div>
            <div class="row">
                <div class="col">
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
                    <div v-else>
                        <div class="title-1 title-5">
                            <span class="title-label" style="font-size:1.2em">Nacionales</span>
                        </div>
                        <div class="row mt-4 mx-auto my-auto justify-content-center">
                            <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <div class="carousel-item n"
                                        v-for="(destacada, index_destacada) in destacadas_nacionales"
                                        v-bind:key="'n'+index_destacada" :class="{'active':index_destacada == 0}">
                                        <div class="col-md-3 ps-2 pe-2">
                                            <a v-if="destacada.id_tipo_servicio == 1"
                                                v-bind:href="base_url+'actividades/ver/'+destacada.servicio_slug">
                                                <div class="category-box">
                                                    <div class="img-category">
                                                        <div class="side-effect"></div>
                                                        <div>
                                                            <img v-if="destacada.img_principal!=null"
                                                                v-bind:src="destacada.img_principal"
                                                                v-bind:alt="destacada.servicio"
                                                                class="img-fluid blur-up lazyload bg-img">
                                                        </div>
                                                        <div class="top-bar">
                                                            <h5>{{destacada.valor_desde | currency}}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="content-category">
                                                        <div class="top">
                                                            <h3>{{destacada.servicio}}</h3>
                                                        </div>
                                                        <span v-html="destacada.resumen"></span>
                                                    </div>
                                                </div>
                                            </a>
                                            <a v-if="destacada.id_tipo_servicio == 2"
                                                v-bind:href="base_url+'paquetes/ver/'+destacada.servicio_slug">
                                                <div class="category-box">
                                                    <div class="img-category">
                                                        <div class="side-effect"></div>
                                                        <div>
                                                            <img v-if="destacada.img_principal!=null"
                                                                v-bind:src="destacada.img_principal"
                                                                v-bind:alt="destacada.servicio"
                                                                class="img-fluid blur-up lazyload bg-img">
                                                        </div>
                                                        <div class="top-bar">
                                                            <span class="text-white fw-bold">Desde</span>
                                                            <h5 style="margin-top:-8px">
                                                                {{destacada.valor_desde | currency}}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="content-category">
                                                        <div class="top">
                                                            <h3>{{destacada.servicio}}</h3>
                                                        </div>
                                                        <span v-html="destacada.resumen"></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel"
                                    role="button" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </a>
                                <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel"
                                    role="button" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <div class="title-1 title-5">
                            <span class="title-label" style="font-size:1.2em">Internacionales</span>
                        </div>
                        <div class="row mt-4 mx-auto my-auto justify-content-center">
                            <div id="recipeCarouselInt" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <div class="carousel-item int "
                                        v-for="(destacada, index_destacada) in destacadas_internacionales"
                                        v-bind:key="'i'+index_destacada" :class="{'active':index_destacada == 0}">
                                        <div class="col-md-3 ps-2 pe-2">
                                            <a v-if="destacada.id_tipo_servicio==1"
                                                v-bind:href="base_url+'actividades/ver/'+destacada.servicio_slug">
                                                <div class="category-box">
                                                    <div class="img-category">
                                                        <div class="side-effect"></div>
                                                        <div>
                                                            <img v-if="destacada.img_principal!=null"
                                                                v-bind:src="destacada.img_principal"
                                                                v-bind:alt="destacada.servicio"
                                                                class="img-fluid blur-up lazyload bg-img">
                                                        </div>
                                                        <div class="top-bar">
                                                            <h5>{{destacada.valor_desde | currency}}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="content-category">
                                                        <div class="top">
                                                            <h3>{{destacada.servicio}}</h3>
                                                        </div>
                                                        <span v-html="destacada.resumen"></span>
                                                    </div>
                                                </div>
                                            </a>
                                            <a v-if="destacada.id_tipo_servicio == 2"
                                                v-bind:href="base_url+'paquetes/ver/'+destacada.servicio_slug">
                                                <div class="category-box">
                                                    <div class="img-category">
                                                        <div class="side-effect"></div>
                                                        <div>
                                                            <img v-if="destacada.img_principal!=null"
                                                                v-bind:src="destacada.img_principal"
                                                                v-bind:alt="destacada.servicio"
                                                                class="img-fluid blur-up lazyload bg-img">
                                                        </div>
                                                        <div class="top-bar">
                                                            <span class="text-white fw-bold">Desde</span>
                                                            <h5 style="margin-top:-8px">
                                                                {{destacada.valor_desde | currency}}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="content-category">
                                                        <div class="top">
                                                            <h3>{{destacada.servicio}}</h3>
                                                        </div>
                                                        <span v-html="destacada.resumen"></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarouselInt"
                                    role="button" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </a>
                                <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarouselInt"
                                    role="button" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- tours section end -->

    <!-- package section start-->
    <section class="category-wrapper section-b-space">
        <div class="container">
            <div v-if="ofertas.length!=0" class="title-1 title-5">
                <span class="title-label">Ofertas imperdibles</span>
                <h2 class="my-text-primary">Salidas destacadas</h2>
                <p>Nuestros ofertas vigentes.</p>
            </div>
            <div v-if="loading_paquetes_destacados" class="text-center">
                <div class="loadingio-spinner-pulse-pc66029qoh">
                    Cargando...
                    <div class="ldio-dt8bwii34sa">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="row">
                    <div v-for="(oferta, index) in ofertas" v-bind:key="index" class="col-lg-6">
                        <div>
                            <div>
                                <div class="category-wrap">
                                    <div class="category-img">
                                        <a v-if="oferta.id_tipo_servicio == 1"
                                            v-bind:href="base_url+'actividades/ver/'+oferta.servicio_slug">
                                            <img v-if="oferta.img_principal!=null" v-bind:src="oferta.img_principal"
                                                alt="" class="img-fluid blur-up lazyload">
                                        </a>
                                        <a v-if="oferta.id_tipo_servicio == 2"
                                            v-bind:href="base_url+'paquetes/ver/'+oferta.servicio_slug">
                                            <img v-if="oferta.img_principal!=null" v-bind:src="oferta.img_principal"
                                                alt="" class="img-fluid blur-up lazyload">
                                        </a>
                                    </div>
                                    <div class="category-content">
                                        <div>
                                            <div class="top">
                                                <a href="#">
                                                    <h3>{{oferta.servicio}}</h3>
                                                </a>
                                            </div>
                                            <span v-html="oferta.resumen"></span>
                                            <div class="bottom text-end">
                                                <span class="fw-bold text-muted">Desde</span>
                                                <h3>{{oferta.valor_desde | currency}}</h3>
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
    <!-- package section end-->

    <?php

  if (isset($recursos->ZONAINFERIOR) && $recursos->ZONAINFERIOR->url != null) {
    ?>
    <section class="category-sec ratio3_2 section-b-space">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php
            if ($recursos->ZONAINFERIOR->enlace != null) {
              ?>
                    <a href="<?= $recursos->ZONAINFERIOR->enlace ?>">
                        <img src="<?= $recursos->ZONAINFERIOR->url ?>" class="img-fluid blur-up lazyload" alt="">
                    </a>
                    <?php
            } else {
              ?>
                    <img src="<?= $recursos->ZONAINFERIOR->url ?>" class="img-fluid blur-up lazyload" alt="">
                    <?php
            }

            if ($recursos->ZONAINFERIOR->enlace != null && $recursos->ZONAINFERIOR->boton != null) {
              ?>
                    <div class="text-center mt-3 mb-3">
                        <a class="btn btn-rounded btn-text-white btn-lg color1"
                            href="<?= $recursos->ZONAINFERIOR->enlace ?>">
                            <?= $recursos->ZONAINFERIOR->boton ?>
                        </a>
                    </div>
                    <?php
            }
            ?>
                </div>
            </div>
        </div>
    </section>
    <?php
  }
  ?>

    <!-- service section start-->
    <section class="section-b-space">
        <div class="container">
            <div class="title-1 title-5">
                <h2 class="my-text-primary">VIAGGI VIAJES Y TURISMO tu mejor aliado </h2>
                <p class="fw-bold">
                    Especialistas en planes a CHILE Y EUROPA
                </p>
            </div>
            <div class="row service-part">
                <div class="col-lg-4">
                    <div class="service-wrapper">
                        <div>
                            <h3>Los mejores precios</h3>

                            <p>Hacemos los mejores convenios y buscamos por ti la mejor opción para tu viaje.
                                Nuestro compromiso es brindarte experiencias inolvidables al mejor precio posible.
                            </p>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="service-wrapper">
                        <div>
                            <h3>Seguridad y respaldo</h3>
                            <p>Cotiza y compra por nuestras lineas autorizadas con los mejores expertos, y permitenos en
                                tu viaje
                                acompañarte antes, durante y despues del mismo..</p>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="service-wrapper">
                        <div>
                            <h3>Asesoría</h3>

                            <p>Además encuentra una guía en situaciones especificas, de la mano de nuestros asesores.
                                Nuestro compromise es que viajes bien.
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- service section end -->

    <div id="login_home" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"> Inicio de sesión </h5>
                    <a id="login_home_close" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div v-if="loading" class="text-center">
                        <div class="loadingio-spinner-pulse-pc66029qoh">
                            Cargando...
                            <div class="ldio-dt8bwii34sa">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <transition v-if="msg_validate!=null" appear name="slide-fade">
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i> {{msg_validate}}
                            </div>
                        </transition>
                        <div class="card card-body shadow">
                            <div v-if="!registro">
                                <transition appear name="slide-fade">
                                    <div>
                                        <div>
                                            <form v-on:submit.prevent="set_login">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Correo electrónico</label>
                                                    <input v-model="login.email" type="email" class="form-control"
                                                        id="exampleInputEmail1" aria-describedby="emailHelp">

                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Contraseña</label>
                                                    <input v-model="login.password" type="password" class="form-control"
                                                        id="exampleInputPassword1">
                                                </div>

                                                <div class="button-bottom">
                                                    <button type="submit"
                                                        class="w-100 btn btn-primary rounded-pill">Ingresar</button>
                                                    <div class="text-end mt-2 mb-4">
                                                        <a v-on:click="load_olvido_pass"
                                                            data-bs-target="#modal_olivido_pass" data-bs-toggle="modal"
                                                            href="javascript:void(0)">
                                                            ¿Olvidaste tu contraseña?
                                                        </a>
                                                    </div>
                                                    <div class="divider mt-4 mb-4">
                                                        <h6>¿Aun no tienes cuenta con nosotros?</h6>
                                                    </div>
                                                    <a v-on:click="registro = true"
                                                        class="w-100 btn btn-solid btn-outline rounded-pill">Regístrate
                                                        ahora</a>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </transition>
                            </div>
                            <div v-else>
                                <transition appear name="slide-fade">
                                    <form v-on:submit.prevent="set_registro">
                                        <div class="form-group">
                                            <label for="name">Nombres</label>
                                            <input v-model="nuevo_registro.nombres" type="text" class="form-control"
                                                id="name">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Apellidos</label>
                                            <input v-model="nuevo_registro.apellidos" type="text" class="form-control"
                                                id="lastname">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">No. de identificación</label>
                                            <input v-model="nuevo_registro.identificacion" type="text"
                                                class="form-control" id="identificacion">
                                        </div>
                                        <div class="form-group">
                                            <label for="cod_pais">País</label>
                                            <v-select v-model="nuevo_registro.cod_pais" :options="paises" label="name"
                                                :clearable="false">
                                            </v-select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPhone1">Teléfono</label>
                                            <input v-model="nuevo_registro.telefono" type="text" class="form-control"
                                                id="exampleInputPhone1">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Correo electrónico</label>
                                            <input v-model="nuevo_registro.email" type="email" class="form-control"
                                                id="exampleInputEmail1">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Contraseña</label>
                                            <input v-model="nuevo_registro.password" type="password"
                                                class="form-control" id="exampleInputPassword1">
                                        </div>
                                        <div class="button-bottom">
                                            <button type="submit" class="w-100 btn btn-primary rounded-pill">
                                                Continuar </button>
                                            <div class="divider mt-3 mb-3">
                                                <h6>¿Ya tienes cuenta con nosotros?</h6>
                                            </div>
                                            <a v-on:click="registro = false"
                                                class="w-100 btn btn-solid rounded-pill btn-outline">Inicia
                                                sesión</a>
                                        </div>
                                    </form>
                                </transition>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_olivido_pass" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"> Recupera tu cuenta </h5>
                    <a id="olvido_home_close" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <transition v-if="msg_validate_recovery!=null" appear name="slide-fade">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> {{msg_validate_recovery}}
                        </div>
                    </transition>
                    <div v-if="msg_validate_recovery_success!=null">
                        <transition appear name="slide-fade">
                            <div v-if="msg_validate_recovery_success">
                                <div class="text-success text-center">
                                    <i class="fa fa-check-circle fa-2x"></i> <br>
                                    Hemos enviado un mensaje a tu dirección de correo electrónico suministrada con las
                                    indicaciones
                                    para restablecer tu contraseña.
                                </div>
                                <div class="mt-4 mb-3">
                                    <button data-bs-dismiss="modal" class="w-100 btn btn-success rounded-pill">
                                        Aceptar </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <div v-else>
                        <transition appear name="slide-fade">
                            <div>
                                <div class="text-muted text-center mb-3">
                                    <i class="fa fa-info-circle"></i> Digita tu correo electrónico. Te enviaremos las
                                    indicaciones para recuperar tu cuenta.
                                </div>
                                <form v-on:submit.prevent="set_recovery">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Correo electrónico</label>
                                        <input v-model="recovery.email" type="email" class="form-control"
                                            id="exampleInputEmailRecovery" aria-describedby="emailHelp">

                                    </div>
                                    <button type="submit" class="w-100 btn btn-primary rounded-pill">
                                        Continuar </button>
                                </form>
                            </div>
                        </transition>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="videollamada" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-end">
                        <a v-on:click="close_modal_reunion()" id="videollamada_close" type="button" class="close"
                            data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div v-if="reunion==null">
                        <div class="mt-3 mb-3 text-center text-primary">
                            <i class="fas fa-video"></i>
                            ¿Tienes una cita con nosotros? <br> Digita tu número de identificación para continuar.
                        </div>
                        <div class="form-group">
                            <input v-model="identificacion_llamada" class="form-control"
                                placeholder="No. de identificación">
                        </div>
                        <div class="text-end">
                            <button @click="validar_reunion"
                                :disabled="identificacion_llamada==null || identificacion_llamada.trim().length==0"
                                class="btn btn-primary btn-sm rounded-pill">
                                Continuar <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div v-if="reunion" class="text-center">
                        <div class="mt-3 mb-3 text-center text-primary fw-bold">{{msg_reunion}}</div>
                        <a v-if="parametros.length!=0" v-bind:href="url_llamada" target="_blank"
                            class="btn btn-rounded btn-text-white color1 mt-2 rounded-pill">
                            <i class="fas fa-video"></i>
                            Ingresar
                        </a>
                    </div>
                    <div v-if="reunion!=null && !reunion" class="text-center">
                        <div class="mt-3 mb-3 text-center text-primary fw-bold">{{msg_reunion}}</div>
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

    <div id="asistencia" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-end">
                        <a @click="msg_send_data=false" type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div v-if="!msg_send_data">
                        <div class="mt-4 mb-4">
                            <div class="text-center mb-4">
                                <h5>¡Hola! <br> Gracias por contactarnos. <br> Déjanos los siguientes datos y nos
                                    comunicaremos contigo
                                    cuanto antes para darte las mejores alternativas para tu viaje.</h5>
                            </div>
                            <div class="card card-body">
                                <form v-on:submit.prevent="send_datos">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input v-model="datos.nombre" class="form-control" type="text" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cod_pais">País</label>
                                        <v-select v-model="datos.cod_pais" :options="paises" label="name"
                                            :clearable="false">
                                        </v-select>
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input v-model="datos.telefono" class="form-control" type="text" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Correo electrónico</label>
                                        <input v-model="datos.email" class="form-control" type="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Cuéntanos cuál es tu destino</label>
                                        <input v-model="datos.destino" class="form-control" type="text" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha de ida</label>
                                        <input v-model="datos.fecha_ida" class="form-control" type="date" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha de regreso</label>
                                        <input v-model="datos.fecha_regreso" :min="datos.fecha_ida" class="form-control"
                                            type="date" required>
                                    </div>
                                    <div class="mt-2 text-end">
                                        <button type="submit" class="btn btn-primary rounded-pill btn-sm">
                                            <i class="fas fa-paper-plane"></i> Enviar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <div class="mt-2 mb-2 ">
                                <h5>O si lo prefieres</h5>
                            </div>
                            <a v-if="parametros.length!=0"
                                v-bind:href="'https://api.whatsapp.com/send?phone='+parametros.WHATSAPP.valor"
                                target="_blank" class="btn btn-success rounded-pill">
                                <i class="fab fa-whatsapp"></i>
                                Escríbenos ahora
                            </a>
                        </div>
                    </div>
                    <div v-else>
                        <div class="text-center mb-4">
                            <h5>🤗</h5>
                            <h5 class="mt-3">¡Gracias por tu información! <br> Buscaremos una buena propuesta para ti y
                                pronto te
                                contáctaremos.</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url() ?>assets/js/app/home.min.js?v=1"></script>
<script>
$(function() {

    let item = document.querySelectorAll(".carousel .n");
    item.forEach((el) => {
        const minPerSlide = 4;
        let next = el.nextElementSibling;
        for (var i = 1; i < minPerSlide; i++) {
            if (!next) {
                // wrap carousel by using first child
                next = item[0];
            }
            let cloneChild = next.cloneNode(true);
            el.appendChild(cloneChild.children[0]);
            next = next.nextElementSibling;
        }
    });

    let items_int = document.querySelectorAll(".carousel .int");
    items_int.forEach((el) => {
        const minPerSlide = 4;
        let next = el.nextElementSibling;
        for (var i = 1; i < minPerSlide; i++) {
            if (!next) {
                // wrap carousel by using first child
                next = items_int[0];
            }
            let cloneChild = next.cloneNode(true);
            el.appendChild(cloneChild.children[0]);
            next = next.nextElementSibling;
        }
    });
});
</script>