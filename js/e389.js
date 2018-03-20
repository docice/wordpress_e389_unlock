jQuery(document).ready(function($) {
    function e389_submit(){
	var data = {
	action: 'e389',
	security : e389Ajax.security,
	imei: jQuery('#imei').val()
	};
	$.post(e389Ajax.ajaxurl, data, function(response) {
	    jQuery("#e389output").html(response);
	});
    }
    jQuery('.e389submit').click(function() {
	e389_submit();
    });
    $("#imei").on( "keypress", function(event) {
    if (event.which == 13 && !event.shiftKey) {
        event.preventDefault();
	e389_submit();
    }
    });
});
