<?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
        <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>staff' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span>Users </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE COBROS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
<?php endif ?>

<!-- Shortcuts -->
<?php $this->load->view('backend/staff/partials/shortcuts'); ?>

<!-- Gateways -->
      <div class="row">
            <div class="col-lg-12">
                    <div class="panel-heading font-bold">
                        Pasarelas de Pago
                    </div>                    
            </div>
        </div>
<!-- Messages -->
<?php $this->load->view('backend/messages'); ?>
<!-- Gateway panels -->
<?php $items = 0;
if(!empty($gateways)):
  foreach($gateways as $gateway): 
    

    if($items == 0) echo "<div class='row'>";

    // si multiplo de 4
    if($items%4==0):?>
      </div>
      <div class="row">
    <?php endif; $items++;?>

        <div class="col-xs-12 col-sm-6 col-md-3" style="">
              <div class="hpanel <?php if($gateway->type == 'online') echo 'hbgblue'; ?>">
                  <div class="panel-body">
                    <div class="pull-left">
                      <?php if($gateway->demo == 1): ?>
                      <span class="label label-danger">Demo</span>
                      <?php endif; ?>
                    </div>
                      <a class="small-header-action html5history" href="<?php echo base_url().'staff/gateways/edit/'.$gateway->id; ?>">
                          <div class="clip-header" style="font-size:15px;">
                              <i class="fa fa-gear"></i>
                          </div>
                      </a>
                      <div class="text-center">
                          <h2 class="m-b-xs font-bold"><?php echo $this->lang->line('gateways_'.$gateway->name.'_name');?></h2>
                          <p ><?php echo $this->lang->line('gateways_'.$gateway->name.'_description');?></p>                
                          
                          <?php  
                          if($gateway->active !== '1')
                          {
                            echo '<span class="label label-warning">Desabilitado </span>';
                          }
                          else if ($gateway->name != 'gocardless')
                          {
                            echo '<span class="label label-success">Activo</span>';
                          }
                          else
                          {
                            if(empty($show_gateways['gocardless']))
                            {
                                echo '<a href="'.base_url().'oauth/gocardless/connect" class="btn btn-xs btn-default html5history"><i class="fa fa-sign-in"></i> Conectar</a>';
                            }
                            else if( $show_gateways['gocardless'] == 'action_required')
                            {
                                echo '<a href="'.base_url().'oauth/gocardless/verify" class="btn btn-xs btn-warning2 html5history"><i class="fa fa-sliders"></i> Completar datos</a> o ';

                                echo '<a href="'.base_url().'oauth/gocardless/update" class="btn btn-xs btn-default html5history"><i class="fa fa-refresh"></i> Actualizar</a>';
                            }
                            else if( $show_gateways['gocardless'] == 'in_review')
                            {
                                echo '<span class="label label-primary2">En revisión</span> o ';
                                echo '<a href="'.base_url().'oauth/gocardless/update" class="btn btn-xs btn-default html5history"><i class="fa fa-refresh"></i> Actualizar</a>';
                            }
                            else if( $show_gateways['gocardless'] == 'successful')
                            {
                                echo '<span class="label label-success">Activo</span>';
                            }

                          }
                          ?>
                    
                      </div>
                  </div>
              </div>
          </div>
            
  <?php endforeach ?>
<?php echo '</div>'; ?> 
<?php endif ?>   
   
      
      

