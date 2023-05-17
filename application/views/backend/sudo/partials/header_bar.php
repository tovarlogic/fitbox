<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>FITBOX.ES </span>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">FITBOX.ES</span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a class="" href="profile.html">Mi perfil</a>
                    </li>
                    <li>
                        <a class="" href="<?php echo base_url(); ?>auth/logout">Logout</a>
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
                            <a href="<?php echo base_url(); ?>staff/account/profile" class="html5history">Cuenta</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>staff/account/memberships" class="html5history">Planes contratados</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>auth/change_password" class="html5history">Cambiar contraseña</a>
                        </li>
                        <li class="summary"><a href="<?php echo base_url(); ?>auth/logout">Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    </nav>
</div>