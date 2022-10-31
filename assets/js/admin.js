jQuery(document).ready(function ($) {
   
	// Initialize Select2
	if ($('.fourtwo-donor-list--select')[0]) {
		$('.fourtwo-donor-list--select').select2({
			placeholder: fourtwo_donor_vars.strings.dimensions_placeholder,
			allowClear: true,
		}); 
	} 
	
	$('#fourtwo_donor_list_settings ul.select2-selection__rendered').sortable({
		containment: 'parent',
		forceHelperSize: true,
		forcePlaceholderSize: true,
		scroll: true,
	});
   
	// Handle show/hide for name dimension field (on page load)
	if ($('#mdc_cmb_fieldset_fourtwo_donor_list_name_dimensions')[0]) {
		if ($('#mdc_cmb_fourtwo_donor_list_enable_search').is(':checked')) {
			$('#mdc_cmb_fieldset_fourtwo_donor_list_name_dimensions').show();
		}
	}
	
	// Handle show/hide for name dimension field (on checkbox click)
	$('#mdc_cmb_fourtwo_donor_list_enable_search').on('click', function() {
		if ($(this).is(':checked')) {
			$('#mdc_cmb_fieldset_fourtwo_donor_list_name_dimensions').show();
		} else {
			$('#mdc_cmb_fieldset_fourtwo_donor_list_name_dimensions').hide();
		}
	});
   
});