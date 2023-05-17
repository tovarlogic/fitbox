/**
 * HOMER - Responsive Admin Theme
 * version 1.8
 *
 */

$(document).ready(function () {

    // Set your publishable key
    Stripe.setPublishableKey('<?php echo $this->config->item('stripe_publishable_key'); ?>');

    // Callback to handle the response from stripe
    function stripeResponseHandler(status, response) {
        if (response.error) {
            // Enable the submit button
            $('#payBtn').removeAttr("disabled");
            // Display the errors on the form
            $('.card-errors').addClass('alert alert-danger');   
            $(".card-errors").html('<p>'+response.error.message+'</p>');
        } else {
            var form$ = $("#paymentFrm");
            // Get token id
            var token = response.id;
            // Insert the token into the form
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            // Submit form to the server
            form$.get(0).submit();
        }
    }

    // On form submit
    $("#paymentFrm").submit(function() {
        // Disable the submit button to prevent repeated clicks
        $('#payBtn').attr("disabled", "disabled");
    
        // Create single-use token to charge the user
        Stripe.createToken({
            number: $('#card_number').val(),
            exp_month: $('#card_exp_month').val(),
            exp_year: $('#card_exp_year').val(),
            cvc: $('#card_cvc').val()
        }, stripeResponseHandler);
    
        // Submit from callback
        return false;
    });

};