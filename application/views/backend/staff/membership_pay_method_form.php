
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
                          <li>
                              <span><a href='<?php echo base_url(); ?>staff/users' class='html5history'>Usuarios</a></span>
                          </li>
                          <li class="active">
                              <span>Alta en plan </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE USUARIOS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
     <?php endif ?>

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
                        <?php echo form_open("staff/userMembership/changePaymentMethod/".$user->id."/".$membership_id, array('id' => 'Form')); ?>
                          

                            <div class ="row">
                              <div class="form-group col-md-6">
                              <div class="form-group col-md-6">
                                <label>Método pago</label> <?php echo form_dropdown('payment_method', $pay_method_list, ($this->input->post('payment_method'))? $this->input->post('payment_method') : '', 'class="form-control"');?>
                              </div>
                            </div>
                            <div>
                                <?php echo form_submit('submit', 'Cambiar método de pago', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                        <script>

                          $('form').on("submit", function(e){

                              var form = $(this);
                              var url2 = "<?php echo base_url("staff/userMembership/list/".$user->id); ?>";
                              e.preventDefault();
                              $.ajax({
                                  type: "POST",
                                  url: form.attr("action"),
                                  data: form.serialize(),
                                  dataType: "html",
                                  cache: false,

                                  success: function(data){
                                    $('.content').empty();
                                    $('.content').html(data);
                                    history.pushState(null, null, url2);
                                  },

                                  error: function() { alert("Error posting."); }
                             });
                              return false;
                          });

                      </script>
                    </div>
                </div>
            </div>   
                 













      
