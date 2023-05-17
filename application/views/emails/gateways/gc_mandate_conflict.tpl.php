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

                        
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; padding-top: 0; text-align: left !important" align="left">Hola,
                          </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Te informamos que, acabamos de detectar que se ha creado manualmente un nuevo mandato de domiciliación bancaria automática en la pasarela GoCardless (GC) y que existe un conflicto que requiere de tu atención urgentemente.</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left"> Hemos comparado los datos (email y nombre; o si existe el campo "fitbox_user_id") del cliente en GC con los que tenemos registrados en FitBox y no coinciden inequívocamente. Resulta imprescindible que cualquiera de los administradores del box, confirme manualmente a que usuario está dirigido el mandato para que este sea efectivo. Esto se puede hacer en <a href="https://www.fitbox.es/staff/gateways/conflicts"> Cobros >> conflictos </a></p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left"> Aprovechamos para resumir aquí la información de GoCardless más relevante: <br>
                            <ul class="unstyled">
                              <li><b>Referencia del mandato:</b> <?php echo $reference; ?></li>
                              <li><b>Nombre del cliente:</b> <?php echo $customer_name; ?></li>
                              <li><b>E-mail del cliente:</b> <?php echo $customer_mail; ?></li>
                              <li><b>ID del cliente:</b> <?php echo $customer_id; ?></li>
                            </ul>
                          </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left"> Por favor, considere realizar todas las gestiones de cobros a través de Fitbox, así se evitarían problemas originados por errores humanos. Estos errores podrían originar, entre otros problemas, conlictos como el actual, devolución de cobros e incluso duplicidad de cobros a un mismo cliente.</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left"> Aprovechamos la ocasión para recordar las recomendaciones que ayudan a evitar conflictos potenciales si, apesar de todo, decida seguir gestionando cobros manualmente a través de gocardless.com: <br>
                            <ul class="unstyled">
                              <li>Si crea nuevos clientes no olvide añadir un "campo personalizado" llamado <b>fitbox_user_id</b> y cuyo valor es el nº de socio del cliente que se puede consultar en Fitbox.es</li>
                              <li>Si crea domiciliaciones (subscripciones) no olvide añadir un "campo personalizado" llamado <b>fitbox_plan_id</b> y cuyo valor es el nº de la tarifa contratada y que se puede consultar en Fitbox.es</li>
                            </ul>
                          </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Si tuviera dudas sobre cualquier asunto, recuerde que estamos a su disposición. No dude en contactarnos.</p>
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
