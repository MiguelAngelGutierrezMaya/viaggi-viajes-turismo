<!-- header start -->
<?php
$class = "overlay-black";
$logo = "assets/images/icon/logo-sm.png";
/* if (isset($tipo) && $tipo == 1) {
    $logo = "assets/images/icon/logo-white.png";
    $class = "overlay-blue";
} */
?>
<header id="menu" class="<?= $class ?>">
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
                            <img src="<?= base_url() . $logo ?>" alt="" class="img-fluid blur-up lazyload">
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
                                            <span>Back</span>
                                            <i aria-hidden="true" class="fa fa-angle-right ps-2"></i>
                                        </div>
                                    </li>
                                    <li class="dropdown">
                                        <a href="<?= base_url() ?>" class="nav-link menu-title">Inicio</a>
                                    </li>
                                    <li class="dropdown">
                                        <a @click="tipo_busqueda=4" href="<?= base_url() ?>#buscador"
                                            class="nav-link menu-title">Vuelos y
                                            Hoteles</a>
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
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#asistencia"
                                            data-bs-backdrop="false" class="nav-link menu-title">Seguros de viaje</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <ul class="header-right">
                        <li class="user user-light bg_dark">
                            <a v-if="sess==null" data-bs-toggle="modal" data-bs-target="#login_menu"
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="login_menu" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"> Inicio de sesión </h5>
                    <a id="login_home_close_menu" type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
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
                                                            data-bs-target="#modal_olivido_pass_menu"
                                                            data-bs-backdrop="false" data-bs-toggle="modal"
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
                                            <label for="exampleInputEmail1">Teléfono</label>
                                            <input v-model="nuevo_registro.telefono" type="text" class="form-control"
                                                id="exampleInputEmail1">
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

    <div id="modal_olivido_pass_menu" class="modal fade" tabindex="-1" role="dialog">
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
</header>
<!--  header end -->
<script src="<?= base_url() ?>assets/js/app/menu.min.js?v=<?= date("YmdHis") ?>"></script>