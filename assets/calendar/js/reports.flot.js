jQuery(document).ready(function($){


/* ie indexOf */
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}
/* append tooltip */
$('body').append('<div id="ttipX" ></div>');

/* default error */
window.serviceErr = 'Please select Service';
window.dateFromErr = 'Please select starting date ';
window.dateToErr = 'Please select ending date';

/* main trigger */
window.graphOn = false;

$('#ViewReportsSlideCE').click(function(){

	var query = getReportsQuery('');
	if(query == ''){return false;}
		
	if(window.graphOn == false){
		$("html, body").animate({ scrollTop: $(document).height() }, 1200);
		$('.bw-reports-wrapper').slideDown(800);
		window.graphOn = true;
	}	
	if(document.getElementById("EventsXY")){
		window.ReportDriver.handler = 'events';
	}else if(document.getElementById("AppointmentsXY")){
		window.ReportDriver.handler = 'appointments';
	}else if(document.getElementById("Multi_daysXY")){
		window.ReportDriver.handler = 'multi_days';
	}
	
	$('.extra').html('');
	$(this).css("opacity","0.7");	
	//console.log(window.ajaxDIRx+'/reports.plot.php?c='+window.ReportDriver.handler+'&'+query);
	$.getJSON(window.ajaxDIRx+'/reports.plot.php?c='+window.ReportDriver.handler+'&'+query, generateReport).error(function(){
		$('#graph').css("background","none").html('<center style="padding: 125px;">Error getting data. Please make sure that the file /includes/dbconnect.php is edited correctly.</center>');
	});
	return false;
});


function ReportsError(errorToDisplay){
	$('.errors-reports').html(errorToDisplay).fadeIn(500).fadeOut(6000);
}

$('.rememberOption').click(function(){
	$(this).parent().slideUp();
	/* remember for this user only ? */
});

function generateReport(json,status,xhr){
	if(xhr.readyState == 4 && xhr.status == 200){
		//console.log(json);
		/* add data */
		window.ReportDriver.input( json );
		/* if we can display, clean board else show error */
		if(window.ReportDriver.display == false){
			   $('#ViewReportsSlideCE').css("opacity","1");
			   $('#graph').css("background","none").html('<center style="padding: 125px;">Nothing to display.. please select different date range or service</center>');
			return false;
		}else{  }
		
		drawReport(window.ReportDriver.output);
		
		return false;
	}
}

function drawReport(plotData){
	
	$('#graph').css("background"," url(images/loading.gif) no-repeat center").html('');
	
	setTimeout(function(){
	
	$('#graph').css("background","none").html('');
	 
	 var graph = $('#graph');
	 var options = {
		lines: {
				show: true
			},
			points: {
				show: true
			},
		xaxis: {		
			//zoomRange: ZOOM.x,
			panRange: window.PANX,
			mode: "time",
			dayNames : window.dashboardChartDays ,
			monthNames: window.dashboardChartMonths,
			timeformat: "%b %d",
            //min: MIN.x,
            //max: MAX.x,
            labelWidth: 38
		},
		yaxis: {
			//zoomRange: [1,null],
			panRange: [1,null],
			//max: (MAX.y + 100), /* +100 to display more condensed */
			//min: MIN.y,
			tickDecimals: 0,
			minTickSize: 1,
			labelWidth: 10
		},
		zoom: {
			interactive: true
		},
		pan: {
			interactive: true
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
	
	
	window.graphic = $.plot(graph,plotData,options);

	/* Add top right arrows navigation */
	function addArrow(dir, right, top, offset) {
		$("<img class='reportsGraphNav arrow ' src='js/flot/navigation/arrow-" + dir + ".gif' style='right:" + right + "px;top:" + top + "px'>")
			.appendTo(graph)
			.click(function (e) {
				e.preventDefault();
				window.graphic.pan(offset);
			});
	}
	addArrow("left", 55, 20, { left: -100 });
	addArrow("right", 25, 20, { left: 100 });
	addArrow("up", 40, 5, { top: -100 });
	addArrow("down", 40, 35, { top: 100 });
	
	$('.reportsGraphNav.arrow').fadeIn(1500);
	graph.css("background-image","none");
	/* start tt */
	graph.bind("plothover", function (event, pos, item) {		
	    if (item && item.datapoint[1] > 0) {
	    	/* change tool tip position by hovered point */
	        window.graphic.highlight(item.series, item.datapoint);
	        $('#ttipX').css({
	        	top: item.pageY + 5,
				left: item.pageX + 5,
				display: 'block',
				opacity: 0.5
	        });
	
	        $('#ttipX').html(item.series.data[item.dataIndex][2].toolTip);
			/* move the tool tip to the left side of the point */
			if( item.dataIndex > item.series.data.length / 2 ){
				var width = $('#ttipX').css("width");
				var newWidth = item.pageX - parseInt(width.substr(0,(width.length -2))) - 5;
				$('#ttipX').css({left:newWidth});	
			}
			return false;
	    }else{
	    	/* hide tip and clear the spot highlight */
	    	$('#ttipX').html('')
	    	window.graphic.unhighlight();
	    	$('#ttipX').css("display","none");
	    }
	});
	$('#ViewReportsSlideCE').css("opacity","1");
	$('#graph').css("background","none")
	return false;
	},1000)
}

window.PANX = null;

function getReportsQuery(query){
	if(query == ''){
		var service, dateFrom, dateTo = null;
		service = $('#serviceID').val();
		dateFrom = $('#dateFrom').val();
		dateTo    = $('#dateTo').val();
	
		/* display result here */
		$('#updateDateFrom').text(dateFrom);
		$('#updateDateTo').text(dateTo);
		var serviceTitle = $('#serviceID_title').text();
		$('#forService').text(serviceTitle);
		if(service.length > 0){
			query += 'service='+service;
		}else {ReportsError(window.serviceErr);return '';}
		if(dateFrom.length > 0){
			query += '&dateFrom='+dateFrom;
		}else {ReportsError(window.dateFromErr);return '';}
		if(dateTo.length > 0){
			query += '&dateTo='+dateTo;
		}else {ReportsError(window.dateToErr);return '';}
		var f = parseInt(new Date(dateFrom).getTime());
		var t = parseInt(new Date(dateTo).getTime());
		window.PANX = [f, t];
		
	} return query;//+"&eventsIDs=1,2,3,4,5,6,7,8,9,10";
}

/* reports obj */
window.REvs = {
	init : function( json ){
		/* First, i will extract all events from current json data */
		this.XY = null;
		this.dataRecipient = null;
		this.series = [];
		this.filter = [0];
		this.events = [];
		this.evtIDs  = [];
		for(var index in json){
			if(json[index].eventID && parseInt(json[index].eventID) > 0 && this.evtIDs.indexOf(parseInt(json[index].eventID),0) == -1){
				/* and i will store them in events array. */
				console.log(json[index].eventID);
				this.events.push([parseInt(json[index].eventID),json[index].title]);
				this.evtIDs.push(parseInt(json[index].eventID));
			}
		}
		this.outputEvents(this.events);
		/* After events are collected, will display them. */
		this.dataRecipient = json;	
		return this.generateSeries();
	},
	generateSeries : function(){
		this.series = [];
		/* After reseting the series, */
		if(this.dataRecipient.length > 0){
			/* if we have data, */
			for(var j in this.filter){	
			/* loop trough each event filter */	
				this.XY = {j:[]};
				var y   = 0;
				for(var i in this.dataRecipient){
			/* and collect plot and tool tip data */
					if(this.filter[j] == this.dataRecipient[i]['eventID'] || this.filter.length == 1){
						var x = this.dataRecipient[i]['dateCreated'];
						var toolTip = this.dataRecipient[i]['name']+" ("+this.dataRecipient[i]['qty']+")";
						y++;
						this.XY.j.push([x,y,{"toolTip":toolTip}]);
					}
					
				} if(this.XY.j.length > 0){  this.series.push({"data":this.XY.j,"color":this.getColor(this.filter[j])}); }
			}		
		}
		/* then return new series array of objects, or just empty array if no data */
		return this.series;
	},
	getColor: function(evID){
		/* Get color by event ID */
		var index = this.evtIDs.indexOf(parseInt(evID),1);
		if(index == -1){return "#c3c3c3";}
		/* default line color */
		var i = 0;
		var f = false;
		for(var key in $.color.MakeMyColor){
			/* Max 42 distinct colors */
			if(i == index){
				f = true;
				return 'rgb('+$.color.MakeMyColor[key][0]+','+$.color.MakeMyColor[key][1]+', '+$.color.MakeMyColor[key][2]+')';
			}i++;}
			/* bind color with id, if we have enough colors, else generate one */
			if(f==false){
				return 'rgb('+(index + 10)+','+(index + 5)+', '+(index + 10)+')';
			}
		
	},
	XY : null,
	dataRecipient : null,
	series : [],
	filter : [0],
	events : [],
	evtIDs  : [],
	outputEvents : function(eventsArray){
		//console.log(eventsArray);
		$('.extra').html('').css("display","none");
		if(eventsArray.length > 1){
			$('.extra').append("<br /><p style='margin:0;' ><a href='#' id='evcAll' >Check All</a> | <a href='#' id='evucAll' >Uncheck All</a> </p><br />");
		}
		for(var i = 0; i <= eventsArray.length-1; i ++){
			/* For displaying i will separate id and title of the event and will just append each event in events place */
			var eID = eventsArray[i][0];
			var eNm = eventsArray[i][1];
			var eventName = "<div class='evcbx' ><input type='checkbox' class='evcbxTrigger' name='evcbx[]' id='eID_"+ eID +"' /><span style='background:"+ this.getColor(eID) +";' class='vfvCbx' ></span><span class='vfvCbx_text' >" + eNm + "</span></div>"
			$('.extra').append(eventName);
		}		
			$('.extra').append("<div class='clrEvts' ></div>");
		
		if(eventsArray.length >= 1){
			
			setTimeout(function(){$('.extra').fadeIn(1000)},800);
			
		}
			
		$('.evcbxTrigger').change(function(){
			/* after events are attached, i will bind, i should use bind */
			var id = $(this).attr("id").substr(4);
			
			if($(this).is(':checked')){
				window.REvs.filter.push(parseInt(id));
			}else{ /* add or remove, if changed ckbx is un/checked */
				window.REvs.filter.splice(window.REvs.filter.indexOf(parseInt(id)), 1);
			}
			
			drawReport(window.REvs.generateSeries());
			/* And will draw the report by regenerating series from current data, without making another call to ss */
			return false;
		});
		/* check all */
		$('#evcAll').click(function(i){
			$.each($('.evcbxTrigger'),function(){
				$(this).attr("checked","checked");
				var id = $(this).attr("id").substr(4);
				window.REvs.filter.push(parseInt(id));	
			});
			$('#graph').css("background","url(images/loading.gif) no-repeat center").html('');
			drawReport(window.REvs.generateSeries());
			return false;
		});
		/* un check all */
		$('#evucAll').click(function(){
			$.each($('.evcbxTrigger'),function(){
				$(this).removeAttr("checked");
				var id = $(this).attr("id").substr(4);
				window.REvs.filter.splice(window.REvs.filter.indexOf(parseInt(id)), 1);	 
			});
			window.REvs.filter = [0];
			$('#graph').css("background","url(images/loading.gif) no-repeat center").html('');
			drawReport(window.REvs.generateSeries());
			return false;
		});
	}
};

/* appointments obj */
window.RApp = {
	init : function(json){
		this.XY = [];
		this.series = [];
		//console.log(json);
		var y = 0;
		for(var i in json){
			var x = json[i]['dateCreated'];
			var toolTip = json[i]['name']+" ("+json[i]['qty']+")";
			y++;
			this.XY.push([x,y,{"toolTip":toolTip}]);
		}
		this.series.push({"data":this.XY});
		return this.series;
	},
	XY : [],
	series : []
}
/* multi days obj */
window.RMds = {
	init : function(json){
		this.XY = [];
		this.series = [];
		//console.log(json);
		var y = 0;
		for(var i in json){
			var x = json[i]['dateCreated'];
			var toolTip = json[i]['name']+" ("+json[i]['qty']+")";
			y++;
			this.XY.push([x,y,{"toolTip":toolTip}]);
		}
		this.series.push({"data":this.XY});
		return this.series;
	},
	XY : [],
	series : []
}
/* need something to handle data in all cases */

window.ReportDriver = {
	input : function( json ){
		/* Each obj should have this methods */
		this.output =  [];
		this.output = this.obj().init(json);
		if(this.output.length > 0 && json != null){ 
				this.display = true;
				//console.log(this.output);
				return this.output;
		}else{  this.display = false; }
		
	},
	fromCacheData : function(){
		return this.obj().catchAndTrow();
	},
	display : false,
	output : [],
	/* this will tell witch handler obj to use, default events */
	handler : null,
	obj : function() {
		switch(this.handler){
			case 'events': 		return window.REvs; break;
			case 'appointments': return window.RApp; break;
			case 'multi_days':  return window.RMds; break;
		}
	}
	
	
};





});


