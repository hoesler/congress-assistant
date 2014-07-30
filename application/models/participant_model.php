<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Participant_model extends CI_Model {

    var $id   = '';
    var $title = '';
    var $firstName = '';
    var $lastName = '';
    var $email = '';
    var $uuid = '';
    var $organization = '';
    var $department = '';
    var $level = '';
    var $type = '';
    var $silverback = '';
    var $hasCancelled = '';
    
    var $hasContribution = NULL;
    var $contribution = NULL;
    
    private $table_name = 'participants';
    
    function __construct(/* int */ $id = 0)
    {
        parent::__construct();
        $this->load->model('Silverback_model');
	if ($id > 0) {
		$this->from_id($id);
	}
    }
    
    function from_uuid($uuid)
    {
        $this->db->where('uuid', $uuid);
        $query = $this->db->get($this->table_name);
        
        if ($query->num_rows() == 1) {
        	$this->fromRow($query->row());
        	return $this;
        }
        else 
        	return FALSE;
    }

	function from_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);
        
        if ($query->num_rows() == 1) {
        	$this->fromRow($query->row());
        	return $this;
        }
        else {
        	log_message('error', "Found  ".$query->num_rows()." entries for id ".$id);
        	return FALSE;
        }
    }
    
    function from_email($email)
    {
        if (! preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email))
        	return FALSE;
        
        $this->db->where('email', $email);
        $query = $this->db->get($this->table_name);
        
        if ($query->num_rows() == 1) {
        	$this->fromRow($query->row());
        	return $this;
        }
        else {
        	log_message('error', "$email not unique in table ".$this->table_name);
        	return FALSE;
        }
    }

	function random() {
		$this->db->order_by('id', 'random');
		$this->db->limit(1);
		$query = $this->db->get($this->table_name);
        
        if ($query->num_rows() == 1) {
        	$this->fromRow($query->row());
        	return $this;     	
        }
        else 
        	return FALSE;
	}
	
	private function fromRow($row) {
		$this->id = $row->id;
       	$this->firstName = $row->firstName;
       	$this->lastName = $row->lastName;
       	$this->email = $row->email;
       	$this->uuid = $row->uuid;
       	$this->isSilverback = $row->isSilverback;
       	$this->organization = $row->organization;
       	$this->department = $row->department;
       	$this->level = $row->level;
       	$this->type = $row->type;
       	$this->title = $row->title;
       	$this->silverback = $row->silverback;
       	$this->hasCancelled = $row->hasCancelled;
	}
	
	function getSilverBackAnswer() {
		if ($this->isSilverback) {
			$this->db->where('participantId', $this->id);
	        $query = $this->db->get('silverback_answers');
	        
	        if ($query->num_rows() == 1) {
	        	return $query->row();
	        }
	        else 
	        	return FALSE;
		}
	
	}
	
	function isSilverback() {
		return $this->silverback == "ANSWERED";
	}

	function isSilverbackParticipant() {
		$this->db->where('studentid', $this->id);
		$this->db->or_where('silverbackId', $this->id);
		$query = $this->db->get('silverback_student_map');
		return $query->num_rows() > 0;
	}	
	
	function getSilverback() {
		$ret = new Silverback_model();
		if ($this->isSilverback()) {
			$ret->fromSilverbackId($this->id);
		}
		else if ($this->level == 'STUDENT')
			$ret->fromStudentId($this->id);
		return $ret;
	}
	
	function getSelectedSilverbackActivity() {
		if ($this->level == 'STUDENT') {
			$this->db->where('studentId', $this->id);
			$this->db->join('participants', 'silverback_student_map.silverbackId = participants.id');
			$this->db->join('silverback_answers', 'silverback_answers.participantId = participants.id');
	        $query = $this->db->get('silverback_student_map');
	        
	        if ($query->num_rows() == 1) {
	        	return $query->row();
	        }
	        else 
	        	return FALSE;
		}
		else
			return FALSE;
	}
	
	function isPosterVisitor() {
		$this->db->where('visitorId', $this->id);
		$query = $this->db->get('poster_visitor_map');
		
		return $query->num_rows() > 0;
	}
	
	function getPosterStudents() {
		$this->db->where('visitorId', $this->id);
		$this->db->where('accepted', 1);
		$query = $this->db->get('poster_visitor_map');
		
		return array_map(create_function('$row', 'return $row->presenterId;'), $query->result());
	}
	
	function getPosterVisitors() {
		$this->load->model('Participant_list_model');
		$this->db->where('presenterId', $this->id);
		$query = $this->db->get('poster_visitor_map');
	
		if ($query->num_rows() > 0) {
			$ret = new Participant_list_model();
			$ret->id(array_map(create_function('$row', 'return $row->visitorId;'), $query->result()));		
			$ret->fetch();
			return $ret->models;	
		}
		else
			return array();
	}
	
	function getContribution() {
		if ($this->hasContribution !== NULL) {
			return $this->contribution;
		}
		else {
			$this->db->where('participantId', $this->id);
			$query = $this->db->get('contributions');
			
			if ($query->num_rows() == 1) {
				$this->hasContribution = TRUE;
				$this->contribution = $query->row();
			}
			else 
				$this->hasContribution = FALSE;
				
			return $this->getContribution();
		}
	}
	
	function name() {
		return $this->firstName . " " . $this->lastName;
	}
}

?>
