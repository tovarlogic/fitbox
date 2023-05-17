      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>staff' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span>Emails </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE COMUNICACIÓNES
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Email / SMS
              </div>
              <form method="POST" class="form-horizontal">
                <!-- Panel DESTINATARIOS -->
                <div class="col-md-4" style="">
                  <div class="hpanel">
                      <div class="panel-body">
                        <div class="form-group">
                            <label for="inpuFname">Destinatarios:</label>
                            <div class="input-group">
                                <div class="input-group-btn bs-dropdown-to-select-group">
                                    <button type="button" class="btn btn-default btn-searchsm dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
                                        <span data-bind="bs-drp-sel-label">Selecciona</span>
                                        <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value="">
                                        <span class="caret"></span>

                                    </button>
                                    <ul class="dropdown-menu" role="menu" style="">

                                        <li data-value="student"><a href="#">Staff</a></li>
                                        <li data-value="parent"><a href="#">Clientes</a></li>
                                        <li data-value="parent"><a href="#">Invitados</a></li>
                                    </ul>
                                </div>
                                <input type="text" value="" data-record="" data-email="" data-mobileno="" class="form-control" autocomplete="off" name="text" id="search-query">

                                <div id="suggesstion-box">
                                </div>
                                <span class="input-group-btn">
                                    <button  class="btn btn-primary btn-searchsm add-btn" type="button">Añadir</button>
                                </span>
                            </div>
                        </div>
                        <div class="dual-list list-right">
                            <div class="well minheight260">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" name="SearchDualList" class="form-control" placeholder="Buscar..." />
                                            <div class="input-group-btn"><span class="btn btn-default "><i class="fa fa-search"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wellscroll">
                                    <ul class="list-group send_list">
                                    </ul>
                                </div>
                            </div>
                        </div>
                      </div>
                  </div>
                </div>

                <!-- Panel COMPOSE -->
                <div class="col-md-8" style="">
                  <div class="hpanel email-compose">
                      <div class="panel-body">
                            <!-- <div class="form-group required"><label class="col-sm-1 control-label text-left">Para:</label>
                              <div class="col-sm-11"><input type="text" class="form-control input-sm"></div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-1 control-label text-left">Cc:</label>
                              <div class="col-sm-11"><input type="text" class="form-control input-sm"></div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-1 control-label text-left">Bcc:</label>
                              <div class="col-sm-11"><input type="text" class="form-control input-sm"></div>
                            </div> -->
                            <div class="form-group required">
                              <label class="col-sm-1 control-label text-left">Asunto:</label>
                              <div class="col-sm-11"><input type="text" class="form-control input-sm" ></div>
                            </div>
                            <div class="panel-body no-padding">
                                <div class="summernote" style="display: none;"> </div>
                            </div>
                            
                      </div>
                      <div class="panel-footer">
                        <div class="pull-right">
                            <div class="btn-group">
                                <button class="btn btn-default"><i class="fa fa-edit"></i> Guardar</button>
                                <button class="btn btn-default"><i class="fa fa-trash"></i> Descartar</button>
                            </div>
                        </div>
                        <button class="btn btn-primary">Enviar email</button>
                    </div>
                  </div>
                </div>
            </form>
            </div>
          </div>
        </div>



<script>
    $('.summernote').summernote();

    $(document).on('click', '.dropdown-menu li', function () {
        $("#suggesstion-box ul").empty();
        $("#suggesstion-box").hide();
    });

    $(document).ready(function (e) {
        $(document).on('click', '.bs-dropdown-to-select-group .dropdown-menu li', function (event) {
            var $target = $(event.currentTarget);
            $target.closest('.bs-dropdown-to-select-group')
                    .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
                    .end()
                    .children('.dropdown-toggle').dropdown('toggle');
            $target.closest('.bs-dropdown-to-select-group')
                    .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
            return false;
        });

    });

</script>