
$("#form_login").submit(function(e) {
	var data = {
		email: $("#email").val(),
		password: $("#password").val(),
	};

	$.ajax({
		url: baseurl + '/index.php/login/ajax_login',
		method: 'POST',
		data: data,
		dataType: "json",
		error: function(e, ts, et) { alert(e); alert(ts); alert(et);},

		success: function(response)
		{
			// Login status [success|invalid]
			var login_status = response.login_status;
			// If login is invalid, we store the 
				if(login_status == 'invalid')
				{
					$(".login-page").removeClass('logging-in');
					alert("nop");

				}
				else
				if(login_status == 'success')
				{
					window.location.href = response.redirect_url;
				}
		}
	});
	e.preventDefault(); // avoid to execute the actual submit of the form.
});
