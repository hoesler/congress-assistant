<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Poster extends CI_Controller {

	var $activity_end_date = "30 July 2015 4pm";

	function __construct()
	{
	    parent::__construct();
	    $this->load->model('Participant_model', 'participant');
	        
	    $uuid = $this->uri->segment(3);
	    		 		
	    if ($this->participant->from_uuid($uuid) === FALSE) {
	    	// TODO: show a better error page
	    	show_error('No a valid UUID');
	    }
	    else {
	    	if ($this->participant->level != 'STUDENT'
	    		&& $this->participant->level != 'COMMITTEE') {
	    		// TODO: show a better error page
	    		show_error("You're not a student");
	    	}
	    	
	    	if (date(time()) > strtotime($this->activity_end_date)) {
	    		redirect('participant/'.$this->participant->uuid);
	    		return;
	    	}
	    	
	    	$contribution = $this->participant->getContribution();
	    	if ($this->participant->level != 'COMMITTEE' &&
	    		($contribution == FALSE ||
	    		! ( $contribution->type == 'REGULAR_POSTER' ||
	    			$contribution->type == 'ESSENCE_POSTER'))) {
	    		// TODO: show a better error page
	    		show_error("You're not presenting a poster");
	    	}
	    }
	}
	
	function index() {
		$this->load->view('header');
		$this->load->view('poster', array('participant' => $this->participant, 'activity_end_date' => $this->activity_end_date));
		$this->load->view('footer');
	}
	
	function add() {
		$studentId = $this->participant->id;
		$selectedIDs = $this->input->post('participants');
		
		if (!is_array($selectedIDs)) {
			return;
		}
		
		foreach ($selectedIDs as $participantId) {
			$this->db->insert(
				'poster_visitor_map',
				array(	'presenterId'	=> $studentId,
						'visitorId'		=> $participantId
				)
			);
		}
	}
	
	function get() {
		$this->db->select('participants.*');
		$this->db->join('participants', 'participants.id = poster_visitor_map.visitorId');
		$this->db->where('presenterId', $this->participant->id);
		$query = $this->db->get('poster_visitor_map');
		
		echo json_encode($query->result());
	}
	
	function save() {
		
		$this->db->where('presenterId', $this->participant->id);
		$this->db->delete('poster_visitor_map');
		
		$visitorIds = $this->input->post('visitors');
		
		for ($i = 0; $i < count($visitorIds), $i < 5; ++$i) {
			$this->db->insert(
				'poster_visitor_map',
				array(	'presenterId'	=> $this->participant->id,
						'visitorId'		=> $visitorIds[$i]
				)
			);
		}		
	}
}

?>