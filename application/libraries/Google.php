<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Google Library
 * Handle Google API
 */

require APPPATH . 'libraries/Google/Client.php';

class Google extends Google_Client {

	public function __construct($params = array())
    {
    	parent::__construct($config=array());
        // $this->_ci =& get_instance();
    	// $this->_ci = new Google_Client();
    }
}


/* End of file Google.php */
/* Location: ./application/libraries/Google.php */