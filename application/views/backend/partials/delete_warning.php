    <div class="row pop-in">
        <div class="col-md-3" style="">
            <div class="hpanel hred">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <h4>Advertencia</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-attention fa-4x danger"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-danger">¿Seguro que quieres borrarlo?</h1>
                        <small>
                            Una vez borrada, esta información no se podrá recuperar.
                        </small>
                    </div>
                </div>
                <div class="panel-footer">
                    <?php 
                    echo form_open($url, array('id' => 'form')); 
                    echo form_hidden('option', '');
                    echo form_submit('confirmation', 'confirm', 'class="btn btn-xs btn-danger m-t-n-xs" onclick="this.form.option.value=this.value"');
                    echo form_submit('confirmation', 'cancel', 'class="btn btn-xs btn-default m-t-n-xs" onclick="this.form.option.value=this.value"');
                    echo form_close();
                    ?>

                     <script>

                          $('form').submit(function(e) {
                            var form = $(this);
                            var url2 = "<?php echo base_url($url); ?>";
                            e.preventDefault();
                            $.ajax({
                                type: "POST",
                                url: form.attr("action"),
                                data: form.serialize(),
                                dataType: "html",
                                cache: false,

                                success: function(data){
                                  $('.content').empty();
                                  $('.content').html(data);
                                  history.pushState(null, null, url2);
                                },

                                error: function() { alert("Error posting."); }
                           });
                            return false;
                          });

                      </script>
                </div>
            </div>
        </div>
    </div>
