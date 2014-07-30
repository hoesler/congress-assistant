
<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slides extends CI_Controller {

	function __construct() {
		parent::__construct();          
	}

	function index() {
	
		if ($this->input->get('startTime') && $this->input->get('endTime')) {
			$startTime = $this->input->get('startTime'); // timestamp is s
			$endTime = $this->input->get('endTime'); // timestamp is s
			
			$this->db->where('startTime <=', date('H:i', $endTime));		
			$this->db->where('endTime >=', date('H:i', $startTime));
			$this->db->like('days', date('D', $startTime));
		};
		//echo date('r', $startTime);
		$query = $this->db->get('lhc_slides');
		//echo $this->db->last_query();
		echo json_encode($query->result());
	}	
}

?>
