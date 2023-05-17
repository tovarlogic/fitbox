      <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='/fitbox/athlete' class='html5history'>Inicio</a></li>
                          <li>Log book</li>
                          <li class="active">
                              <span>Biometrías </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      BIOMETRÍAS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>

      <div class="text-danger" id="infoMessage"><?php echo $this->session->flashdata('message')?></div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#tab-1">Peso</a></li>
                  <li ><a data-toggle="tab" href="#tab-2">Presión Arterial</a></li>
                  <li ><a data-toggle="tab" href="#tab-3">Altura</a></li>
                  <!-- <li ><a data-toggle="tab" href="#tab-4">Colesterol</a></li>
                  <li ><a data-toggle="tab" href="#tab-5">Glucosa</a></li> -->
              </ul>
              <div class="tab-content ">
                      <div id="tab-1" class="tab-pane active">
                          <div class="panel-body">

                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="pull-right">
                                      <a href='<?php echo base_url(); ?>athlete/weight/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                                    </div>
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Buscar registros">
                                    <?php $this->load->view('backend/messages'); ?>
                                    <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Fecha</th>
                                          <th >Peso (kg)</th>
                                          <!-- <th >Variación (kg)</th> -->
                                          <th data-hide="phone">Grasa (%)</th>
                                          <!-- <th data-hide="phone">IMC</th> -->
                                          <th data-hide="phone,tablet">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($weights as $weight): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $weight->date;?></td>
                                            <td ><?php echo $weight->weight;?></td>
                                            <!-- <td><?php   ?></td> -->
                                            <td><?php echo $weight->fat;?></td>
                                            <!-- <td><?php ?></td> -->
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/weight/edit/<?php echo $weight->id; ?>' type="button" class="btn btn-warning btn-xs html5history">Editar</a>
                                              <a href='<?php echo base_url(); ?>athlete/deleteWeight/<?php echo $weight->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                      </tbody>
                                      <tfoot>
                                        <tr>
                                            <td colspan="12">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row">
                                      <button id="weight-whole">Todo</button>
                                    <button id="weight-12months">12 meses</button>
                                    <button id="weight-6months">6 meses</button>
                                    <button id="weight-3months">3 meses</button>
                                        <div class="col-md-12">
                                            <div class="text-center small">
                                                <i class="fa fa-laptop"></i> Gráfica
                                            </div>
                                            <div class="flot-chart" style="height: 220px">
                                                <div class="flot-chart-content" id="flot-area-chart"></div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                              
                          </div>
                      </div>


                      <div id="tab-2" class="tab-pane">
                          <div class="panel-body">

                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="pull-right">
                                      <a href='<?php echo base_url(); ?>athlete/bp/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                                    </div>
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Buscar registros">
                                    <table id="table2" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Fecha</th>
                                          <th >Sistólica</th>
                                          <th >Diastólica</th>
                                          <th data-hide="phone">Pulso</th>
                                          <th data-hide="phone">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($BPs as $bp): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $bp->timestamp;?></td>
                                            <td ><?php echo $bp->systolic;?></td>
                                            <td><?php echo $bp->diastolic;?></td>
                                            <td><?php echo $bp->pulse;?></td>
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/bp/edit/<?php echo $bp->id; ?>' type="button" class="btn btn-warning btn-xs html5history">Editar</a>
                                              <a href='<?php echo base_url(); ?>athlete/deleteBP/<?php echo $bp->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                      </tbody>
                                      <tfoot>
                                        <tr>
                                            <td colspan="12">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row">
                                      <button id="bp-whole">Todo</button>
                                    <button id="bp-12months">12 meses</button>
                                    <button id="bp-6months">6 meses</button>
                                    <button id="bp-3months">3 meses</button>
                                        <div class="col-md-12">
                                            <div class="text-center small">
                                                <i class="fa fa-laptop"></i> Gráfica
                                            </div>
                                            <div class="flot-chart" style="height: 220px">
                                                <div class="flot-chart-content" id="flot-area-chart2"></div>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                              
                          </div>
                      </div>
                      

                      <div id="tab-3" class="tab-pane">
                          <div class="panel-body">
                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="pull-right">
                                      <a href='<?php echo base_url(); ?>athlete/height/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                                    </div>
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Buscar registros">
                                    <table id="table2" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Fecha</th>
                                          <th >Altura</th>
                                          <th data-hide="phone">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($heights as $height): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $height->date;?></td>
                                            <td ><?php echo $height->height;?></td>
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/height/edit/<?php echo $height->id; ?>' type="button" class="btn btn-warning btn-xs html5history">Editar</a>
                                              <a href='<?php echo base_url(); ?>athlete/deleteHeight/<?php echo $height->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning">Borrar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                      </tbody>
                                      <tfoot>
                                        <tr>
                                            <td colspan="12">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                  </div>
                                </div>
                              
                          </div>
                      </div>


                </div>
          </div>
      </div>
     </div>
              
