<?php echo "<?php echo \$this->Form->create('User'); ?>";?>
<fieldset>
	<legend><?php echo "<?php echo __('Login') ?>"?></legend>
	<?php echo "<?php \n
	\techo \$this->Form->input('email', array('placeholder' => 'User name'));
	\techo \$this->Form->input('password', array('placeholder' => 'Password'));
	?>";?>
</fieldset>
<?php echo "<?php echo \$this->Form->end(array('label' => __('Login'), 'class' => 'btn btn-primary')); ?>"?>
