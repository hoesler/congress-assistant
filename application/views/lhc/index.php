<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<title>Lecture Hall Client</title>
	
	<link rel="stylesheet/less" type="text/css" href="<?= site_url('assets/styles/lhc.less') ?>"> 

	<script type="text/template" id="lhc_event_template">
		<div class="discussion"></div>
		<div class="lhc_event_info <%= type %>" title="<%= contributionKey %>">
		<div class="time"><%= moment(startTime).format("HH:mm") %></div>
		<div class="contributor"><%= firstName + ' ' + lastName %></div>
		<div class="title"><%= title %></div>
		</div>
		</a>
	</script>

	<script type="text/template" id="talk_template">
		<div class="countdown_time"></div>
	</script>
	
	<script type="text/template" id="break_template">
		<div class="countdown_time"></div>
		<div class="content"><%= content %></div>
	</script>

	<script type="text/javascript" src="<?= site_url('assets/bower_components/requirejs/require.js') ?>"></script>
	<script type="text/javascript" src="<?= site_url('assets/scripts/config.js') ?>"></script>
	<script type="text/javascript">		
		require(['less', 'moment'])
		require(['scripts/app/lhc', 'backbone'], function(lhc, backbone) {
			new lhc.ApplicationController({config: {base_url: '<?= base_url(); ?>', log_level: 2}});
			backbone.history.start({root: "<?= site_url('lhc/') ?>"})
		});
	</script>
	
</head>
<body>

	<div id="lhc_app">

		<div id="header">
			<div id="right">
				<div id="time"></div>
				<div id="version"></div>
			</div>
		</div>
		
		<div id="countdown"></div>
		
		<ul id="break_slides"></ul>		

		<div id="next_event_info">
			<div class="left">
				<div class="time">-08:16</div>
				<div class="description_next"></div>
				<div class="description_none">No further talks today</div>
			</div>
			
			<div class="right">
				<div class="bar"></div>
			</div>
		</div>
		
		<div id="timeline">
			<div id="lhc_events">
				<ul id="lhc_event_list"></ul>
			</div>
		</div>
	</div>
</body>
</html>
