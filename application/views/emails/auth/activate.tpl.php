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
                        

                        
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; padding-top: 0; text-align: left !important" align="left">Bienvenido a FitBox,</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Para activar tu cuenta recientemente creada, y poder empezar a usarla, necesitamos que nos confirmes que efectivamente has solicitado crearla. Para ello simplemente pulsa en el botón de más abajo. Una vez la actives te pediremos que crees una contraseña de acceso.</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Te informamos que al activar la cuenta aceptas explícitamente que tus datos personales como nombre y apellidos, sexo, fecha de nacimiento, teléfono, DNI y datos bancarios queden registrados en nuestra base de datos. En todo momento tu cuenta bancaria se almacena encriptada y será unicamente accesible por los administradores de tu box. Estos datos son en todo momento TU propiedad privada y nunca los compartiremos con terceros, salvo con los administradores de tu box. En cualquier momento podrás solicitar que tus datos sean borrados, para ello debes de ponerte en contacto con dichos administradores. </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Adicionalmente, activando la cuenta cedes a fitbox.es los datos que registres sobre hábitos alimenticios, resultados de entrenamientos y biometrías. Esta información tampoco será compartida con terceros, salvo con los administradores de tu box y serán empleados únicamente con fines estadísticos, para gestion de entrenamientos y seguimiento del progreso personal. </p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left"><a href="<?php echo base_url(); ?>auth/activate/<?php echo $id."/".$activation;?>" target="_blank">Activar cuenta y aceptar condiciones de privacidad.</a></p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Un saludo,</p>
                          <p class="intercom-align-left" style="line-height: 1.5; margin: 0 0 17px; text-align: left !important" align="left">Equipo FitBox.es</p>
                      </td>
                    </tr>
                   </tbody>
                  </table>
                  </td>
                 </tr>
                </tbody>
              </table>
              <?php $this->load->view('emails/partials/footer'); ?>