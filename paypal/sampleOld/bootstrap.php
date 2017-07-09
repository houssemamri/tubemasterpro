<?php
/*
 * Sample bootstrap file.
 */

// Include the composer Autoloader
// The location of your project's vendor autoloader.
$composerAutoload = dirname(dirname(dirname(__DIR__))) . '/autoload.php';
if (!file_exists($composerAutoload)) {
    //If the project is used as its own project, it would use rest-api-sdk-php composer autoloader.
    $composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';


    if (!file_exists($composerAutoload)) {
        echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
        exit(1);
    }
}
require $composerAutoload;
require __DIR__ . '/common.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

error_reporting(E_ALL);
ini_set('display_errors', '1');

//sandbox rene
// Replace these values by entering your own ClientId and Secret by visiting https://developer.paypal.com/webapps/developer/applications/myapps
//$clientId = 'AVqMexDxYqNv6BULfKRGAjDP-hDTKqKycSNKAmmSgSYNITCbBDgq1cCwIGMT';
//$clientSecret = 'EKVWXRAwCqcjphPyFyC1fmiIO7t_4gWNdqRP8MtA-P4DMiWlchlbUZJsB0er';

//sandbox AustraliaWOW Tube Lead Freak
$clientId = 'Ab4QaRBskcgDHnuwmRZLO95E7cP_V9vCDscbrfwBJb5tMiB50_Mc-oniBOfa';
$clientSecret = 'EIWNNhD8FTsfi94RO7Hc8_wkPFUr8XJ9Y7O5DdNJqKjpM2Iw817mGNPpeGqZ';
//live AustraliaWOW Tube Lead Freak
//$clientId = 'AQobLxAIdfsjux9hu3Vwplqs5FaOxm4a_rO9fjedXGhcxxg-uLLkeVHpSips';
//$clientSecret = 'EAywxRCy0pii3O9lV-ryoEzEAG300Gkaf_lcpXxt3sYnwWr2qa5Uw-99JVZ0';

/** @var \Paypal\Rest\ApiContext $apiContext */
$apiContext = getApiContext($clientId, $clientSecret);

return $apiContext;
/**
 * Helper method for getting an APIContext for all calls
 * @param string $clientId Client ID
 * @param string $clientSecret Client Secret
 * @return PayPal\Rest\ApiContext
 */
function getApiContext($clientId, $clientSecret)
{

    // #### SDK configuration
    // Register the sdk_config.ini file in current directory
    // as the configuration source.
    /*
    if(!defined("PP_CONFIG_PATH")) {
        define("PP_CONFIG_PATH", __DIR__);
    }
    */


    // ### Api context
    // Use an ApiContext object to authenticate
    // API calls. The clientId and clientSecret for the
    // OAuthTokenCredential class can be retrieved from
    // developer.paypal.com

    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            $clientId,
            $clientSecret
        )
    );

    // Comment this line out and uncomment the PP_CONFIG_PATH
    // 'define' block if you want to use static file
    // based configuration

    $apiContext->setConfig(
        array(
            'mode' => 'sandbox',
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => 'PayPal.log',
            'log.LogLevel' => 'FINE',
            'validation.level' => 'log',
            'cache.enabled' => true,
            // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
        )
    );

    // Partner Attribution Id
    // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
    // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
    // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

    return $apiContext;
}