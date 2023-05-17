if (!("console" in window) )
{
    var names = ["log", "debug", "info", "warn", "error", "assert", "dir", "dirxml",
    "group", "groupEnd", "time", "timeEnd", "count", "trace", "profile", "profileEnd"];

    window.console = {};
    for (var i = 0; i < names.length; ++i)
        window.console[names[i]] = function() {}
}


function addMessage(text,type){
    var messBox = $("#mess");
    var close = $("<span>",{id:'close'});
    close.bind("click",function(){
        messBox.fadeOut();
    })
    close.html("X");
    if(type=='error'){
        
        messBox.html("<div class='error'>"+text+"</div>");
        messBox.find(".error").append(close);
    }else{
        messBox.html("<div class='success'>"+text+"</div>");
        messBox.find(".error").append(close);
    }
    
    messBox.fadeIn();
    setTimeout(function(){
        messBox.fadeOut();
       
    },5000)
}
function checkNumeric(value){
    var anum=/(^\d+$)|(^\d+\.\d+$)/
    if (anum.test(value))
        return true;
    return false;
}

function noAlpha(obj){
    reg = /[^0-9.,]/g;
    obj.value =  obj.value.replace(reg,"");
}
function onlyDigits(obj){
    reg = /[^0-9]/g;
    obj.value =  obj.value.replace(reg,"");
}
function formatNumber(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' +  x[1].substring(0,2) : '.00';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function ArrayIndexOf(array,item,from){
    var len = array.length;
    for (var i = (from < 0) ? Math.max(0, len + from) : from || 0; i < len; i++){
        if (array[i] === item) return i;
    }
    return -1;
}
/* taken from mootools */
function ArrayContains(array,item,from){
    return ArrayIndexOf(array,item,from) != -1;
}


