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
                           <h2>Completa tu reserva</h2>
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
            <div v-if="cart!=null" class="col-xl-12 col-lg-12">
               <div>
                  <div v-if="cart!=null">
                     <div class="row">
                        <div class="col-md-6 offset-md-3">
                           <div class="card card-body text-center shadow mt-3 mb-3">
                              <h4> 👋 Hola {{sess.cliente.nombres}}!</h4>
                              <h6>
                                 Procederás al registro de tu reserva. Para continuar presion el botón <strong>Reservar Ahora</strong> para terminar.
                              </h6>
                           </div>
                           <div class="card card-body shadow">
                              <h4 class="fw-bold">Detalle de la reserva</h4>
                              <div v-for="(item, index) in cart" v-bind:key="index">
                                 <h6 class="fw-bold my-text-primary">{{item.servicio}}</h6>
                                 <h6 class="text-end fw-bold">{{item.valor | currency}}</h6>
                              </div>
                           </div>
                           <hr>
                           <h4>Subtotal</h4>
                           <h4 class="text-end fw-bold my-text-secondary">{{subtotal | currency}}</h4>
                           <div v-if="descuento!=0">
                              <h4>Descuento</h4>
                              <h4 class="text-end fw-bold my-text-secondary">{{descuento | currency}}</h4>
                           </div>
                           <h4>Total</h4>
                           <h4 class="text-end fw-bold my-text-secondary">{{total | currency}}</h4>
                           <div class="text-center d-grid gap-2 mt-4">
                              <button v-on:click="set_reserva()" class="btn btn-primary rounded-pill"
                                 :disabled="loading">
                                 Reservar ahora </button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

</div>
<script src="<?=base_url()?>assets/js/app/checkout.js?v=<?=date("YmdHis")?>"></script>