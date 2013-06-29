jQuery(document).ready(function($) {
	var fileslist = $('#uploadfileslist');
	$('#fileupload').fileupload({
		dataType: 'json',
		dropZone: $('#dropzone'),
		add: function (e, data) {
			data.context = $('<li class="span4"><div class="uploadingshade"><div class="progress"><div class="bar" style="width:0%;"></div></div><span class="progresstext"></span></div><div class="media"><a class="pull-left" href="#"><span class="fileiconholder"></span></a><div class="media-body"><span class="filename">Uploading...</span></div></div></li>').prependTo(fileslist);
			$(data.context).find('.fileiconholder').html($('#nofile').html());
			$(data.context).find('.filename').html(data.files[0].name);
			fixthumbs();
			data.submit();
		},
		done: function (e, data) {
			//console.log(data);
			$.each(data.result.files, function (index, file) {
				
				if (index==0) {
					
					if (file.error) {
						$(data.context).addClass('alert alert-error').find('.filename').replaceWith($('<span />').text(file.name));
						$(data.context).find('.fileiconholder').html($('#errorfile').html());
						var alerter = $('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><strong>Error:</strong> <span class="errorwhy"></span>: <span class="errorwhat"></span>.</div>');
						alerter.find('.errorwhat').text(file.name);
						alerter.find('.errorwhy').text(file.error);
						$('#uploaderrors').append(alerter);
					}
					else {
						$(data.context).find('.fileiconholder').replaceWith($(file.icon));
						$(data.context).find('.filename').replaceWith($('<a />').text(file.name).prop('href', file.url));
						$('.nofiles').fadeOut();
						
						
					}
					
					$(data.context).find('.uploadingshade').fadeOut( function() { $(this).remove(); });
					
				}
				else {
					var newfile = $('<li class="span4"><div class="media"><a class="pull-left" href="#"><span class="fileiconholder"></span></a><div class="media-body"><span class="filename">Uploading...</span></div></div></li>');
					
					if (file.error) {
						newfile.addClass('alert alert-error').find('.filename').replaceWith($('<span />').text(file.name));
						newfile.find('.fileiconholder').html($('#errorfile').html());
						var alerter = $('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><strong>Error:</strong> <span class="errorwhy"></span>: <span class="errorwhat"></span>.</div>');
						alerter.find('.errorwhat').text(file.name);
						alerter.find('.errorwhy').text(file.error);
						$('#uploaderrors').append(alerter);
					}
					else {
						newfile.find('.fileiconholder').replaceWith($(file.icon));
						newfile.find('.filename').replaceWith($('<a />').text(file.name).prop('href', file.url));
					}
					
					newfile.hide();
					
					newfile.prependTo(fileslist).fadeIn('slow');
				}
			});
			fixthumbs();
		},
		progress: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$(data.context).find('.progresstext').text(progress + '%');
			$(data.context).find('.progress .bar').css(
				'width',
				progress + '%'
			);
		}/*,
		progressall: function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		fileslist.find('tfoot .progress .bar').text(progress + '%').css(
		'width',
		progress + '%'
		);
		}*/
	});
	
	$(document).bind('dragover', function (e) {
		var dropZone = $('#dropzone'),
			timeout = window.dropZoneTimeout;
		if (!timeout) {
			dropZone.addClass('in');
		} else {
			clearTimeout(timeout);
		}
		if (e.target === dropZone[0]) {
			dropZone.addClass('hover');
		} else {
			dropZone.removeClass('hover');
		}
		window.dropZoneTimeout = setTimeout(function () {
			window.dropZoneTimeout = null;
			dropZone.removeClass('in hover');
		}, 100);
	});
	$(document).bind('drop dragover', function (e) {
		e.preventDefault();
	});
	
	
	function fixthumbs() {
		$('.thumbfixed').removeClass('thumbfixed');
		$('.row-fluid ul.thumbnails li.span6:nth-child(2n + 3)').addClass('thumbfixed');//.css('margin-left','0px');
		$('.row-fluid ul.thumbnails li.span4:nth-child(3n + 4)').addClass('thumbfixed');//.css('margin-left','0px');
		$('.row-fluid ul.thumbnails li.span3:nth-child(4n + 5)').addClass('thumbfixed');//.css('margin-left','0px'); 
	}
	
	$(window).resize(fixthumbs());
	
	$("#basic :file").filestyle({
		buttonText: "Browse"
	});
	
	if (document.location.hash == '#basic_uploader') {
		$('#uploadmethods a[href="#basic"]').tab('show');
	}
	else {
		$('#uploadmethods a[href="#improved"]').tab('show');
	}
	
	$('#gobasic').click( function() {
		$('#uploadmethods a[href="#basic"]').tab('show');
	});
	
	$('a[data-toggle="tab"]').on('shown', function (e) {
		document.location.hash = $(e.target).attr('href')+'_uploader';
	})
	
	$('#basicsubmit').click( function() {
		$('#loader').fadeIn();
	});
	
});
