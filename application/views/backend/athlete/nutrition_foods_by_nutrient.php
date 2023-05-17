
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading hbuilt">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Alimentos
              </div>
              <div class="panel-body">


                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th>Grupo</th>
                            <th>Alimento</th>
                            <th><?php echo $name." (".$unit.")"; ?></th>
                            <th data-hide="phone"> Detalles</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          foreach($foods as $food): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?> 
                          <tr class=<?php echo $class; ?> role="row">
                              <td><?php echo $food->group;?></td>
                              <td><?php echo ($food->brand ==null)? $food->Shrt_Desc : $food->Shrt_Desc." (".$food->brand.")";?></td>
                              <td><?php echo $food->$nutrient;?></td>
                              <td>
                                <a class='btn btn-success btn-xs food' href="#" value='<?php echo $food->id; ?>'><i class='fa fa-eye'></i></a>
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


<script>

  var table = $('.footable').footable();

  $('.food').on("click", function(e){
    var link = $(this);
    var food_id = link.attr("value");
    $.ajax({
        type: "POST",
        url: '<?php echo base_url()."athlete/nutrients/food/"; ?>'+ food_id,
        data: "food_id="+food_id+"&ajax=" + true,
        dataType: "html",

        success: function(data){
          $( "#nutrients_container" ).empty();
          $( "#nutrients_container" ).html( data );
        },

        error: function() { alert("Error posting feed."); }
   });
});

</script>