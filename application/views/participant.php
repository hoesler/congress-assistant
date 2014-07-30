<div class="section">
		<h1>Personal Information</h1>
		<ul class="plain">
			<li><span class="description">Name:</span><?=$participant->firstName . " " . $participant->lastName?></li>
			<li><span class="description">Affiliation:</span><?= implode_nonempty(', ', array($participant->organization, $participant->department)) ?></li>
			<li><span class="description">Level:</span><?=$participant->level?></li>
			
			<?php if ($contribution) { ?>
			<li>
				<span class="description">Contribution:</span><?=$contribution->contributionKey?>
			</li>
			<?php } ?>
		</ul>
	</div>
	
	<? if($participant->silverback != NULL
		&& $participant->silverback != 'NO_ANSWER'){ ?>
	<div class="section">
		<h1>Meet a silverback activity</h1>
		
		<? if($participant->getSilverBackAnswer() == FALSE){ ?>
		You haven't made a choice yet. <b>Please do it <?= anchor('silverback/confirm/'.$participant->uuid, "here"); ?>.</span>
		
		<? } else if ($participant->getSilverBackAnswer()->day == 'NOT_IN') { ?>
		You have <b>declined</b> your participation.
		
		<? } else { ?>
			You have <b>confirmed</b> your participation. The dinner takes place on
			
			<b>
			<?php
				$silverback = $participant->getSilverBackAnswer();
				$day = $silverback->day;
				if (preg_match('/.+TUESDAY/', $day)) { print("Tuesday, 23rd (20:30)"); }
				elseif (preg_match('/.+MONDAY/', $day)) { print("Tuesday, 22rd (20:30)"); }
			?>
			</b>.<br>	
		<? }?>
	</div>
	<? } ?>
	
	<?php if ($participant->level == "STUDENT") { ?>
	
		<?php
			$silverbackActivity = $participant->getSelectedSilverbackActivity();
			if($silverbackActivity !== FALSE) {
		?>
		<div class="section">
			<h1>Meet a silverback activity</h1>
			
			You have selected to go out with <b><?= implode_nonempty(' ', array($silverbackActivity->title, $silverbackActivity->firstName, $silverbackActivity->lastName)) ?></b> on
			
			<b>
			<?php 
				$day = $silverbackActivity->day;
				if (preg_match('/.+TUESDAY/', $day)) { print("Tuesday, 23rd (20:30)"); }
				elseif (preg_match('/.+MONDAY/', $day)) { print("Tuesday, 22rd (20:30)"); }
			?>
			</b>.<br>
			
			The dinner will take place in the <b><?= $silverbackActivity->restaurant ?></b>
		</div>
		<? } ?>
		
		<? if(preg_match('/.+POSTER/', $contribution->type)) { ?>
		<div class="section">
			<h1>Meet me at my poster activity</h1>
			
			<?php $visitors = $participant->getPosterVisitors(); ?>
			
			You have invited <?= count($visitors) ?> persons.
			<ul>
			<?php foreach ($visitors as $visitor) { ?>
				<li><?= implode_nonempty(' ', array($visitor->title, $visitor->firstName, $visitor->lastName)) ?></li>
			<?php } ?>
			</ul>
			
		</div>
		<?php } ?>

	<?php } ?>