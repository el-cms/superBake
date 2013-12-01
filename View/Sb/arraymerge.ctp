<?php echo $this->Html->css('/sb/css/additions'); ?>
<h1>Function test <small>Sbc::updateArray($default, $defined)</small></h1>
<div class="row">
	<div class="col-lg-12">
		<?php if (!$result) { ?>
			<div class="alert alert-info">
				<p>Mark down the two arrays in yml format. The left column should contain the defaults values, and the right one contains the defined values.</p>
				<p><em>This page helps me to make the updateArray() method working, as I have misbehavours during dev time. It will be removed from the plugin later.</em></p>
			</div>
			<?php
		} else {
			echo '<pre class="pre-scrollable">' . $result . '</pre>';
		}
		?>
	</div>
</div>
<?php echo $this->Form->create(false); ?>
<div class="row">
	<div class="col-lg-6 col-md-6">
		<h2>Default values</h2>
		<textarea name="default" style="width:100%; min-height:200px; font-family: monospace"><?php if($result){echo $default;}?></textarea>
	</div>
	<div class="col-lg-6 col-md-6">
		<h2>Defined values</h2>
		<textarea name="defined" style="width:100%; min-height:200px; font-family: monospace"><?php if($result){echo $defined;}?></textarea>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 text-center">
		<p>
			<?php echo $this->Form->submit('Test !', array('class' => 'btn btn-primary')); ?>
		</p>
	</div>
</div>
<?php echo $this->Form->end(); ?>