<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * YouTube Library
 * Handle YouTube API
 */

require APPPATH . 'libraries/Google/Service/YouTube.php';

class Youtube extends Google_Service_YouTube {

	function __construct( Google_Client $client )
    {
        parent::__construct( $client );
        // $this->_ci =& get_instance();
    }
}

/* End of file YouTube.php */
/* Location: ./application/libraries/YouTube.php */