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



<script>

    $(function () {

        var data5 = <?php echo $members['active_history']; ?>;
        var data6 = <?php echo $members['pending_history']; ?>;
        var data7 = <?php echo $members['guests_history']; ?>;

        /**
         * Flot charts data and options
         */

        var dataset2 = [
            { data: data5, label: "Activos"},
            { data: data6, label: "Sin plan"},
            { data: data7, label: "Invitados"},
        ];


        var chartUsersOptions2 = {
            series: {
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1,
                    fill: 0
                },
            },
            xaxis: {
                mode: "categories",
                showTicks: false,
                gridLines: false
            },
            grid: {
                tickColor: "#f0f0f0",
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#62cb31", "#3498db", "#ffb606"]
        };

        $.plot($("#flot-active-chart"), dataset2, chartUsersOptions2);

        var data1 = <?php echo $members['renew_history']; ?>;
        var data2 = <?php echo $members['cancelled_history']; ?>;
        var data3 = <?php echo $members['new_history']; ?>;
        var data4 = <?php echo $members['expired_history']; ?>;

        var dataset = [
            { data: data3, label: "Altas"},
            { data: data1, label: "Renovaciones"},
            { data: data4, label: "Caducados"},
            { data: data2, label: "Cancelaciones"},
        ];

        
        var chartUsersOptions = {
            series: {
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1,
                    fill: 0
                },
            },
            xaxis: {
                mode: "categories",
                showTicks: false,
                gridLines: false
            },
            grid: {
                tickColor: "#f0f0f0",
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#3498db", "#62cb31", "#ffb606", "#FF9898"]
        };

        $.plot($("#flot-stats-chart"), dataset, chartUsersOptions);



    });

</script>
