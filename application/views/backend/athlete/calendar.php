      <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>athlete' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span>Calendario de Clases y Actividades </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE CLASES Y ACTIVIDADES
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>
      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Calendario
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-12">
                     <iframe src="<?php echo base_url(); ?>calendar/index" frameBorder="0" scrolling="no" width="100%" height="1500" ></iframe> 
                  </div>
                </div>
                
              </div>
            </div>
        </div>
     </div>


<!-- Vendor scripts -->
<script src="<?php echo base_url(); ?>assets/vendor/jquery/dist/jquery.min.js"></script>
