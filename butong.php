<?php
    
    $_url = htmlspecialchars_decode($_POST['url']);
    
    function curl_download($Url){
     
        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }
     
        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();
     
        // Now set some options (most are optional)
     
        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);
     
        // Set a referer
        curl_setopt($ch, CURLOPT_REFERER, "https://developers.google.com/youtube/js_api_reference");
     
        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36");
     
        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);
     
        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
     
        // Download the given URL, and return output
        $output = curl_exec($ch);
     
        // Close the cURL resource, and free system resources
        curl_close($ch);
     
        return $output;
    }

    $str = "";
    $str .= curl_download($_url);

    echo substr_count($str, '"allow_html5_ads": "1"');
?>