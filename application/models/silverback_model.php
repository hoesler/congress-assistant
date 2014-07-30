<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Silverback_model extends CI_Model {

	var $id   = '';
	var $day = '';
	var $restaurant = '';
	var $students = array();
	var $silverback = NULL;

	private function fromResult($result) {
		$this->id = $result[0]->id;
	   	$this->day = $result[0]->day;
	   	$this->restaurant = $result[0]->restaurant;
	   	$this->silverback = new Participant_model($result[0]->participantId);	

	   	foreach ($result as $row)
	   	{
			$model = new Participant_model($row->studentId);
	   		array_push($this->students, $model);
	   	}
	}

	function fromSilverbackId($id)
	{
	    $this->db->select('silverback_answers.*, silverback_student_map.studentId');
	    $this->db->where('participantId', $id);
	    $this->db->join('silverback_student_map', 'silverback_student_map.silverbackId = silverback_answers.participantId');
	    $query = $this->db->get('silverback_answers');
	    
	    if ($query->num_rows() > 0) {
	    	$this->fromResult($query->result());
	    }
	    else {
	    	log_message('error', "Found  0 entries for id ".$id);
	    	return FALSE;
	    }
	}

	function fromStudentId($id)
	{
	    $this->db->select('silverbackId');
	    $this->db->where('studentId', $id);
	    $query = $this->db->get('silverback_student_map');
	    
	    if ($query->num_rows() == 1) {
	    	$this->fromSilverbackId($query->row()->silverbackId);
	    }
	    else {
	    	log_message('error', "Found  0 entries for id ".$id);
	    	return FALSE;
	    }
	}

}

?>
