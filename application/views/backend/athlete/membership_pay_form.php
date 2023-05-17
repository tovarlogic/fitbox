
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
                          <li>
                              <span><a href='<?php echo base_url(); ?>athlete/profile/<?php echo $user->id;?>' class='html5history'>Planes</a></span>
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
                        
                        <form method="post" id="payment-form" action="<?php echo base_url(); ?>athlete/membership/payment/<?php echo $membership->id; ?>">
                            
                          <div class="text-center m-b-md" id="wizardControl">

                            <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Detalles</a>
                            <a class="btn btn-default" href="#step2" data-toggle="tab">Step 2 - Datos tarjeta</a>

                          </div>

                          <div class="tab-content">

                              <div id="step1" class="p-m tab-pane active">
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
                                          <label>
                                            <?php 
                                              if($mem->period == 'M') echo 'Meses a renovar'; 
                                              else if ($mem->period == 'W') echo "Semanas a renovar"; 
                                              else if ($mem->period == 'Y') echo "Años a renovar"; 
                                              else echo "Periodo a renovar"; 
                                            ?>
                                          </label> 

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
                              </div>

                              <div id="step2" class="p-m tab-pane">
                                <div class="row">
                                    <div class="col-lg-3 text-center">
                                        <i class="pe-7s-credit fa-5x text-muted"></i>
                                        <p class="small m-t-md">
                                            <strong>Renovación de </strong> <?php echo $mem->title; ?> <strong>para </strong> <?php echo $user; ?>.<br/>
                                            <p id="fechas"></p>
                                            <strong>TOTAL A PAGAR </strong> <h2 id="rate_amount2"> </h2><br/>
                                        </p>
                                    </div>

                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                              <label for="card-element">
                                                Tarjeta de crédito o débito
                                              </label>
                                              <div id="card-element">
                                                <!-- A Stripe Element will be inserted here. -->
                                              </div>

                                              <!-- Used to display form errors. -->
                                              <div id="card-errors" role="alert"></div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="text-right m-t-xs">
                                    <button class="btn btn-primary submitWizard payBtn" id ="payBtn" type="submit">Realizar Pago</button>
                                </div>
                                </div>
                              </div>

                          </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>

<script>
// Create a Stripe client.
var stripe = Stripe('<?php echo $public_key; ?>');

// Create an instance of Elements.
var elements = stripe.elements();
var response = '';

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
  base: {
    color: '#32325d',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {
  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
});

// Handle form submission.
var form = document.getElementById('payment-form');

form.addEventListener('submit', function(event) 
{
  event.preventDefault();
  changeLoadingState(true);

  var formu = $(this);
  var url2 = "<?php echo base_url("athlete/membership/renew/".$membership->id); ?>";
 
  $.ajax({
        type: "POST",
        url: formu.attr("action"),
        data: formu.serialize(),
        dataType: "json",

        success: function(data){
            response = data;
            history.pushState(null, null, url2);
            
            confirmPayment();

            //$('.content').empty();
            //$('.content').html(data);
        },

        error: function(error) { 
          alert("Error posting feed.");  
          alert(JSON.stringify(error))
        }
   });
});

var confirmPayment = function(){
  alert(response);
  stripe.confirmCardPayment(response.client_secret, 
  {
    payment_method: 
    {
      card: card,
      billing_details: 
      {
        name: response.client_name,
        email: response.client_email,
        phone: response.client_phone,
      }
    }
  }).then(function(result) {
    if (result.error) {
      // Show error to your customer (e.g., insufficient funds)
      showError(result.error.message);
    } else {
      // The payment has been processed!
      if (result.paymentIntent.status === 'succeeded') 
      {

        changeLoadingState(true);
        document.getElementById("payment-form").classList.add("hidden");
        successAlert();
      }
      else if (result.paymentIntent.status === 'requires_capture') 
      {

        changeLoadingState(true);
        document.getElementById("payment-form").classList.add("hidden");
        capturedAlert();

      }
      else
      {
        changeLoadingState(true);
        document.getElementById("payment-form").classList.add("hidden");
        infoAlert(result.paymentIntent.status);
      }
    }
  });
};
/* ------- Post-payment helpers ------- */

