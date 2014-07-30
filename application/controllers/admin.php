<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	private $email_table = 'mail_participant_map';

	function __construct()
	{
		parent::__construct();

		$this->load->library('tank_auth');
		
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		}
		
		$this->load->model('Participant_model', 'participant');
		$this->load->model('Participant_list_model');
		$this->load->library('email');
		$this->load->helper('personalized_email');		
	}
	
	public function index()
	{
		$data['user_id']	= $this->tank_auth->get_user_id();
		$data['username']	= $this->tank_auth->get_username();
		
		$this->load->view('header');
		$this->load->view('admin/welcome', $data);
		$this->load->view('footer');
	}
	
	public function editmail()
	{
		$this->load->config('email', TRUE);
		$data = array(
			'from' => $this->config->item('from_address', 'email'),
			'subject' => '',
			'body' => '',
			'mailId' => md5(uniqid()),
			'receipient' => FALSE
		);
		$mail_from_session = $this->session->userdata('mail_template');
		if ($mail_from_session !== FALSE)
			$data = array_merge($data, $mail_from_session);
				
		$this->load->helper('form');
		$this->load->view('header');
		$this->load->view('admin/edit_mail_form', $data);
		$this->load->view('footer');
	}

	public function sendmail()
	{
		$mail_from_session = $this->session->userdata('mail_template');
		if ($mail_from_session !== FALSE) {
				
			$receivers = $this->getReceivers($mail_from_session['receipient']);
					
			// insert mail into DB
			$this->db->insert('mail', array(
				'subject' => $mail_from_session['subject'],
				'body' => $mail_from_session['body']));
			$mailId = $this->db->insert_id();
						
			foreach ($receivers as $receiver) {
				
				// add entry to mail_participant_map
				$mailParticipantMap = array(
					'participantId'=> $receiver->id,
					'mailId' => $mailId);
				$this->db->insert('mail_participant_map', $mailParticipantMap);
			}
			
			redirect('admin/sendmail_report');
		}
		else {
			show_error('Found no mail template in cookie. Are Cookies enabled? MailTemplate: ' . $mail_from_session);
		}
	}
	
	public function sendmail_report() {
		$this->load->view('header');
		$this->load->view('admin/sendmail_report');
		$this->load->view('footer');
	}
	
	public function preview_mail()
	{		
		$this->config->load('email', TRUE);
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('subject', 'Subject', 'required');
		$this->form_validation->set_rules('body', 'Message Body', 'required');
		
		$post_data = array(
			'mailId' => $this->input->post('mailId'),
			'receipient' => $this->input->post('receipient'),
			'subject' => $this->input->post('subject'),
			'body' => $this->input->post('body'));
		
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('header');
			$this->load->view('admin/edit_mail_form', $post_data);
			$this->load->view('footer');
		}
		else {
			$receivers = $this->getReceivers($this->input->post('receipient'));

			$sampleReceiver = NULL;
			if (count($receivers) > 0)
				$sampleReceiver = $receivers[array_rand($receivers)];
			else
				$sampleReceiver = $this->participant->random();
			
			$email = $this->prepareEmailToParticipant($sampleReceiver, $post_data);
			
			$this->session->set_userdata('mail_template', $post_data);
			
			$this->load->view('header');
			$this->load->view('admin/preview_mail', array('email' => $email, 'receivers' => $receivers));
			$this->load->view('footer');
		}		
	}
	
	private function prepareEmailToParticipant(Participant_model $participant, $emaildata) {
		
		$this->config->load('email', TRUE);

		$this->email->to($participant->email);
		$this->email->from($this->config->item('from_address', 'email'), $this->config->item('from_name', 'email'));
		$this->email->reply_to($this->config->item('reply_to_address', 'email'), $this->config->item('reply_to_name', 'email'));
		$this->email->subject($emaildata['subject']);
		
		$message = $emaildata['body'];
		$message = searchReplaceMessageText($message, $participant);
			
		$this->email->message($message);
		
		return $this->email;
	}
	
	private function getReceivers($key) {
	
		$ret = new Participant_list_model();
			
		// list
		switch ($key) {
		    case "committee":
		    	$ret->level('COMMITTEE');
		        break;
		    case "students":
		    	$ret->level('STUDENT');
		        break;
		    case "poster_students":
		       	$ret->level('STUDENT');
		       	$ret->contribution('REGULAR_POSTER');
		       	$ret->contribution('ESSENCE_POSTER');
		       	break;
	       	case "all_essence_poster":
	       	   	$ret->contribution('ESSENCE_POSTER');
	       	   	break;
	       	case "all_regular_poster":
	       	   	$ret->contribution('REGULAR_POSTER');
	       	   	break;
	       	case "silverback_seniors":
	       		$ret->level('SENIOR');
	       		$ret->silverback_dinner(true);
	       		break;
	       	case "silverback_students":
	       		$ret->level('STUDENT');
	       		$ret->silverback_dinner(true);
	       		break;	
	       	case "poster_visitors":
       			$ret->posterVisitor(true);
       			break;	
		    default:
		    	return $ret->models;
		}
		
		$ret->fetch();		
		return $ret->models;
	}
	
	function silverback() {
	
		$this->db->select('*');
		$this->db->from('participants');
		$this->db->join('silverback_answers', 'silverback_answers.participantId = participants.id');
		
		$query = $this->db->get();
		
		$this->load->view('header');
		$this->load->view('admin/silverback', array('answers' => $query->result()));
		$this->load->view('footer');
	}
	
	function slides() {
		$query = $this->db->get('lhc_slides');
		
		$this->load->view('header');
		$this->load->view('admin/edit_slides', array('slides' => $query->result()));
		$this->load->view('footer');
	}
	
	function edit_slides() {	
		if ( $this->uri->segment(3) ) {
			if($_SERVER['REQUEST_METHOD'] == 'PUT') {
				$data = json_decode(file_get_contents("php://input"), TRUE);
				$this->db->where('id', $this->uri->segment(3));
				$this->db->update('lhc_slides', array(
					'title' => $data['title'],
					'content' => $data['content'],
					'startTime' => $data['startTime'],
					'endTime' => $data['endTime'],
					'days' => json_encode($data['days'])
					)
				);
			}
			else if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
				$this->db->delete('lhc_slides', array('id' => $this->uri->segment(3)));
			}
			else {
				// GET
			}
		}
		else if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->db->insert(
				'lhc_slides',
				array(
					'title' => $this->input->post('title'),
					'content' => $this->input->post('content'),
					'startTime' => $this->input->post('startTime'),
					'endTime' => $this->input->post('endTime'),
					'days' => json_encode($this->input->post('days'))
					)
			);
			echo json_encode(array('id' => $this->db->insert_id()));
		}
		else {
			$query = $this->db->get('lhc_slides');
			$result = $query->result();
			
			array_walk($result, create_function('&$item, $key','$item->days = json_decode($item->days);'));
			
			echo json_encode($result);
		}
	}
	
	function participants() {
		$ret = new Participant_list_model();
		$ret->fetch();
		
		$models = $ret->models;
		uasort($models, create_function('$a, $b', 'return strcmp($a->lastName, $b->lastName);'));
		
		$this->load->view('header');
		$this->load->view('admin/participants', array('participants' => $models));
		$this->load->view('footer');
	}
	
	function orals() {
		$ret = new Participant_list_model();
		$ret->contribution(array('INVITED_TALK', 'PLENARY_TALK', 'ORAL_PRESENTATION'));	
		$ret->fetch();
		
		$models = $ret->models;
		uasort($models, create_function('$a, $b', 'return strcmp($a->getContribution()->startTime, $b->getContribution()->startTime);')); // sort by startDate
		
		$roomContributorsMap = array();
		foreach ($models as $model) {
			$contribution = $model->getContribution();
			
			if ( ! array_key_exists($contribution->room, $roomContributorsMap) ) {
				$roomContributorsMap[$contribution->room] = array();
			}
			
			array_push($roomContributorsMap[$contribution->room], $model);
		}
		ksort($roomContributorsMap);
		
		$this->load->view('header');
		$this->load->view('admin/orals', array('roomContributorsMap' => $roomContributorsMap));
		$this->load->view('footer');
	}
	
	function pdfReceived() {
		$this->db->where('contributionKey', $this->input->post('contributionKey'));
		$this->db->set('pdfReceived', $this->input->post('pdfReceived'));
		$this->db->update('contributions');
	}
}

?>
