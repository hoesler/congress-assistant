<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Silverback extends CI_Controller {

	private $answers_table = 'silverback_answers';

	function __construct()
    {
        parent::__construct();
        $this->load->model('Participant_model', 'participant');
    }
    

	public function confirm()
	{
		$uuid = $this->uri->segment(3);
				 		
		if ($this->participant->from_uuid($uuid) === FALSE) {
			show_404('');
		}
		else {
			if ($this->participant->silverback == NULL) {
				show_404('');
			}
			else if ($this->participant->silverback == 'ANSWERED') {
				redirect('silverback/error/chosen/'.$this->participant->uuid);
			}
			else if ($this->participant->silverback == 'NO_ANSWER') {
				redirect('silverback/error/deadline/'.$this->participant->uuid);
			}
			else {
				$this->load->helper('form');
				$this->load->view('header');
				$this->load->view('silverback/confirm', $this->participant);
				$this->load->view('footer');
			}
		}
	}
	
	public function answer() {
		$uuid = $this->uri->segment(3);
				 		
		if ($this->participant->from_uuid($uuid) === FALSE) {
			show_404('');
		}
		else {
			
			$this->db->where('participantId', $this->participant->id);
			$query = $this->db->get($this->answers_table);
			
			if ($query->num_rows() == 0) {
				$answer = $query->row();
				
				$participate = $this->input->post('participate');						
				$data = array(
				   'day' => $participate ? $this->input->post('day') : 'NOT_IN',
				   'timeOfAnswer' => date('Y-m-d H:i:s'),
				   'participantId' => $this->participant->id
				);				
				$this->db->insert($this->answers_table, $data);
				
				$this->db->where('id', $this->participant->id);
				$this->db->update('participants', array('silverback' => 'ANSWERED')); 
								
				redirect('silverback/thankyou/'.$this->participant->uuid);
			}
			else {
				redirect('silverback/error/chosen/'.$this->participant->uuid);
			}
		}
	}
	
	public function meet() {
		$uuid = $this->uri->segment(3);
						 		
		if ($this->participant->from_uuid($uuid) === FALSE) {
			show_404('');
		}
		else {		
			// TODO: Here could be a check if the uuid belongs to a phd student		
			
			// check if student did not already made a choice		
			$this->db->where('studentId', $this->participant->id);
			$query = $this->db->get('silverback_student_map');
			if ($query->num_rows() != 0) {
				redirect('silverback/error/chosen/'.$this->participant->uuid);
				return;
			}
			
			$hasInsertData = $this->input->post('submit');		
			if ($hasInsertData) {
				$this->_meet_insert($this->input->post('silverbackId'));
			}
			else {
				$this->_meet_list();
			}
		}
	}
	
	private function _meet_list() {
		$this->db->select('*, (SELECT COUNT(*) FROM '.$this->db->dbprefix('silverback_student_map').' WHERE '.$this->db->dbprefix('silverback_student_map').'.silverbackId = '.$this->db->dbprefix('silverback_answers').'.participantId) AS nStudents', FALSE); 
		$this->db->from('silverback_answers');
		$this->db->join('participants', 'participants.id = silverback_answers.participantId');
		$this->db->where('day !=', 'NOT_IN');
		$this->db->order_by('lastName, firstName');
		$query = $this->db->get();
		
		$data = array();
		foreach ($query->result() as $row)
		{
		    array_push($data, $row);
		}
		
		$this->load->view('header');
		$this->load->view('silverback/select', array('silverbacks' => $data, 'student' => $this->participant));
		$this->load->view('footer');
	}
	
	private function _meet_insert($silverbackId) {
		
		if ($this->input->post('activationId') != md5('activationId')) {
			show_error('activationId missing');
		}
		
		$student = $this->participant;
		
		$silverback = new Participant_model;
		$silverback->from_id($silverbackId);
		
		if ($silverback === FALSE) {
			show_error('silverbackId unknown');
		}
		
		$this->db->query('LOCK TABLES '.$this->db->dbprefix('silverback_student_map').' WRITE, '.$this->db->dbprefix('silverback_answers').' READ;');
		
		
		// check if student did not already made a choice		
		/*
		$this->db->where('studentId', $student->id);
		$query = $this->db->get('silverback_student_map');
		if ($query->num_rows() != 0) {
			redirect('silverback/error/chosen/'.$this->participant->uuid);
			return;
		}
		*/
		
		// check if pairing is allowed
		$this->db->where('silverbackId', $silverback->id);
		$query = $this->db->get('silverback_student_map');
		$nPairingsCurrnet = $query->num_rows();
		
		$this->db->where('participantId', $silverback->id);
		$query = $this->db->get('silverback_answers');
		if ($query->num_rows() != 1) {
			show_error('Found no answer from Silverback with id = '.$silverback->id);
			return;
		}
		
		$nPairingsMax = $query->row()->maxStudents;
		
		if ($nPairingsCurrnet >= $nPairingsMax) {
			redirect('silverback/error/full/'.$this->participant->uuid);
			return;			
		}
		
		// insert pairing
		$this->db->insert('silverback_student_map', array('silverbackId' => $silverback->id, 'studentId' => $student->id));
		
		$this->db->query("UNLOCK TABLES;");
		
		redirect('silverback/thankyou/'.$this->participant->uuid);
	}
	
	function thankyou() {
		$uuid = $this->uri->segment(3);
						 		
		if ($this->participant->from_uuid($uuid) === FALSE) {
			show_404('');
		}
		else {
			$this->load->view('header');
			$this->load->view('silverback/thankyou', $this->participant);
			$this->load->view('footer');
		}
	}
	
	function error() {		
		$errortype = $this->uri->segment(3);						 		
		$uuid = $this->uri->segment(4);
		
		if ($this->participant->from_uuid($uuid) === FALSE) {
			show_404('');
		}
		else {
			
			switch($errortype) {
				case "deadline":
				case "full":
				case "chosen":
					$this->load->view('header');
					$this->load->view('silverback/errormessage_'.$errortype, $this->participant);
					$this->load->view('footer');
					break;
				default:
					show_404('');
			}
		}
	}
}

?>
