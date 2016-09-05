$(document).ready(function() {
	$("#subscribe").click(function() {

		var name = $("input#name");
		var email = $("input#email");

		if (name.is(':valid') && email.is(':valid')) {
			$.post("scripts/add_user.php", {
				name: name.val(), 
				email: email.val()
			}).done(function(data) {
				$("#info").text("Success! Added to email list.");
				$("#info").removeClass().addClass("success");
			}).fail(function(data) {
				$("#info").text("Error! Could not communicate with server.");
				$("#info").removeClass().addClass("error");
			});
		} else {
			$("#info").text("Error! Please make sure all fields are valid.");
			$("#info").removeClass().addClass("error");
		}

		$("input").val('');
	})
});