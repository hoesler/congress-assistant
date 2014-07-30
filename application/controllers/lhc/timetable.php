
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timetable extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
		$room = $this->input->get('room');
		
		if ($room == '')
			return;
		
		$this->db->where('room', $room);
		$query = $this->db->get('timetable');
		
		echo json_encode($query->result());
	}	
}

?>
