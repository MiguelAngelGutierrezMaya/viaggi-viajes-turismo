<div id="cart">
    <!-- breadcrumb start -->
    <section class="bg-inner">
        <div class="hotel-title-section" style="margin-top:5em">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hotel-name">
                            <div class="left-part">
                                <div class="top">
                                    <h2>Tu carrito</h2>
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
                <div class="col-xl-9 col-lg-8">
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
                        <transition appear name="slide-fade">
                            <div v-if="cart_success" class="alert alert-success text-center p-3">
                                <i class="fa fa-check-circle"></i> <br> ¡Muy bien! Has agregado satisfactoriamente un
                                nuevo
                                servicio en el
                                carrito.

                            </div>
                        </transition>
                        <div class="guest-detail">
                            <div v-if="cart!=null">
                                <h2>Tus reservas en el carrito</h2>
                                <div v-for="(item, index) in cart" v-bind:key="index"
                                    class="card card-body shadow border">
                                    <div class="row">
                                        <div class="col-3">
                                            <img v-bind:src="item.img" class="img-thumbnail">
                                        </div>
                                        <div class="col-9">
                                            <h5 class="fw-bold my-text-primary"><i class="fas fa-map-marked-alt"></i>
                                                <a v-bind:href="base_url+'actividades/ver/'+item.slug"
                                                    target="_blank">{{item.servicio}}
                                                </a>
                                            </h5>
                                            <h6>
                                                <i class="fas fa-users"></i> {{item.adultos}} adultos - {{item.ninos}}
                                                niños - {{item.infantes}}
                                                infantes
                                            </h6>
                                            <h6>
                                                <i class="fas fa-calendar"></i> {{item.fecha}}
                                            </h6>
                                            <h6 v-if="item.horario!=null">
                                                <i class="fas fa-clock"></i> {{item.horario.horario}}
                                            </h6>
                                            <h6 v-if="item.punto_salida!=null">
                                                <i class="fas fa-map-marked-alt"></i> Punto de salida:
                                                {{item.punto_salida.punto_salida}}
                                            </h6>
                                            <div class="text-end">
                                                <small>Valor</small>
                                                <h5 class="my-text-secondary fw-bold">{{item.valor | currency}}</h5>
                                            </div>
                                            <div class="text-end mt-3">
                                                <!-- <a v-on:click="load_editar(index)" href="javascript:void(0)" class="text-primary">
                                       <i class="fa fa-edit"></i> Modificar
                                    </a> -->
                                                <a v-on:click="load_quitar(index)" href="javascript:void(0)"
                                                    class="text-danger">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card card-body shadow mt-4 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-3">
                                            <div v-if="msg_validate_cupon == null">
                                                <h2>¿Tienes un cupón de descuento?</h2>
                                                <span> Ingresá el código de tu cupón para obtener tu beneficio</span>
                                                <div>
                                                    <input v-model="codigo_cupon" class="form-control"
                                                        placeholder="Código de cupón">
                                                    <div v-if="cupon_invalido!=null" class="text-danger"> <i
                                                            class="fa fa-exclamation-circle"></i>
                                                        {{cupon_invalido}}</div>
                                                    <div class="mt-2 text-end">
                                                        <button v-on:click="valida_cupon"
                                                            class="btn btn-primary btn-sm rounded-pill ">
                                                            Agregar </button>
                                                    </div>

                                                </div>
                                            </div>
                                            <transition v-else appear name="slide-fade">
                                                <div>
                                                    <div class="alert alert-success mt-2 mb-2">
                                                        <i class="fa fa-check-circle"></i> ¡Felicitaciones! Tendrás
                                                        {{descuento | currency}}
                                                        de
                                                        descuento en tu reserva.
                                                    </div>
                                                    <div class="text-end">
                                                        <a v-on:click="quitar_cupon" href="javascript:void(0)"
                                                            class="text-danger">Eliminar
                                                            cupón</a>
                                                    </div>
                                                </div>
                                            </transition>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div v-else>
                                <div class="text-muted text-center">
                                    <i class="fa fa-info-circle"></i> <br> 😕 Aún no has agregado servicios en el
                                    carrito.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="cart!=null" class="col-xl-3 col-lg-3">
                    <div class="guest-detail">
                        <div v-if="cart!=null">
                            <h2>Detalle del pago</h2>
                            <div v-for="(item, index) in cart" v-bind:key="index">
                                <h6 class="fw-bold my-text-primary">{{item.servicio}}</h6>
                                <h6 class="text-end fw-bold">{{item.valor | currency}}</h6>
                            </div>
                            <hr>
                            <h2>Subtotal</h2>
                            <h4 class="text-end fw-bold my-text-secondary">{{subtotal | currency}}</h4>
                            <div v-if="descuento!=0">
                                <h2>Descuento</h2>
                                <h4 class="text-end fw-bold my-text-secondary">{{descuento | currency}}</h4>
                            </div>
                            <h2>Total</h2>
                            <h4 class="text-end fw-bold my-text-secondary">{{total | currency}}</h4>
                            <div class="text-center d-grid gap-2 mt-4">
                                <button v-on:click="valida_login()" class="btn btn-primary rounded-pill"> Pagar ahora
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- tours section start -->
    <section class="category-sec ratio3_2 section-b-space mt-0">
        <div class="container">
            <div class="title-1 title-5 mb-4">
                <h2 class="my-text-primary">Otras actividades imperdibles</h2>
            </div>
            <div class="row">
                <div class="col">
                    <div v-if="loading_recomendadas" class="text-center">
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
                        <transition appear name="slide-fade">
                            <div class="category-wrapper">
                                <div class="row">
                                    <div v-for="(destacada, index_destacada) in recomendadas"
                                        v-bind:key="index_destacada" class="col-lg-6 col-12">
                                        <transition appear name="slide-fade">
                                            <a v-bind:href="base_url+'actividades/ver/'+destacada.servicio_slug">
                                                <div class="category-wrap">
                                                    <div class="category-img">
                                                        <img v-if="destacada.img_principal!=null"
                                                            v-bind:src="destacada.img_principal"
                                                            v-bind:alt="destacada.servicio"
                                                            class="img-fluid blur-up lazyload">
                                                        <div class="side-effect"></div>
                                                    </div>
                                                    <div class="category-content">
                                                        <div>
                                                            <div class="top my-text-secondary">
                                                                <h3>{{destacada.servicio}}</h3>
                                                            </div>
                                                            <div class="bottom">
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
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- tours section end -->
    <div id="modificar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Modificar servicio</h5>
                    <a type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div v-if="item_sel!=null">
                        <div class="card card-body shadow">
                            <p class="fw-bold text-primary"><i class="fas fa-map-marked-alt"></i>
                                {{item_sel.servicio}}
                            </p>
                            <p>
                                <i class="fas fa-users"></i> {{item_sel.pasajeros}} pasajeros
                            </p>
                            <p>
                                <i class="fas fa-calendar"></i> {{item_sel.fecha}}
                            </p>
                            <p v-if="item_sel.horario!=null">
                                <i class="fas fa-clock"></i> {{item_sel.horario.horario}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-on:click="delete_item" type="button" class="btn btn-primary rounded-pill"> <i
                            class="fa fa-save"></i> Guardar </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="quitar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Quitar servicio</h5>
                    <a type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div v-if="item_sel!=null">
                        <h6> ¿Deseas eliminar este servicio del carrito?</h6>
                        <div class="card card-body shadow">
                            <p class="fw-bold text-primary"><i class="fas fa-map-marked-alt"></i>
                                {{item_sel.servicio}}
                            </p>
                            <p>
                                <i class="fas fa-users"></i> {{item_sel.pasajeros}} pasajeros
                            </p>
                            <p>
                                <i class="fas fa-calendar"></i> {{item_sel.fecha}}
                            </p>
                            <p v-if="item_sel.horario!=null">
                                <i class="fas fa-clock"></i> {{item_sel.horario.horario}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-on:click="delete_item" type="button" class="btn btn-danger rounded-pill"> Sí </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal"> No
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="login" class="modal fade" tabindex="-1" role="dialog">
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
                        <div class="text-center mt-2 mb-2">- ó -</div>
                        <!-- <div class="card card-body shadow">
              <div class="row">
                <div class="col-lg-12 text-center">
                  <div class="d-grid gap-2">

                    <a v-on:click="fb_auth" class="btn btn-primary rounded-pill btn-block">
                      <i class="fab fa-facebook"></i>
                      Continuar con Facebook
                    </a>
                    <a v-on:click="google_auth" class="btn btn-danger mt-3  rounded-pill btn-block">
                      <i class="fab fa-google"></i>
                      Continuar con Google
                    </a>
                  </div>
                </div>
              </div>
            </div> -->
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
</div>
<script src="<?= base_url() ?>assets/js/app/cart.min.js?v=<?= date("YmdHis") ?>"></script>