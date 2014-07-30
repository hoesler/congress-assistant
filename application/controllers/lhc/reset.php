
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reset extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {	
		print json_encode(array('reset' => 0));
	}	
}

?>
