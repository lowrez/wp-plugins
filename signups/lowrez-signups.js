jQuery(function () {
	jQuery(".tablegroup").tablegroup(0, 2, true);
	if (document.location.hash == '#signup-others') {
		jQuery('#signup-others').collapse('show');
		jQuery('#signup-yours').collapse('hide');
	}
	
	jQuery('.signup-parts.accordion-group').each( function() {
		var ct = jQuery(this).find('.accordion-body table.signup-custom').find('tr').size()-1;
		jQuery(this).find('.accordion-heading h4').append(jQuery('<small />').text(' ('+ct+')'));
	});
	
});