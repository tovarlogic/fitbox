
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
                        <!-- Display errors returned by createToken -->
                        <script src="https://js.stripe.com/v3/"></script>
                        <?php echo form_open("athlete/membership/payment/".$membership->id); ?>
                          <div class="row">
                              <div class="col-lg-3 text-center">
                                  <i class="pe-7s-user fa-5x text-muted"></i>
                                  <p class="small m-t-md">
                                      <strong>Renovación de </strong> <?php echo $mem->title; ?> <strong>para </strong> <?php echo $user; ?>.
                                  </p>
                              </div> 
                              <div class="col-lg-9">
                                <div class ="row">
                                  <div class="form-group col-md-4"><label>Desde</label> <?php echo form_input($from, $this->input->post('from'), 'readonly="" id="from"');?></div>
                                  <div class="form-group col-md-4">
                                    <label><?php 
                                      if($mem->period == 'M') echo 'Meses a renovar'; else if ($mem->period == 'W') echo "Semanas a renovar"; else if ($mem->period == 'Y') echo "Años a renovar"; else echo "Periodo a renovar"; 
                                    ?></label> 
                                    <?php echo form_dropdown('times', $times_list, ($this->input->post('times'))? $this->input->post('times') : $times_status, 'class="form-control" id="times"');?></div>
                                </div>
                                <div class ="row" id="ajax-container">
                                  <div class="form-group col-md-4">
                                    <label>Hasta</label><h3><?php echo form_input($to, $to, 'readonly="" id="to"');?></h3>
                                  </div>
                                  <div class="form-group col-md-6">
                                    <label>Precio (€)</label><p><h3><?php echo form_input($rate_amount, $price, 'readonly="" id="rate_amount"');?></h3>
                                  </div>
                                </div>
                              </div>
                          </div>

                          <div class="text-right m-t-xs">
                              <button class="btn btn-primary payBtn" id ="payBtn" type="submit">Continuar</button>
                          </div>

                          </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>


<script>

$('#from').datepicker();


$('#times').on("change", function(e){
  var times = $('#times').val();
     var from = $('#from').val();
     var coupon = $('#coupon').val();
     $.ajax({
        url : '<?php echo  site_url("athlete/membershipPaymentRecalc/".$membership->membership_id); ?>',
        type : 'POST'  ,
        data : { times: times, from: from, coupon: coupon },
        dataType: 'json',
        success : function(data){
            document.getElementById('rate_amount').value = data.rate_amount;
            document.getElementById('to').value = data.to;
            document.getElementById('rate_amount2').value = data.rate_amount+' €';
        }
     }); 
});

$('#from').on("change", function(e){
  var times = $('#times').val();
     var from = $('#from').val();
     var coupon = $('#coupon').val();
     $.ajax({
        url : '<?php echo  site_url("athlete/membershipPaymentRecalc/".$membership->membership_id); ?>',
        type : 'POST'  ,
        data : { times: times, from: from, coupon: coupon },
        dataType: 'json',
        success : function(data){
            document.getElementById('rate_amount').value = data.rate_amount;
            document.getElementById('to').value = data.to;
            document.getElementById('rate_amount2').value = data.rate_amount+' €';
        }
     }); 
});

$('#coupon').on("change", function(e){
  var times = $('#times').val();
     var from = $('#from').val();
     var coupon = $('#coupon').val();
     $.ajax({
        url : '<?php echo  site_url("athlete/membershipPaymentRecalc/".$membership->membership_id); ?>',
        type : 'POST'  ,
        data : { times: times, from: from, coupon: coupon },
        dataType: 'json',
        success : function(data){
            document.getElementById('rate_amount').value = data.rate_amount;
            document.getElementById('to').value = data.to;
        }
     }); 
});  

$('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("athlete/membership/renew/".$membership->id); ?>";
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize(),
        dataType: "html",

        success: function(data){
            history.pushState(null, null, url2);
            $('.content').empty();
            $('.content').html(data);
        },

        error: function() { alert("Error posting feed."); }
   });

});                

</script>










      
