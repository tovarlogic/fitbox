
                    
                    
                        <div class="col-lg-9" style="">

                            <div class="row">

                                <div class="col-md-6" style="">
                                    <div class="hpanel">
                                        <div class="panel-heading">
                                            Actividades y clases
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
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
                                                                    <a href="<?php echo base_url(); ?>staff/booking/show/<?php echo $date."/".$sch['startH'].$sch['startM']."/".$sch['id']; ?>" type="button" class="btn btn-default btn-xs html5history_custom">Asistentes <i class="fa fa-group"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    <?php endif ?>
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
                                            <?php $this->load->view('backend/messages'); echo "hola";?>
                                            
                                            <form action="<?php echo base_url(); ?>staff/booking/add/<?php echo $date."/".$time."/".$id; ?>" method="post">
                                                <div class="input-group">
                                                    <select name="search" id="search" class="select2 form-control" title="Seleccione cliente" required>
                                                        <option></option>
                                                        <?php 
                                                            foreach ($options as $opt) 
                                                            {
                                                              echo "<option value='".$opt['id']."'>" .$opt['name']."</option>" ;
                                                            }
                                                        ?>
                                                    </select>
                                                    <?php if(isset($athletes) AND sizeof($athletes) > 0): ?>
                                                    <span class="input-group-btn"> <button type="submit" class="btn"><i class="glyphicon glyphicon-plus small"></i></button> </span>
                                                    <?php endif ?> 
                                                    <span class="input-group-btn"> <button class="btn">Invitado</button> </span>
                                                </div>
                                            </form>  
                                            <table id="example1" class="footable table table-stripped toggle-arrow-tiny default breakpoint footable-loaded" data-page-size="8" data-filter="#filter">
                                                <thead>
                                                <tr>
                                                    <th class="footable-visible footable-sortable">Nombre<span class="footable-sort-indicator"></span></th>
                                                    <th class="footable-visible footable-sortable">Telefono<span class="footable-sort-indicator"></span></th>
                                                    <th data-hide="all" class="footable-sortable" style="display: none;">Acciones<span class="footable-sort-indicator"></span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $rows = 0;
                                                    if(sizeof($athletes) > 0):
                                                        foreach($athletes as $ath):
                                                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                                            <tr class="footable-<?php echo $class;?> footable-detail-show" style="display: table-row;">
                                                                <td class="footable-visible"><?php echo $ath->first_name." ".$ath->last_name;?></td>
                                                                <td class="footable-visible"><?php echo $ath->phone;?></td>
                                                                <td class="footable-visible footable-last-column">
                                                                    <?php if($date >= date("Y-m-d")): ?>
                                                                    <a href="<?php echo base_url(); ?>staff/booking/cancel/<?php echo $date."/".$time."/".$id."/".$ath->id;?>" type="button" class="btn btn-danger btn-xs html5history_delete"><i class="fa fa-times"></i></a>    
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

<script type="text/javascript">

    $('.select2').select2({

            placeholder: "-- Seleccionar cliente a añadir --",
            allowClear: false,

    });

    $('#actividades').on("click", "a.html5history_custom", function(e){
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function(data){
                $('.ajax_container2').empty();
                $('.splash4').css('display', 'block')
            },
            success: function(data){
                $('.splash4').css('display', 'none')
                $('.ajax_container2').html(data);
            },
            error: function (request, status, error) {
                $('.splash4').css('display', 'none')
                $('.ajax_container2').html(request.responseText);
            }
        });
        e.preventDefault();
    });


    $(document).on("click", "a.html5history_delete", function(e){
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function(data){
                $('.ajax_container').empty();
                $('.splash3').css('display', 'block')
            },
            success: function(data){
                $('.splash3').css('display', 'none')
                $('.ajax_container').html(data);
            },
            error: function (request, status, error) {
                $('.splash3').css('display', 'none')
                $('.ajax_container').html(request.responseText);
            }
        });
        e.preventDefault();
    });

</script>