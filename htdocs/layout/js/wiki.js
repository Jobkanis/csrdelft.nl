jQuery(document).ready(function () {
	jQuery(document).resize(function () {
		jQuery('#wikiframe').css({
			'width': window.innerWidth,
			'height': window.innerHeight
		});
	});
	jQuery(document).trigger('resize');
});