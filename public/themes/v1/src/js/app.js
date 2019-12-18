$(function(){
	// add a js confirmation dialogue instead of sending the user
	// to the confirm feed delete page
	$(".btn-delete").on("click", function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		// TODO could store as json and parse
		var feed_id = $(this).data('id');
		var feed_name = $(this).data('name');
		// handle POST to delete feed
		if (confirm("Are you sure you want to delete the "+ feed_name +" feed?")) {
			$.ajax({
				type: 'POST',	// could be DELETE if we're being RESTful
				url: url,
				data: { id: feed_id },
				dataType: 'json',
				timeout: 300,
				success: function(data){
					window.location = "/feeds";
				},
				error: function(xhr, type){
					alert("Sorry something went wrong and the feed could not be deleted");
				}
			});
		}
	})
});