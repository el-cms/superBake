<h1>Configuration summary</h1>
<?php if (Configure::read('Sb.executeTroughGUI') === true): ?>
	<div class="alert alert-danger text-center">
		<strong>WARNING:</strong> Before using the generation buttons, read the documentation to know what they do.
		<br/>
		<strong>Additionnally</strong> if you select a configuration file that's
		different of the file defined in <code>&lt;Sb&gt;/Config/bootstrap.php</code>,
		don't even try to generate things from the GUI, because the Shell will aways use
		the default file.
		<br/>
		<small>If you want to disable the "generation from GUI" feature, set <code>Sb.executeTroughGUI</code> to <code>false</code>
		in the plugin's bootstrap file.</small>
	</div>
	<?php
endif;
echo $this->element('config_file_changer');
?>
<h2>General and defaults</h2>
<?php if (Configure::read('Sb.executeTroughGUI') === true): ?>
	<div class="toolbar-sb">
		<strong>General: </strong>
		<div class="btn-group">
			<?php echo $this->Sb->execBtn('--help', 'Show <strong>help</strong>'); ?>
			<?php echo $this->Sb->execBtn('all', 'superBake <strong>all</strong>', 'danger'); ?>
		</div>
		<div class='toolbar-sb-line'>
			<strong>MVC:</strong>
			<div class="btn-group">
				<?php echo $this->Sb->execBtn('models', 'superBake <strong>all Models</strong>'); ?>
				<?php echo $this->Sb->execBtn('controllers', 'superBake <strong>all Controllers</strong>'); ?>
				<?php echo $this->Sb->execBtn('views', 'superBake <strong>all Views</strong>'); ?>
			</div>
		</div>
		<div class='toolbar-sb-line'>
			<strong>Files: </strong>
			<div class="btn-group">
				<?php echo $this->Sb->execBtn('files', 'superBake <strong>standalone files</strong>'); ?>
				<?php echo $this->Sb->execBtn('required', 'Copy <strong>required files</strong>'); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
<h3>Errors and warnings:</h3>
<ul>
	<li class="text-danger"><?php echo "You have $logErrors errors in your configuration file. " . (($logErrors > 0) ? "Please check the " . $this->Html->link('logs', array('action' => 'check')) : 'That\'s perfect !'); ?></li>
	<li class="text-warning"><?php echo "You have $logWarnings warnings from your configuration file. " . (($logWarnings > 0) ? "This is not fatal, but you should " . $this->Html->link('check if all is allright', array('action' => 'check')) : 'That\'s perfect !'); ?></li>
</ul>
<h3>General:</h3>
<ul>
	<li>You use the <strong><?php echo $completeConfig['general']['editorLicenseTemplate'] ?></strong> license to protect your generated files.</li>
	<li><strong>Prefixes:</strong> <?php echo ($completeConfig['general']['usePrefixes']) ? 'you use ' . count($completeConfig['defaults']['actions']) . ' prefixes: <strong>' . $defaults_prefixes_list . '</strong>' : 'you don\'t want to use prefixes'; ?></li>
	<li>The package name used for your app is <strong><?php echo $completeConfig['general']['basePackage'] ?></strong>.</li>
</ul>
<h2>Plugins</h2>
<strong>Legend:</strong>
<?php

function gIcon($text = null, $size = null) {
	$i = '<i ';
	$i.= 'class="icon-check-sign ' . ((!is_null($size)) ? ' icon-x' . $size : '') . ' text-success"';
	$i.= ((!is_null($text)) ? ' data-toggle="tooltip" title="' . $text . '"' : '');
	$i.= '></i> ';
	return $i;
}

function dGIcon($text = null, $size = null) {
	$i = '<i ';
	$i.= 'class="icon-remove-sign ' . ((!is_null($size)) ? ' icon-x' . $size : '') . ' text-danger"';
	$i.= ((!is_null($text)) ? ' data-toggle="tooltip" title="' . $text . '"' : '');
	$i.= '></i> ';
	return $i;
}
?>
<dl class="dl-horizontal">
	<dt><?php echo gIcon(__d('sb', 'Some reason')) ?></dt>
	<dd>Must be generated</dd>
	<dt><?php echo dGIcon(__d('sb', 'Some reason')) ?></dt>
	<dd>Must not be generated</dd>
