$(function()
{
	refresh_users();
});

function refresh_users()
{
	$kyrst.ajax.get
	(
		BASE_URL + 'admin/get-users',
		{
		},
		{
			success: function(result)
			{
				$('#users_container').html(result.data.users_html);
			}
		}
	);
}