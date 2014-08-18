<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Participants extends CI_Controller {

	function __construct()
	{
	    parent::__construct();
	    $this->load->model('Participant_list_model');
	}
	
	function lastName_like() {	
		$selector = urldecode($this->uri->segment(3));
		$this->db->like('lastName', $selector);
		$this->runQuery();
	}

	function lastName_startsWith() {
		$selector = urldecode($this->uri->segment(3));
		$this->db->like('lastName', $selector, 'after');
		$this->runQuery();
	}
	
	private function runQuery() {
		if ($this->input->get('exclude_uuid')) {
			$this->db->where('uuid !=', $this->input->get('exclude_uuid'));
		}
		
		$this->db->where('hasCancelled', '0');
		$this->db->where('level != ', 'COMMITTEE');
		$query = $this->db->get('participants');
		
		echo json_encode($query->result());
	}
	
	function index() {
	
		if (!$this->input->get('unlock')) {
			show_404();
		}
	
		$listModel = new Participant_list_model();
		
		if ($this->input->get('level')) {
			foreach (explode(",", $this->input->get('level')) as $part ) {
				$listModel->level($part);
			}
		}
		
		if ($this->input->get('include_cancelled') != '') {
			$listModel->includeCancelled(true);		
		}
		
		if ($this->input->get('contribution')) {
			foreach (explode(",", $this->input->get('contribution')) as $part ) {
				$listModel->contribution($part);
			}				
		}
		
		if ($this->input->get('lastName')) {
			$listModel->lastName($this->input->get('lastName'));
		}
		
		if ($this->input->get('silverback_dinnner')) {
			$listModel->silverback_dinner($this->input->get('silverback_dinnner'));
		}
		
		if ($this->input->get('exclude_uuid')) {
			foreach (explode(",", $this->input->get('exclude_uuid')) as $part ) {
				$listModel->exclude_uuid($part);
			}
		}
		
		$listModel->fetch();
		
		echo json_encode($listModel->models);
	}
}

?>