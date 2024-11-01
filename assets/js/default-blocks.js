var $ = jQuery;

$(document).ready(function () {

	$(document).wisyWideModeRows();
	$(window).on('resize', function () {
		$(document).wisyWideModeRows();
	});
	$(document).on('wisyDOMChanged', function () {
		$(document).wisyWideModeRows();
	});

	//var player = new Plyr('.wisy-media-player.plyr-player');

	$(document).on( 'click', '.wisy-block.widget-gdpr-notice .accept-btn, .wisy-block.widget-gdpr-notice .close-btn', function () {
		if ( $(this).hasClass('accept-btn') ) {
			wisySetCookie( 'wisy_gdpr_accepted', '1', 365 );
		}

		$(this).parents('.wisy-block.widget-gdpr-notice').addClass('hidden');
	} );

});

$.fn.wisyWideModeRows = function () {
	$('.wisy-row.wisy-wide-mode', $(this)).each(function () {
		var rowSample = ( $(this).next().hasClass('wisy-wide-mode-sample') ) ? $(this).next() : $(this).after('<div class="wisy-wide-mode-sample"></div>');

		var row = $(this),
			rowRect = rowSample[0].getBoundingClientRect(),
			windowWidth = document.body.clientWidth;

		$(row).css({
			'width': windowWidth + 'px',
			'margin-left': (rowRect.left*-1) + 'px'
		});
	});
};

function wisySetCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function wisyGetCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return '';
}

MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

var observer = new MutationObserver(function(mutations, observer) {
	// fired when a mutation occurs
	//$(document).wisyWideModeRows();
	//var player = new Plyr('.wisy-media-player.plyr-player', {});
	$('body').trigger('wisyDOMChanged');
});

observer.observe(document, {
	subtree: true,
	attributes: true
});