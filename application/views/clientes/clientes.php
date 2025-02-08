<div id="cliente">
  <div v-if="loading" class="loader-wrapper img-gif">
    <img src="<?= base_url() ?>assets/images/loader.gif" alt="">
  </div>
  <!-- breadcrumb start -->
  <section class="breadcrumb-section parallax-img pt-0">
    <img src="<?= $recursos->BANNER_SECUNDARIO->url ?>" class="bg-img img-fluid blur-up lazyload" alt="">
    <div class="breadcrumb-content overlay-black">
      <div>
        <h2>Tu cuenta</h2>
        <nav aria-label="breadcrumb" class="theme-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tu cuenta</li>
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
  <section class="book-table section-b-space p-0 single-table bg-inner">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="table-form">
            <div class="container">
              <div class="row">
                <div class="col-lg-3">
                  <div class="pro_sticky_info" data-sticky_column>
                    <div class="dashboard-sidebar">
                      <div class="profile-top text-center">
                        <div class="profile-detail">
                          <h5 class="fw-bold">{{sess.cliente.nombres}}</h5>
                          <h6>{{sess.cliente.telefono}}</h6>
                          <h6>{{sess.cliente.email}}</h6>
                        </div>
                      </div>
                      <div class="faq-tab">
                        <ul class="nav nav-tabs" id="top-tab" role="tablist">
                          <li class="nav-item"><a data-bs-toggle="tab" class="nav-link active"
                              href="#dashboard">Reservas</a>
                          </li>
                          <li class="nav-item"><a data-bs-toggle="tab" class="nav-link" href="#profile">Cuenta</a>
                          </li>
                          <li class="nav-item"><a v-on:click="logout" class="nav-link"
                              href="javascript:void(0)">Salir</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-9">
                  <div class="product_img_scroll" data-sticky_column>
                    <div class="faq-content tab-content" id="top-tabContent">
                      <div class="tab-pane fade show active" id="dashboard">
                        <div class="dashboard-main">
                          <div class="dashboard-intro">
                            <h5>👋 Hola <span>{{sess.cliente.nombres}}!</span></h5>
                            <p v-if="reservas!=null">Estas son tus reservas con nosotros.</p>
                          </div>
                          <div class="counter-section">
                            <div v-if="loading_reservas" class="text-center">
                              <div class="loadingio-spinner-pulse-pc66029qoh">
                                Cargando reservas...
                                <div class="ldio-dt8bwii34sa">
                                  <div></div>
                                  <div></div>
                                  <div></div>
                                </div>
                              </div>
                            </div>
                            <div v-else>
                              <div class="row">
                                <div v-for="(reserva, index_reserva) in reservas" v-bind:key="index_reserva"
                                  class="col-lg-12">
                                  <div class="card card-body shadow mt-2 mb-2">
                                    <h5 class="fw-bold my-text-success">
                                      {{reserva.cod_reserva}}
                                      <span v-if="reserva.estado==0"
                                        class="badge bg-warning rounded-pill">Pendiente</span>
                                      <span v-if="reserva.estado==1"
                                        class="badge bg-success rounded-pill">Aprobada</span>
                                      <span v-if="reserva.estado==3" class="badge bg-danger rounded-pill">Anulada</span>
                                    </h5>
                                    <div v-for="(servicio, index_servicio) in reserva.servicios"
                                      v-bind:key="index_servicio">
                                      <div v-if="servicio.tipo == 1">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-map-marked-alt"></i>
                                          {{servicio.servicio}}
                                        </h5>
                                      </div>
                                      <div v-if="servicio.tipo == 2">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-box"></i>
                                          Paquete {{servicio.origen.ciudad}} - {{servicio.destino.ciudad}}
                                        </h5>
                                      </div>
                                      <div v-if="servicio.tipo == 3">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-bed"></i>
                                          {{servicio.servicio}}
                                        </h5>
                                      </div>
                                      <div v-if="servicio.tipo == 4">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-plane-departure"></i>
                                          Tiquete {{servicio.origen.ciudad}} - {{servicio.destino.ciudad}}
                                        </h5>
                                      </div>
                                      <div v-if="servicio.tipo == 5">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-user-md"></i>
                                          Asistencia médica
                                        </h5>
                                      </div>
                                      <div v-if="servicio.tipo == 6">
                                        <h5 class="fw-bold my-text-primary"><i class="fas fa-file"></i>
                                          Otros servicios
                                        </h5>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-6 text-end">
                                        <small>Valor</small>
                                        <h5 class="my-text-info fw-bold">{{reserva.total | currency}}</h5>
                                      </div>
                                      <div class="col-6 text-end">
                                        <small>Saldo</small>
                                        <h5 class="my-text-secondary fw-bold">{{reserva.saldo | currency}}</h5>
                                      </div>
                                    </div>
                                    <div class="text-end mt-3">
                                      <a v-bind:href="base_url+'clientes/reserva/'+reserva.id_reserva" target="_blank"
                                        class="btn btn-primary rounded-pill">Ver detalles</a>
                                      <a v-if="(reserva.c_paso == reserva.servicios.length && reserva.reviews < reserva.servicios.length) && reserva.estado == 1"
                                        v-bind:href="base_url+'clientes/review/'+reserva.id_reserva" target="_blank"
                                        class="btn btn-info text-white rounded-pill">Califica tu experiencia</a>
                                    </div>

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="profile">
                        <div class="dashboard-box">
                          <div class="dashboard-title">
                            <h4>Cuenta</h4>
                          </div>
                          <div class="dashboard-detail">

                            <div>
                              <div>
                                <small>Nombres y apellidos</small>
                                <h6 class="my-text-primary fw-bold">{{sess.cliente.nombres}}
                                  {{sess.cliente.apellidos}}</h6>
                              </div>
                              <div>
                                <small>Identificación</small>
                                <h6 class="my-text-primary fw-bold">{{sess.cliente.identificacion}}</h6>
                              </div>
                              <div>
                                <small>Correo electrónico</small>
                                <h6 class="my-text-primary fw-bold">{{sess.cliente.email}}</h6>
                              </div>
                              <div>
                                <small>Teléfono</small>
                                <h6 class="my-text-primary fw-bold">{{sess.cliente.telefono}}</h6>
                              </div>
                            </div>

                          </div>
                        </div>
                        <div class="dashboard-box" style="margin-top: 3rem;">
                          <div class="dashboard-title">
                            <h4>Cambiar contraseña</h4>
                          </div>
                          <div class="dashboard-detail">
                            <transition v-if="msg_validate!=null" appear name="slide-fade">
                              <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i> {{msg_validate}}
                              </div>
                            </transition>
                            <transition v-if="msg_success_pass!=null" appear name="slide-fade">
                              <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> {{msg_success_pass}}
                              </div>
                            </transition>
                            <form v-on:submit.prevent="update_pass">
                              <div class="form-group">
                                <label>Contraseña actual</label>
                                <input v-model="password" type="password" class="form-control">
                              </div>
                              <div class="form-group">
                                <label>Nueva contraseña </label>
                                <input v-model="nuevo_password" type="password" class="form-control">
                              </div>
                              <div class="form-group">
                                <label>Confirma la nueva contraseña </label>
                                <input v-model="nuevo_password_confirm" type="password" class="form-control">
                              </div>
                              <div class="text-end">
                                <button type="submit" class="btn btn-primary rounded-pill">
                                  <i class="fa fa-save"></i> Actualizar
                                </button>
                              </div>
                            </form>
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
  <!-- section start-->
  <section class="small-section dashboard-section bg-inner" data-sticky_parent>

  </section>
  <!-- section end-->
</div>


<script src="<?= base_url() ?>assets/js/app/cliente.js?v=<?= date("YmdHis") ?>"></script>