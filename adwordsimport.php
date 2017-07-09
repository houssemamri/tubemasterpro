<?php

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function file_get_contents_utf8($fn) { 
            $content = file_get_contents($fn); 
            return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
    } 


    function csvtotxt($path,$url_path){


        //$url = 'http://203.201.129.15/AWSERVICE_WP_WEB/awws/AWService_WP.awws?wsdl';
        //$url = 'http://203.201.129.15/TTP_WEB/awws/TTP.awws?wsdl';
        $url = 'http://203.201.129.15/TUBEMASTERPRO_WEB/awws/TTP.awws?wsdl';
        $client = new SoapClient($url);
        $field['data'] = array();

        $create_name = generateRandomString() . ".txt";
        //get csv file content
        $data = file_get_contents_utf8("$path");
        //$handle = fopen("$path", "r");
        //$fileip = fread($handle, filesize($path));
        //fclose($handle);
        

        //write to text
        ///[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u
        //$write_file = $data;preg_replace('/[^\p{L}\s]/u','',$data);
        //$write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u','',$data);
        $write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u','',$data);
        $text_path = "assets/csvtotxt/$create_name";
        $myfile = fopen("$text_path", "w") or die("Unable to open file!");
        fwrite($myfile, $write_file);
        fclose($myfile);   
        
        echo "File to send: $url_path/$text_path <br> <hr noshade>";
        echo "data:";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        echo "write_file:";
        echo "<pre>";
        print_r($write_file);
        echo "</pre>";  
        
        
        $field['data']['argText'] = "$url_path/$text_path";
        $response   =  (array) $client->__soapCall("pcActions_Parse_AdWordsImport", array($field['data']));
        $result     = $response['pcActions_Parse_AdWordsImportResult'];
        
        echo "response:";
        echo "<pre>";
        print_r($response);
        echo "</pre>";
        //delete  data
        //return $response;
        $exp_one    = explode("<sJSON>",$result);
        $exp_two    = explode("</sJSON>",$exp_one[1]);
        
        //delete text file
        unlink($text_path);

        echo "cleaned:";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        
        //delete  data
        return $exp_two[0];

    }
    
    function csvtotxtold($path,$url_path){
 
        //$url = 'http://203.201.129.15/AWSERVICE_WP_WEB/awws/AWService_WP.awws?wsdl';
        //$url ='http://203.201.129.15/TTP_WEB/awws/TTP.awws?wsdl';
        $url = 'http://203.201.129.15/TUBEMASTERPRO_WEB/awws/TTP.awws?wsdl';
        $client = new SoapClient($url);
        $field['data'] = array();

        $create_name = generateRandomString() . ".txt";
        //get csv file content
        $data = file_get_contents_utf8("$path");

        $write_file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u','',$data);
        $text_path = "assets/csvtotxt/$create_name";
        $myfile = fopen("$text_path", "w") or die("Unable to open file!");
        fwrite($myfile, $write_file);
        fclose($myfile);     

        $field['data']['argText'] = "$url_path/$text_path";
        $response   =  (array) $client->__soapCall("pcActions_Parse_AdWordsImport", array($field['data']));
        $result     = $response['pcActions_Parse_AdWordsImportResult'];
        $exp_one    = explode("<sJSON>",$result);
        $exp_two    = explode("</sJSON>",$exp_one[1]);
        
        
        //delete text file
        unlink($text_path);
        
        //return  data
        return $exp_two[0];
        //return $response;

    }

     $csv_path = "assets/testfile2.csv";
     $text_url_path = "http://www.tubemasterpro.com";
     //$text_url_path ="203.201.129.3";
     //$text_url_path = "http://10.62.0.110";
     $get_response = csvtotxt($csv_path,$text_url_path);
     
     $json = json_encode(array(
	                   'error' => 0,
	                  'error_msg'  => "success",
	                  'parse_data' => $get_response
	                    ));
	 echo $json;
    
    echo "<hr noshade>";
    echo "Result here: <br>";
     echo "<pre>";
     print_r($get_response);
     echo "</pre>";
?>