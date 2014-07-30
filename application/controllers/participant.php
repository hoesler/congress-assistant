<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Participant extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model('Participant_model', 'participant');
        $this->load->model('Participant_list_model', 'participant');
    }

	public function index()
	{
		$uuid = $this->uri->segment(2);
				 		
		if ($this->participant->from_uuid($uuid) === FALSE
			|| $this->participant->hasCancelled) {
			show_404('');
		}
		else {
			$this->load->helper('array2');
			
			$this->load->view('header');
			$this->load->view('participant', array('participant' => $this->participant, 'contribution' => $this->participant->getContribution()));
			$this->load->view('footer');
		}
	}
}

?>
