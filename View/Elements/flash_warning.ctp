<div class="alert alert-warning" data-alert="alert">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<strong><?php echo __('Warning:'); ?></strong>
	<?php
	if (is_array($message)) {
		echo $this->Html->nestedList($message);
	} else {
		echo $message;
	}
	?>
</div>
