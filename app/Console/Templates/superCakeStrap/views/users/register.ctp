<?php echo "<?php echo \$this->Form->create('User'); ?>"?>
<?php echo "<?php echo \$this->Html->script('bootstrap-datetimepicker.min.js')?>";?>
<fieldset>
	<legend><?php echo "<?php __('Register' )?>"?></legend>
		<?php echo "<?php\n
		\techo \$this->Form->input('username', array('placeholder'=>__('User Name')));
		\techo \$this->Form->input('password', array('placeholder'=>__('Password')));
		\techo \$this->Form->input('password2', array('label' => __('Please confirm you pasword:'), 'type'=>'password', 'placeholder'=>__('Password (again)'), 'required'=>true));
		\techo \$this->Form->input('email', array('placeholder'=>__('Email')));
	\t?>"?>
</fieldset>
<?php echo "<?php echo \$this->Form->end(array('label'=>__('Submit'), 'class'=>'btn btn-primary')); ?>"?>
