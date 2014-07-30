<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	if ( ! defined('STDIN')) exit('Access allowed only for cron tasks');

class Sendmail extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();		
		$this->load->model('Participant_model', 'participant');
		$this->load->library('email');
		$this->load->helper('personalized_email');
	}
	
	
	function index() {
		$this->db->select('mail_participant_map.*, mail.body, mail.subject');
		$this->db->from('mail_participant_map');
		$this->db->join('mail', 'mail.id = mail_participant_map.mailId');		
		$this->db->where('status', 'QUEUED');
		$this->db->limit(100);
		$query = $this->db->get();
		
		$this->db->where('status', 'QUEUED');
		$this->db->limit(100);
		$this->db->update('mail_participant_map', array('status' => 'IN_PROCESS'));

		foreach ($query->result_array() as $row)
		{  
		   if ($this->participant->from_id($row['participantId']) !== FALSE) {
		   		$this->_prepareEmailToParticipant($this->participant, $row);
		   		// send mail
		   		if ($this->email->send()) {
					$this->db->where('id', $row['id']);
					$this->db->update('mail_participant_map', array('status' => 'SENT'));
				}
				else {
					$this->db->where('id', $row['id']);
					$this->db->update('mail_participant_map', array('status' => 'ERROR'));
					log_message('error', 'Failed to send mail: '.$this->email->print_debugger());
				}
		   }
		   else {
				$this->db->where('id', $row['id']);
				$this->db->update('mail_participant_map', array('status' => 'ERROR'));
		   		log_message('error', "Participant_model could not be initialized for id " . $row['participantId']);
		   }
		}
	}
	
	private function _prepareEmailToParticipant(Participant_model $participant, $emaildata) {
		
		$this->load->library('email');

		$this->config->load('email', TRUE);

		$this->email->to($participant->email);
		$this->email->from($this->config->item('from_address', 'email'), $this->config->item('from_name', 'email'));
		$this->email->reply_to($this->config->item('replyto_address', 'email'), $this->config->item('from_name', 'email'));
		$this->email->subject($emaildata['subject']);
		
		$message = $emaildata['body'];
		$message = searchReplaceMessageText($message, $participant);
			
		$this->email->message($message);
		
		return $this->email;
	}
}

?>