<!-- Vendor scripts -->
<script>

var table = $('.footable').footable();
var table = jQuery('.footable').footable();

$('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

function addMonths(date, months) {
  date.setMonth(date.getMonth() + months);
  return date;
}

var previousPoint = null, previousLabel = null;

$.fn.UseTooltip = function () {
    $(this).bind("plothover", function (event, pos, item) {                         
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
 
                $("#tooltip").remove();
                 
                var x = item.datapoint[0];
                var y = item.datapoint[1];                
 
                console.log(x+","+y)
 
                showTooltip(item.pageX, item.pageY,
                  new Date(x).toLocaleDateString() + "<br/>" + "<strong>" + y + "</strong> (" + item.series.label + ")");
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 20,
        border: '2px solid #4572A7',
        padding: '2px',     
        size: '10',   
        'border-radius': '6px 6px 6px 6px',
        'background-color': '#fff',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}


function getWeightOptions(months)
{
  var options = {
            series: {
                splines: {
                    show: true,
                    tension: 0.30,
                    lineWidth: 1,
                    fill: true,
                    fillColor: {
                        colors: [ { opacity: 0.5 }, { opacity: 0.5 }
                        ]
                    }
                },
                points: { show: true },
            },
            xaxis: {
                mode: "time",
                minTickSize: [1, "month"],
                min: (addMonths(new Date(), months)).getTime(),
                max: (new Date()).getTime(),
                timeBase: "milliseconds",
                timeformat: "%m/%Y",
                autoScale: "none",
            },
            yaxis: { position: 'left', autoScale: 'loose' },
            grid: {
                tickColor: "#f0f0f0",
                hoverable: true,
                mouseActiveRadius: 50,
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#3498db", "#62cb31"],
            tooltip: true,
            zoom: {
              interactive: true
            },
            pan: {
              interactive: true,
              enableTouch: true
            }
        };

  return options;
}

        var chartWeightOptions = {
            series: {
                splines: {
                    show: true,
                    tension: 0.30,
                    lineWidth: 1,
                    fill: true,
                    fillColor: {
                        colors: [ { opacity: 0.5 }, { opacity: 0.5 }
                        ]
                    }
                },
                points: { show: true },
            },
            xaxis: {
                mode: "time",
                timeBase: "milliseconds",
                minTickSize: [1, "month"],
                timeformat: "%m/%Y",
            },
            yaxis: { position: 'left', autoScale: 'loose' },
            grid: {
                tickColor: "#f0f0f0",
                hoverable: true,
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#3498db", "#62cb31"],
            tooltip: true
        };

        var dataset = [
            { data: <?php echo $weight_history['weight']; ?>, label: "Peso"},
            { data: <?php echo $weight_history['fat']; ?>, label: "% Grasa"}
        ];

        var plot = $.plot($("#flot-area-chart"), dataset, chartWeightOptions);
          var y = plot.getAxes().yaxis;
          y.options.min = 0;
          y.options.max = 60.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();

        $("#weight-whole").click(function () {
          var plot = $.plot($("#flot-area-chart"), dataset, chartWeightOptions);
          var y = plot.getAxes().yaxis;
          y.options.min = 0;
          y.options.max = 60.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });


        $("#weight-12months").click(function () { 
          var plot = $.plot($("#flot-area-chart"), dataset, getWeightOptions(- 12));
          plot.UseTooltip();
          var y = plot.getAxes().yaxis;
          y.options.min = 0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

        

        $("#weight-6months").click(function () { 
          var plot = $.plot($("#flot-area-chart"), dataset, getWeightOptions(- 6));
          plot.UseTooltip();
          var y = plot.getAxes().yaxis;
          y.options.min = 0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

        $("#weight-3months").click(function () { 
          var plot = $.plot($("#flot-area-chart"), dataset, getWeightOptions(- 3));
          plot.UseTooltip();
          var y = plot.getAxes().yaxis;
          y.options.min = 0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

        $("#flot-area-chart").UseTooltip();
        /// BP
        /// 
function getBPOptions(months)
{
  var options = {
            series: {
              lines: {
                show: false
              }
            },
            xaxis: {
                mode: "time",
                timeBase: "milliseconds",
                minTickSize: [1, "month"],
                timeformat: "%m/%Y",
                autoScale: "none",
                min: (addMonths(new Date(), months)).getTime(),
                max: (new Date()).getTime()
            },
            yaxis: { position: 'left', autoScale: 'loose' },
            grid: {
                tickColor: "#f0f0f0",
                hoverable: true,
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            colors: [ "#3498db", "#62cb31"],
            tooltip: true
        };

  return options;
}

        var chartBPOptions = {
            series: {
              lines: {
                show: false
              }
            },
            xaxis: {
              mode: "time",
              timeBase: "milliseconds",
              minTickSize: [1, "month"],
              timeformat: "%m/%Y",
            },
            yaxis: {
              autoScale: 'loose',
              min: 40,
            },
            grid: {
                tickColor: "#f0f0f0",
                hoverable: true,
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f'
            },
            tooltip: true
        };

        var data3 = <?php echo $bp_history; ?>;

        var data3_points = {
          //do not show points
          radius: 0,
          errorbars: "y",
          yerr: {show:true, upperCap: "-", lowerCap: "-", radius: 5}
        };

        var data = [
          {color: "green", lines: {show: false}, points: data3_points, data: data3, label:"Presión arterial"}
        ];


          var plot = $.plot($("#flot-area-chart2"), data, chartBPOptions);
          var y = plot.getAxes().yaxis;
          y.options.min = 40;
          y.options.max = 200.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();



        $("#bp-whole").click(function () {
          var plot = $.plot($("#flot-area-chart2"), data, chartBPOptions);
          var y = plot.getAxes().yaxis;
          y.options.min = 40;
          y.options.max = 200.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });


        $("#bp-12months").click(function () { 
          var plot = $.plot($("#flot-area-chart2"), data, getBPOptions(- 12));
          var y = plot.getAxes().yaxis;
          y.options.min = 40;
          y.options.max = 200.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

        $("#bp-6months").click(function () { 
          var plot = $.plot($("#flot-area-chart2"), data, getBPOptions(- 6));
          var y = plot.getAxes().yaxis;
          y.options.min = 40;
          y.options.max = 200.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

        $("#bp-3months").click(function () { 
          var plot = $.plot($("#flot-area-chart2"), data, getBPOptions(- 3));
          var y = plot.getAxes().yaxis;
          y.options.min = 40;
          y.options.max = 200.0;
          y.options.autoScaleMargin = 0.1;
          y.options.autoScale = "loose";
          y.options.growOnly = false;
          plot.setupGrid(true);
          plot.draw();
        });

</script>
