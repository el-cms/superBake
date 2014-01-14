<div class="alert alert-info" data-alert="alert">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<strong><?php echo __('Info:'); ?></strong>
	<?php
	if (is_array($message)) {
		echo $this->Html->nestedList($message);
	} else {
		echo $message;
	}
	?>
</div>
