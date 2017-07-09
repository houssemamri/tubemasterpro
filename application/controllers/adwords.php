<?php
error_reporting(E_STRICT | E_ALL);
ini_set('display_errors', '1');

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adwords extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        //load our new Adwords library
        $this->load->library('my_adwords');

    }

    function index()
    {

        try {
            // Get the client ID and secret from the auth.ini file. If you do not have a
            // client ID or secret, please create one of type "installed application" in
            // the Google API console: https://code.google.com/apis/console#access
            // and set it in the auth.ini file.
            $user = new AdWordsUser();
            $user->LogAll();

            // Get the OAuth2 credential.
            $oauth2Info = GetOAuth2Credential($user);

            // Enter the refresh token into your auth.ini file.
            printf("Your refresh token is: %s\n\n", $oauth2Info['refresh_token']);
            printf("In your auth.ini file, edit the refresh_token line to be:\n"
                . "refresh_token = \"%s\"\n", $oauth2Info['refresh_token']);
        } catch (OAuth2Exception $e) {
            ExampleUtils::CheckForOAuth2Errors($e);
        } catch (ValidationException $e) {
            ExampleUtils::CheckForOAuth2Errors($e);
        } catch (Exception $e) {
            printf("An error has occurred: %s\n", $e->getMessage());
        }

    }
}