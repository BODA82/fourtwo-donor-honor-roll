jQuery(document).ready(function ($) {
   
   if ($('.fourtwo-donor-list--select')[0]) {
	   
	   console.log('select2');
	   $('.fourtwo-donor-list--select').select2({
		   placeholder: fourtwo_donor_vars.strings.dimensions_placeholder,
		   allowClear: true
	   });
	   
   } 
   
});