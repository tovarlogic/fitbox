
         <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
        <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='/fitbox/athlete' class='html5history'>Inicio</a></li>
                          <li>
                              <span><a href="#" onclick="goTo('athlete','biometrics');">Gestión de Biometrías</a></span>
                          </li>
                          <li class="active">
                              <span>Registro de Altura </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE BIOMETRÍAS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>

      <div class="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        <?php echo $page_title;?>
                    </div>
                    <div class="panel-body">
                        <?php $this->load->view('backend/messages'); ?>
                        <?php echo ($action == 'add') ? form_open("athlete/routine/add/") : form_open("athlete/routine/edit/".$id); ?>
                            <div class ="row">
                              <div class="form-group col-md-3"><label>Deporte</label> 
                                <?php echo form_dropdown('id_sport', $sport_list, ($this->input->post('id_sport'))? $this->input->post('id_sport') : $phase_status, 'class="form-control" id="id_sport"');?>
                              </div>
                              <div class="form-group col-md-3"><label>Fase</label> 
                                <?php echo form_dropdown('id_phase', $phase_list, ($this->input->post('id_phase'))? $this->input->post('id_phase') : $phase_status, 'class="form-control" id="id_phase"');?>
                              </div>
                              <div class="form-group col-md-3"><label>Categoría</label> 
                                <?php echo form_dropdown('id_category', $category_list, ($this->input->post('id_category'))? $this->input->post('id_category') : $category_status, 'class="form-control" id="id_category"');?>
                              </div>
                              <div class="form-group col-md-3"><label>Tipo</label>
                                <?php echo form_dropdown('id_type', $type_list, ($this->input->post('id_type'))? $this->input->post('id_type') : $phase_status, 'class="form-control" id="id_type" onChange="updateForm();"');?>
                              </div>
                            </div>                        
                             <div class="hr-line-dashed"></div>
                            <div id="aditional_form">










      
