<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->config('errors', TRUE);
	}
	
	function page_missing() {
		
		$this->output->set_status_header('404');
		
		$heading = "404 Page Not Found";
		$message = 'Sorry, the page you requested was not found.';
		
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '')
		{
			// broken link somewhere, so send an email
			// and display the right info to the user.

			$referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
			log_message('error', '404 Page Not Found --> '.uri_string() . ' - Referrer = ' . $referer);

			// search engines to look out for, should account for around 98-99% of searches
			// (source: http://www.hitwise.com/us/datacenter/main/dashboard-10133.html )
			$search_engines = array(
				'www.google.',
				'search.yahoo.',
				'bing.',
				'ask.',
				'alltheweb.',
				'altavista.',
				'search.aol',
				'baidu.'
			);

			if(strpos($referer, base_url()) !== FALSE) // is it a broken internal link?
			{
				$message .= ' It looks like we have a broken link on the site - we have been notified and we\'ll get it fixed as soon as possible.';
			}
			else
			{				
				$source_text = 'another site';
				
				foreach($search_engines as $search_engine)
				{
					if(strpos($referer, $search_engine) !== FALSE) // bad search engine result?
					{
						$source_text = 'a search engine';						
						break; // no point continuing to loop once we have found a match
					}
				}
				
				$message .= ' It looks like you came from ' . $source_text . ' with a broken link - we have been notified and we\'ll get it fixed as soon as possible.';
			}

			// $message .= ' In the meantime, why not <a href="/about">find out more about us</a>, <a href="/blog">have a read of our blog</a>, or <a href="/portfolio">check out our portfolio</a>? You\'re more than welcome to <a href="/contact">drop us a line</a>, too.';

			// send email notifying admin of broken link - have to use native function
			// as the CI super-object is not yet instantiated :(
			$email_text = 'Broken link at: ' . $_SERVER['HTTP_REFERER'];
			
			$this->load->helper('email');
			send_email($this->config->item('mail_to_address', 'errors'), "Broken inbound link", $email_text);

		}
		else // no referer, so probably came direct
		{			
			$message .= ' It looks like you came directly to this page, either by typing the URL or from a bookmark. Please make sure the address you have typed or bookmarked is correct - if it is, then unfortunately the page is no longer available.';
			
			// $message[] = 'But all is not lost - why not <a href="/about">find out more about us</a>, <a href="/blog">have a read of our blog</a>, or <a href="/portfolio">check out our portfolio</a>? You\'re more than welcome to <a href="/contact">drop us a line</a>, too.';
			log_message('error', '404 Page Not Found --> '.uri_string());
		}
		
		$this->load_error_view(array('heading' => $heading, 'message' => $message));
	}
	
	private function load_error_view($data = array()) {
		$this->load->view('header');
		$this->load->view('error', $data);
		$this->load->view('footer');
	}
}

?>