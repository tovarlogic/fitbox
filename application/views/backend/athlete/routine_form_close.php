                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class ="row">
                                <div class="form-group col-md-4"><label>Título</label>
                                    <?php echo form_input('name', ($this->input->post('name'))? $this->input->post('name') : $name_status, 'class="form-control" id="name"');?>
                                </div>
                            </div>
                            <div class ="row">
                                <div class="form-group col-md-4 summernote"><label>Descripción</label>
                                    <?php echo form_textarea('description', ($this->input->post('description'))? $this->input->post('description') : $description_status, 'class="form-control" id="description"');?>
                                </div>
                            </div>
                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                    </div>
                </div>
            </div>

<script>
$(".select2").select2();
$('.summernote').summernote({
    airMode: true,
});
function updateForm(){
    var id_type = $('#id_type').val();
    var id = "<?php echo $id; ?>";
         $.ajax({
            url : '<?php echo base_url()."athlete/setRoutineForm/update/"; ?>'+id,
            type : 'POST'  ,
            data: {id_type: id_type},
            success : function(data){
                $('#aditional_form').empty;
                $('#aditional_form').html(data);  
            },
            complete : function(){
                $(".select2").select2();
                $('.summernote').summernote({
                    airMode: true,
                });
                $('.date').datepicker({
                  autoclose: true
                });
                $(".spin").TouchSpin({
                    
                    max: 999
                });
            }
         });
 }

 $('form').submit(function(e) {
    var form = $(this);
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: '<?php echo ($action == 'add')? base_url()."athlete/routine/add/" : base_url()."athlete/routine/edit/".$id; ?>',
        data: form.serialize(),
        dataType: "html",

        success: function(data){
            history.pushState(null, null, url);
            $('.content').empty();
            $('.content').html(data);
        },

        error: function() { alert("Error posting feed."); }
   });

});
</script>










      
