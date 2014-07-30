<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Participant_list_model extends CI_Model {
		
	var $levels = array();
	var $includeCancelled = FALSE;
	var $contributions = array();
	var $lastName = NULL;
	var $silverback_dinner = NULL;
	var $poster_visitor = NULL;
	var $exclude_uuid = array();
	var $ids = array();
	
	var $models = array();
	
	function __construct()
	{
	    parent::__construct();
	    $this->load->model('Participant_model');
	}
	
	private function str2bool($var) {
	    switch (strtolower($var)) {
	        case ("true"): return TRUE;
	        case ("false"): return FALSE;
	        default: NULL;
	    }
	}
	
	function id($var) {
		if (is_array($var)) {
			$this->ids = array_merge($this->ids, $var);
		}
		else if (is_int($var)) {
			array_push($this->ids, $var);
		}
		else {
			log_message('error', 'argument mut be an array of integers or a single integer');
		}
	}
	
	function level($var) {
		if (is_array($var)) {
			$this->levels = array_merge($this->levels, $var);
		}
		else if(is_string($var) && $var != '')
			array_push($this->levels, $var);
	}
	
	function contribution($var) {
		if (is_array($var)) {
			$this->contributions = array_merge($this->contributions, $var);
		}
		if(is_string($var) && $var != '')
			array_push($this->contributions, $var);
	}
	
	function exclude_uuid($var) {
		if(is_string($var) && $var != '')
			array_push($this->exclude_uuid, $var);
	}
	
	function includeCancelled($var) {
		if(!is_bool($var)) {
			if(is_string($var) && !is_null($this->str2bool($var))) {
				$var = $this->str2bool($var);
			}
			else {
				return;
			}
		}
		
		$this->includeCancelled = $var;
	}
	
	function lastName($var) {
		if(is_string($var) && $var != '')
			$this->lastName = $var;
	}
	
	function silverback_dinner($var) {
		if(!is_bool($var)) {
			if(is_string($var) && !is_null($this->str2bool($var))) {
				$var = $this->str2bool($var);
			}
			else {
				return;
			}
		}
		$this->silverback_dinner = $var;
	}
	
	function posterVisitor($var) {
		if(!is_bool($var)) {
			if(is_string($var) && !is_null($this->str2bool($var))) {
				$var = $this->str2bool($var);
			}
			else {
				return;
			}
		}
		$this->poster_visitor = $var;
	}
	
	function fetch() {
		if ( count($this->ids) > 0 ) {
			$this->db->where_in('participants.id', $this->ids);
		}
		else {			
			if (!empty($this->levels)) {
				$this->db->where_in('participants.level', $this->levels);
			}
			
			if (!$this->includeCancelled) {
				$this->db->where('participants.hasCancelled', '0');		
			}
			
			if (!empty($this->contributions)) {
				$this->db->where('EXISTS (SELECT * FROM '.$this->db->dbprefix('contributions').' WHERE '.$this->db->dbprefix('contributions').'.participantId = '.$this->db->dbprefix('participants').'.id AND '.$this->db->dbprefix('contributions').'.type IN('.implode(",", array_map(create_function('$el', 'return "\'".mysql_real_escape_string($el)."\'";'), $this->contributions)).'))', NULL, FALSE);				
			}
			
			if (!is_null($this->lastName)) {
				$this->db->like('participants.lastName', $this->lastName, 'after');
			}
			
			if (!is_null($this->silverback_dinner)) {
				$this->db->where('EXISTS (SELECT * FROM '.$this->db->dbprefix('silverback_student_map').' WHERE '.$this->db->dbprefix('silverback_student_map').'.silverbackId = '.$this->db->dbprefix('participants').'.id OR '.$this->db->dbprefix('silverback_student_map').'.studentId = '.$this->db->dbprefix('participants').'.id)', NULL, FALSE);
			}
			
			if (!empty($this->exclude_uuid)) {
				$this->db->where_not_in('participants.uuid', $this->exclude_uuid);
			}
			
			if (!is_null($this->poster_visitor)) {
				$this->db->where('EXISTS (SELECT * FROM '.$this->db->dbprefix('poster_visitor_map').' WHERE '.$this->db->dbprefix('poster_visitor_map').'.visitorId = '.$this->db->dbprefix('participants').'.id)', NULL, FALSE);
			}
		}
		
		$this->db->select('participants.id');
		$query = $this->db->get('participants');
		
		//echo $this->db->last_query();
		//echo $query->num_rows();
		
		foreach ($query->result() as $row)
		{
			$model = new Participant_model();
			$model->from_id($row->id);
			array_push($this->models, $model);
		}
	}
}

?>