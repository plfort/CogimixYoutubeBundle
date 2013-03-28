function youtubePlayer(musicPlayer) {
	
	this.name = "Youtube";
	this.interval;
	this.musicPlayer = musicPlayer;
	this.currentState = null;
	this.ytplayer = null;
	this.widgetElement = $("#youtubeplayerContainer");
	var params = {
		allowScriptAccess : "always",
	    wmode: "transparent"
	};
	var atts = {
		id : "youtubeplayer"
	};
	var self = this;
	
	
	
	if (self.ytplayer == null) {
			swfobject.embedSWF(location.protocol +"//www.youtube.com/apiplayer?enablejsapi=1&version=3", "youtubeplayer", "300",
					"300", "8", null, null, params, atts);
	}	

	this.hideWidget=function(){
		if(self.widgetElement!=null){
			console.log('hide youtube player in youtubeplugin');
			self.widgetElement.addClass('fakeHide');
		}
	}
	this.showWidget = function(){
		if(self.widgetElement!=null){
			console.log('show youtube player in youtubeplugin');
			self.widgetElement.removeClass('fakeHide');
		}
	}
	this.play = function(item) {
		
		var videoId = item.entryId;

			if(self.currentState == 1){
				self.stop();
			}
			self.ytplayer.loadVideoById(videoId);

	};
	this.stop = function(){
		console.log('call stop in youtube plugin');
		self.ytplayer.stopVideo();
		//window.clearInterval(self.interval);
	}
	
	this.pause = function(){
		self.ytplayer.pauseVideo();
	}
	this.resume = function(){
		self.ytplayer.playVideo();
	}
	
	this.playHelper = function() {
		self.ytplayer.playVideo();
	};

	this.onYoutubePlayerReady = function(playerId) {
		
		console.log('second catch player ready !');
		if (self.ytplayer == null) {
			self.ytplayer = document.getElementById("youtubeplayer");
			console.log('add event listener');
			self.ytplayer.addEventListener("onStateChange",
					"onYoutubePlayerStateChange");
			
			setTimeout(self.playHelper, 1000);
		} else {
			setTimeout(self.playHelper, 1000);
		}

	};

	this.onYoutubePlayerStateChange = function(newState) {
		var oldState = self.currentState;
		console.log('Youtube state changed ' + newState);
		self.currentState = newState;
		if(newState == -1 || newState == 5){
			
			self.clearInterval();
			self.musicPlayer.unbinCursorStop();
			self.musicPlayer.cursor.progressbar( "value",0 );
			self.hideWidget();
			return;
		}
		if(newState == 2){
			console.log('Clear interval');
			self.clearInterval();
			return;
		}
		
		if (newState == 0) {
			
			self.clearInterval();
			self.musicPlayer.unbinCursorStop();
			self.musicPlayer.cursor.progressbar( "value",0 );
			console.log('Call next from youtube plugin');
			self.musicPlayer.next();
			return;
		}
		if (newState == 1) {
			self.showWidget();
			self.musicPlayer.enableControls();
			self.musicPlayer.cursor.slider("value", 0);
			
			var duration = self.ytplayer.getDuration();
			var loaded = self.ytplayer.getVideoLoadedFraction();
			self.musicPlayer.cursor.slider("option", "max", duration).progressbar({value:loaded*100,});	
			
			self.musicPlayer.bindCursorStop(function(value) {
				self.ytplayer.seekTo(value, true);
			});
			
			console.log('Create interval');
			this.createCursorInterval(1000);
					
			return;
		}

	};
	this.clearInterval=function(){
		console.log('clear interval: '+self.interval);
		window.clearInterval(self.interval);
	};
	
	this.createCursorInterval=function(delay){
		self.clearInterval();
		self.interval = window.setInterval(function() {
			//console.log('update youtube cursor');
			var percentLoaded=self.ytplayer.getVideoLoadedFraction();
			var duration = self.ytplayer.getDuration();
			self.musicPlayer.cursor.progressbar('value',percentLoaded*100);
			if(self.musicPlayer.cursor.data('isdragging')==false){
				self.musicPlayer.cursor.slider("value", self.ytplayer
						.getCurrentTime())
			}
		}, delay);
		console.log('Interval : '+self.interval+' created');
	};
	
}

function onYouTubePlayerReady(playerId) {
	console.log('first catch player ready !');
	musicPlayer.plugin['yt'].onYoutubePlayerReady(playerId);
}
function onYoutubePlayerStateChange(newState) {

	musicPlayer.plugin['yt'].onYoutubePlayerStateChange(newState);
}
