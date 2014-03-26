var $volume_control;

$player_manager = new PlayerManager();
$player_manager.init(buzz);

$(function()
{
	$volume_control = $('#volume_control');

	$player_manager.after_dom_init();

	$('#volume_control').on('change', function()
	{
		update_volume_control_appearance($(this).val());
	});

	update_volume_control_appearance(volume);
});

window.onbeforeunload = function()
{
	$player_manager.onbeforeunload();
}

function update_volume_control_appearance(value)
{
	var value = (value - $volume_control.attr('min')) / ($volume_control.attr('max') - $volume_control.attr('min'));

	$volume_control.css('background-image', '-webkit-gradient(linear, left top, right top, color-stop(' + value + ', #62B2E8), color-stop(' + value + ', #AAA))');
}