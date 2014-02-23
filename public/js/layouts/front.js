$player_manager = new PlayerManager();
$player_manager.init(buzz);

$(function()
{
	$player_manager.after_dom_init();
});