
<?php
	
	$timeWindowStart = "04 July 2011 4pm";
	$timeWindowStop = "08 July 2011 4pm";
	
	$now = date(time());
	$timeWindowOpen = $now >= strtotime($timeWindowStart) && $now <= strtotime($timeWindowStop);
?>


	<div class="section">
		<h1>Meet a Silverback</h1>
		
		<p>The list below shows the senior scientists you are able to join for a dinner. For each person the day varies according to the list, the time however is fixed to <b>20:30</b> for all of them.
		The selection process <b>starts on <?= $timeWindowStart ?></b> and <b>closes on <?= $timeWindowStop ?></b> and is done using a "first come, first served" rule.<br><br>
		
		Good luck! 
		</p>
		
		<?= form_open('/silverback/meet/'.$student->uuid); ?>
		<?= form_hidden('studentId', $student->id); ?>
		
		
		<?php if ($timeWindowOpen) echo form_hidden('activationId', md5('activationId')); ?>
				
		<table class="silverback_select">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Department</th>
			<th>Day of dinner</th>
			<th>Available seats</th>
		</tr>
		
		<?php $i=0; foreach($silverbacks as $silverback) { ?>
			<?php
				$nFreeStudents = $silverback->maxStudents - $silverback->nStudents;
				$isDisabled = $nFreeStudents == 0;
			?>
			<tr class="<?php if ($isDisabled) echo 'disabled'; ?>">
				<td>
					<?php if ($timeWindowOpen) { ?>
						<input type="radio" name="silverbackId" value="<?= $silverback->id ?>"  <?php if ($isDisabled) echo 'disabled'; ?>></input>
					<?php } ?>
				</td>
				<td><?= sprintf('%s, %s', $silverback->lastName, $silverback->firstName) ?></td>
				<td><?= implode_nonempty(', ', array($silverback->organization, $silverback->department)) ?></td>
				<td><?php switch ($silverback->day) { case 'MONDAY': case 'ASSIGNED_MONDAY': echo "Monday"; break; case 'TUESDAY': case 'ASSIGNED_TUESDAY': echo "Tuesday"; break; }  ?></td>
				<td><?= $silverback->maxStudents - $silverback->nStudents ?></td>
			</tr>
		<?php } ?>
		</table>
		
		<?php if ($timeWindowOpen) echo form_submit('submit', 'Submit'); ?>
		</form>
	</div>
