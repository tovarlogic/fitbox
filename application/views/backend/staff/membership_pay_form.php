
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
                          <li>
                              <span><a href='<?php echo base_url(); ?>staff/userMembership/list/<?php echo $user->id;?>' class='html5history'>Planes</a></span>
                          </li>
                          <li class="active">
                              <span>Registrar pago </span>
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
                        <?php echo form_open("staff/membershipPayment/".$action, array('id' => 'Form'), array('user_id' => $membership->user_id, 'memberships_user_id' => $membership->id, 'membership_id' => $membership->membership_id)); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Renovación de</label> <h2><?php echo $mem->title; ?></h2></div>
                              <div class="form-group col-md-4"><label>Para</label> <h2><?php echo $user; ?></h2></div>
                              
                            </div>
                            <div class ="row">
                              <?php if($membership->status == 'y') $option = 'readonly=""'; else $option = "";?>
                              <div class="form-group col-md-4"><label>Desde</label> <?php echo form_input($from, $this->input->post('from'), 'id="from"');?></div>
                              <div class="form-group col-md-4"><label>Periodos</label> <?php echo form_dropdown('times', $times_list, ($this->input->post('times'))? $this->input->post('times') : $times_status, 'class="form-control" id="times"');?></div>
                              <div class="form-group col-md-4"><label>Método</label> <?php echo form_dropdown('pp', $pp_list, ($this->input->post('pp'))? $this->input->post('pp') : $pp_status, 'class="form-control"');?></div>
                            </div>   
                            <div class ="row"> 
                              <div class="form-group col-md-4"><label>Descuento</label> <?php echo form_dropdown('coupon', $coupons_list, ($this->input->post('coupon'))? $this->input->post('coupon') : $coupon_status, 'class="form-control" id="coupon"');?></div>
                            </div>                      
                            <div class ="row" id="ajax-container">
                              <div class="form-group col-md-4"><label>Hasta</label><p><h3><?php echo form_input($to, $to, 'readonly="" id="to"');?></h3></div>
                              <div class="form-group col-md-4"><label>Precio (€)</label><p><h3><?php echo form_input($rate_amount, $price, 'readonly="" id="rate_amount"');?></h3></p></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-12"><label>Notas</label> <?php echo form_textarea($notes);?></div>
                            </div> 
                            <div class ="row">
                                <?php echo form_submit('submit', 'Registrar pago', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>
                      <script>

                          $('#from').datepicker();

                          $('form').on("submit", function(e){

                              var form = $(this);
                            var url2 = "<?php echo base_url("staff/userMembership/list/".$membership->user_id); ?>";
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


                        $('#times').on("change", function(e){
                            var times = $('#times').val();
                               var from = $('#from').val();
                               var coupon = $('#coupon').val();
                               $.ajax({
                                  url : '<?php echo  site_url("staff/membershipPaymentRecalc/".$membership->membership_id); ?>',
                                  type : 'POST'  ,
                                  data : { times: times, from: from, coupon: coupon },
                                  dataType: 'json',
                                  success : function(data){
                                      document.getElementById('rate_amount').value = data.rate_amount;
                                      document.getElementById('to').value = data.to;
                                  }
                               }); 
                          });

                        $('#from').on("change", function(e){
                            var times = $('#times').val();
                               var from = $('#from').val();
                               var coupon = $('#coupon').val();
                               $.ajax({
                                  url : '<?php echo  site_url("staff/membershipPaymentRecalc/".$membership->membership_id); ?>',
                                  type : 'POST'  ,
                                  data : { times: times, from: from, coupon: coupon },
                                  dataType: 'json',
                                  success : function(data){
                                      document.getElementById('rate_amount').value = data.rate_amount;
                                      document.getElementById('to').value = data.to;
                                  }
                               }); 
                          });

                        $('#coupon').on("change", function(e){
                            var times = $('#times').val();
                               var from = $('#from').val();
                               var coupon = $('#coupon').val();
                               $.ajax({
                                  url : '<?php echo  site_url("staff/membershipPaymentRecalc/".$membership->membership_id); ?>',
                                  type : 'POST'  ,
                                  data : { times: times, from: from, coupon: coupon },
                                  dataType: 'json',
                                  success : function(data){
                                      document.getElementById('rate_amount').value = data.rate_amount;
                                      document.getElementById('to').value = data.to;
                                  }
                               }); 
                          });


                      </script>
                    </div>
                </div>
            </div>











      
