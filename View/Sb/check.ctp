<h1>Population log</h1>
<?php
echo $this->element('config_file_changer');
?>
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-info">
			This page outputs the logs from <code>sbc->loadFile</code>: the configuration file is loaded and populated, and errors and warning are thrown during this process. On the right panel, this is the <em>complete</em> config file, after population.
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6">
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
		<div id="log">
			<ul class="log">
				<?php
				foreach ($log as $entry) {
					switch ($entry['type']) {
						case 'warning':
							$type = 'text-warning';
							$icon = 'warning-sign';
							break;
						case 'error':
							$type = 'text-danger';
							$icon = 'bug';
							break;
						case 'success':
							$type = 'text-success';
							$icon = 'check-sign';
							break;
						case 'info':
							$type = 'text-info';
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
	</div>
	<div class="col-lg-6 col-md-6">
		<h2>Full YML output</h2>
		<div class="alert alert-info">
			This is the complete configuration array for your configuration file. You can take part of it as examples if you have trouble.
		</div>
		<div id="render">
			<pre class="syntax yaml">
<?php echo $completeConfig ?>
			</pre>
		</div>
	</div>
</div>