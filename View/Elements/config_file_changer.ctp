<div class="well well-sm">
	<form method="post">
		<label for="configFile">Config file:</label>
		<select name="configFile">
			<?php
			foreach ($configFiles as $file) {
				echo "<option value=\"$file\"" . (($file === $configFile) ? ' SELECTED' : '') . ">" . (($file === $configFile) ? ' > ' : '') . "$file</option>";
			}
			?>
		</select>
		<input type="submit" class="btn btn-primary" value="Change config file">
	</form>
	<?php
	if (!is_null($configFileDescription)) {
		echo "<p> > $configFileDescription</p>";
	}
	?>
</div>