var showError = function(errorMsgText) {
  changeLoadingState(false);
  var errorMsg = document.getElementById("card-errors");
  errorMsg.textContent = errorMsgText;
  setTimeout(function() {
    errorMsg.textContent = "";
  }, 4000);
};

// Show a spinner on payment submission
var changeLoadingState = function(isLoading) {
  if (isLoading) {
    document.getElementById("payBtn").disabled = true;
    // document.getElementById("spinner").classList.remove("hidden");
    // document.getElementById("button-text").classList.add("hidden");
  } else {
    document.getElementById("payBtn").disabled = false;
    // document.getElementById("spinner").classList.add("hidden");
    // document.getElementById("button-text").classList.remove("hidden");
  }
};

var successAlert = function() {
  swal.fire({
        title: "Transacción completada!",
        text: "El cobro ya ha sido efectuado, en breve terminaremos el proceso de renovación de tu plan. Muchas gracias!",
        type: "success"
    },
    function(isConfirm){
        if (isConfirm) {
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        }
    });
};


var capturedAlert = function(m) {
  Swal.fire({
        title: "Transacción aceptada!",
        html: "Estamos renovando tu plan. <br>Muchas gracias!",
        icon: "success",
        timer: 5000,
        timerProgressBar: true,
        onBeforeOpen: () => {
          Swal.showLoading()
          timerInterval = setInterval(() => {
            const content = Swal.getContent()
            if (content) {
              const b = content.querySelector('b')
              if (b) {
                b.textContent = Swal.getTimerLeft()
              }
            }
          }, 100)
        },
        
        onClose: () => {
          clearInterval(timerInterval)
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        },
    },
    function(isConfirm){
        if (isConfirm) {
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        }
    });
};

var infoAlert = function(m) {
  Swal.fire({
        title: "Transacción aceptada!",
        html: "Estamos renovando tu plan. <br>Muchas gracias! (code:"+m+")",
        icon: "info",
        timer: 5000,
        timerProgressBar: true,
        onBeforeOpen: () => {
          Swal.showLoading()
          timerInterval = setInterval(() => {
            const content = Swal.getContent()
            if (content) {
              const b = content.querySelector('b')
              if (b) {
                b.textContent = Swal.getTimerLeft()
              }
            }
          }, 100)
        },
        
        onClose: () => {
          clearInterval(timerInterval)
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        },
    },
    function(isConfirm){
        if (isConfirm) {
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        }
    });
};

var errorAlert = function() {
  swal.fire({
        title: "Transacción cancelada!",
        text: "",
        type: "error"
    },
    function(isConfirm){
        if (isConfirm) {
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        }
    });
};
</script>
<script>

$('#from').datepicker();

document.getElementById("rate_amount2").innerHTML = '<?php echo $price; ?>' +' €';
document.getElementById("fechas").innerHTML = '<strong>Desde </strong> <?php echo date("d/m/Y", strtotime($from['value'])); ; ?> <strong>hasta </strong> <?php echo date("d/m/Y", strtotime($to['value'])); ; ?>';

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
            document.getElementById("fechas").innerHTML = '<strong>Desde </strong>'+ from +' <strong>hasta </strong>'+ data.to ;
            document.getElementById("rate_amount2").innerHTML = data.rate_amount +' €' ;
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
            document.getElementById("fechas").innerHTML = '<strong>Desde </strong>'+ data.from +' <strong>hasta </strong>'+ data.to ;
            document.getElementById("rate_amount2").innerHTML = data.rate_amount +' €' ;
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
            document.getElementById("rate_amount2").innerHTML = data.rate_amount +' €' ;
        }
     }); 
});  

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('a[data-toggle="tab"]').removeClass('btn-primary');
    $('a[data-toggle="tab"]').addClass('btn-default');
    $(this).removeClass('btn-default');
    $(this).addClass('btn-primary');
});
                 
</script>










      
