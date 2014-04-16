<?php /*<div id="header_player_container" class="player" data-size="header" data-identifier="header">
	<div class="waveform-background"></div>
</div>*/ ?>

<div id="header_player_container">
	<div id="header_player_controls">
		<a href="javascript:" id="header_player_play" class="btn btn-xs btn-primary">&#x25B6;</a>
		<a href="javascript:" id="header_player_pause" class="btn btn-xs btn-primary">||</a>
		<a href="javascript:" id="header_player_stop" class="btn btn-xs btn-primary disabled">&#x25FC;</a>
	</div>

	<a href="<?= isset($current_song) ? e($current_song->song->get_url(Song::URL_PUBLIC)) : 'javascript:' ?>" id="header_player_title"<?php if ( isset($current_song) ): ?> style="display:inline-block"<?php endif ?>><?= isset($current_song) ? e($current_song->song->get_title()) : '' ?></a>
	<span id="header_player_time"></span>
</div>

<form action="/" method="post" id="volume_control_container">
	<input type="range" name="volume" id="volume_control" min="0" max="100" value="<?= $volume ?>">
</form>