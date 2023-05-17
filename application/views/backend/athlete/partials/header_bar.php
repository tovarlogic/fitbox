<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span><?php echo $box->name; ?></span>
    </div>
    <span class="label label-success pull-right">v.1</span>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary"><?php echo $box->name; ?></span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="<?php echo base_url(); ?>athlete/profile" class="html5history"><i class="pe-7s-id"></i> Mi cuenta</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url(); ?>auth/change_password" class="html5history"> <i class="pe-7s-key"></i> Cambiar contraseña</a>
                    </li>
                    <?php if($many_profiles === TRUE): ?>
                    <li>
                        <a href="<?php echo base_url(); ?>select" class="html5history"><i class="pe-7s-users"></i> Cambiar tipo de perfil</a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a class="" href="<?php echo base_url(); ?>auth/logout"><i class="pe-7s-power"></i> Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="pe-7s-user"></i>
                    </a>
                    <ul class="dropdown-menu hdropdown notification animated fadeIn">
                        <li>
                            <a href="<?php echo base_url(); ?>athlete/profile" class="html5history"><i class="pe-7s-id"></i> Mi cuenta</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>auth/change_password" class="html5history"> <i class="pe-7s-key"></i> Cambiar contraseña</a>
                        </li>
                        <?php if($many_profiles === TRUE): ?>
                        <li>
                            <a href="<?php echo base_url(); ?>select" class="html5history"><i class="pe-7s-users"></i> Cambiar tipo de perfil</a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a class="" href="<?php echo base_url(); ?>auth/logout"><i class="pe-7s-power"></i> Cerrar sesión</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    </nav>
</div>
