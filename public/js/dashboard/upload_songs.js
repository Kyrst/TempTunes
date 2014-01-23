$(function()
{
	$('#files_input').on('change', function()
	{
		var $this = $(this),
			allowed_file_types = ['audio/mp3'],
			num_files = this.files.length,
			$element = document.querySelector('#files_input');

		$this.fadeOut('slow', function()
		{
			var errors = [];

			for ( i = 0; i < num_files; i++ )
			{
				var file = this.files[i],
					progress_bar_selector = 'upload_item_progress_bar_' + i,
					xhr = new XMLHttpRequest(),
					is_last = (i === (num_files - 1));

				if ( !xhr.upload )
				{
					alert('no support');
					break;
				}

				$('#upload_progress_items').prepend('<div id="upload_item_' + i + '" class="upload-item"><span class="upload-item-title">' + file.name + '</span><progress id="' + progress_bar_selector + '" class="upload-item-progress-bar"></progress><span id="upload_item_progress_status_' + i + '" class="upload-item-progress-status">0%</span><div class="clear"></div><ul id="upload_item_progress_buttons_' + i + '" class="upload-item-progress-buttons"><li><a href="javascript:" id="upload_item_cancel_button_' + i + '" data-index="' + i + '">Cancel</a></li></ul><div id="upload_item_status_' + i + '" class="upload-item-status"></div></p>');

				if ( !$kyrst.in_array(file.type, allowed_file_types) /* && file.size <= $id("MAX_FILE_SIZE").value*/ )
				{
					var error_message = 'WRONG_TYPE';

					errors.push(
					{
						file: file,
						error: error_message
					});

					$('#upload_item_progress_buttons_' + i).hide();
					$('#upload_item_status_' + i).html(error_message).addClass('error').show();
					$('#' + progress_bar_selector).val(0);
					$('#upload_item_progress_status_' + i).html('');

					continue;
				}

				if ( file.size > parseInt($('#MAX_FILE_SIZE').val(), 10) )
				{
					var error_message = 'TOO_BIG';

					errors.push(
					{
						file: file,
						error: error_message
					});

					$('#upload_item_progress_buttons_' + i).hide();
					$('#upload_item_status_' + i).html(error_message).addClass('error').show();
					$('#' + progress_bar_selector).val(0);
					$('#upload_item_progress_status_' + i).html('');

					continue;
				}

				xhr.open('POST', BASE_URL + 'dashboard/upload-songs');

				(function(progress_bar_selector, i)
				{
					xhr.upload.onprogress = function(e)
					{
						$('#' + progress_bar_selector).attr('value', e.loaded).attr('max', e.total);

						$('#upload_item_progress_status_' + i).html(Math.round(e.loaded / e.total * 100) + '%');
					};
				}(progress_bar_selector, i));

				(function(i, is_last)
				{
					xhr.onload = function(result) // Upload complete
					{
						$('#upload_item_cancel_button_' + i).hide();

						$('#upload_item_progress_buttons_' + i).hide();
						$('#upload_item_status_' + i).html('Done...').addClass('success').show();
						$('#upload_item_' + i).fadeOut(function()
						{
							if ( is_last )
							{
								$('#upload_complete_container').fadeIn().delay(5000, function()
								{
									//$kyrst.redirect(BASE_URL + 'dashboard/my-songs');
								});
							}
						});
					};
				}(i, is_last));

				var form = new FormData();
				form.append('song_id', current_song_id);
				form.append('title', file.name);
				form.append('file', $element.files[i]);

				xhr.send(form);
			}

			/*if ( errors.length > 0 )
			{
				$kyrst.ui.show_error('File error!' + $kyrst.var_dump(errors, true));
			}*/
		});
	});
});