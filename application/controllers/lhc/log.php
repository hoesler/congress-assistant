
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
		$this->db->insert('lhc_log',
			array('message' => $this->input->post('message'))
		);
	}	
}

?>
