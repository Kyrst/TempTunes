var wavesurfers = [],
	num_song_uploads = 0;

$(function()
{
	binds();

	num_song_uploads = js_song_uploads.length;

	var wavesurfer_options =
	{
		height			: 96,
		waveColor		: '#428BCA',
		progressColor	: '#2D6CA2',
		loaderColor		: '#FFF',
		cursorColor		: 'navy',
		cursorWidth		: 0,
		markerWidth		: 2,
		normalize		: false
	};

	for ( var i = 0; i < num_song_uploads; i++ )
	{
		var song_upload = js_song_uploads[i];

		wavesurfer_options.container = document.querySelector('#waveform_' + song_upload.id);

		wavesurfers[song_upload.id] = Object.create(WaveSurfer);
		wavesurfers[song_upload.id].init(wavesurfer_options);
		wavesurfers[song_upload.id].filename = song_upload.filename;
		wavesurfers[song_upload.id].load(song_upload.filename);

		var song_upload_id = song_upload.id;

		(function(song_upload_id)
		{
			// Progress bar
			var $song_buttons_container = document.querySelector('#song_buttons_container_' + song_upload_id),
				$song_error_container = document.querySelector('#song_error_container_' + song_upload_id),
				$waveform_container = document.querySelector('#waveform_container_' + song_upload_id),
				$play_button = document.querySelector('#play_button_' + song_upload_id),
				$pause_button = document.querySelector('#pause_button_' + song_upload_id),
				$stop_button = document.querySelector('#stop_button_' + song_upload_id);

			var progressDiv = document.querySelector('#progress_bar_' + song_upload_id);
			var progressBar = progressDiv.querySelector('.progress-bar');

			wavesurfers[song_upload_id].on('loading', function(percent)
			{
				progressDiv.style.display = 'block';
				progressBar.style.width = percent + '%';
			});

			wavesurfers[song_upload_id].on('ready', function()
			{
				var timeline = Object.create(WaveSurfer.Timeline);

				timeline.init(
				{
					wavesurfer: wavesurfers[song_upload_id],
					container: "#wave_timeline_" + song_upload_id,
					primaryColor: '#C0C0C0'
				});

				progressDiv.style.display = 'none';
				//$song_buttons_container.style.display = 'block';

				$play_button.removeAttribute('disabled');

				//$('#waveform_' + song_upload_id + ' wave').style('height', wavesurfer_options.height + 'px', 'important');
			});

			wavesurfers[song_upload_id].on('play', function(percent)
			{
				//$play_button.style.display = 'none';
				//$pause_button.style.display = 'block';
			});

			wavesurfers[song_upload_id].on('finish', function(percent)
			{
				$play_button.style.display = 'block';
				$stop_button.setAttribute('disabled');
			});

			wavesurfers[song_upload_id].on('destroy', function()
			{
				progressDiv.style.display = 'none';
			});

			wavesurfers[song_upload_id].on('error', function(error)
			{
				$waveform_container.style.display = 'none';
				progressDiv.style.display = 'none';
				progressBar.style.display = 'none';

				$song_error_container.innerHTML = error + ' <a href="javascript:" id="retry_' + song_upload_id + '" data-song_id="' + song_upload_id + '" class="retry btn btn-xs btn-primary">Retry</a>';
				$song_error_container.style.display = 'block';

				// Retry
				$('#retry_' + song_upload_id).on('click', function()
				{
					var $this = $(this),
						song_upload_id = $this.data('song_upload_id');

					$this.text('Retrying...').addClass('disabled').prop('disabled', true);

					wavesurfers[song_upload_id].load(wavesurfers[song_upload_id].filename);
				});
			});
		}(song_upload_id));
	}
});

function binds()
{
	// Play
	$('#versions_tab_content').find('.play-button').on('click', function()
	{
		var song_upload_id = $(this).data('song_upload_id'),
			$play_button = document.querySelector('#play_button_' + song_upload_id),
			$pause_button = document.querySelector('#pause_button_' + song_upload_id),
			$stop_button = document.querySelector('#stop_button_' + song_upload_id);

		$play_button.style.display = 'none';
		$pause_button.style.display = 'block';
		$stop_button.removeAttribute('disabled');

		wavesurfers[song_upload_id].play();
	});

	// Pause
	$('#versions_tab_content').find('.pause-button').on('click', function()
	{
		var song_upload_id = $(this).data('song_upload_id'),
			$play_button = document.querySelector('#play_button_' + song_upload_id),
			$pause_button = document.querySelector('#pause_button_' + song_upload_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		wavesurfers[song_upload_id].pause();
	});

	// Stop
	$('#versions_tab_content').find('.stop-button').on('click', function()
	{
		var song_upload_id = $(this).data('song_upload_id'),
			$play_button = document.querySelector('#play_button_' + song_upload_id),
			$pause_button = document.querySelector('#pause_button_' + song_upload_id),
			$stop_button = document.querySelector('#stop_button_' + song_upload_id);

		$pause_button.style.display = 'none';
		$play_button.style.display = 'block';

		$stop_button.setAttribute('disabled');

		wavesurfers[song_upload_id].stop();
	});
}