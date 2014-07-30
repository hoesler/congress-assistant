
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roomlist extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
		$this->db->select('room');
		$this->db->group_by('room');
		$query = $this->db->get('timetable');
				
		$rooms = array();
		foreach( $query->result() as $row) {
			array_push($rooms, $row->room);
		}
			
		echo json_encode($rooms);
	}	
}

?>
