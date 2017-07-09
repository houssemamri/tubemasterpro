$(document).ready(function() {

    "use strict";


    //Video Background
    /*
    $("#top").vide("assets/images/video/ocean", {
		posterType: "jpg"
	});
*/
    if(navigator.userAgent.indexOf("Firefox") != -1 )
    {
        // $("#top").vide("/assets/main/assets/video/TTP-landingpage-800_bitrate.ogv", {
        //     posterType: "png"
        // });

         $("#top").vide({
            ogv: "/assets/main/assets/video/TTP-landingpage-800_bitrate.ogv",
            poster: "/assets/main/assets/video/TTP-landingpage-800-bitrate-low.png"
        }, {
            posterType: "png"
        });
    }
    else{
        // $("#top").vide("/assets/main/assets/video/TTP-landingpage-800 bitrate.mp4", {
        //     posterType: "png"
        // });

        $("#top").vide({
            mp4: "/assets/main/assets/video/TTP-landingpage-low bitrate.mp4",
            poster: "/assets/main/assets/video/TTP-landingpage-800-bitrate-low.png"
        }, {
            posterType: "png"
        });
    }


    //Youtube Background Video
    $(function(){
      $(".player").mb_YTPlayer();
    });


});
