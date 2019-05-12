jQuery(document).ready(function($) {
	$('.show-if-agent').hide();
	$('.show-if-id').hide();
	$('#chooseproperties').change(function(event) {
		event.preventDefault();
		if ($(this).val() == 'id') {
			$('.show-if-id').show();
			$('.show-if-agent').hide();
		}
		if ($(this).val() == 'agent') {
			$('.show-if-agent').show();
			$('.show-if-id').hide();
		}
		if ($(this).val() != 'agent' && $(this).val() != 'id') {
			$('.show-if-agent').hide();
			$('.show-if-id').hide();
		}
	});
});