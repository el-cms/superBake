<?php
//Has a register part ?
if (!isset($hasRegister)) {
	$hasRegister = true;
}
?>

<div class="row">
	<?php
	if ($hasRegister === true) {
		?>
		<div class="col-lg-6">
			<h2><?php echo "<?php echo __('Register');?>" ?></h2>
		</div>
		<?php
	}
	?>
	<div class="col-lg-<?php echo ($hasRegister === true) ? '6' : '3 col-lg-push-4'; ?>">
		<div class="well well-small ">
			<?php echo "<?php echo \$this->Form->create('User', array('role'=>'form')); ?>"; ?>
			<legend><?php echo "<?php echo __('Please, sign in');?>" ?></legend>
			<div class="form-group">
			<?php echo "<?php
			\techo \$this->Form->input('email', array('placeholder' => __('User name'), 'div'=>false, 'label'=>false, 'class'=>'form-control'));
			\techo \$this->Form->input('password', array('placeholder' => __('Password'), 'div'=>false, 'label'=>false, 'class'=>'form-control'));
			?>\n";?>
			</div>
			<?php echo "<?php echo \$this->Form->end(array('label' => __('Login'), 'class' => 'btn btn-block btn-primary')); ?>\n" ?>
		</div>
	</div>
</div>
