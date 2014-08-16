<?= doctype('html5'); ?>

<html>
<head>
<?php echo meta('Content-type', 'text/html; charset='.config_item('charset'), 'equiv');?>

<title>ESEB 2011 | PhD Activities</title>

<link rel="stylesheet/less" type="text/css" href="<?= site_url('assets/styles/base.less') ?>">

<script type="text/javascript" src="<?= site_url('assets/bower_components/requirejs/require.js') ?>"></script>
<script type="text/javascript" src="<?= site_url('assets/scripts/config.js') ?>"></script>
<script type="text/javascript">		
	require(['less'])
</script>

</head>
<body>

<div id="header"></div>
<div id="content">