</dl>
<div class="panel-group" id="accordion_Plugins">
	<?php
	$i = 0; // Counter for first element
	foreach ($completeConfig['plugins'] as $plugin => $pluginConfig) {
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion_Plugins" href="#collapse_P_<?php echo $plugin ?>">
						<?php echo ($pluginConfig['generate']) ? gIcon('This plugin will be generated', 2) : dGIcon('This plugin should not be generated', 2); ?><?php echo $plugin ?> <small>(<?php echo $pluginConfig['displayName'] ?>)</small>
					</a>
				</h3>
			</div>
			<div id="collapse_P_<?php echo $plugin ?>" class="panel-collapse collapse <?php echo (($i === 0) ? 'in' : '') ?>">
				<div class="panel-body">
					<div class="toolbar-sb">
						<strong>For this plugin:</strong>
						<div class="btn-group">
							<?php echo $this->Sb->execBtn("pluginMVC $plugin", 'Bake <strong>MVC</strong>', (($pluginConfig['generate']) ? 'default' : 'default disabled')); ?>
							<?php echo $this->Sb->execBtn("pluginModels $plugin", 'Bake <strong>all models</strong>', (($pluginConfig['generate']) ? 'default' : 'default disabled')); ?>
							<?php echo $this->Sb->execBtn("pluginControllers $plugin", 'Bake <strong>all controllers</strong>', (($pluginConfig['generate']) ? 'default' : 'default disabled')); ?>
							<?php echo $this->Sb->execBtn("pluginViews $plugin", 'Bake <strong>all views</strong>', (($pluginConfig['generate']) ? 'default' : 'default disabled')); ?>
						</div>
					</div>
					<h4>Parts</h4>
					<?php
					foreach ($pluginConfig['parts'] as $part => $partConfig) {
						?>
						<!-- Plugin accordions: parts -->
						<div class="panel-group" id="accordion_plugin_<?php echo $plugin ?>_parts">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion_plugin_<?php echo $plugin ?>_parts" href="#accordion_plugin_<?php echo $plugin ?>_parts_collapse<?php echo $part ?>">
											<?php echo (($pluginConfig['generate'] === true) ? (($partConfig['generate'] === true) ? gIcon('This part will be generated') : dGIcon('This part will NOT be generated')) : dGIcon('Plugin state will prevent this part generation')) . $part ?>
										</a>
									</h5>
								</div>
								<div id="accordion_plugin_<?php echo $plugin ?>_parts_collapse<?php echo $part ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="row">
											<div class="col-lg-4">
												<div class="block">
													<div class="block-header">
														Model
													</div>
													<?php echo ($partConfig['haveModel'] === true) ? (($pluginConfig['generate'] === true) ? (($partConfig['generate'] === true) ? (($partConfig['model']['generate'] === true) ? gIcon('This model will be generated') . $this->Sb->execBtn("model $plugin." . $partConfig['model']['name']) : dGIcon('This model should not be generated')) : dGIcon('This model should not be generated because of part state')) : dGIcon('This model should not be generated because of plugin state.')) . $partConfig['model']['name'] : '<span class="text-muted">This part have no model</span>' ?>
												</div>
											</div>
											<div class="col-lg-8">
												<div class="block">
													<div class="block-header">
														Controller
													</div>
													<?php echo ($partConfig['haveController'] === true) ? (($pluginConfig['generate'] === true) ? (($partConfig['generate'] === true) ? (($partConfig['controller']['generate'] === true) ? gIcon('This controller will be generated') . $this->Sb->execBtn("controller $plugin." . $partConfig['controller']['name']) : dGIcon('This controller should not be generated')) : dGIcon('This controller should not be generated because of part state')) : dGIcon('This controller should not be generated because of plugin state.')) . $partConfig['controller']['name'] : '<span class="text-muted">This part have no controller</span>' ?>
													<?php if ($partConfig['haveController'] === true) { ?>
														<div class="block-sub-wrapper">
															<div class="block-subtitle" style="width:50%">
																Actions
															</div>
															<div class="block-subtitle" style="width:50%">
																Views
															</div>
														</div>
														<?php
														foreach ($partConfig['controller']['actions'] as $prefix => $actions) {
															echo $prefix;
															foreach ($actions as $action => $actionConfig) {
																?>
																<div class="row rowLine">
																	<div class="col-lg-6">
																		<?php echo (($actionConfig['haveView'] === true) ? '<i class="icon-eye-open" data-toggle="tooltip" title="This action have a view"></i> ' : '<i class="icon-eye-close" data-toggle="tooltip" title="This action have no view"></i> ') . $action; ?>
																	</div>
																	<div class="col-lg-6">
																		<?php
																		if ($actionConfig['haveView'] === true) {
																			echo (($pluginConfig['generate'] === true) ? (($partConfig['generate'] === true) ? (($actionConfig['view']['generate'] === true) ? gIcon('This view will be generated') . $this->Sb->execBtn("view $plugin." . $partConfig['controller']['name'] . ".$action") : dGIcon('This view will not be generated')) : dGIcon('This view will not be generated, according to part state')) : dGIcon('This view will not be generated, according to plugin state.')) . ' ' . ((!empty($actionConfig['view']['template'])) ? $actionConfig['view']['template'] : '<span class="text-muted">No template specified</span>');
																		} else {
																			echo '<div class="text-center text-muted">-</div>';
																		}
																		?>
																	</div>
																</div>
																<?php
															}
															?>
															<?php
														}
														?>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/ Plugin accordions -->
						<?php
					}
					?>
					<h4>Menus</h4>
					<dl>
						<?php
						foreach ($pluginConfig['menus'] as $menu => $menuConfig) {
							?>
							<dt><?php echo ($menuConfig['generate'] === true) ? gIcon('This menu will be generated') : dGIcon('This menu won\'t be generated') ?><?php echo $menu; ?></dt>
							<dl>
								<?php echo str_replace('::', DS, $menuConfig['template']); ?> <i class="icon-chevron-sign-right"></i> <?php echo str_replace('::', DS, $plugin . DS . 'View' . DS . $menuConfig['targetPath']); ?>
							</dl>
							<?php
						}
						?>
					</dl>
					<h4>Files</h4>
					<dl>
						<?php
						foreach ($pluginConfig['files'] as $file => $fileConfig) {
							?>
							<dt><?php echo ($fileConfig['generate'] === true) ? gIcon('This file will be generated') : dGIcon('This file won\'t be generated') ?><?php echo $file; ?></dt>
							<dl><?php echo str_replace('::', DS, $fileConfig['template']); ?> <i class="icon-chevron-sign-right"></i> <?php echo str_replace('::', DS, $plugin . DS . $fileConfig['targetPath']); ?></dl>
							<?php
						}
						?>
					</dl>

					<h4>Required files and folders</h4>
					<dl>
						<?php
						foreach ($pluginConfig['required'] as $required => $requiredConfig) {
							?>
							<dt><?php echo ($requiredConfig['generate'] === true) ? gIcon('This will be copied') : dGIcon('This won\'t be copied') ?><?php echo $required; ?></dt>
							<dl><i class="icon-file"></i> <?php echo str_replace('::', DS, $requiredConfig['source']); ?> <i class="icon-chevron-sign-right"></i> <?php echo str_replace('::', DS, $plugin . DS . $requiredConfig['target']); ?></dl>
							<?php
						}
						?>
					</dl>
				</div>
			</div>
		</div>
		<?php
		$i++;
	}
	?>
</div>

<!--Modal for results -->
<div class="modal fade" id="results_modal" tabindex="-1" role="dialog" aria-labelledby="Results" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="results_title">Execution output</h4>
			</div>
			<div class="modal-body">
				<pre id="log">
				</pre>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	//
	// Original code from here:
	// http://www.codeproject.com/Articles/34072/Displaying-a-Progress-Bar-Loading-Box-During-any-T
	//
	$.showprogress = function()
	{
		$.hideprogress();
		$("BODY").append(
						'<div id="processing_container">' +
						'<div id="processing_icon"><i class="icon-cog icon-spin"></i></div>' +
						'Executing command' +
						'</div>');
		$("#processing_container").css({
			position: 'fixed',
			zIndex: 99999,
			padding: '15px',
			width: '200px',
			left: '50%',
			top: '50%',
			border: '1px solid rgba(0,0,0,0.5)',
			'background-color': '#FFF',
			'margin-left': '-100px',
			'border-radius': '5px',
			'font-weight': 'bold',
			'text-align': 'center',
			'box-shadow': '0px 0px 15px rgba(0,0,0,0.8)'
		});
		$("#processing_icon").css({
			'font-weight': 'bold',
			'font-size': '5em',
		});
	}

	$.hideprogress = function()
	{
		$("#processing_container").remove();
	}

	function run_cmd(cmd) {
		$.showprogress();
		$.ajax({
			url: '<?php echo $this->Html->url(array('action' => "execute_cmd")) ?>/' + cmd
		}).done(function(data) {
			$.hideprogress();
			$('#log').text(cmd + "\n" + data);
//			console.log(data);
//			$('#results_title').text(title);
			$('#results_modal').modal('show');
		});
	}
</script>