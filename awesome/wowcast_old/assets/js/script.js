$(document).ready(function(){
	
	//---- VARIABLES ----//
	var bcTimeOut;
	var eventFavCount = 0;
	var entityRef = {
	    "&": "&amp;",
	    "<": "&lt;",
	    ">": "&gt;",
	    '"': '&quot;',
	    "'": '&#39;',
	    "/": '&#x2F;',
	    " ": '&nbsp;'
	};
	
	//---- FUNCTION VARIABLES ----//
	var getCaret = function(element) {
	    var res = {
	        text: '',
	        start: 0,
	        end: 0
	    };
	
	    if (!element.value) {
	        return res;
	    }
	
	    try {
	        if (window.getSelection) {
	            res.start = element.selectionStart;
	            res.end   = element.selectionEnd;
	            res.text  = element.value.slice(res.start, res.end);
	        } else if (doc.selection) {
	            element.focus();
	
	            var range1 = doc.selection.createRange(),
	                range2 = doc.body.createTextRange();
	
	            res.text = range1.text;
	
	            try {
	                range2.moveToElementText(element);
	                range2.setEndPoint('StartToStart', range1);
	            } catch (e) {
	                range2 = element.createTextRange();
	                range2.setEndPoint('StartToStart', range1);
	            }
	
	            res.start = element.value.length - range2.text.length;
	            res.end = res.start + range1.text.length;
	        }
	    } catch (e) {
	        
	    }
	
	    return res;
	};
	
	//- Initialise Video and Chatbox
	chatBoxInit();
	videoBoxResize();
	
	//- Resize window event
    $(window).resize(function() {
        videoBoxResize();
    });
	
	//- Clear content modal everytime the modal is closed or hidden
	$('#modal').on('hidden.bs.modal', function (e) {
	    $('#modal').html('');
	});
    
	//- Chat box scroll event
	$('#chat-extended-message').on('scroll', function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {}
    })

	$('.chat-constrainer').scroll(function() {
	    var scrollPos = $(this).scrollTop() + $(this).height();
	    var listPos = $("#chat-messages-extended").height();
	    if (scrollPos > (listPos - 100)) {
	        $('.missed-messages-container').removeClass('dont-show');
	        $('#not-seen-mesages').show();
	        $('#not-seen-mesages').children().insertAfter('#not-seen-mesages');
	
	        // rmeove flag
	        $('.missed-messages-container').removeClass('dont-show');
	
	    }
	});
	
	if ( session ) {
		// Get a reference to our posts
		var ref = new Firebase(fireBaseURL+"data/messages/"+room);
		// Retrieve new posts as they are added to our database
		ref.on("child_added", function(snapshot, prevChildKey) {
		  var newPost = snapshot.val();
		  //console.log(newPost);
		  createMessage(newPost,snapshot.key(),session.connection,userType);
		});
		
		ref.once("value", function(snapshot) {
			if (snapshot.val()) {
				$.each(snapshot.val(), function(key,val){
					var childRef = new Firebase(fireBaseURL+"data/messages/"+room+"/"+key);
					var msgId    = key;
					
					if (val.user_fav) {
						var favCount 	= Object.keys(val.user_fav).length;
						var favRef 		= new Firebase(fireBaseURL+"data/messages/"+room+"/"+key+"/user_fav");
						favRef.on("child_added", function(snap, prevChildKey) {
							favRef.once("value", function(snapshot) {
								favCount = Object.keys(snapshot.val()).length;
								//console.log(favCount);
								if ( !$('#'+msgId+' .favorite-container').hasClass('faved') ) {
									$('#'+msgId+' .favorite-container').addClass('faved');
								}
								$('#'+msgId+' .favorite-container .favorite-count').text(favCount);
							});
						});
						
						favRef.on("child_removed", function(snap, prevChildKey) {
							favRef.once("value", function(snapshot) {
								if ( snapshot.val() ) {
									favCount = Object.keys(snapshot.val()).length;
								}
								else {
									favCount = 0;
								}
								//console.log(favCount);
								if ( favCount <= 0 ) {
									$('#'+msgId+' .favorite-container').removeClass('faved');
								}
								else {
									$('#'+msgId+' .favorite-container').addClass('faved');
								}
								
								$('#'+msgId+' .favorite-container .favorite-count').text(favCount);
							});
						});
					}
					
					childRef.on("child_added", function(snap, prevChildKey) {
						if (snap.key() == 'user_fav') {
							$('#'+msgId+' .favorite-container').addClass('faved');
							$('#'+msgId+' .favorite-container .favorite-count').text(1);
						}
					});
					
					childRef.on("child_removed", function(snap, prevChildKey) {
						if (snap.key() == 'user_fav') {
							$('#'+msgId+' .favorite-container').removeClass('faved');
							$('#'+msgId+' .favorite-container .favorite-count').text(0);
						}
					});
				});
			}
		});
		
		session.on("signal:kick_user", function(event) {
			var kick_arr = event.data.split('|');
			if ( session.connection.connectionId == kick_arr[2] ) {
				$('#loading-screen h1').text('Disconnecting from stream...');
				$('#loading-screen').fadeIn();
				session.disconnect();
			}
		  //console.log("Signal sent from connection " + event.from.id);
		  // Process the event.data property, if there is any data.
		});
	}
	
	//-- TWITTER SHARE --//
	$('#tt-share').on('click', function(e){
		e.preventDefault();
		
		var roomInfo = $('#room-info').data('room');
		var trainer  = (roomInfo.trainer) ? '@'+roomInfo.trainer : '';
		var guest    = (roomInfo.guest) ? '& @'+roomInfo.guest : '';
	
		var html = '<div id="edit-tweet" class="" style="left: 214.6px; top: 203.078px;"> '+
	    '<div class="close"></div> ' +
	    '<h1> Compose tweet </h1>' +
	    '<div class="textarea-wrapper">' +
	    '<textarea id="composed-tweet-text" maxlength="110">'+roomName+' w/ '+trainer+' '+guest+'</textarea> '+
	    '</div>'+
	    '<div class="button-container"> <span class="left"> WOW!CAST room link will be added</span> <span id="tweet-cap">35/110</span> <button class="send-tweet"> Post Tweet </button>  </div>' +
	    '</div>';
	    $('#modal').html(html);
	    $('#modal').modal('show');
	    
	    $('#modal #edit-tweet #tweet-cap').text($('#modal #edit-tweet #composed-tweet-text').val().length+'/110');
	    
	    $('#modal #edit-tweet #composed-tweet-text').on('keyup', function(e){
		    e.preventDefault();
		    thisVal = $(this).val();
			$('#modal #edit-tweet #tweet-cap').text(thisVal.length+'/110');
	    });
	    
		
		$('#modal #edit-tweet .close').on('click', function(e){
			e.preventDefault();
			$('#modal').modal('hide');
			$('#modal').empty();
		});
		
		$('#modal #edit-tweet .send-tweet').on('click', function(e){
			e.preventDefault();
			var thisButton = $(this);
			thisButton.prop('disabled', true);
			var data = {
				type  		: 'post_tweet',
				room_url	: roomShortUrl,
				tweet 		: $('#composed-tweet-text').val()
			};
			$.ajax({
	            type: "POST",
	            url: baseurl+"room-ajax.php",
	            data: data,
	            dataType: 'json',
	            success: function(data) {
	            	//console.log(data);
	            	if (data.result.errors) {
	            		alert(data.result.errors[0].message);
	            	}
	            	else {
						$('#modal').modal('hide');
						$('#modal').empty();
	            	}
					thisButton.prop('disabled', false);
	            }
	        });
		});
	});

	//---- FUNCTIONS ----//

    function chatBoxInit () {
	    var chatInput = $('#chat-input');
	    var charCount = $('#char-count-chat');
	
		setTimeout(function() {
	        chatInput.autogrow();
	    }, 4000);
	    
	    setTimeout(function() {
	        $(".chat-constrainer").scrollTop($("#chat-messages-extended").height());
	    }, 1000)
	
	    chatInput.on('keydown', function(e) {
	        var msg = chatInput.val().trim();
	        if (e.keyCode === 13 && e.shiftKey) {
	            e.preventDefault();
	            return false;
	        } else if (e.keyCode === 13 && !e.shiftKey) {
	            if (!msg) {
	                e.preventDefault();
	                return false;
	            } else {
                	sendMessage(msg);
                    $("#chat-input").val("");
                    return false;
	            }
	        }
	    });
	    
	    chatInput.on('keyup', function(e) {
	        var msg = chatInput.val();
	        charCount.html(msg.length + "/500");
	    });
	
	}
	
	function escape (string) {
	    return String(string).replace(/[&<>"'\/\ ]/g, function(s) {
	        return entityRef[s];
	    });
	}
	
	function videoBoxResize () {
		var room = $('#single-room');
	    var roomWidth = room.width();
	    var roomHeight = room.height();
	
	    if (window.innerWidth < 1200) {
	        if (roomHeight < roomWidth) {
	            roomHeight = roomHeight;
	        } else {
	            roomHeight = roomWidth;
	        }
	    } else {
	        if (roomHeight < roomWidth) {
	            roomHeight = roomHeight - 70;
	        } else {
	            roomHeight = roomWidth - 70;
	        }
	    }
	    $('.stream-container').height(roomHeight+100).width(roomHeight+100);
	}
	
	function createMessage (msgData,msgId,connection,userTypeMain) {
		
	    if(msgData) {
	    	var msgClass 		= '';
	    	var faveClass		= '';
	    	var html     		= '';
	    	var nameStyle		= '';
	    	var spanBadge		= '';
	    	var userType 		= $.trim(msgData.type).toLowerCase();
	    	var userMsg  		= $.trim(msgData.message).toLowerCase();
	    	var faveCount		= (msgData.user_fav) ? Object.keys(msgData.user_fav).length : 0;
	    	var faveCountClass 	= "";
	    	
	    	if ( faveCount > 0 ) {
		    	faveClass 		= "faved";
	    	}
	    	
	    	if (userType == 'trainer') {
	    		//nameStyle= 'style="color:#4884B8;"';
		    	spanBadge= '<span style="background-color:#4884B8; background-image:url('+baseurl+'assets/img/host_badge_letter.svg);" class="host-label avatar-badge"> </span>';
	    	}
	    	else if (userType == 'guest') {
	    		//nameStyle= 'style="color:#E8A82C;"';
		    	spanBadge= '<span style="background-color:#E8A82C; background-image:url('+baseurl+'assets/img/guest_badge_letter.svg);" class="caller-label avatar-badge"> </span>';
	    	}
	    	
	    	if ( userMsg == "joined the conversation" ) {
		    	html = '<li class="activity-item border chat">' +
						    '<div class="card">' +
							 	'<div class="profile-card">' +
							 		'<div data-user_id="" class="card-avatar smaller profile-link">' +
							 			'<img class="" data-user_id="" src="'+msgData.pic+'">' +
							 		'</div>'+
								 	'<div class="card-info">' +
										'<h1> <span data-user_id="" class="profile-link">'+msgData.name+' </span>' +
										'<span class="hint-text">joined</span> </h1>' +
									'</div>' +
							 	'</div>'+
							'</div>' +
						'</li>';
	    	}
	    	else {
	    		var message 		= msgData.message;
	    		var match   		= '';
	    		var newString     	= '';
	    		
	    		if ( message.match(/(\.jpg|\.png|\.bmp|\.gif)/) ) {
	    			var imageRegEx = /(https?:\/\/\S+\.(?:jpg|png|gif|bmp))/;
			    	match          = message.match(imageRegEx);
			    	newString      = '<a href="#" onclick="popup_video(\''+match[0]+'\',\'image\');return false;"><img src="'+match[0]+'" width="100%" height="100%" /></a>';
			    	message        = message.replace(imageRegEx, newString);
	    		}
	    		
	    		if ( message.indexOf('youtu.be') >= 0 ) {
	    			var videoRegEx = /https?:\/\/(www\.)?youtu\.be\/([^ &\n]+)(&.*?(\n|\s))?/i;
				    match 		   = message.match(videoRegEx);
				    newString      = '<object width="100%" height="100%"><param name="movie" value="http://www.youtube.com/v/'+match[2]+'"></param><embed src="http://www.youtube.com/v/'+match[2]+'" type="application/x-shockwave-flash" width="100%" height="100%"></embed></object>';
			    	message        = message.replace(videoRegEx, newString);
	    		}
	    		
	    		if ( message.indexOf('youtube.com') >= 0 ) {
		    		var videoRegEx = /https?:\/\/(www\.)?youtube\.com\/watch\?v=([^ &\n]+)(&.*?(\n|\s))?/i;
				    match 		   = message.match(videoRegEx);
				    newString      = '<object width="100%" height="100%"><param name="movie" value="http://www.youtube.com/v/'+match[2]+'"></param><embed src="http://www.youtube.com/v/'+match[2]+'" type="application/x-shockwave-flash" width="100%" height="100%"></embed></object>';
			    	message        = message.replace(videoRegEx, newString);
	    		}
	    		
	    		if ( message.indexOf('vimeo.com') >= 0 ) {
		    		var videoRegEx = /https?:\/\/(www\.)?vimeo\.com\/([^ ?\n\/]+)((\?|\/).*?(\n|\s))?/i;
				    match 		   = message.match(videoRegEx);
				    newString      = '<object width="100%" height="100%"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='+match[2]+'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='+match[2]+'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%"></embed></object>';
			    	message        = message.replace(videoRegEx, newString);
	    		}
	    		
	    		var kick_html = '';
	    		if ( userTypeMain == 'Trainer' ) {
		    		kick_html = '<span class="kick-icon inline-icon" data-connection_id="'+msgData.connection_id+'"> </span>';
	    		}
	    		
	    		var atUserRegex = new RegExp(/\@\w+/g);
	    		
				if ( atUserRegex.test(message) ) {
					var matches = message.match(atUserRegex);
					console.log(matches);
					$.each(matches, function(key, val){
						var searchUser = val;
						var reg = new RegExp(searchUser, "g");
						var splitName = searchUser.split('@');
						message = message.replace(reg, '<a class="name-link" data-username="'+splitName[1]+'">'+val+'</a>');
					});
				}
				
				if ( message.indexOf('@'+userName) >= 0 ) {
					
					mentionsCount++;
					var mentionsCountEl = $('#mentions-count');
					mentionsCountEl.text('('+mentionsCount+')');
					mentionsCountEl.removeClass('bounceOut');
					mentionsCountEl.addClass('animated bounceIn');
				}
					
	    		//var question = message.substring(0, 3);
	    		var splitMsgSpace = message.split(" ", 2);
	    		var qAndA = splitMsgSpace[0].substring(0, 2);
	    		
	    		if (qAndA.toLowerCase() == '/q') {
		    		var messageBadge = '<div class="messageBadge" style="background-image:url('+baseurl+'assets/img/feed-questions-icon-red.svg);"></div>';
		    		var number 		 = splitMsgSpace[0].toLowerCase().split('/q', 2)[1];
					var numberText 	 = (number && number != '') ? '<span class="q-number">#'+number+'</span> ' : '';
	    		}
	    		else if (qAndA.toLowerCase() == '/a') {
		    		var messageBadge = '<div class="messageBadge" style="background-image:url('+baseurl+'/img/feed-answers-icon-green.svg);"></div>';
		    		var number       = splitMsgSpace[0].toLowerCase().split('/a', 2)[1];
					var numberText   = (number && number != '') ? '<span class="a-number">#'+number+'</span> ' : '';
	    		}
	    		
	    		if ( qAndA.toLowerCase() == '/q' || qAndA.toLowerCase() == '/a' ) {
					var splitMsg     = message.split(splitMsgSpace[0], 2);
	    			
		    		html = '<li id="'+msgId+'" data-type="message" class="chat-message chat-bar-item message">' +
		    					'<div class="chat-message-wrapper user-msg">' +
		    						'<div class="avatar-block">' +
		    							spanBadge +
		    							messageBadge +
		    							'<div class="timestamp-tip"></div>' +
		    							'<img src="'+msgData.pic+'" alt="" class="profile-link profile-avatar">' +
		    						'</div>' +
		    						'<div class="message-block msg-container">' +
		    							'<div class="actions-block">' +
		    								'<a class="username profile-link" '+nameStyle+'>' +
		    									'<span>@</span><span>'+msgData.name+'</span>' +
		    								'</a>' +
											'<div class="favorite-container '+faveClass+'" data-chat_id="'+msgId+'" data-user_id="">' +
												'<span class="favorite-count">'+faveCount+'</span>' +
												'<span class="favorite-msg inline-icon"></span>' +
												'<div class="favorited-by-container"></div>' +
											'</div>' +
											'<span class="reply-msg inline-icon" data-username="'+msgData.name+'"></span>' +
											kick_html +
										'</div>' +
										'<span class="Linkify">' +
											'<p class="text chat-question">' +
												numberText+splitMsg[1]+
											'</p>' +
										'</span>' +
										'<span></span>' +
									'</div>' +
								'</div>' +
							'</li>';
		    									
	    		}
	    		else {
	    		
			    	html = '<li data-user_id="" id="'+msgId+'" data-type="message" class="message">' +
					    '<div class="user-msg">' +
						 '<div class="profile-image">' +
						  spanBadge +
						  '<img class="modal-link" data-user_id="" src="'+msgData.pic+'">' +
						 '</div>'+
						 '<div class="msg-container">' +
						  '<span class="time-stamp-chat"></span>' +
						  '<div class="chat-item-actions">' +
						   '<a class="anchor-name modal-link" data-user_id="" '+nameStyle+'>@'+msgData.name+'</a>' +
						    '<div class="favorite-container '+faveClass+'" data-chat_id="'+msgId+'" data-user_id="">' +
							 '<span class="favorite-count"> '+faveCount+'</span>' +
							 '<span class="favorite-msg inline-icon"> </span>' +
							'</div>' +
							'<span class="reply-msg inline-icon" data-username="'+msgData.name+'"> </span>' +
							 kick_html +
						   '</div>' +
						   '<p>'+message+'</p>' +
						  '</div>' +
						 '</div>' +
						'</li>';
				}
					
				//- Mentions
				clearTimeout(bcTimeOut);
				if ( message.indexOf('@'+$('#caller-0-wrapper').data('uname')) >= 0 ) {
					$('#trainer').css('border-color', 'gold');
					bcTimeOut = setTimeout(function(){
						$('#trainer').css('border-color', 'white');
					}, 500);
				}
				else if ( message.indexOf('@'+$('#caller-1-wrapper').data('uname')) >= 0 ) {
					$('#guest').css('border-color', 'gold');
					bcTimeOut = setTimeout(function(){
						$('#guest').css('border-color', 'white');
					}, 500);
				}
	    	}
	    	
			$('#chat-messages-extended').prepend(html);
			
	        $(".chat-constrainer").scrollTop($("#chat-messages-extended").height());
	        
	        
			var favRef = new Firebase(fireBaseURL+"data/messages/"+msgData.room_id+"/"+msgId+"/user_fav");
			favRef.once("value", function(snapshot) {
				if ( snapshot.val() !== null ) {
					var userExist = false;
					$.each(snapshot.val(), function(key,val){
						if (val.user_name == userName) {
							userExist = true;
						}
					});
					
					if ( userExist ) {
						if ( !$('#'+msgId+' .favorite-container').hasClass('clicked') ) {
							$('#'+msgId+' .favorite-container').addClass('clicked');
						}
					}
				}
			});
		
			$('#'+msgId+' .favorite-container').on('click', function(e){
				e.preventDefault();
				var favRef = new Firebase(fireBaseURL+"data/messages/"+msgData.room_id+"/"+msgId+"/user_fav");
				favRef.once("value", function(snapshot) {
					if ( snapshot.val() === null ) {
	            		favRef.push().set({
							"user_name"   : userName
						});
						
						$('#'+msgId+' .favorite-container').addClass('faved clicked');
						$('#'+msgId+' .favorite-container .favorite-count').text(1);
	        		}
	        		else {
	        			var userExist = false;
						$.each(snapshot.val(), function(key,val){
							if (val.user_name == userName) {
								var removeRef = new Firebase(fireBaseURL+"data/messages/"+msgData.room_id+"/"+msgId+"/user_fav/"+key);
								removeRef.remove();
								userExist = true;
								return false;
							}
						});
						
						if ( !userExist ) {
							favRef.push().set({
								"user_name"   : userName
							});
							
							favRef.once("value", function(newSnap) {
								var newFavCount = Object.keys(newSnap.val()).length;
								$('#'+msgId+' .favorite-container').addClass('faved clicked');
								$('#'+msgId+' .favorite-container .favorite-count').text(newFavCount);
							});
						}
						else {
							favRef.once("value", function(newSnap) {
								if ( newSnap.val() !== null ) {
									var newFavCount = Object.keys(newSnap.val()).length;
									$('#'+msgId+' .favorite-container .favorite-count').text(newFavCount);
									$('#'+msgId+' .favorite-container').removeClass('clicked');
								}
								else {
									$('#'+msgId+' .favorite-container').removeClass('faved clicked');
									$('#'+msgId+' .favorite-container .favorite-count').text(0);
								}
							});
						}
					}
				});
			});
			
			
			$('#'+msgId+' .reply-msg').on('click', function(e){
				var recepient = $(this).data('username');
				if ( recepient != userName ) {
					var subject   = '@'+recepient+' ';
					var chat 	  = $('#chat-input');
					var charCount = $('#char-count-chat');
					
					chat.val(subject);
					chat.focus();
					
					var msg 	  = chat.val();
					charCount.html(msg.length + "/500");
				}
			});
			
	    	if ( userTypeMain == 'Trainer' ) {
				$('#'+msgId+' .kick-icon').on('click', function(e){
					e.preventDefault();
					var connId = $(this).data('connection_id');
					if ( userType != 'trainer' ) {
						var recepient = $(this).prev('.reply-msg').data('username');
						var popUpHTML = '<div id="incoming-modal" tabindex="-1" role="dialog" aria-labelledby="incoming-modal-label">' +
										  '<div class="modal-dialog modal-sm" role="document">' +
										    '<div class="modal-content">' +
										      '<div class="modal-header">' +
										        '<h4 class="modal-title" id="myModalLabel">Message</h4>' +
										      '</div>' +
										      '<div class="modal-body">' +
										      	'<p>Are you sure you want to kick user '+recepient+'?</p>' +
										      '</div>' +
										      '<div class="modal-footer">' +
										        '<button id="kick-user" data-id="'+connId+'" type="button" class="btn btn-success">Yes</button>' +
										        '<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>' +
										      '</div>' +
										    '</div>' +
										  '</div>' +
										'</div>';
						$('#modal').html(popUpHTML);
						$('#modal').modal('show');
						
						$('#kick-user').on('click', function(e){
							e.preventDefault();
							$('#modal').modal('hide');
							session.signal(
						    {
						      data: 'kick|mbox|'+$(this).data('id'),
						      type: "kick_user"
						    },
							    function(error,data) {
							      if (error) {
							        console.log("signal error ("
							                     + error.code
							                     + "): " + error.message);
							      } else {
							      	console.log(data);
							      }
							    }
						    );
						});
					}
				});
			}
		}
	}
	
	function sendMessage(msg) {
		var myFirebaseRef = new Firebase(fireBaseURL+"data/messages/"+room);
		myFirebaseRef.push().set({
			"connection_id" : session.connection.connectionId,
			"room_id"   : room,
			"name"      : userName,
			"message"   : msg,
			"user_fav"  : {},
			"pic"		: profilePic,
			"type"		: userType
		});
	}
	
	//---- HELPER FUNCTIONS ----//
	//- Autogrow for chatbox
	$.fn.autogrow = function(options) {
	
	    options = $.extend({
	        vertical: true,
	        horizontal: false,
	        characterSlop: 0
	    }, options);
	
	    this.filter('textarea,input').each(function() {
	
	        var $this = $(this),
	            minHeight = $this.height(),
	            maxHeight = $this.attr("maxHeight"),
	            minWidth = typeof($this.attr("minWidth")) == "undefined" ? 0 : $this.attr("minWidth");
	
	        if (typeof(maxHeight) == "undefined") maxHeight = 1000000;
	
	        var shadow = $('<div class="autogrow-shadow"></div>').css({
	            position: 'absolute',
	            top: -10000,
	            left: -10000,
	            fontSize: $this.css('fontSize'),
	            fontFamily: $this.css('fontFamily'),
	            fontWeight: $this.css('fontWeight'),
	            lineHeight: $this.css('lineHeight'),
	            resize: 'none'
	        }).appendTo(document.body);
	
	        shadow.html('a');
	        var characterWidth = shadow.width();
	        shadow.html('');
	
	        var update = function(val) {
	
	            var times = function(string, number) {
	                for (var i = 0, r = ''; i < number; i++) r += string;
	                return r;
	            };
	
	            if (typeof val === 'undefined') val = this.value;
	            if (val === '' && $(this).attr("placeholder")) val = $(this).attr("placeholder");
	
	            if (options.vertical)
	                val = val.replace(/&/g, '&amp;')
	                    .replace(/</g, '&lt;')
	                    .replace(/>/g, '&gt;')
	                    .replace(/\n$/, '<br/>&nbsp;')
	                    .replace(/\n/g, '<br/>')
	                    .replace(/ {2,}/g, function(space) {
	                        return times('&nbsp;', space.length - 1) + ' ';
	                    });
	            else
	                val = escape(val);
	
	            shadow.html(val).css("width", "auto");
	            
	            if (options.horizontal) {
	                var slopWidth = options.characterSlop * characterWidth + 2;
	
	                var newWidth = Math.max(shadow.width() + slopWidth, minWidth);
	                var maxWidth = options.maxWidth;
	                
	                if (maxWidth) newWidth = Math.min(newWidth, maxWidth);
	                $(this).css("width", newWidth);
	            }
	
	            if (options.vertical) {
	                shadow.css("width", $(this).width() - parseInt($this.css('paddingLeft'), 10) - parseInt($this.css('paddingRight'), 10));
	                var shadowHeight = shadow.height();
	                var newHeight = Math.min(Math.max(shadowHeight, minHeight), maxHeight);
	                $(this).css("height", newHeight);
	                $(this).css("overflow", newHeight == maxHeight ? "auto" : "hidden");
	            }
	        };
	
	        $(this)
	            .change(function() {
	                update.call(this);
	                return true;
	            })
	            .keyup(function() {
	                update.call(this);
	                return true;
	            })
	            .keypress(function(event) {
	                var val = this.value;
	                var caret = getCaret(this);
	
	                var valAfterKeypress = val.slice(0, caret.start) + String.fromCharCode(event.which) + val.slice(caret.end);
	                update.call(this, valAfterKeypress);
	                return true;
	            })
	            .bind("update.autogrow", function() {
	                update.apply(this);
	            })
	            .bind("remove.autogrow", function() {
	                shadow.remove();
	            });
	
	        update.apply(this);
	
	    });
	
	    return this;
	};
	
});