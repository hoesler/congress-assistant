	<script type="text/template" id="bse_slide_template">
		<%= title %>
		<div class="delete">x</div>
	</script>	

	<link rel="stylesheet/less" type="text/css" href="<?= site_url('assets/styles/slides.less') ?>">
	
	<script type="text/javascript" src="<?= site_url('assets/bower_components/requirejs/require.js') ?>"></script>
	<script type="text/javascript" src="<?= site_url('assets/scripts/config.js') ?>"></script>
	<script type="text/javascript">
		require(['less']);
		require(['scripts/app/slide_editor', 'backbone'], function(slide_editor, backbone) {
			new slide_editor.ApplicationController({config: {base_url: '<?= base_url(); ?>'}});
	    	backbone.history.start({root: "<?= site_url('/admin/slides/') ?>"});
		});
	</script>

	<div class="section">
	
		<h1>Edit slides</h1>
		
		<div id="slide_editor_app">
		
			<div id="left">
				<button id="add">add</button>
				<ul id="slideList"></ul>
			</div>
			
			<div id="right">
			
				<table>
					<tr><td>Title of the silde:</td><td><input type="text" name="title" value="Insert title" /></td></tr>
					
					<tr><td>Day(s) when visible:</td><td><?= form_multiselect('days', array('Sun' => "Sunday", 'Mon' => "Monday", 'Tue' => "Tuesday", 'Wed' => "Wednesday")); ?></td></tr>
					
					<tr><td>Visible from </td>
						<td>
							<input type="text" name="starttime_hh" value="<?= date('H') ?>" size="2" />:
							<input type="text" name="starttime_mm" value="<?= date('i') ?>" size="2" />
						
							till
							<input type="text" name="endtime_hh" value="<?= date('H') ?>" size="2" />:
							<input type="text" name="endtime_mm" value="<?= date('i') ?>" size="2" />
						</td>
					</tr>
				</table>
				
				
				<textarea name="editor1">&lt;p&gt;Initial value.&lt;/p&gt;</textarea><br>
				<button id="save" disabled="disabled">saved</button>
			</div>
		
		</div>
		
		</form>
	</div>
