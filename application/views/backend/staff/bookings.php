<?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
<div class="small-header">
  <div class="hpanel">
      <div class="panel-body">
          <div id="hbreadcrumb" class="pull-right">
              <ol class="hbreadcrumb breadcrumb">
                  <li><a href='<?php echo base_url(); ?>staff' class='html5history'>Inicio</a></li>
                  <li class="active">
                      <span>Reservas </span>
                  </li>
              </ol>
          </div>
          <h2 class="font-light m-b-xs">
              GESTIÓN DE RESERVAS
          </h2>
          <small>Dando el mejor servicio posible</small>
      </div>
  </div>
</div>
<?php endif ?>
<!-- Shortcuts -->
<?php $this->load->view('backend/staff/partials/shortcuts'); ?>

<div class="row">

    <div class="col-lg-12" style="">
        <div class="hpanel">
            <div class="panel-heading">
                Reservas
            </div>
            <div class="panel-body">

            <p>
                Consulta el histórico de reservas de sus clientes para cada actividad programada y si es necesario puede modificar o añadir reservas nuevas.
            </p>
                <div class="row">

                    <div class="col-lg-3 " style="">
                        <div id="datepicker" data-date-format="yyyy-mm-dd" data-date="<?php echo $date;?>"></div>
                    </div>
                    <div class="splash3" style = "display:none" align="center"> 
                        <img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" class="center"/> 
                    </div>
                    <div class="ajax_container">
                        <div class="col-lg-9" style="">

                            <div class="row">

                                <div class="col-md-6" style="">
                                    <div class="hpanel">
                                        <div class="panel-heading">
                                            Actividades y clases
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table id="actividades" class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Hora</th>
                                                        <th>Actividad</th>
                                                        <th>Ocupación</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    
                                                    <?php $rows = 0;
                                                    if($schedule):
                                                        foreach($schedule as $sch):
                                                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                                            <tr>
                                                                <td><?php echo $sch['startH'].":".$sch['startM'];?></td>
                                                                <td>
                                                                    <span class="font-bold" style="color:#<?php echo $sch['color'];?>"><?php echo $sch['name'];?></span>
                                                                    <?php echo $sch['reservations']."/".$sch['space'];?>
                                                                </td>
                                                                <?php 
                                                                    $width = $sch['reservations']*100/$sch['space'];
                                                                    if($width < 20) $bar_color = 'info';
                                                                    else if($width < 70) $bar_color = 'success';
                                                                    else if($width < 90) $bar_color = 'warning';
                                                                    else if($width < 100) $bar_color = 'warning2';
                                                                    else $bar_color = 'danger';

                                                                ?>

                                                                <td>
                                                                    <div class="progress m-t-xs full progress-small">
                                                                        <div style="width: <?php echo $width."%";?>" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?php echo $width;?>" role="progressbar" class=" progress-bar progress-bar-<?php echo $bar_color;?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-right">
                                                                    <a href="<?php echo base_url(); ?>staff/bookings/<?php echo $date."/".$sch['startH'].$sch['startM']."/".$sch['id']; ?>" type="button" class="btn btn-default btn-xs html5history_custom">Asistentes <i class="fa fa-group"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="splash4" style = "display:none" align="center"> 
                                    <img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" class="center"/> 
                                </div>
                                <div class="ajax_container2">
                                <div class="col-md-6" style="">
                                    <div class="hpanel">
                                        <div class="panel-heading">
                                            Asistentes
                                        </div>
                                        <div class="panel-body">
                                            <?php $this->load->view('backend/messages'); ?>
                                            <?php if(is_array($options) AND sizeof($options) > 0): ?>
                                            <div class="row show-grid">
                                                <form action="<?php echo base_url(); ?>staff/addBooking/<?php echo $date."/".$time."/".$id; ?>" method="post">
                                                    <div class="col-xs-12 col-md-8">
                                                        <select name="clientes" id="clientes" class="select2 form-control" title="Seleccione cliente" required>
                                                            <option></option>
                                                            <?php 
                                                                foreach ($options as $opt) 
                                                                {
                                                                  echo "<option value='".$opt['id']."'>" .$opt['name']."</option>" ;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-md-4">
                                                        <?php if(is_array($options) AND sizeof($options) > 0): ?>    
                                                        <span class="input-group-btn"> 
                                                            <button type="submit" class="btn btn-info"> Crear reserva</button>     
                                                        </span>
                                                        <?php endif ?>
                                                    </div>
                                                </form>   
                                            </div>                                             
                                            <?php endif ?>

                                            <?php if(is_array($guests) AND sizeof($guests) > 0): $qtty = array('1' =>'1', '2' =>'2', '3' =>'3', '4' =>'4', '5' =>'5'); ?>
                                            <div class="row show-grid">
                                                <form action="<?php echo base_url(); ?>staff/addGuestBooking/<?php echo $date."/".$time."/".$id; ?>" method="post">
                                                    <div class="col-xs-12 col-md-8">
                                                        <select name="invitados" id="invitados" class="select2 form-control" title="Seleccione cliente" required>
                                                            <option></option>
                                                            <?php 
                                                                foreach ($guests as $opt) 
                                                                {
                                                                  echo "<option value='".$opt['user_id']."'>" .$opt['name']."</option>" ;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-md-2">
                                                        <select name="qtty" id="qtty" class="form-control">
                                                            <?php 
                                                                foreach ($qtty as $key => $value) 
                                                                {
                                                                  echo "<option value='".$value."'>" .$key."</option>" ;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-md-2">
                                                        <span class="input-group-btn"> 
                                                            <button type="submit" class="btn btn-info">Crear</button> 
                                                     
                                                        </span>
                                                    </div>
                                                </form>
                                            </div> 
                                            <?php endif ?>                                              
                                                
                                            <?php if($id != null): ?>
                                            <div class="row show-grid">
                                                <a href="<?php echo base_url(); ?>staff/addGuestAndBooking/<?php echo $date."/".$time."/".$id."/"?>" type="button" class="btn btn-outline btn-info html5history"> Crear invitado y reserva (1 click)</a> 
                                            </div>
                                            <?php endif ?>
                                            <table id="asistentes" class="footable table table-stripped toggle-arrow-tiny default breakpoint footable-loaded" data-page-size="8" data-filter="#filter">
                                                <thead>
                                                <tr>
                                                    <th class="footable-visible footable-sortable">Nombre<span class="footable-sort-indicator"></span></th>
                                                    <th class="footable-visible footable-sortable">Telefono<span class="footable-sort-indicator"></span></th>
                                                    <th data-hide="all" class="footable-sortable" style="display: none;">Acciones<span class="footable-sort-indicator"></span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $rows = 0;
                                                    if(is_array($athletes) AND sizeof($athletes) > 0):
                                                        foreach($athletes as $ath):
                                                            if($ath->muID == null)
                                                                $name = $ath->first_name." ".$ath->last_name." (prueba ".$ath->qty." persona/s)";
                                                            else
                                                                $name = $ath->first_name." ".$ath->last_name;    
                                                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                                            <tr class="footable-<?php echo $class;?> footable-detail-show" style="display: table-row;">
                                                                <td class="footable-visible"><?php echo $name?></td>
                                                                <td class="footable-visible"><?php echo $ath->phone;?></td>
                                                                <td class="footable-visible footable-last-column">
                                                                    <?php if($date >= date("Y-m-d")): ?>
                                                                    <a href="<?php echo base_url(); ?>staff/deleteBooking/<?php echo $date."/".$time."/".$id."/".$ath->id;?>" type="button" class="btn btn-danger btn-xs html5history_delete"><i class="fa fa-times"></i></a>    
                                                                <?php endif ?> 
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    <?php endif ?>     
                                                    </tbody>   
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




            </div>
        </div>

    </div>

</div>


<!-- Vendor scripts -->





<script>

  var table = jQuery('.footable').footable();
  var url = "<?php echo base_url(); ?>"+"staff/bookings/";

  jQuery('#datepicker').datepicker({
    language: "es",
    format: "yyyy-mm-dd",
    clearBtn: true,
    weekStart: 1
    

  });
    jQuery('#datepicker').on('changeDate', function(e) {
        var d = e.date;        
        var fecha = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);

        $.ajax({
            url: url+fecha,
            type: 'get',
            dataType: 'html',
            beforeSend: function(data){
                $('.content').empty();
                $('.splash2').css('display', 'block')
            },
            success: function(data){
                $('.splash2').css('display', 'none')
                $('.content').html(data);
            },
            error: function (request, status, error) {
                $('.splash2').css('display', 'none')
                $('.content').html(request.responseText);
            }
        });
    });

    jQuery('#actividades').on("click", "a.html5history_custom", function(e){
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function(data){
                $('.content').empty();
                $('.splash2').css('display', 'block')
            },
            success: function(data){
                $('.splash2').css('display', 'none')
                $('.content').html(data);
            },
            error: function (request, status, error) {
                $('.splash2').css('display', 'none')
                $('.content').html(request.responseText);
            }
        });
        e.preventDefault();
    });

jQuery('#asistentes').on("click", "a.html5history_delete", function(e){
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function(data){
                $('.content').empty();
                $('.splash2').css('display', 'block')
            },
            success: function(data){
                $('.splash2').css('display', 'none')
                $('.content').html(data);
            },
            error: function (request, status, error) {
                $('.splash2').css('display', 'none')
                $('.content').html(request.responseText);
            }
        });
        e.preventDefault();
    });


    jQuery('form').on("submit", function(e){
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: "html",
            beforeSend: function(data){
                $('.content').empty();
                $('.splash2').css('display', 'block')
            },
            success: function(data){
                $('.splash2').css('display', 'none')
                $('.content').html(data);
            },
            error: function (request, status, error) {
                $('.splash2').css('display', 'none')
                $('.content').html(request.responseText);
            }
       });
        return false;
    });  

    jQuery('#clientes').select2({

            placeholder: "-- Seleccionar cliente --",
            allowClear: false,

    });

    jQuery('#invitados').select2({

            placeholder: "-- Seleccionar invitado --",
            allowClear: false,

    });


</script>