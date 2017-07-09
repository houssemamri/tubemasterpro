$(document).ready(function() {
    
    "use strict";
    
    
    //Video Background
    /*
    $("#top").vide("assets/images/video/ocean", {
		posterType: "jpg"
	});
*/
    $("#top").vide("assets/video/TTP-landingpage-800 bitrate.mp4", {
		posterType: "jpg"
	});

    //Youtube Background Video
    $(function(){
      $(".player").mb_YTPlayer();
    });


});
