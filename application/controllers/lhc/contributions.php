
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contributions extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
		$room = $this->input->get('room');
		$day = $this->input->get('day');
		
		if ($room == '' || $day == '') {
			echo "[]";
			return;
		}
		
		$this->db->select('participants.firstName, participants.lastName, contributions.*');
		$this->db->join('participants', 'participants.id = contributions.participantId');
		$this->db->where('hasCancelled', '0');
		$this->db->where('room', $room);
		$this->db->where('date(startTime)', $day);
		$this->db->where('contributions.type IS NOT NULL', NULL, FALSE);
		$query = $this->db->get('contributions');
		
		echo json_encode($query->result());
	}	
}

?>
