$(function(){
	var replacePage = function(url) {
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'html',
			beforeSend: function(data){
				$('.content').empty();
				$('.splash2').css('display', 'block')
			},
			success: function(data){
				$('.splash2').css('display', 'none')
				$('.splash').css('display', 'none')
				$('.content').html(data);
			},
			error: function (request, status, error) {
				$('.splash2').css('display', 'none')
				$('.content').html(request.responseText);
		    }
		});
	}

	$(document).on("click", "a.html5history", function(e){
		history.pushState(null, null, this.href);
		replacePage(this.href);
		e.preventDefault();
	});


	$(document).on("submit", "#html5form", function(e){

	    var url = this.action;

	    for (i = 0; i < this.elements.length; i++) { 
		  url = url + "/" + this.elements[i].value
		}
	    
	    history.pushState(null, null, url);
	    replacePage(url);
	    e.preventDefault();
	});


	$(document).on("click", "a.html5link-hide-menu", function(e){
		if ($(window).width() < 769) {
            $("body").toggleClass("show-sidebar");
        } else {
            $("body").toggleClass("hide-sidebar");
        }
		history.pushState(null, null, this.href);
		replacePage(this.href);
		e.preventDefault();

	});

	$(document).on("click", "a.html5history_warning", function(e){
		href = this.href;
		e.preventDefault();
		swal.fire({
            title: "Seguro que quieres borrar este registro?",
            text: "Una vez borrado los cambios no se podrán deshacer!",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Borrar",
            closeOnConfirm: true,
            closeOnCancel: true 
        }).then((result) => {
			if (result.value) {
                history.pushState(null, null, href);
				replacePage(href);
				e.preventDefault();
        	}   
        });
	});

	$(document).on("click", "a.html5history_warning_nourl", function(e){
		href = this.href;
		e.preventDefault();
		swal.fire({
            title: "Seguro que quieres borrar este registro?",
            text: "Una vez borrado los cambios no se podrán deshacer!",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Borrar",
            closeOnConfirm: true,
            closeOnCancel: true 
        }).then((result) => {
			if (result.value) {
				replacePage(href);
				e.preventDefault();
        	}   
        });
	});

	$(window).bind('popstate', function(){
		replacePage(location.pathname);
	});
});

////////////////
// EN DESUSO
///////////////
function goTo(controller, method, param) {
    $( ".content" ).empty();
    var domain = "https://www.fitbox.es/";
    //var domain = "http://localhost/fitbox/";
    if(param === undefined) var url = controller+"/"+method;
    else var url = controller+"/"+method+"/"+param;

    $.post( domain + url, {ajax: true}, function( data ) {
        if (data === false)
        {
            window.location.replace(domain+"auth/login");
        }
        else
        {
            $( ".content" ).html( data );
            History.pushState(null, 'Fitbox.es',domain + url);
        }
    });
    return false; //href link won´t be followed on click
};

