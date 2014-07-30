<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function searchReplaceMessageText($text, Participant_model $participant) {			
		$pattern = '/%PERSONAL_URL%/i';
		$replacement = site_url('participant/' . $participant->uuid);
		$text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%SILVERBACK_URL%/i';
		$replacement = site_url('silverback/confirm/' . $participant->uuid);
		$text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%RECIPIENT_NAME%/i';
		$replacement = $participant->firstName . " " . $participant->lastName;
		$text = preg_replace($pattern, $replacement, $text);
			
		$pattern = '/%SILVERBACK_SELECTION_LINK%/i';
		$replacement = site_url('silverback/meet/' . $participant->uuid);
		$text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%POSTER_URL%/i';
		$replacement = site_url('poster/index/' . $participant->uuid);
		$text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%SILVERBACK_RESTAURANT%/i';
		$replacement = ($participant->isSilverbackParticipant()) ? $participant->getSilverback()->restaurant : '';
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/%SILVERBACK_NAME%/i';
                $replacement = ($participant->isSilverbackParticipant()) ? $participant->getSilverback()->silverback->name(): '';
                $text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%SILVERBACK_DAY%/i';
		$replacement = '';
		if ($participant->isSilverbackParticipant()) {
			switch ($participant->getSilverback()->day) {
				case 'MONDAY':
				case 'ASSIGNED_MONDAY':
					$replacement = "Monday, 22nd August";
					break;
				case 'TUESDAY':
				case 'ASSIGNED_TUESDAY':
					$replacement = "Tuesday, 23nd August";
					break;
				case 'NOT_IN':
				default:
					break;
			}
		}
		$text = preg_replace($pattern, $replacement, $text);
		
		$pattern = '/%SILVERBACK_STUDENT_LIST%/i';
		$replacement = ($participant->isSilverbackParticipant()) ? implode("\n", array_map(create_function('$model', 'return $model->name();'), $participant->getSilverback()->students)) : '';
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/%POSTER_STUDENT_LIST%/i';
		$replacement = ($participant->isPosterVisitor()) ? implode("\n", array_map(
			create_function('$id',
				'$model = new Participant_model(); $model->from_id($id);' .
				'return $model->getContribution()->contributionKey  . "  |  " .  $model->firstName . " " . $model->lastName . "  |  " . $model->getContribution()->title . "\n";'
			), $participant->getPosterStudents())) : '';
		$text = preg_replace($pattern, $replacement, $text);

		return $text;
	}

?>
