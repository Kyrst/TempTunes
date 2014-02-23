var edit_song_version_dialog, edit_song_version_dialog_song_version_id, edit_song_version_dialog_save_button;

$(function()
{
	// Save song upload
	edit_song_version_dialog_save_button =
	{
		title: 'Save',
		class: 'btn-primary	',
		on_click: function()
		{
			edit_song_version_dialog.close();
		}
	};

	// Song upload dialog
	edit_song_version_dialog = $kyrst.ui.init_dialog_from_element
	(
		'#edit_song_version_dialog',
		false,
		550,
		450,
		true,
		false,
		false,
		[
			edit_song_version_dialog_save_button,
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
					BASE_URL + 'dashboard/my-songs/get-song-version',
					{
						song_version_id: edit_song_version_dialog_song_version_id
					},
					{
						success: function(result)
						{
							if ( result.errors.length === 0 )
							{
								$('#edit_song_version_dialog_title').val(result.data.song_version.title);
								$('#edit_song_version_dialog_description').val(result.data.song_version.description);
							}
						},
						error: function()
						{
							edit_song_version_dialog_save_button.hide();

							edit_song_version_dialog.set_title('Error');
							edit_song_version_dialog.show_error('Something went wrong.');
						},
						complete: function(result)
						{
						}
					}
				);

				edit_song_version_dialog.hide_loader();
			},
			before_close: function()
			{
			},
			after_close: function()
			{
			}
		}
	);

	$('#song_versions_tbody').find('.edit-song-version').on('click', function()
	{
		edit_song_version_dialog_song_version_id = $(this).data('song_version_id');

		edit_song_version_dialog.set_title('Loading Song Version...');
		edit_song_version_dialog.show_loader();
		edit_song_version_dialog.open();
	})

	$('#song_versions_tbody').find('.delete-song-version').on('click', function()
	{
		var id = $(this).data('song_version_id'),
			title = $(this).data('song_version_title');

		$kyrst.ui.show_confirm
		(
			'Are you sure you want to delete <strong>' + title + '</strong>.',
			function()
			{
				console.log('delete...');
			}
		);
	});
});