<link rel="stylesheet/less" type="text/css" href="<?= site_url('assets/styles/poster.less') ?>">

<script type="text/javascript" src="<?= site_url('assets/scripts/date.format.js') ?>"></script>
<script type="text/javascript" src="<?= site_url('assets/scripts/jquery.js') ?>"></script> 
<script type="text/javascript" src="<?= site_url('assets/scripts/json2.js') ?>"></script>
<script type="text/javascript" src="<?= site_url('assets/scripts/underscore.js') ?>"></script>          
<script type="text/javascript" src="<?= site_url('assets/scripts/backbone.js') ?>"></script>

<script type="text/template" id="poster_participant_template">
	<input type="checkbox"/>
	<div class="name">
		<%= lastName %>, <%= firstName %>
	</div>
	<div class="info">
		<%= [organization,department].filter(function(val) { return /^.+$/.test(val); }).join(", ") %>
	</div>
</script>

<script type="text/template" id="poster_selected_participant_template">
	<div class="remove">x</div>
	<%= lastName %>, <%= firstName %><br>
	<div class="info"><%= [organization,department].filter(function(val) { return /^.+$/.test(val); }).join(", ") %></div>
</script>

<script type="text/javascript" src="<?= site_url('assets/scripts/poster.js') ?>"></script>


<div class="section">
	<h1>Meet me at my poster</h1>
	<p>
	Hello <?= $participant->firstName ?> <?= $participant->lastName ?>,<br>
	
	below you can select up to <b>5</b> out of all conference participants you wish to visit you at your poster. You can modify your selection till <?= $activity_end_date ?> (MEZ). Don't forget to always press the <b>save</b> button before you leave this site.
	</p>
	
	<div id="poster_app">
		
		<div class="left">
			<h3>Your selection<input type="button" value="saved" class="save" disabled></input></h3>
			<ol id="selected_participants"></ol>
		</div>
		
		<div class="right">
			<h3>Search and add participants</h3>
			<div id="search">
				<small>Search for a person by entering his or her lastname (and pressing enter)</small>
				<input type="text" value=""/>
			</div>
			
			<small>... or browse by the first character of the lastname</small>
			<ol id="alphabet_selector">
				<?php foreach(range('A', 'Z') as $letter) { ?>
				    	<li class="letter"><?= $letter ?></li>
				<?php } ?>
			</ol>
			
			
			<ul id="participants"></ul>
		</div>
	</div>
</div>



<script type="text/javascript">
	$(function () {
		var regexResultArray = /^.*\/poster\/index\/(.+)$/.exec(window.location.pathname);
		if (regexResultArray.length > 1) {
	    	new PosterApp.AppView({uuid: regexResultArray[1], config: {base_url: '<?= base_url(); ?>'}});
	    }
	});
</script> 
