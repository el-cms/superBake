<h1>Population log</h1>
<div class="alert alert-info">
	This page outputs the logs from <code>Sbc->loadFile</code>: the configuration file is loaded and populated, and errors and warning are thrown during this process. On the right panel, this is the <em>complete</em> config file, after population.
</div>
<h2>Configuration log</h2>
<?php
if ($logErrors > 0) {
	echo '<div class="alert alert-danger">Errors were found during the process. Check the logs.</div>';
} else {
	echo '<div class="alert alert-success">Your configuration file seems to be valid';
	if ($logWarnings > 0) {
		echo ', even if there are warnings';
	}
	echo '.</div>';
}
?>
<div>
	<strong>Filters:</strong>
	<a href="#" onclick="toggleState('info')"><i class="icon-info-sign"></i> Toggle infos</a>
		<a href="#" onclick="toggleState('success')"><i class="icon-check-sign"></i> Toggle success</a>
		<a href="#" onclick="toggleState('warning')"><i class="icon-warning-sign"></i> Toggle warnings</a>
		<a href="#" onclick="toggleState('error')"><i class="icon-bug"></i>Toggle errors</a>
	<script>
		var showInfos = true;
		var showSuccess = true;
		var showWarnings = true;
		var showErrors = true;

		function toggleState(txtClass) {
			if (txtClass === 'info') {
				state = showInfos;
			}
			if (txtClass === 'info') {
				state = showSuccess;
			}
			if (txtClass === 'warning') {
				state = showWarnings;
			}
			if (txtClass === 'error') {
				state = showErrors;
			}

			if (state === true) {
				$('.log-' + txtClass).hide();
				state = false;
			} else {
				$('.log-' + txtClass).show();
				state = true;
			}

			if (txtClass === 'info') {
				showInfos = state;
			}
			if (txtClass === 'info') {
				showSuccess = state;
			}
			if (txtClass === 'warning') {
				showWarnings = state;
			}
			if (txtClass === 'error') {
				showErrors = state;
			}
		}
	</script>
</div>
<div id="log">
	<ul class="log">
		<?php
		foreach ($log as $entry) {
			switch ($entry['type']) {
				case 'warning':
					$type = 'text-warning log-warning';
					$icon = 'warning-sign';
					break;
				case 'error':
					$type = 'text-danger log-error';
					$icon = 'bug';
					break;
				case 'success':
					$type = 'text-success log-success';
					$icon = 'check-sign';
					break;
				case 'info':
					$type = 'text-info log-info';
					$icon = 'info-sign';
					break;
				default:
					$type = '';
					$icon = 'plus';
					break;
			}
			$level = ' lvl' . $entry['level'];
//					if ($entry['level'] >= $level) {
			echo ("<li><pre class=\"$type$level\"><i class=\"icon-$icon\"></i> ${entry['message']}</pre></li>");
//					}
		}
		?>
	</ul>

</div>
<h2>Full YML output</h2>
<div class="alert alert-info">
	This is the complete configuration array for your configuration file. You can take part of it as examples if you have trouble.
	<br>
	<span class="text-danger">Don't copy the whole output, or you'll get strange behaviours...</span>
</div>
<div id="render">
	<pre class="syntax yaml">
		<?php echo $completeConfig ?>
	</pre>
</div>