jQuery(function () {
	jQuery(".tablegroup").tablegroup(0, 2, true);
	jQuery('#signup_datelist .newdate a').data('addnew', jQuery('#signup_datelist .newdateblank').clone()).click( function(e) {
		e.preventDefault();
		jQuery('#signup_datelist .newdate').before(jQuery(this).data('addnew').clone());
	});
	jQuery('#signup_datelist').on('click', 'a.delbutton', function(e) {
		e.preventDefault();
		jQuery(this).closest('li').remove();
	});
});