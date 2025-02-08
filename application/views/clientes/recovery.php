<div id="recovery">
  <!-- breadcrumb start -->
  <section class="breadcrumb-section parallax-img pt-0">
    <img src="<?=base_url()?>assets/images/inner-pages/bg-actividades.jpg" class="bg-img img-fluid blur-up lazyload"
      alt="">
    <div class="breadcrumb-content overlay-black">
      <div>
        <h2>Recupera tu cuenta</h2>

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
  <section class="single-section xs-section bg-inner">
    <div class="container">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="card card-body shadow">
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
            <transition appear name="slide-fade">
              <form v-on:submit.prevent="update_pass">

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
                    <i class="fa fa-save"></i> Guardar
                  </button>
                </div>
              </form>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
let token = "<?=$token?>";
</script>
<script src="<?=base_url()?>assets/js/app/recovery.js?v=<?=date("YmdHis")?>"></script>