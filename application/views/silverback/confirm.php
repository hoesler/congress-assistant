
	<div class="section">
		<h1>Plese confirm your participation</h1>
		<p>
			Hello <?= implode(' ', array($title,$firstName,$lastName)) ?>,<br><br>
			please fill out the following form to confirm or decline your participation in the <b>"Meet a Silverback Activity"</b> of this years ESEB conference.<br>
			Please notice, that you <b>cannot alter</b> your descision on this website, once it has been made.
		</p>
		
		<?= form_open('/silverback/answer/'.$uuid); ?>
		
			<ul class="plain" style="margin-top: 2em;">
				<li>
					<input type="radio" name="participate" value="1" checked="checked">Yes, I want to take part<br>
					
					<div style="margin: 0em 0em 1.3em 1.5em;">
						<p>Please select the day you prefer for the dinner or "Assign me" if you want us to make the descision for you.<br>
						We'll reseve a table for <b>8.30 pm</b>, one hour after the program is finished. If you prefer a different time, please let us know.</p>
						<ul class="plain">		
							<li><input type="radio" name="day" value="ASSIGN_ME" checked="checked">Assign me</li>
							<li><input type="radio" name="day" value="MONDAY">Monday</li>
					    	<li><input type="radio" name="day" value="TUESDAY">Tuesday</li>
						</ul>
					</div>
					
				</li>
				
				<li>
					<input type="radio" name="participate" value="0">No, thank you<br>					
				</li>
				
				<br>
				<button type="submit" name="submit" value="submit">I made my descision</button>
			</ul>
		
		</form>
	</div>