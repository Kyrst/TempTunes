var add_friend_dialog, add_friend_dialog_selected_friend_id = null, add_friend_button;

$(function()
{
	add_friend_button =
	{
		title: 'Send Friend Request',
		class: 'btn-primary',
		on_click: function()
		{
			add_friend_button.set_title('Sending Friend Request...', true);

			$kyrst.ajax.post
			(
				BASE_URL + 'ajax/send-friend-request',
				{
					friend_user_id: add_friend_dialog_selected_friend_id
				},
				{
					success: function()
					{
						add_friend_dialog.close();
					}
				}
			);
		}
	};

	add_friend_dialog = $kyrst.ui.init_dialog_from_element
	(
		'#add_friend_dialog',
		false,
		500,
		210,
		false,
		false,
		false,
		[
			add_friend_button,
			{
				title: 'Cancel',
				class: 'btn-default',
				close_on_click: true
			}
		],
		{

		}
	);

	$('#add_friend_button').on('click', function()
	{
		add_friend_dialog.open();
	});

	$('#friend_identifier').autocomplete(
	{
		source: BASE_URL + 'ajax/get-friends-autocomplete',
		minLength: 2,
		select: function(event, ui)
		{
			add_friend_dialog_selected_friend_id = ui.item.id;
		}
	});

	$('.accept-friend-request, .deny-friend-request').on('click', function()
	{
		var id = $(this).data('id'),
			accept_or_deny = $(this).data('accept_or_deny');

		$kyrst.ajax.post
		(
			BASE_URL + 'ajax/respond-to-friend-request',
			{
				id: id,
				accept_or_deny: accept_or_deny
			},
			{
				success: function(result)
				{
					if ( $('.friend-request').length <= 1 )
					{
						$('#friend_requests_container').fadeOut();
					}
					else
					{
						$('#friend_request_' + id).fadeOut();
					}
				}
			}
		);
	});
});