<script src="<?= site_url("scripts/jquery.js"); ?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$('input.pdfReceptionIndicator').bind('change', function(event) {
		  	$.post('<?= site_url("admin/pdfReceived") ?>', { contributionKey: $(this).val(), pdfReceived: $(this).attr('checked') ? 1 : 0 });
		});
	});
</script>

<?php 

foreach ($roomContributorsMap as $room => $participants) {
	print '<h2>' . $room . '</h2>';
?>

	<table class="orals">
	<?php
		
		foreach ($participants as $participant) {
			$contribution = $participant->getContribution();
	?>		
	
		<?php if (strtotime($contribution->startTime) < time()) { ?><tr class="done"><?php } else { ?><tr><?php } ?>
			<td><?= date("D, H:i", strtotime($contribution->startTime)) ?></td>
			<td><?= "(" . (strtotime($contribution->endTime) - strtotime($contribution->startTime)) / 60 . ")"?></td>
			<td><b><?= $participant->name() ?></b></td>
			<td><?= form_checkbox('pdfReceived', $contribution->contributionKey, $contribution->pdfReceived, 'class="pdfReceptionIndicator"'); ?></td>
		</tr>
	
	<?php
		}
	?>
	
	</table>

<?php
	}
?>