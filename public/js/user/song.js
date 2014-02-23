var num_song_versions = 0;

$(function()
{
	binds();

	num_song_versions = js_song_versions.length;

	for ( var i = 0; i < num_song_versions; i++ )
	{
		var song_version = js_song_versions[i];

		wavesurfer_options.container = document.querySelector('#waveform_' + song_version.id);

		wavesurfers[song_version.id] = Object.create(WaveSurfer);
		wavesurfers[song_version.id].init(wavesurfer_options);
		wavesurfers[song_version.id].filename = song_version.filename;
		wavesurfers[song_version.id].load(song_version.filename);

		var song_version_id = song_version.id;

		(function(song_version_id)
		{
			// Progress bar
			var $song_buttons_container = document.querySelector('#song_buttons_container_' + song_version_id),
				$song_error_container = document.querySelector('#song_error_container_' + song_version_id),
				$waveform_container = document.querySelector('#waveform_container_' + song_version_id),
				$play_button = document.querySelector('#play_button_' + song_version_id),
				$pause_button = document.querySelector('#pause_button_' + song_version_id),
				$stop_button = document.querySelector('#stop_button_' + song_version_id);

			var progressDiv = document.querySelector('#progress_bar_' + song_version_id);
			var progressBar = progressDiv.querySelector('.progress-bar');

			wavesurfers[song_version_id].on('loading', function(percent)
			{
				progressDiv.style.display = 'block';
				progressBar.style.width = percent + '%';
			});

			wavesurfers[song_version_id].on('ready', function()
			{
				var timeline = Object.create(WaveSurfer.Timeline);

				timeline.init(
				{
					wavesurfer: wavesurfers[song_version_id],
					container: "#wave_timeline_" + song_version_id,
					primaryColor: '#C0C0C0'
				});

				progressDiv.style.display = 'none';
				//$song_buttons_container.style.display = 'block';

				$play_button.removeClass('disabled');

				//$('#waveform_' + song_version_id + ' wave').style('height', wavesurfer_options.height + 'px', 'important');
			});

			wavesurfers[song_version_id].on('play', function(percent)
			{
				//$play_button.style.display = 'none';
				//$pause_button.style.display = 'block';
			});

			wavesurfers[song_version_id].on('finish', function(percent)
			{
				$play_button.style.display = 'block';
				$stop_button.setAttribute('disabled');
			});

			wavesurfers[song_version_id].on('destroy', function()
			{
				progressDiv.style.display = 'none';
			});

			wavesurfers[song_version_id].on('error', function(error)
			{
				$waveform_container.style.display = 'none';
				progressDiv.style.display = 'none';
				progressBar.style.display = 'none';

				$song_error_container.innerHTML = error + ' <a href="javascript:" id="retry_' + song_version_id + '" data-song_id="' + song_version_id + '" class="retry btn btn-xs btn-primary">Retry</a>';
				$song_error_container.style.display = 'block';

				// Retry
				$('#retry_' + song_version_id).on('click', function()
				{
					var $this = $(this),
						song_version_id = $this.data('song_version_id');

					$this.text('Retrying...').addClass('disabled').prop('disabled', true);

					wavesurfers[song_version_id].load(wavesurfers[song_version_id].filename);
				});
			});
		}(song_version_id));
	}
});

function binds()
{
	// Play
	$('#versions_tab_content').find('.play-button').on('click', function()
	{
		var song_version_id = $(this).data('song_version_id'),
			$play_button = document.querySelector('#play_button_' + song_version_id),
			$pause_button = document.querySelector('#pause_button_' + song_version_id),
			$stop_button = document.querySelector('#stop_button_' + song_version_id);

		$play_button.style.display = 'none';
		$pause_button.style.display = 'block';
		$stop_button.removeAttribute('disabled');

		wavesurfers[song_version_id].play();
	});

	// Pause
	$('#versions_tab_content').find('.pause-button').on('click', function()
	{
		var song_version_id = $(this).data('song_version_id'),
			$play_button = document.querySelector('#play_button_' + song_version_id),
			$pause_button = document.querySelector('#pause_button_' + song_version_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		wavesurfers[song_version_id].pause();
	});

	// Stop
	$('#versions_tab_content').find('.stop-button').on('click', function()
	{
		var song_version_id = $(this).data('song_version_id'),
			$play_button = document.querySelector('#play_button_' + song_version_id),
			$pause_button = document.querySelector('#pause_button_' + song_version_id),
			$stop_button = document.querySelector('#stop_button_' + song_version_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		$stop_button.setAttribute('disabled');

		wavesurfers[song_version_id].stop();
	});
}