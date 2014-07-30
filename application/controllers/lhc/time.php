
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
		//$now = mktime(date("H") ,date("i") ,date("s"), 8, 22, 2011);
		$now = time();
		echo json_encode(array('time' => $now * 1000)); // javascript'a Date getTime operates on milliseconds	
	}	
}

?>
