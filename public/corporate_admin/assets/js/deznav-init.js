"use strict"

var dezThemeSet2 = {};

function getUrlParams(dParam){
	var dPageURL = window.location.search.substring(1),
		dURLVariables = dPageURL.split('&'),
		dParameterName,
		i;

	for (i = 0; i < dURLVariables.length; i++) {
		dParameterName = dURLVariables[i].split('=');

		if (dParameterName[0] === dParam) {
			return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
		}
	}
}

(function($) {
	
	"use strict"
	
	var body = $('body');
	var direction =  getUrlParams('dir');
	
	var dezThemeSet2 = {
		typography: "poppins",
		version: "light",
		layout: "vertical",
		primary: "color_11",
		headerBg: "color_11",
		navheaderBg: "color_11",
		sidebarBg: "color_11",
		sidebarStyle: "full",
		sidebarPosition: "fixed",
		headerPosition: "fixed",
		containerLayout: "full"
	};

	new dezSettings(dezThemeSet2);

	jQuery(window).on('resize',function(){
        /*Check container layout on resize */
        dezThemeSet2.containerLayout = $('#container_layout').val();
        /*Check container layout on resize END */
        
		new dezSettings(dezThemeSet2); 
	});

	if(direction == 'rtl' || body.attr('direction') == 'rtl'){
        direction = 'rtl';
		jQuery('.main-css').attr('href','assets/css/style-rtl.css');
    }else{
        direction = 'ltr';
		jQuery('.main-css').attr('href','assets/css/style.css');
	}
	
})(jQuery);