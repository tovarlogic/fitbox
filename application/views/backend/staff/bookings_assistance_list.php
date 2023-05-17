

                                
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
                                            <form action="<?php echo base_url(); ?>staff/addBooking/<?php echo $date."/".$time."/".$id; ?>" method="post">
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
                                                    <?php if(sizeof($options) > 0): ?>
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

