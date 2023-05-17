<?php $this->load->view('emails/partials/head'); ?>
<body>
<table cellpadding="0" cellspacing="0" border="0" class="bgtc personal" align="center" style="background-color: #f9f9f9; border-collapse: collapse; line-height: 100% !important; margin: 0; padding: 0; width: 100% !important" bgcolor="#f9f9f9">
  <tbody>
  <tr>
    <td>
    <!--[if (gte mso 10)]>
      <tr>
      <td>
      <table style="width: 600px">
    <![endif]-->
      <table style="border-collapse: collapse; margin: auto; max-width: 635px; min-width: 320px; width: 100%" class="main-wrap">
        <tbody>
        <tr>
          <td valign="top">
            <table cellpadding="0" cellspacing="0" border="0" class="reply_header_table" style="border-collapse: collapse; color: #c0c0c0; font-family: 'Helvetica Neue',Arial,sans-serif; font-size: 13px; line-height: 26px; margin: 0 auto 26px; width: 100%">
            </table>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main_wrapper" style="padding: 0 20px">

            <table cellpadding="0" cellspacing="0" border="0" class="comment_wrapper_table admin_comment" align="center" style="-webkit-background-clip: padding-box; -webkit-border-radius: 3px; background-clip: padding-box; border-collapse: collapse; border-radius: 3px; color: #545454; font-family: 'Helvetica Neue',Arial,sans-serif; font-size: 13px; line-height: 20px; margin: 0 auto; width: 100%">
              <tbody>
              <tr>
                <td valign="top" class="comment_wrapper_td">
                  <table cellpadding="0" cellspacing="0" border="0" class="comment_header" style="border: none; border-collapse: separate; font-size: 1px; height: 2px; line-height: 3px; width: 100%">
                    <tbody>
                    <tr>
                      <td valign="top" class="comment_header_td" style="background-color: #ff4000; border: none; font-family: 'Helvetica Neue',Arial,sans-serif; width: 100%" bgcolor="#ff4000">
                        
                      </td>
                    </tr>
                    </tbody>
                  </table>
                  <table cellpadding="0" cellspacing="0" border="0" class="comment_body" style="-webkit-background-clip: padding-box; -webkit-border-radius: 0 0 3px 3px; background-clip: padding-box; border-collapse: collapse; border-color: #dddddd; border-radius: 0 0 3px 3px; border-style: solid solid none; border-width: 0 1px 1px; width: 100%">
                    <tbody>
                    <tr>
                      <td class="comment_body_td content-td" style="-webkit-background-clip: padding-box; -webkit-border-radius: 0 0 3px 3px; background-clip: padding-box; background-color: white; border-radius: 0 0 3px 3px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); color: #525252; font-family: 'Helvetica Neue',Arial,sans-serif; font-size: 15px; line-height: 22px; overflow: hidden; padding: 40px 40px 30px" bgcolor="white">
                          <?php echo $sandbox_body; ?>

                        
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; padding-top: 0; text-align: left !important" align="left">Hola <?php echo ucfirst($customer_name); ?>,
                          </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Te informamos que no hemos conseguido efectuar el cobro de los <?php echo $amount; ?>€ correspondientes al plan <?php echo $mem_name; ?> en <?php echo $box_name; ?>.</p>

                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Por favor, revisa si tus datos bancarios introducidos en GoCardless son correctos. De ser así, quizás se trate de un problema con tu banco. Por el momento no volveremos a intentar realizar el cobro hasta el próximo periodo. </p>

                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Ya que, como decíamos, no vamos a poder reintentar el cobro en los próximos días, sino que tendremos que esperar al próximo periodo, es posible que tu plan caduque. Para evitar esto, resulta necesario que realices el pago de una de las siguientes maneras: 
                          <ul class="unstyled">
                              <li>Directamente <b>en el box</b> en efectivo o tarjeta</li>
                              <li>A través de <b>Fitbox.es</b> mediante tarjeta (si en tu box tuvieran habilitada esta opción).</li>
                          </ul>
                          Si por su inmediatez prefieres pagar por la web, puedes dirigirte a <a href="https://www.fitbox.es/athlete/profile"> Tu cuenta >> Acciones -> renovar</a>
                          </p>
                          
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Si tuvieras dudas sobre cualquier asunto, recuerda que tanto desde Fitbox como el staff de tu box estamos a tu completa disposición. No dudes en contactarnos.</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Un saludo,</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Equipo de FitBox.es</p>
                      </td>
                    </tr>
                   </tbody>
                  </table>
                  </td>
                 </tr>
                </tbody>
              </table>
              <?php $this->load->view('emails/partials/footer'); ?>
