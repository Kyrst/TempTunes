/*global PxLoader: true, define: true, soundManager: true */ 

// PxLoader plugin to load sound using Wavesurfer
function PxLoaderWavesurferJS(id, url, tags, priority)
{
    var self = this,
        loader = null;

	this.url = url;
    this.tags = tags;
    this.priority = priority;

    this.sound = Object.create(WaveSurfer);
    this.sound.init(
    {
		container: '#waveform_' + id,
		waveColor: 'violet',
		progressColor: 'purple'
	});

	this.sound.on('ready', function()
	{
		loader.onLoad(self);
	});

	this.sound.on('loading', function(percent)
	{
		if ( percent === 100 )
		{
			loader.onLoad(self);
		}
	});

	this.sound.on('error', function()
	{
		loader.onTimeout(self);
	});

	this.start = function(pxLoader)
	{
		loader = pxLoader;

		var device_or_tablet = navigator.userAgent.match(/(ipad|iphone|ipod)/i);

		if ( device_or_tablet )
		{
			loader.onTimeout(self);
		}
		else
		{
			this.sound['load'](this.url);
		}
	};

	this.checkStatus = function()
	{
		switch( self.sound['readyState'] )
		{
			case 0: // Uninitialised
				break;
			case 1: // Loading
				break;
			case 2: // Failed/error
				loader.onError(self);

				break;
			case 3: // Loaded/success
				loader.onLoad(self);

				break;
		}
	};

	this.onTimeout = function()
	{
		loader.onTimeout(self);
	};

	this.getName = function()
	{
		return url;
	};
}

// add a convenience method to PxLoader for adding a sound
PxLoader.prototype.addSound = function(id, url, tags, priority)
{
    var soundLoader = new PxLoaderWavesurferJS(id, url, tags, priority);

    this.add(soundLoader);

    return soundLoader.sound;
};

// AMD module support
if ( typeof define === 'function' && define.amd )
{
    define('PxLoaderWavesurferJS', [], function()
    {
        return PxLoaderWavesurferJS;
    });
}