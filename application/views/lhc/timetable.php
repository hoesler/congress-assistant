<ul id="timeline">
<?php foreach($result as $row) { ?>
	
	<li class="timeline_entry" rel="<?= $row->startTime . '#' . $row->duration ?>">	
		<span class="<?php $row->programmeType ?>"><?= $row->info ?></span>
	</li>

<?php } ?> 
</ul>