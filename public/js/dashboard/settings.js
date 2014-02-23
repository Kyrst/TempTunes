$(function()
{
	$('#delete_photo').on('click', function()
	{
		$kyrst.ajax.post
		(
			BASE_URL + 'dashboard/delete-user-photo',
			{
			},
			{
				success: function()
				{
					$('#no_photo_container').fadeOut(function()
					{
						$('#photo_container').fadeIn();
					});
				}
			}
		);
	});
});