var selected_songs = [],
	num_selected_songs = 0;

var $delete_selected_button, $merge_selected_button;

$(function()
{
	$delete_selected_button = $('#delete_selected_button');
	$merge_selected_button = $('#merge_selected_button');

	$('#songs_form').find('.checkbox').on('click', function()
	{
		selected_songs = [];

		$('#songs_form').find('.checkbox:checked').each(function(i, $element)
		{
			selected_songs.push($element.value);
		});

		num_selected_songs = selected_songs.length;

		if ( num_selected_songs > 0 )
		{
			$delete_selected_button.removeClass('disabled');

			if ( num_selected_songs > 1 )
			{
				$merge_selected_button.removeClass('disabled');
			}
			else
			{
				$merge_selected_button.addClass('disabled');
			}
		}
		else
		{
			$delete_selected_button.addClass('disabled');
			$merge_selected_button.addClass('disabled');
		}
	});
});