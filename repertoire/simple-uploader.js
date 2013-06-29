jQuery(document).ready(function($) {
	jQuery('.custom_media_upload').click(function(event) {

		event.preventDefault();
		frame = wp.media({
			title : 'Upload Repertoire Media',
			multiple : true,
			library : {
				type : 'repertoire-media'//none
			},
			button : {
				text : 'Upload'
			}
		});
		frame.on('close', function() {
			//var images = frame.state().get('selection');
			//images.each( function (image) { alert(image.id); });
			//console.log(selection.first().url());
			// get selections and save to hidden input plus other AJAX stuff etc.
		});
		frame.open();

	});
});
