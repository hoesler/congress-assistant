	<div class="section">
		<h1>Meet a Silverback</h1>
		
		<table class="silverback">
		
			<tr>
				<th>No.</th>
				<th>Name</th>
				<th>Choice</th>
				<th>Time of Submission</th>
			</tr>
		<?php $i=0; foreach($answers as $answer) { ?>
			<tr class="<?= ($answer->hasCancelled) ? 'cancelled' : ''?>">
				<td><?= ++$i ?></td>
				<td><?= implode(' ', array($answer->firstName, $answer->lastName)) ?></td>
				<td class="<?= $answer->day ?>"><?= $answer->day ?></td>
				<td><?= $answer->timeOfAnswer ?></td>
			</tr>
		<?php } ?>
		</table>
	</div>	


