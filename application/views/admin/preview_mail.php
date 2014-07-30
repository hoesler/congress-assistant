
	<div class="section">
		<h1>Sample Preview</h1>

		<div style="margin: 0 0 1em 0;">		
			<?= anchor('admin/editmail', '<span>Go back to editor</span>', array('class' => 'button')); ?>
			<span style="margin: 0 2em;">&nbsp;</span>
			<?= anchor('admin/sendmail', '<span>Send this mail</span>', array('class' => 'button')); ?>
		</div>
				
		<div style="border: 1px solid black; padding: 1em;">
		
			<div class="description">From</div>
			<div><?= htmlspecialchars(mb_decode_mimeheader($email->_headers['From'])) ?></div>
			
			<div class="description">To</div>
			<div><?= htmlspecialchars(mb_decode_mimeheader($email->_headers['To'])) ?></div>
			
			<div class="description">Reply-To</div>
			<div><?= htmlspecialchars($email->_headers['Reply-To']) ?></div>
			
			<div class="description">Subject</div>
			<div><?= htmlspecialchars(mb_decode_mimeheader($email->_headers['Subject'])) ?></div>
			
			<div style="margin-top: 1em;"><?= nl2br(htmlspecialchars($email->_body)) ?></div>
					
		</div>
		
		<div><small><b>The mail will be send to <?= count($receivers) ?> receivers:</b>
			<?= implode(', ', array_map(create_function('$el', 'return $el->id;'), $receivers)); ?></small>
		</div>
				
	</div>
