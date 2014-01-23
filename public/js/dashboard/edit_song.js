var edit_song_upload_dialog, edit_song_upload_dialog_song_upload_id;

$(function()
{
	edit_song_upload_dialog = $kyrst.ui.init_dialog_from_element
	(
		'#edit_song_upload_dialog',
		false,
		550,
		450,
		true,
		false,
		false,
		[
			{
				title: 'Save',
				class: 'btn-primary	',
				on_click: function()
				{
					// Ajax save...

					edit_song_upload_dialog.close();
				}
			},
			{
				title: 'Close',
				close_on_click: true
			}
		],
		{
			on_open: function()
			{
				$kyrst.ajax.get
				(
					BASE_URL + 'dashboard/my-songs/get-song-upload',
					{
						song_upload_id: edit_song_upload_dialog_song_upload_id
					},
					{
						success: function(result)
						{
							$('#edit_song_upload_dialog_title').val(result.data.song_upload.title);
						},
						error: function(error)
						{
							edit_song_upload_dialog.show_error(error);
							//edit_song_upload_dialog.close();
						},
						complete: function(result)
						{
						}
					}
				);

				edit_song_upload_dialog.hide_loader();
			},
			before_close: function()
			{
			},
			after_close: function()
			{
			}
		}
	);

	edit_song_upload_dialog_song_upload_id = 1;

	edit_song_upload_dialog.set_title('Yoyo');
	edit_song_upload_dialog.show_loader();
	edit_song_upload_dialog.open();
});