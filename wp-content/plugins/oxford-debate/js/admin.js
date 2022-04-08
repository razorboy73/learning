(function($) {
	$(function() {
		
		// Check to make sure the input box exists
		if( 0 < $('#initduration-text').length ) {
            $('#initduration-text').datetimepicker({
          format:'Y-m-d H:i',
          inline:true,
          lang:'en',
          scrollMonth:false,
          scrollTime:false,
          scrollInput:false
        });
  
		} // end if

		// Check to make sure the input box exists
		if( 0 < $('#endduration-text').length ) {
            $('#endduration-text').datetimepicker({
              format:'Y-m-d H:i',
              inline:true,
              lang:'en',
              scrollMonth:false,
              scrollTime:false,
              scrollInput:false
            });
 
		} // end if
		
	});
     
}(jQuery));


jQuery(document).ready(function ($) {
        "use strict";
        // limit usage of colorpicker if wpColorPicker module is not loaded
        if ($.fn.wpColorPicker)
            $('.color-field').wpColorPicker();
});