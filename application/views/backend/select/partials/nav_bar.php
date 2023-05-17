<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <a href="#" onclick="goTo('guest','public');">
                <img src="<?php echo base_url(); ?>assets/images/profile.jpg" class="img-circle m-b" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase"><?php echo $user->first_name." ".$user->last_name; ?></span>
                <small class="text-muted"><?php if($user->username != null) echo "(".$user->username.")"; else  ?></small>
            </div>
        </div>

        <ul class="nav" id="side-menu">
            <li class="active">
                <a href="<?php echo base_url(); ?>guest"> <span class="nav-label">Inicio</span></a>
            </li>
        </ul>
    </div>
</aside>
