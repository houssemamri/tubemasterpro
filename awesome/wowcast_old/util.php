<?php
header("Access-Control-Allow-Origin: *");
if ( isset($_POST['msg']) && !empty($_POST['msg']) ) {
	$msg = $_POST['msg'];
	echo convert_videos($msg);
	exit();
}

################## CONVERT VIDEO ##############
	
	function convert_videos($string) {
		
		/*
		if (preg_match('/(\.jpg|\.png|\.bmp)$/', $string)) {
		    $string = preg_replace('/(https?:\/\/\S+\.(?:jpg|png|gif))\s+/', '<a class="image-popup-no-margins" href="$1"><img src="$1" width="107" height="75" /></a>', $string." ");
		// check if there is youtube.com in string
		} elseif (strpos($string, "youtu.be") !== false) {
			$string = preg_replace('#http://(www\.)?youtu\.be/([^ &\n]+)(&.*?(\n|\s))?#i', '<a href="#" onclick="popup_video(\'http://www.youtube.com/watch?v=$2\',\'youtube\'); return false">http://youtu.be/$2</a>', $string." ");
		} elseif (strpos($string, "youtube.com") !== false) {
		    $string = preg_replace('#http://(www\.)?youtube\.com/watch\?v=([^ &\n]+)(&.*?(\n|\s))?#i', '<a href="#" onclick="popup_video(\'http://www.youtube.com/watch?v=$2\',\'youtube\'); return false">http://www.youtube.com/watch?v=$2</a>', $string." ");
		    $string = preg_replace('#https://(www\.)?youtube\.com/watch\?v=([^ &\n]+)(&.*?(\n|\s))?#i', '<a href="#" onclick="popup_video(\'https://www.youtube.com/watch?v=$2\',\'youtube\'); return false">https://www.youtube.com/watch?v=$2</a>', $string." ");		
		// check if there is vimeo.com in string
		} elseif (strpos($string, "vimeo.com") !== false) {
		    $string = preg_replace('#http://(www\.)?vimeo\.com/([^ ?\n/]+)((\?|/).*?(\n|\s))?#i', '<a href="#" onclick="popup_video(\'http://vimeo.com/$2\',\'vimeo\'); return false">http://vimeo.com/$2</a>', $string." ");
		} else {
		    $string = preg_replace('#((http://|www.)((?!youtube)(?!vimeo)[^.]+\.(com|org|co.il|net|us|ws|info|tv|me|tk|co.uk).*))#', '<a href="$1" target="_blank">$1</a>', $string." ");		
		}
		*/
		if (preg_match('/(\.jpg|\.png|\.bmp)$/', $string)) 
		{
		    $string = preg_replace('/(https?:\/\/\S+\.(?:jpg|png|gif))\s+/', '<a href="#" onclick="popup_video(\'$1\',\'image\');return false;"><img src="$1" width="100%" height="100%" /></a>', $string." ");
		// check if there is youtube.com in string
		} 
		elseif (strpos($string, "youtu.be") !== false) 
		{
			$string = preg_replace('#http://(www\.)?youtu\.be/([^ &\n]+)(&.*?(\n|\s))?#i', '<object width="100%" height="100%"><param name="movie" value="http://www.youtube.com/v/$2"></param><embed src="http://www.youtube.com/v/$2" type="application/x-shockwave-flash" width="100%" height="100%"></embed></object>', $string." ");
		} 
		elseif (strpos($string, "youtube.com") !== false) 
		{
		    $string = preg_replace('#http://(www\.)?youtube\.com/watch\?v=([^ &\n]+)(&.*?(\n|\s))?#i', '<object width="100%" height="100%"><param name="movie" value="http://www.youtube.com/v/$2"></param><embed src="http://www.youtube.com/v/$2" type="application/x-shockwave-flash" width="100%" height="100%"></embed></object>', $string." ");
		    $string = preg_replace('#https://(www\.)?youtube\.com/watch\?v=([^ &\n]+)(&.*?(\n|\s))?#i', '<object width="100%" height="100%"><param name="movie" value="https://www.youtube.com/v/$2"></param><embed src="http://www.youtube.com/v/$2" type="application/x-shockwave-flash" width="100%" height="100%"></embed></object>', $string." ");		
		// check if there is vimeo.com in string
		} 
		elseif (strpos($string, "vimeo.com") !== false) 
		{
		    $string = preg_replace('#http://(www\.)?vimeo\.com/([^ ?\n/]+)((\?|/).*?(\n|\s))?#i', '<object width="100%" height="100%"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=$2&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=$2&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="100%"></embed></object>', $string." ");
		} else 
		{
			$string = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~","<a href=\"\\0\" target='_blank'>\\0</a>", $string);
		   // $string = preg_replace('#((http://|www.)((?!youtube)(?!vimeo)[^.]+\.(com|org|co.il|net|us|ws|info|tv|me|tk|co.uk).*))#', '<a href="$1" target="_blank">$1</a>', $string." ");		
		}		
		return $string;
	}
	
?>