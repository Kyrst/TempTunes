function time() {};

time.prototype =
{
	format_seconds: function(seconds)
	{
		seconds = Math.round(seconds);

		var minutes = Math.floor(seconds / 60);
		minutes = (minutes >= 10) ? minutes : '0' + minutes;

		seconds = Math.floor(seconds % 60);
		seconds = (seconds >= 10) ? seconds : '0' + seconds;

		return minutes + ':' + seconds;
	}
};