$(function(){
    $('.select').msDropdown();
    $('.selectWTF').msDropdown();
    $('select.spin').msDropdown({mainCSS:'spin'});
})
jQuery(document).ready(function($){
	/* wrap date start end */
	if($('.adjStartEnd').length > 0){
		$.each($('.adjStartEnd'),function(){
			var adj_se_bg = "<span class='adj_se_nav_top'></span><span class='adj_se_nav_bottom'></span>";
			var sehtml = "<div class='adj_se_input_bg' style='width:"+$(this).css("width")+";height:"+$(this).css("height")+";padding:"+$(this).css("padding")+";' >"+adj_se_bg+"</div>";
			var wrapper = "<div class='adj_se' ></div>";
			$(this).wrap(wrapper);
			$(this).parent().append(sehtml);	
		})
		findAndBindSE();
	}
	if(document.getElementById('plottedJS')){
	/* add tooltip once */	
	$('body').append('<div id="ttipX" ></div>');	
	/* start graphs with default query null */
	startGraphs('?calendar='+$("#DashboardSelectCalendar").val());
	}
	
	/* style checkboxes from div with class .style_checkboxes  */
	$.each($('.style_checkboxes input[type="checkbox"]'),function(){
            if(!$(this).is(":disabled")){
		$(this).css("display","none");
		
		$(this).wrap('<div class="styled_checkboX unchecked" ></div>');
		
		$(this).parent().addClass($(this).attr("id") + ' ' + $(this).attr("class"));
            }
	});
	
	$('.styled_checkboX').click(function(){
			if($(this).val() == 1){
				
					$(this).removeClass("checked").addClass("unchecked");
					$(this).children().removeAttr("checked");
				
				$(this).val(0);
			}else{
				
					$(this).removeClass("unchecked").addClass("checked");
					$(this).children().attr("checked","checked");
					
				$(this).val(1);
			}
	});
	$('.ToggleSelectAllCheckBoxes').click(function(){
	if($(this).children().val() == 1){
		$.each($('.style_checkboxes tbody .styled_checkboX'),function(){
			$(this).removeClass("checked").addClass("unchecked");
			$(this).children().removeAttr("checked");
			});
		$(this).children().val(0);
	}else{
		$.each($('.style_checkboxes tbody .styled_checkboX'),function(){
			$(this).removeClass("unchecked").addClass("checked");
			$(this).children().attr("checked","checked");
			});
		$(this).children().val(1);
	}
	});
		
});
/* se ev bnd */
function findAndBindSE(){
	$('.adj_SE_remove').click(function(){

		$(this).parent().remove();
		if($("#ff2").length)$("#ff2").val(true);
		return false;
	});
        $('.adj_SE_clear').click(function(){
            if($("#ff2").length)$("#ff2").val(true);
		$(this).parent().find("input").each(function(){
                    $(this).val(" - -");
                });
		return false;
	});
	$('.adj_se_nav_top').click(function(){
			var value = $(this).parent().prev().val();
			if($(this).parent().prev().hasClass('adj_mins_0')){
				$(this).parent().prev().val(compareSEvals('mins','plus',value));
			}else if($(this).parent().prev().hasClass('adj_hrs_0')){
				$(this).parent().prev().attr("value",compareSEvals('hrs','plus',value));
			}else {
				value = parseInt(value);
				newvalue = value + 1;
				if(newvalue < 0){return alert('This value must be a positive integer !');}
				else return  $(this).parent().prev().val(newvalue);
			}
        $(this).parent().parent().find('input').trigger("change")
		});
		$('.adj_se_nav_bottom').click(function(){
			var value = $(this).parent().prev().val();
			if($(this).parent().prev().hasClass('adj_mins_0')){
				$(this).parent().prev().attr("value",compareSEvals('mins','minus',value));
			}else if($(this).parent().prev().hasClass('adj_hrs_0')){
				$(this).parent().prev().attr("value",compareSEvals('hrs','minus',value));
			}else {
				value = parseInt(value);
				newvalue = value - 1;
				if(newvalue < 0){return alert('This value must be a positive integer !');}
				else return $(this).parent().prev().val(newvalue);
			}
            $(this).parent().parent().find('input').trigger("change")
		});
		/*$('.adjStartEnd').mouseover(function(){
			if($(this).val() == '  - -'){
				$(this).val('');
			}
		});
		$('.adjStartEnd').mouseout(function(){
			if($(this).val() == ''){
				$(this).val('  - -');
			}
		});*/
                $('.adjStartEnd').click(function(){
			if($(this).val() == '  - -'){
				$(this).val('');
			}
		});
                $('.adjStartEnd').focus(function(){
			if($(this).val() == '  - -'){
				$(this).val('');
			}
		});
		$('.adjStartEnd').keypress(function(event){
                     var self = this;
	 
                        function handle() {
                            var value = $(self).val();
                            if(String.fromCharCode(event.which) == 'N'){
                                    $.each($(self).parent().parent().children('.adj_se').children('input'),function(){
                                            $(self).val('N/A');
                                            //$(self).css("background","#000");
                                    });
                                    return false;
                            }
                            value-=0;
                            var str = value.toString(); 

                            if(str.length > 2){

                                value = parseInt(str.substr(1,2));

                                $(self).val( value);
                            }

                            if(!(!isNaN(parseFloat(value)) && isFinite(value))){$(self).val('00'); if(value != '  - -'){return alert('Please Insert only Numeric value! !');}}
                            value = parseInt(value-0);
                            if($(self).hasClass('adj_hrs_0') && value > 23 || value < 0 ){$(self).val('00');return alert('Please Insert values for Hours only between 00 and 23!');}
                            if($(self).hasClass('adj_mins_0') && value > 60 || value < 0){$(self).val('00');return alert('Please Insert values for Minutes only between 00 and 59!');}

                            if(value < 10){return $(self).val('0'+value);}else{return $(self).val(value);}
                        }

                        setTimeout(handle, 0);
			
		});

	return false;
}
	function compareSEvals(time,rate,value){
               
		if(value == '' || value == '  - -' || value == 'N/A') value = '00';
		if(!(!isNaN(parseInt(value)) && isFinite(value))){ return alert('Please insert only numeric values!');}
		else value = parseInt(value-0);
                
		switch(time){
			case 'hrs':
			var x = 1;
			var y = 23;
			var z = '00';
			if(value < y)
			break;
			case 'mins':
			var x = 1;
			var y = 59;
			var z = '00';
			break;
		}
		if(rate == 'plus'){var newval = (value + 1);}
		if(rate == 'minus'){var newval = (value - 1);}
                
		if(newval > y || (time == 'hrs' && newval > 23)){return z;}
		if(newval < x){newval = y;}
		if(newval < 10){newval = '0' + newval;}
                
		return newval;
	}
	



