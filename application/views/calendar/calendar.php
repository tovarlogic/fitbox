<?php ob_end_flush();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width" />
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="refresh" content="300">

    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700' >
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/calendar/css/bs-admin.css" /> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/calendar/css/bs_calendar.css" />
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/calendar/css/dd.css" /> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/calendar/css/dropdown-skins.css" />
    <?php if(!empty($canonical)){?>
        <link rel="canonical" href="<?php echo base_url(); ?>calendar/"/>
    <?php }?>

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/3.3.7/css/bootstrap.css">
  <script src="<?php echo base_url(); ?>assets/js/jquery-3.3.1.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/bootstrap-3.3.7.min.js"></script>

    <script type="text/javascript">
$(function () {
    $.fn.popover.Constructor.prototype.reposition = function () {
        var $tip = this.tip()
        var autoPlace = true

        var placement = typeof this.options.placement === 'function' ? this.options.placement.call(this, $tip[0], this.$element[0]) : this.options.placement

        var pos = this.getPosition()
        var actualWidth = $tip[0].offsetWidth
        var actualHeight = $tip[0].offsetHeight

        if (autoPlace) {
            var orgPlacement = placement
            var viewportDim = this.getPosition(this.$viewport)

            placement = placement === 'bottom' &&
                pos.bottom + actualHeight > viewportDim.bottom ? 'top' : placement === 'top' &&
                pos.top - actualHeight < viewportDim.top ? 'bottom' : placement === 'right' &&
                pos.right + actualWidth > viewportDim.width ? 'left' : placement === 'left' &&
                pos.left - actualWidth < viewportDim.left ? 'right' : placement

            $tip
                .removeClass(orgPlacement)
                .addClass(placement)
        }

        var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

        this.applyPlacement(calculatedOffset, placement)
    }
})
</script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/jquery.dd.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/main.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/wp.js"></script>

    <link type="text/css" href="<?php echo base_url(); ?>assets/calendar/css/redmond/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/jquery-ui-1.8.20.custom.min.js"></script> -->
    <script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.2.js"></script>


    <link type="text/css" media="screen" rel="stylesheet" href="<?php echo base_url(); ?>assets/calendar/css/colorbox.css" />
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/calendar/js/spinner.js"></script>





    <title><?php echo $page_title;?></title>

</head>
    <body>
        <div id="mess">

        </div>

<noscript>
    <div class="js_error">Please enable JavaScript or upgrade to better <a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank">browser</a></div>
</noscript>

<div id="index">
    <h1></h1>
    <!-- <p><a href="eventList.php?serviceID=<?php //echo $serviceID?>"><?php //echo EVENTS_LIST ?></a></p> -->
    <div class="calendar">
        <?php

        extract($calendar_vars);
        if (count($services) > 1) {
            ?>
            <div class="servicesListCont">
                <form name="ff1" id="ff1" method="get">
                    <input type="hidden" name="box_id" value="<?php echo $box_id; ?>">
                    <select name="serviceID" onchange="document.forms['ff1'].submit()">
                        <option value="<?php echo -1 ?>" <?php echo ($serviceID == null) ? "selected" : "" ?>>Todos</option>
                        <?php 
                        foreach ($services as $serv) 
                        { ?>
                            <option value="<?php echo $serv['id']; ?>" <?php echo ($serviceID == $serv['id']) ? "selected" : "" ?>><?php echo $serv['name']; ?></option>                            
                        <?php } ?>
                    </select>
                </form>
            </div>
            <div style="clear:both"></div>
        <?php } ?>
        <!-- CALENDAR NAVIGATION -->
        <table cellspacing="5" class="dash_border">
            <tr>
                <td width="100">
                    <?php echo $prev_month_link ?>
                </td>
                <th align="center" width="400">
                    <?php echo $title ?>
                </th>
                <td align="right"  width="100">
                    <?php echo $next_month_link ?>
                </td>
            </tr>
        </table>
        <!-- CALENDAR NAVIGATION END -->
        <br />

        <table cellpadding="2" cellspacing="5" border="0" class="calendarTable" id="tabs-cal">
            <?php echo $calendarHeader; ?>
            <?php echo $calendarHeader_mobile; ?>
            <?php echo $calendar; ?>
            <?php echo $calendar_mobile; ?>
        </table>
    </div>
</div>

<p id="demo"></p>

<div class="footer">
    Powered by <a href="http://www.fitbox.es" target="_blank">FitBox.es</a>
</div>

<script>
    $(function(){
        try{
            if($("#index").length){
                top.resizeFrame($('#index').height()+200,1100);
            }else{
                top.resizeFrame($('#resize').height()+200,1100);
            }
        
        }catch(e){}
    })

</script>

<script>
    $(function(){
        $('#tabs-cal').tabs({collapsible: true, active: false});

        $('[data-toggle="popover"]').popover({
            placement: "right",
            container: "body",
            html: true,
            content: function() {
               var service = $(this).attr("service"); 
               var time = $(this).attr("time"); 
               var response = '';
              $.ajax(
                {
                    url: '<?php echo base_url()."calendar/details"; ?>',
                    method: 'POST',
                    dataType: 'html',
                    data:{ service : service, time : time },
                    success: function(data){
                        $('.popover-content').empty();
                        $('.popover-content').html(data);
                        $('[data-toggle="popover"]').popover('reposition');
                    }
                }
                );
            }
          }).click(function(e) {
            $(this).popover('toggle');
          });

        //borrar al hacer click fuera
        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });

        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {

            });
        });

    })
</script>

<script>
function myFunction(id, time, box, service) 
{
    var response = "";
    $.ajax(
    {
        url: '<?php echo base_url()."calendar/details"; ?>',
        method: 'POST',
        dataType: 'html',
        data:{ id : id, time : time, service : service },
        success: function(data){
            $('#ajax'+id).empty();
            $('#ajax'+id).html(data);
            response = data;
        }
    }
    );
    document.getElementById("ajax" + id).innerHTML = response;
    document.getElementById("ajax" + id).style.display = "block"; 

}

function closeAjax(id) 
{
    document.getElementById("ajax" + id).innerHTML = " "; 
    document.getElementById("ajax" + id).style.display = "none";  
}

$(document).ready(function(){
    var d = new Date();
    var n = d.getDay();
    var next = '<?php echo $next_week; ?>';
    
    if( n == 0) 
    {
        if (next == true) n = 1;
        else n = 7;
    }

    var id = 'day' + n;
    var i = 0;
    document.getElementById(id).setAttribute("aria-hidden", "false");
    document.getElementById(id).setAttribute("aria-expanded", "true");
    document.getElementById(id).style= "";

    document.querySelector("[aria-controls="+id+"]").setAttribute("aria-selected", "true");
    document.querySelector("[aria-controls="+id+"]").className = "ui-state-default ui-corner-top ui-state-focus ui-tabs-active ui-state-active";

    $("li[role='tab']").click(function(){ 
        i++;
        if(i == 1) 
        {
            document.getElementById(id).setAttribute("aria-hidden", "true");
            document.getElementById(id).setAttribute("aria-expanded", "false");
            document.getElementById(id).style= "display: none";

            document.querySelector("[aria-controls="+id+"]").setAttribute("aria-selected", "false");
            document.querySelector("[aria-controls="+id+"]").className = "ui-state-default ui-corner-top ";
        }
     });
});
    


</script>

</body>
</html>