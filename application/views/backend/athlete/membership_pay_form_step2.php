
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
                        <form method="post" id="payment-form" action="<?php echo base_url(); ?>athlete/membership/pay/">
                            <div class="row">
                                    <div class="col-lg-3 text-center">
                                        <i class="pe-7s-credit fa-5x text-muted"></i>
                                        <p class="small m-t-md">
                                            <strong>Renovación de </strong> <?php echo $membership_name; ?> <strong>para </strong> <?php echo $client_name; ?>. <br>
                                            <strong>Desde </strong> <?php echo date("d/m/Y", strtotime($from)); ; ?> <strong>hasta </strong> <?php echo date("d/m/Y", strtotime($to)); ; ?><br><br> 
                                        </p>
                                    </div>

                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                              <label for="card-element">
                                                Tarjeta de crédito o débito
                                              </label>
                                              <div id="card-element">
                                                <!-- A Stripe Element will be inserted here. -->
                                              </div>

                                              <!-- Used to display form errors. -->
                                              <div id="card-errors" role="alert" class="error"></div>
                                            </div>
                                        </div>

                                    <div class="row">
                                      <div class="form-group col-md-6">
                                          <h3><strong>TOTAL A PAGAR: </strong></h3> <h2> <?php echo $price; ?> €</h2><br/>
                                      </div>
                                    </div>
                                  </div>


                              
                                <div class="text-right m-t-xs">
                                    <button class="btn btn-primary payBtn" id ="payBtn" type="submit">
                                      <div class="spinner hidden" id="spinner"></div>
                                      <span id="button-text">Pagar <?php if ($currency == 'usd' OR $currency == 'USD') echo '$ '.$price; else if($currency == 'eur' OR $currency == 'EUR') echo $price.' €'; else echo $price.' '.$currency; ?> </span>
                                      <span id="order-amount"></span>
                                    </button>
                                </div>

                          </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>

<script>
// Create a Stripe client.
var stripe = Stripe('<?php echo $this->config->item('publishable_key'); ?>');

// Create an instance of Elements.
var elements = stripe.elements();

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
  if (event.error) {
     showError(event.error.message);
  }
});

// Handle form submission.
var form = document.getElementById('payment-form');

form.addEventListener('submit', function(event) 
{
  event.preventDefault();
  changeLoadingState(true);

  stripe.confirmCardPayment('<?php echo $secret; ?>', 
  {
    payment_method: 
    {
      card: card,
      billing_details: 
      {
        name: '<?php echo $client_name; ?>',
        email: '<?php echo $client_email; ?>',
        phone: '<?php echo $client_phone; ?>',
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
});

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
  swal.fire({
        title: "Transacción aceptada!",
        text: "Estamos renovando tu plan, una vez terminemos el proceso de renovación efectuaremos el cobro. Muchas gracias!",
        type: "success"
    },
    function(isConfirm){
        if (isConfirm) {
          window.location.href = '<?php echo base_url(); ?>'+'athlete/profile';
        }
    });
};

var infoAlert = function(m) {
  swal.fire({
        title: "Transacción aceptada!",
        text: "Estamos renovando tu plan, una vez terminemos el proceso de renovación efectuaremos el cobro. Muchas gracias! (code:"+m+")",
        type: "info"
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








      