function startGraphs(query){
	/* data will be fetched by query  */
	$('#plottedJS').html('');
	{ $('#plottedJS').css("background",'url(images/loading.gif) no-repeat center');}
	window.hihi = true;
	var urlz = window.ajaxDIRx+'/dashboard.plot.php'+query; 
	var request = $.getJSON(urlz,function(json,textStatus,jqXHR){
		
	if(json.length > 0){
		//var MaxY = 1;
		window.maxx = [];
		for(var i=0;i<=json.length -1;i++){
			if(json[i].data){
			var MaxFromDays = json[i].data.length -1;
			var MaxX = json[i].data[MaxFromDays][0];
			var MinX = json[i].data[0][0];
			$.each(json[i].data,function(){
					if(this[1] > 0){window.maxx.push(this[1]);}	
			});
			}
		}
		var MaxY = Math.max.apply(Math , window.maxx) < 5 ? 5 : Math.max.apply(Math ,maxx) + 1;
		
		/* start graphics configuration */
		var options = {
			lines: {
				show: true
			},
			points: {
				show: true
			},
			legend: {
				backgroundOpacity: 0.5
			},
			xaxis: {			
				mode: "time",
				dayNames : window.dashboardChartDays ,
				monthNames: window.dashboardChartMonths,
    			timeformat: "%b %d",
                min: MinX,
                max: MaxX,
                labelWidth: 38
			},
			yaxis: {
				max: MaxY,
				tickDecimals: 0,
				minTickSize: 1,
				labelWidth: 10
			},
			series: {
				lines: { show: true },
				points: { show: true },
				shadowSize: 4
			},
			grid: {
				clickable:true,
    			hoverable: true,
				borderColor: '#fff',
				minBorderMargin: 10,
				labelMargin:5,
				color: '#333'
			},
			legend: {
				show: false
			}
			

		};
		
		var plotData = [];
		var ttipData = []
		for(var i = 0;i<=json.length -1;i++){
			plotData.push(json[i]);
			ttipData.push(json[i].ttipX)
		}
		
		if(plotData[0].data){
			var ph = $.plot($('#plottedJS'),plotData,options);
			$('#pgraphsDesc').html('');
			var li = '';
			/* build series description */
			for(var i = 0;i <= plotData.length -1; i++){
				if(plotData[i].label && plotData[i].color){
					var label = plotData[i].label;
					var color = plotData[i].color;
					var li = li + '<li class="redoSQUAREx"><div  style="background:'+color+';" >&nbsp;</div><span>&nbsp;-&nbsp;</span>'+label+'</li>';
				}						
			}
			/* append series description */
			$('#pgraphsDesc').html(li);
			
			/* start tool tip */
			$("#plottedJS").bind("plothover", function (event, pos, item) {		
			    if (item && item.datapoint[1] > 0) {
			    	/* change tool tip position by hovered point */
			        ph.highlight(item.series, item.datapoint);
			        $('#ttipX').css({
			        	top: item.pageY + 5,
            			left: item.pageX + 5,
            			display: 'block',
            			opacity: 0.5
			        });
			        
			        /* Current Used Version Start */
			        $('#ttipX').html(item.series.data[item.dataIndex][2].toolTip);
								/* move the tool tip to the left side of the point */
								if( item.dataIndex > item.series.data.length / 2 ){
			       					var width = $('#ttipX').css("width");
									var newWidth = item.pageX - parseInt(width.substr(0,(width.length -2))) - 5;
									$('#ttipX').css({left:newWidth});	
			       				}
			       	/* End Current Version exit; */ return false;
			       				
        
			        /* Old Version */
			       var indexX = item.datapoint[0]+'_'+item.datapoint[1];
			        for(var i = 0; i <= ttipData.length-1;i++){
			        	for(var x in ttipData[i]){
							/* show tip constructed by xaxis_yaxis string */
							if(x == indexX){
								
								$('#ttipX').html(ttipData[i][x]);
		
								/* move the tool tip to the left side of the point */
								if( item.dataIndex > plotData[i].data.length / 2 ){
			       					var width = $('#ttipX').css("width");
									var newWidth = item.pageX - parseInt(width.substr(0,(width.length -2))) - 5;
									$('#ttipX').css({left:newWidth});	
			       				}
							}		        	
			        	}
			        }
			      /* End Old Version */  
			    }else{
			    	/* hide tip and clear the spot highlight */
			    	$('#ttipX').html('')
			    	if(window.hihi == true){ph.unhighlight();}
			    	$('#ttipX').css("display","none");
			    }
			});
			$('.xAxis .tickLabel').click(function(){
				/* highlight the ticks from that day */
				return false;
				/* must increase resolution of item.v +- */
				$(this).toggleClass('Y-highligted');
				if($(this).hasClass('Y-highligted')){
					window.hihi = false;
				}else{
					window.hihi = true;
				}
				var x = ph.getXAxes();
				var ticks = x[0].ticks;
				for(var i in ticks){
					if(ticks[i].label == $(this).text()){
						
						for(var j = 0; j <= MaxY; j++){
							ph.highlight(0, [ticks[i].v, j]);
						}
					}
				}
			})
			
			}else{
				/* if data is here 1 */
				$('#pgraphsDesc').html('');
				$('#plottedJS').html('<center style="padding: 125px;">Nobody has booked here yet..</center>');
			}
			/* if data is here 0 */
			}else {
				$('#pgraphsDesc').html('');
				$('#plottedJS').html('<center style="padding: 125px;">Nobody has booked here yet..</center>');
			}
			
			
	}).error(function(){
		$('#plottedJS').html('<center style="padding: 125px;">Error ! Please check the script Path in includes/dbconnect.php </center>');
	});
	
	request.complete(function(){
		$('#plottedJS').css("background",'none');
	})
	
}

/*function startTime()
{
    var tm = new Date();
    var h = tm.getHours();
    var m = tm.getMinutes();
    var s = tm.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('currTime').innerHTML = h + ":" + m + ":" + s;
    t = setTimeout('startTime()', 500); 
}
function checkTime(i)
{
    if (i < 10)
    {
        i = "0" + i;
    }
    return i;
}

$(function(){
    startTime();
})*/


function openFbPopUp(url) {
    var fburl = url;
    var fbimgurl = 'http://';
    var fbtitle = $(".eventTitle b").text();
    var fbsummary = $(".eventDescr").text();
    var sharerURL = "http://www.facebook.com/sharer/sharer.php?s=100&p[url]=" + encodeURI(fburl) + "&p[images][0]=" + encodeURI(fbimgurl) + "&p[title]=" + encodeURI(fbtitle) + "&p[summary]=" + encodeURI(fbsummary);
    window.open(
        sharerURL,
        'facebook-share-dialog',
        'width=626,height=436');
    return  false;
}