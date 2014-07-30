
	<div class="section">
		<h1>Send mail</h1>
		
		<?= form_open('admin/preview_mail'); ?>
		
		<?= form_hidden('mailId', $mailId); ?>
		
		<div class="description">From</div><?= $from ?><br>
		
		<div class="description">To</div>
		<?= form_dropdown('receipient', array(	
		          'none'						=> 'None',
                  'committee'					=> 'Committee Members',
                  'students'					=> 'PhD Students',
                  'poster_students'				=> 'PhD Students with a Poster',
                  'all_essence_poster'			=> 'Essence Poster Contributors',
                  'all_regular_poster'			=> 'Regular Poster Contributors',
                  'silverback_seniors'			=> 'Silverback Seniors',
                  'silverback_students'			=> 'Silverback Students',
                  'poster_visitors'				=> 'Poster Visitors'
                ), $receipient); ?><br>
			
		<div class="description">Subject</div>
		<input type="text" name="subject" size="70" value="<?= $subject ?>"></input><br>
		
		<small>You can use following placeholders:
			<i>%RECIPIENT_NAME%</i>,
			<i>%PERSONAL_URL%</i>,
			<i>%SILVERBACK_URL%</i>,
			<i>%SILVERBACK_SELECTION_LINK%</i>
		</small><br>	
		<?= form_textarea('body', $body); ?><br>
		
		<?= form_submit('sendmail', 'Preview') ?>
		
		</form>
	</div>
