function youtubePlayer(musicPlayer) {
	
	this.name = "Youtube";
	this.cancelRequested = false;
	this.interval;
	this.musicPlayer = musicPlayer;
	this.currentState = null;
	this.ytplayer = null;
	this.widgetElement = $("#youtubeplayerContainer");
	var params = {
		allowScriptAccess : "always",
	    wmode: "transparent",
	    controls:2,
	    allowFullscreen:true,
	    rel:false,
	    fs:1
	};
	var atts = {
		id : "youtubeplayer",
			allowfullscreen:1,
			
	};
	var self = this;
	
	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	this.requestCancel=function(){
		self.cancelRequested=true;
	}
	

	this.hideWidget=function(){
		if(self.widgetElement!=null){
			loggerYoutube.debug('hide youtube player in youtubeplugin');
			self.widgetElement.addClass('fakeHide');
		}
	}
	this.showWidget = function(){
		if(self.widgetElement!=null){
			loggerYoutube.debug('show youtube player in youtubeplugin');
			self.widgetElement.removeClass('fakeHide');
		}
	}
	this.play = function(item) {
		var videoId = item.entryId;
		
	/*	if (self.ytplayer == null) {
			swfobject.embedSWF("https://www.youtube.com/v/" + videoId
					+ "/?enablejsapi=1&version=3", "youtubeplayer", "350",
					"250", "9", null, null, params, atts);
		} else {*/
			if(self.currentState == 1){
				self.stop();
			}
			self.ytplayer.loadVideoById(videoId);

		//}
		

	};
	this.stop = function(){
		loggerYoutube.debug('call stop in youtube plugin');
		
		if(self.ytplayer!=null){
			self.ytplayer.stopVideo();
		}
		//window.clearInterval(self.interval);
	}
	
	this.pause = function(){
		if(self.ytplayer!=null){
			self.ytplayer.pauseVideo();
		}
	}
	this.resume = function(){
		if(self.ytplayer!=null){
			self.ytplayer.playVideo();
		}
	}
	
	this.setVolume = function(value){
		loggerYoutube.debug('call setVolume youtube');
		if(self.ytplayer!=null){
			self.ytplayer.setVolume(value);
		}
	}
	
	this.playHelper = function() {
		if(self.ytplayer!=null){
			self.setVolume(self.musicPlayer.volume);
			self.ytplayer.playVideo();
		}
	};
	this.onYouTubeIframeAPIReady = function(){
		self.ytplayer = new YT.Player('youtubeplayer', {
	          events: {
	            'onReady': onPlayerReady,
	            'onStateChange': self.onYoutubePlayerStateChange
	          }
		});
	}
		
	this.onYoutubePlayerReady = function(playerId) {
		
		loggerYoutube.debug('second catch player ready !');
		if (self.ytplayer == null) {
			self.ytplayer = document.getElementById("youtubeplayer");
			loggerYoutube.debug('add event listener');
			self.ytplayer.addEventListener("onStateChange",
					"onYoutubePlayerStateChange");
			
			setTimeout(self.playHelper, 1000);
		} else {
			
			setTimeout(self.playHelper, 1000);
		}

	};

	this.onYoutubePlayerStateChange = function(event) {
		var newState = event.data;
		var oldState = self.currentState;
		loggerYoutube.debug('Youtube state changed ' + newState);
		self.currentState = newState;
		
		if(newState == -1 || newState == 5){
			
			self.clearInterval();
			self.musicPlayer.unbinCursorStop();
			self.musicPlayer.cursor.progressbar( "value",0 );
			//self.hideWidget();
			return;
		}
		if(newState == 2){
			loggerYoutube.debug('Clear interval');
			self.clearInterval();
			return;
		}
		
		if (newState == 0) {
			
			self.clearInterval();
			self.musicPlayer.unbinCursorStop();
			self.musicPlayer.cursor.progressbar( "value",0 );
			loggerYoutube.debug('Call next from youtube plugin');
			self.musicPlayer.next();
			return;
		}
		if (newState == 1) {
			if(self.cancelRequested==false){
				//self.showWidget();
				self.musicPlayer.enableControls();
				self.musicPlayer.cursor.slider("value", 0);
				
				var duration = self.ytplayer.getDuration();
				var loaded = self.ytplayer.getVideoLoadedFraction();
				self.musicPlayer.cursor.slider("option", "max", duration).progressbar({value:loaded*100,});	
				
				self.musicPlayer.bindCursorStop(function(value) {
					self.ytplayer.seekTo(value, true);
				});
				
				loggerYoutube.debug('Create interval');
				self.createCursorInterval(1000);
			}else{
				self.cancelRequested=false;
				self.stop();
			}		
			return;
		}
	
	};
	this.clearInterval=function(){
		loggerYoutube.debug('clear interval: '+self.interval);
		window.clearInterval(self.interval);
	};
	
	this.createCursorInterval=function(delay){
		self.clearInterval();
		self.interval = window.setInterval(function() {
			//loggerYoutube.debug('update youtube cursor');
			var percentLoaded=self.ytplayer.getVideoLoadedFraction();
			var duration = self.ytplayer.getDuration();
			self.musicPlayer.cursor.progressbar('value',percentLoaded*100);
			if(self.musicPlayer.controls.volumeSlider.data('isdragging')==false){
		    self.musicPlayer.controls.volumeSlider.slider("value", self.ytplayer.getVolume())
			}
			if(self.musicPlayer.cursor.data('isdragging')==false){
				self.musicPlayer.cursor.slider("value", self.ytplayer
						.getCurrentTime())
			}
		}, delay);
		loggerYoutube.debug('Interval : '+self.interval+' created');
	};
	
}
iconMap['yt'] = 'bundles/cogimixyoutube/images/yt-icon.png';
$("body").on('musicplayerReady',function(event){
	event.musicPlayer.addPlugin('yt',new youtubePlayer(event.musicPlayer));
});

function onYouTubePlayerReady(playerId) {
	loggerYoutube.debug('first catch player ready !');
	musicPlayer.plugin['yt'].onYoutubePlayerReady(playerId);
}
function onYoutubePlayerStateChange(newState) {

	musicPlayer.plugin['yt'].onYoutubePlayerStateChange(newState);
}

function onYouTubeIframeAPIReady(){
	musicPlayer.plugin['yt'].onYouTubeIframeAPIReady();
}

function onPlayerReady(){
	console.log('YOUTUBE PLAYER READY');
}
	
