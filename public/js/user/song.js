$(function()
{
	$('#versions_tab').find('a').on('click', function(e)
	{
		e.preventDefault();

		$(this).tab('show');
	});
});