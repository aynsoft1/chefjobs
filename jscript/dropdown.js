$(document).ready(function() {
	// dropdown animation
	$('.dropdown').on('show.bs.dropdown', function(e) {
		$(this).find('.dropdown-menu').first().stop(true, true).slideDown(250);
	});
	$('.dropdown').on('hide.bs.dropdown', function(e) {
		$(this).find('.dropdown-menu').first().stop(true, true).slideUp(0);
	});
});