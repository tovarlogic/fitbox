<div class="row">
            <div class="col-lg-12 text-center m-t-md">
                <h2>
                    <?php echo $box->name; ?>
                </h2>
            </div>
        </div>

        <!-- Shortcuts -->
        <?php $this->load->view('backend/staff/partials/shortcuts'); ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Dashboard information and statistics
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div style="" class="col-xs-6 col-sm-4">
                                <div class="hpanel hbggreen">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Clientes activos</h3>
                                            <p class="text-big font-light"><?php echo $members['active'] + $members['grace']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-xs-6 col-sm-4">
                                <div class="hpanel hbgblue">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Clientes sin plan</h3>
                                            <p class="text-big font-light"><?php echo $members['pending'] + $members['no_plan']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-xs-6 col-sm-4">
                                <div class="hpanel hbgyellow">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Invitados</h3>
                                            <p class="text-big font-light"><?php echo $members['guests']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center small">
                                    <i class="fa fa-laptop"></i> Total de clientes (a mes vencido) últimos 12 meses
                                </div>
                                <div class="flot-chart" style="height: 160px">
                                    <div class="flot-chart-content" id="flot-active-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div style="" class="col-xs-6 col-sm-3">
                                <div class="hpanel hbgblue">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Altas (mes)</h3>
                                            <p class="text-big font-light"><?php echo $members['new']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-xs-6 col-sm-3">
                                <div class="hpanel hbggreen">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Renovaciones (mes)</h3>
                                            <p class="text-big font-light"><?php echo $members['renew']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-xs-6 col-sm-3">
                                <div class="hpanel hbgorange">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3>Caducados (mes)</h3>
                                            <p class="text-big font-light"><?php echo $members['expired']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-xs-6 col-sm-3">
                                <div class="hpanel hbgred">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3> Bajas (mes)</h3>
                                            <p class="text-big font-light"><?php echo $members['cancelled']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center small">
                                    <i class="fa fa-laptop"></i> Altas y Bajas (a mes vencido) últimos 12 meses
                                </div>
                                <div class="flot-chart" style="height: 160px">
                                    <div class="flot-chart-content" id="flot-stats-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Right sidebar -->
    <?php $this->load->view('backend/sudo/partials/right_bar'); ?>   

<?php $this->load->view('backend/partials/footer'); ?>
