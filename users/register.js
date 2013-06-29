jQuery(document).ready(function($) {
	
	var form = jQuery('#createuser');
	
	form.find('#user_login').val(Math.random().toString(36).substring(7)).closest('tr').hide();
	form.find('#url').closest('tr').hide();
	var voicepart = form.find('#voicepart').closest('tr');
	if (voicepart.is('tr')) {
		form.find('#role').closest('tr').find('label').html('Access Level').end().detach().insertBefore(voicepart);
	}
	
	form.find('#first_name').closest('tr').add(form.find('#last_name').closest('tr')).addClass('form-required').find('label').append('<span class="description">(required)</span>');
	
	jQuery('p.indicator-hint').hide();
	
});