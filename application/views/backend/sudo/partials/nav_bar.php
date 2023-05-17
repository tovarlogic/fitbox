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
                <a href="<?php echo base_url(); ?>sudo" class="html5history menu-link"> <span class="nav-label">Inicio</span></a>
            </li>
            <li>
                <a href="<?php echo base_url(); ?>sudo/boxes" class="html5history menu-link"> <span class="nav-label">Boxes</span></a>
            </li>
            <li>
                <a href="#"><span class="nav-label">Ejercicios</span><span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href='<?php echo base_url(); ?>sudo/sports' class="html5history menu-link"">Deportes</a></li>
                    <li><a href='<?php echo base_url(); ?>sudo/exercise_materials' class="html5history menu-link"">Materiales</a></li>
                    <li><a href='<?php echo base_url(); ?>sudo/exercise_types' class="html5history menu-link"">Tipos</a></li>
                    <li><a href='<?php echo base_url(); ?>sudo/exercises' class="html5history menu-link"">Ejercicios básicos</a></li>
                    <li><a href='<?php echo base_url(); ?>sudo/exercise_variations' class="html5history menu-link"">Variantes</a></li>
                </ul>
            </li>    
            <li>
                <a href='<?php echo base_url(); ?>sudo/conf/' class="html5history"><span class="nav-label">Configuración</span></a>
            </li> 
        </ul>
    </div>
</aside>