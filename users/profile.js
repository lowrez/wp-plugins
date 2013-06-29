jQuery(document).ready(function($) {
	
	var form = jQuery('#your-profile');
	
	form.find('#comment_shortcuts').closest('table.form-table').hide().prev('h3').hide();
	form.find('#url, #display_name, #nickname').closest('tr').hide();
	form.find('#role').closest('tr').find('label').html('Access Level');
	form.find('#description').closest('tr').hide().closest('table.form-table').prev('h3').html('Password');
	/*.siblings('span.description')
	.html('If you would like, please describe yourself in 150 words, to be displayed on the <a href="/about/who-we-are/" target="_blank">Who We Are</a> <strong>public</strong> page.')
	.closest('tr').find('label').html('<em>Who We Are</em> Biographical Text');*/
		
	form.find('#postcode').removeClass('regular-text');
	/*form.find('#street').replaceWith( function() {
		var me = jQuery(this);
		var tx = jQuery('<textarea />');
		tx.attr('id', me.attr('id'));
		tx.attr('name', me.attr('name'));
		tx.val( me.val() );
		return tx;
	} );*/
	
	jQuery('p.indicator-hint').hide();
	
});