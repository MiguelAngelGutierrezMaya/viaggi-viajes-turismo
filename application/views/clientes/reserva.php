<div id="cart">

  <!-- breadcrumb start -->
  <section class="breadcrumb-section parallax-img pt-0">
    <img src="<?= $recursos->BANNER_SECUNDARIO->url ?>" class="bg-img img-fluid blur-up lazyload" alt="">
    <div class="breadcrumb-content overlay-black">
      <div>
        <h2>Detalle de reserva
          <?= $reserva->cod_reserva ?>
        </h2>
        <nav aria-label="breadcrumb" class="theme-breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle de reserva</li>
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
                <div v-if="cart!=null" class="col-xl-12 col-lg-12">
                  <div>
                    <div v-if="cart!=null">
                      <div class="row">
                        <div class="col-md-6 offset-md-3">
                          <?php

                          switch ($reserva->estado) {
                            case 0:
                              ?>
                              <div class="alert alert-warning text-warning fw-bold">
                                <i class="fas fa-clock"></i> Reserva pendiente.
                              </div>
                              <?php
                              break;

                            case 1:
                              ?>

                              <div class="alert alert-success text-success fw-bold">
                                <i class="fa fa-check-circle"></i> Reserva completa.
                              </div>
                              <?php
                              break;

                            case 3:
                              ?>

                              <div class="alert alert-danger text-danger fw-bold">
                                <i class="fa fa-exclamation-circle"></i> Reserva anulada.
                              </div>
                              <?php
                              break;
                          }
                          ?>
                          <div class="card card-body shadow mb-4">
                            <small>Código de reserva</small>
                            <h6 class="fw-bold">
                              <?= $reserva->cod_reserva ?>
                            </h6>
                            <small>Fecha de reserva</small>
                            <h6 class="fw-bold">
                              <?= $reserva->fecha_reg ?>
                            </h6>
                          </div>
                          <h5 class="text-primary fw-bold">Servicios</h5>



                          <?php
                          foreach ($reserva->servicios as $servicio) {

                            ?>
                            <div class="card card-body shadow mb-4">
                              <?php

                              switch ($servicio->tipo) {
                                case 1:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-map-marked-alt"></i>
                                    <?= $servicio->servicio->servicio ?>
                                  </h6>
                                  <small>Pasajeros</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->adultos ?> adultos -
                                    <?= $servicio->ninos ?> niños -
                                    <?= $servicio->infantes ?> infantes
                                  </h6>
                                  <small>Fecha de actividad</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_actividad->f ?>
                                  </h6>
                                  <?php
                                  if ($servicio->horario != null) {
                                    ?>
                                    <small>Horario</small>
                                    <h6 class="fw-bold">
                                      <?= $servicio->horario->desde ?>
                                    </h6>

                                    <?php
                                  }
                                  ?>

                                  <?php

                                  break;

                                case 2:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-plane-departure"></i>
                                    Paquete
                                    <?= $servicio->origen->ciudad ?> -
                                    <?= $servicio->destino->ciudad ?>
                                  </h6>
                                  <small>Pasajeros</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->num_pasajeros ?>
                                  </h6>
                                  <small>Fecha ida</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_ida->f ?>
                                  </h6>
                                  <small>Fecha regreso</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_regreso->f ?>
                                  </h6>



                                  <?php
                                  break;

                                case 3:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-bed"></i>
                                    <?= $servicio->servicio->servicio ?>
                                  </h6>
                                  <small>Pasajeros</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->num_pasajeros ?>
                                  </h6>
                                  <small>Fecha ida</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_ida->f ?>
                                  </h6>
                                  <small>Fecha regreso</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_regreso->f ?>
                                  </h6>



                                  <?php
                                  break;

                                case 4:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-plane-departure"></i>
                                    Tiquete
                                    <?= $servicio->origen->ciudad ?> -
                                    <?= $servicio->destino->ciudad ?>
                                  </h6>
                                  <small>Pasajeros</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->num_pasajeros ?>
                                  </h6>
                                  <small>Fecha ida</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_ida->f ?>
                                  </h6>
                                  <?php
                                  if ($servicio->tipo_tiquete == 'RT') {
                                    ?>
                                    <small>Fecha regreso</small>
                                    <h6 class="fw-bold">
                                      <?= $servicio->fecha_regreso->f ?>
                                    </h6>
                                    <?php
                                  }
                                  ?>



                                  <?php
                                  break;

                                case 5:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-plane-departure"></i>
                                    Asistencia médica
                                    <?= $servicio->origen->ciudad ?> -
                                    <?= $servicio->destino->ciudad ?>
                                  </h6>
                                  <small>Pasajeros</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->num_pasajeros ?>
                                  </h6>
                                  <small>Fecha ida</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_ida->f ?>
                                  </h6>
                                  <small>Fecha regreso</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->fecha_regreso->f ?>
                                  </h6>



                                  <?php
                                  break;

                                case 6:
                                  ?>

                                  <h6 class="fw-bold"> <i class="fas fa-file"></i>
                                    Otros servicios</h6>
                                  <small>Descripción</small>
                                  <h6 class="fw-bold">
                                    <?= $servicio->descripcion ?>
                                  </h6>


                                  <?php
                                  break;
                              }

                              if ($reserva->estado == 1) {
                                ?>
                                <hr>
                                <h6 class="fw-bold">Descarga tus vouchers</h6>

                                <?php
                                foreach ($servicio->vouchers as $voucher) {
                                  ?>
                                  <div class="ms-2">
                                    <a href="<?= $voucher->url ?>" target="_blank">
                                      <i class="fa fa-file-download"></i>
                                      <?= $voucher->titulo ?>
                                    </a>
                                  </div>
                                  <?php
                                }
                                ?>

                                <?php

                              }

                              ?>
                            </div>

                            <?php
                          }
                          ?>
                          <h5 class="text-primary fw-bold">Pago</h5>
                          <div class="card card-body shadow text-end mb-4">
                            <small>Subtotal</small>
                            <h6 class="fw-bold text-primary">
                              <?= $reserva->valor_f ?>
                            </h6>
                            <?php
                            if ($reserva->descuento > 0) {
                              ?>
                              <small>Descuento</small>
                              <h6 class="fw-bold text-primary">
                                <?= $reserva->descuento_f ?>
                              </h6>
                              <?php
                            }
                            ?>
                            <small>Total</small>
                            <h6 class="fw-bold text-primary">
                              <?= $reserva->total_f ?>
                            </h6>
                            <?php
                            if ($reserva->saldo != 0) {
                              ?>
                              <small>Saldo</small>
                              <h6 class="fw-bold my-text-secondary">
                                <?= $reserva->saldo_f ?>
                              </h6>
                              <?php
                            }
                            ?>
                          </div>
                          <?php

                          if (count($reserva->notas) != 0) {
                            ?>
                            <h5 class="text-primary fw-bold">Observaciones</h5>
                            <div class="card card-body shadow  mb-4">
                              <?php
                              foreach ($reserva->notas as $nota) {
                                ?>
                                <div class="card card-body bg-light p-2 mt-2">
                                  <small class="text-muted fst-italic"><?= $nota->fecha_reg ?> -
                                    <?= $nota->usuario ?>
                                  </small>
                                  <div>
                                    <?= $nota->nota ?>
                                  </div>
                                </div>
                                <?php
                              }
                              ?>
                            </div>
                            <?php
                          }

                          if ($reserva->estado == 0) {
                            ?>
                            <!-- <div class="text-center d-grid gap-2 mt-4">
                            <a href="<?= base_url() ?>cart/pay/<?= $id_reserva ?>" class="btn btn-primary rounded-pill"
                              :disabled="loading">
                              Pagar ahora </a>
                          </div> -->


                            <div class="card card-body shadow mt-4 mb-4 text-center">

                              Realiza tu pago a través de los siguientes canales:
                              <div class="mt-3 text-primary">
                                <h6 class="fw-bold">Transferencia a cuenta de ahorros Bancolombia No.
                                  <?= $parametros->TRANSFERENCIAS->valor ?>
                                </h6>
                              </div>
                              <div class="text-primary">
                                <h6 class="fw-bold">Recarga Nequi al No.
                                  <?= $parametros->NEQUI->valor ?>
                                </h6>
                              </div>

                              <div class="mt-3">
                                ¿Alguna duda adicional?
                                <div><a class="btn btn-success rounded-pill"
                                    href="https://api.whatsapp.com/send?phone=<?= $parametros->WHATSAPP->valor ?>"
                                    target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                    Escríbenos
                                  </a></div>

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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="single-section xs-section bg-inner">
    <div class="container">
      <div class="row">

      </div>
    </div>
  </section>

</div>
<script src="<?= base_url() ?>assets/js/app/clientes/reserva.js?v=<?= date("YmdHis") ?>"></script>