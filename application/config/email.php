<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
| -------------------------------------------------------------------------
| This file lets you define parameters for sending emails.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/email.html
|
*/
$config['mailtype'] = 'text';
$config['charset'] = 'utf-8';
$config['newline'] = '\r\n';

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'localhost';
$config['smtp_port'] = '25';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';

$config['from_name'] = 'Configure me';
$config['from_address'] = 'Configure me';
$config['reply_to_name'] = $config['from_name'];
$config['reply_to_address'] = $config['from_address'];

$local_overwrite = dirname(__FILE__) . '/email.local.php';
if (is_readable($local_overwrite))
	include($local_overwrite);

/* End of file email.php */
/* Location: ./application/config/email.php */
