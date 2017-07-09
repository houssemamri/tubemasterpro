<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
define('SRC_PATH', APPPATH.'third_party/AdWords/');
define('LIB_PATH', 'Google/Api/Ads/AdWords/Lib');
define('COMMON_UTIL_PATH', 'Google/Api/Ads/Common/Util');
define('COMMON_LIB_PATH', 'Google/Api/Ads/Common/Lib');
define('UTIL_PATH', 'Google/Api/Ads/Common/Util');
define('AW_UTIL_PATH', 'Google/Api/Ads/AdWords/Util');

// Configure include path
ini_set('include_path', implode(array(
        ini_get('include_path'), PATH_SEPARATOR, SRC_PATH))
);

// Include the AdWordsUser file

require_once SRC_PATH.LIB_PATH. '/AdWordsUser.php';
require_once SRC_PATH.COMMON_LIB_PATH.'/ValidationException.php';
require_once SRC_PATH.COMMON_UTIL_PATH.'/OAuth2Handler.php';



class My_adwords extends AdWordsUser {

    public $auth_url;

    public function __construct() {
        parent::__construct();

    }


    function GetOAuth2Credential() {
        $ci = & get_instance();

        $user=$this;
        $redirectUri = 'https://www.tubemasterpro.com/dashboard/campaigns_list';
        $offline = false;

        $OAuth2Handler = $user->GetOAuth2Handler();
        try{
            if($_GET["code"]){

                $token = $OAuth2Handler->GetAccessToken(
                    $user->GetOAuth2Info(), $_GET["code"], $redirectUri);
                $ci->session->set_userdata(array("aw_token" => $token));


                header('Location: '.$redirectUri);
            }
        } catch(Exception $e){
            header('Location: '.$redirectUri);
        }


        if(!$ci->session->userdata("aw_token")){
            $authorizationUrl = $OAuth2Handler->GetAuthorizationUrl(
                $user->GetOAuth2Info(), $redirectUri, $offline);
            $this->auth_url = $authorizationUrl;
            return false;
        }

        $user->SetOAuth2Info($ci->session->userdata("aw_token"));
        return true;
    }

    function GetCampaigns() {

        $user=$this;

        // Get the service, which loads the required classes.

        $campaignService = $user->GetService('CampaignService','v201607');

        // Create selector.
        $selector = new Selector();
        $selector->fields = array('Id', 'Name','Status','StartDate');
        $selector->ordering[] = new OrderBy('Name', 'ASCENDING');

        $campaigns = array();
        // Create paging controls.

        $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);
//        do {

            // Make the get request.
            $query = 'SELECT Id, Name, Status ORDER BY Name';
            $pageQuery = sprintf('%s LIMIT %d,%d', $query, 0,
                AdWordsConstants::RECOMMENDED_PAGE_SIZE);

            $page = $campaignService->query($pageQuery);
        print($page->totalNumEntries);
        die();
            //$page = $campaignService->get($selector);

            // Display results.
            if (isset($page->entries)) {
                foreach ($page->entries as $campaign) {
                    $campaigns[] = $campaign;
                }
            }
            // Advance the paging index.
  //          $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
//        } while ($page->totalNumEntries > $selector->paging->startIndex);


        return $campaigns;
    }

}

