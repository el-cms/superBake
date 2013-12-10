<?php

switch ($alertClass) {
	case 'error':
		$class = "danger";
		break;
	case 'warning':
		$class = "warning";
		break;
	case 'success':
		$class = "success";
		break;
	default:
		$class = "info";
		break;
}
$content = "<?php
	if (is_array(\$message)) {
		echo \$this->Html->nestedList(\$message);
	} else {
		echo \$message;
	}
	?>";

echo sTheme::alert($content, $alertClass, true);
