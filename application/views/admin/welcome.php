	<div class="section">
		<h1>Welcome <?php echo $username; ?></h1>
		
		<p>You can do the following here:</p>
		<ul class ="plain">
			<li><?php echo anchor('/admin/editmail/', 'Send mails to the participants'); ?></li>
			<li><?php echo anchor('/admin/silverback/', 'View the "Meet a Silverback" table'); ?></li>
			<li><?php echo anchor('/admin/slides/', 'Edit the slides for the lecture halls'); ?></li>			
			<li><?php echo anchor('/admin/orals/', 'List all oral presentations'); ?></li>
			<li><hr></li>
			<li><?php echo anchor('/auth/logout/', 'Logout'); ?></li>
		</ul>
		
	</div>	


