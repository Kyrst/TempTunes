<?php /*<div id="header_player_container" class="player" data-size="header" data-identifier="header">
	<div class="waveform-background"></div>
</div>*/ ?>

<div id="header_player_container">
	<a href="javascript:" id="header_player_title"></a>
	<span id="header_player_time"></span>
</div>

<form action="/" method="post" id="volume_control_container">
	<input type="range" name="volume" id="volume_control" min="1" max="100" value="<?= $volume ?>">
</form>