<?php 
$modules = $this->config->item('modules', 'settings')['athlete'];
?>
<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <a href="<?php echo base_url(); ?>athlete/profile" class="html5history">
                <?php $image = 'profile.jpg';
                if($user->gender == 'M') 
                    $image = 'male2.png'; 
                else 
                    $image = 'female2.png';  
                ?>
                <img src="<?php echo base_url().'assets/images/'.$image; ?>" class="img-circle m-b" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase"><?php echo $user->first_name." ".$user->last_name; ?></span>
                <small class="text-muted"></small>
            </div>
        </div>

        <ul class="nav" id="side-menu">
            <li class="active">
                <a href="<?php echo base_url(); ?>athlete" class="html5link-hide-menu menu-link"> <span class="nav-label">Inicio</span></a>
            </li>
            <?php if(in_array('booking', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Mi Box</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo base_url(); ?>athlete" class="html5link-hide-menu menu-link">Calendario</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('sports', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Entrenamientos</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo base_url(); ?>athlete/plan" class="html5link-hide-menu menu-link">Plan de entreno</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/routines" class="html5link-hide-menu menu-link">Rutinas</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/train_log" class="html5link-hide-menu menu-link">Historico</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/PRs" class="html5link-hide-menu menu-link">Marcas Personales</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/custom" class="html5link-hide-menu menu-link">Personalizados</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/favorite" class="html5link-hide-menu menu-link">Favoritos</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('log_book', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Log Book</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo base_url(); ?>athlete/new_train" class="html5link-hide-menu menu-link">Entrenamiento</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/PRs" class="html5link-hide-menu menu-link">Marca Personal</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/biometrics" class="html5link-hide-menu menu-link">Biometrías</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/sleep" class="html5link-hide-menu menu-link">Sueño</a></li>
                    <li><a href="<?php echo base_url(); ?>athlete/injuries" class="html5link-hide-menu menu-link">Lesiones</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('nutrition', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Nutrición</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo base_url(); ?>athlete/nutrition" class="html5link-hide-menu menu-link">Nutrición</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if(in_array('profile', $modules[ENVIRONMENT])): ?>
            <li>
                <a href="#"><span class="nav-label">Mi cuenta</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo base_url(); ?>athlete/profile" class="html5link-hide-menu menu-link">Mi cuenta</a></li>
                    <li><a href="<?php echo base_url(); ?>auth/change_password" class="html5link-hide-menu menu-link">Cambio de contraseña</a></li>
                    <li><a href="<?php echo base_url(); ?>auth/logout" class="html5link-hide-menu menu-link">Salir</a></li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>

