<?php 
$modules = $this->config->item('modules', 'settings')['staff'];
?>
<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <a href="" >
                <img src="<?php echo base_url(); ?>assets/images/profile.jpg" class="img-circle m-b" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase"><?php echo $user->first_name." ".$user->last_name; ?></span>

                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <small class="text-muted"><?php echo $user->username; ?></small>
                    </a>
                    
                </div>
            </div>
        </div>

        <ul class="nav" id="side-menu">
            <li class="active">
                <a href="<?php echo base_url(); ?>staff" class="html5link-hide-menu menu-link"> <span class="nav-label">Inicio</span></a>
            </li>

            <?php if(in_array('users', $modules[ENVIRONMENT])): ?>
            <li>
                <a href='<?php echo base_url(); ?>staff/users' class="html5link-hide-menu menu-link"">Usuarios</a>
            </li>
            <?php endif; ?>
            <?php if(in_array('gateways', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Cobros</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href='<?php echo base_url(); ?>staff/gateways' class="html5link-hide-menu menu-link">Pasarelas de pago</a></li>
                    <li><a href='<?php echo base_url(); ?>staff/transactions/list/<?php echo date("Y"); ?>/<?php echo date("m"); ?>/all'' class="html5link-hide-menu menu-link">Transacciones</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('booking', $modules[ENVIRONMENT])): ?>
             <li>
                <a href="#"><span class="nav-label">Actividades</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href='<?php echo base_url(); ?>staff/services' class="html5link-hide-menu menu-link">Servicios</a></li>
                    <li><a href='<?php echo base_url(); ?>staff/memberships' class="html5link-hide-menu menu-link">Tarifas</a></li>
                    <li><a href='<?php echo base_url(); ?>staff/coupons' class="html5link-hide-menu menu-link">Descuentos</a></li>
                    <li><a href="<?php echo base_url(); ?>staff/schedules" class="html5link-hide-menu menu-link">Clases</a></li>                   
                    <li><a href='<?php echo base_url(); ?>staff/events' class="html5link-hide-menu">Eventos</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('communications', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#" class="html5history"><span class="nav-label">Comunicación</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                     <li><a href='<?php echo base_url(); ?>staff/emails' class="html5link-hide-menu menu-link">Emails</a></li>
                     <li><a href='<?php echo base_url(); ?>staff/notificactions' class="html5link-hide-menu menu-link">Notificaciones</a></li>
                     <li><a href='<?php echo base_url(); ?>staff/messages' class="html5link-hide-menu menu-link">Mensajes internos</a></li>
                     <li><a href='<?php echo base_url(); ?>staff/sms' class="html5link-hide-menu menu-link">SMS</a></li>
                     <li><a href='<?php echo base_url(); ?>staff/feedback' class="html5link-hide-menu menu-link">Buzón de sugerencias</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('sports', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#" class="html5history"><span class="nav-label">Deporte</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                    <li><a href="panels.html">Materiales</a></li>
                    <li><a href="typography.html">Ejercicios</a></li>
                    <li><a href="buttons.html">Rutinas</a></li>
                    <li><a href="components.html">Entrenamientos</a></li>
                    <li><a href="typography.html">Categorías</a></li>
                    <li><a href="alerts.html">Planes Entrenamiento</a></li>
                </ul> 
            </li>
            <?php endif; ?>
            <?php if(in_array('blog', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#" class="html5history"><span class="nav-label">Blog</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                    <li><a href="contacts.html">Sistema</a></li>
                    <li><a href="contacts.html">Calendario</a></li>
                    <li><a href="contacts.html">Backups</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('shop', $modules[ENVIRONMENT])): ?>
            <li>
               <a href="#" class="html5history"><span class="nav-label">Tienda</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                    <li><a href="contacts.html">Sistema</a></li>
                    <li><a href="contacts.html">Calendario</a></li>
                    <li><a href="contacts.html">Backups</a></li>
                </ul>
            </li> 
            <?php endif; ?>
            <?php if(in_array('finance', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#" class="html5history"><span class="nav-label">Finanzas</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                    <li><a href="contacts.html">Sistema</a></li>
                    <li><a href="contacts.html">Calendario</a></li>
                    <li><a href="contacts.html">Backups</a></li>
                </ul>
            </li> 
            <?php endif; ?>
            <?php if(in_array('stats', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#" class="html5history"><span class="nav-label">Estadisticas</span><span class="fa arrow"></span> </a>
                 <ul class="nav nav-second-level">
                    <li><a href="contacts.html">Sistema</a></li>
                    <li><a href="contacts.html">Calendario</a></li>
                    <li><a href="contacts.html">Backups</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php 
            $group = $this->session->userdata('group');
            if(in_array('settings', $modules[ENVIRONMENT]) AND $this->is_admin == TRUE ): ?>           
            <li>
                <a href='<?php echo base_url(); ?>staff/conf/' class="html5link-hide-menu menu-link"><span class="nav-label">Configuración</span></a>
            </li> 
            <?php endif; ?>

        </ul>
    </div>
</aside>
