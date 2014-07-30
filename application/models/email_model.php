<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	var $id = -1;
	var $subject = NULL;
	var $body = NULL;
	
	var $receiverId = -1;
    var $status = NULL;
     
    
    public function fetchForProcessing($mail_participantmap_id) {
    	
    	$this->db->query("LOCK TABLES mail_participant_map WRITE;");
    	 
		$this->db->from('mail_participant_map');
		$this->db->join('participants', 'participants.id = mail_participant_map.participantId');
		$this->db->where('id', $mail_participantmap_id);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			
			$this->id = $result->id;
			$this->subject = $result->subject;
			$this->body = $result->body;
			$this->receiverId = $result->participantId;
			$this->status = $result->status;
		}
		
		if ($this->status != 'QUEUED')
			return FALSE;
		
		$this->db->where('id', $mail_participantmap_id);
		$this->db->update('mail_participant_map', array('status' => 'IN_PROCESS'));
		
		$this->db->query("UNLOCK TABLES;");
		
		return $this;
    }
}

?>