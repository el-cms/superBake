<?php

/**
 * This menu just lists the pages in the "app/views/pages" folder.
 * 
 * It accepts an array of options, accessible via $options['key'], where 'key' can be:
 * - langSupport (true/false). If true, will only list the pages that ends with a _lang string
 * 		ie: welcome_eng.ctp/welcome_fra.ctp
 * If you use the lang support, create a menu that uses the langSelector template (will change the site lang)
 * 
 * 
 * 
 */
// Directory
//$pagesFolder=APP_DIR.DS.'Views'.DS.'Pages'.DS;
$pagesFolder = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DS . 'View' . DS . 'Pages' . DS;
// File list
$files = array();

// Check folder presence
if (!is_dir($pagesFolder)) {
	$this->speak(__d('superBake', 'The "%s" folder does not exists', $pagesFolder), 'error', 0);
	echo '<li><a href="#" class="text-error">ERROR</a></li>';
} else {
	$dir = opendir($pagesFolder);
	while ($file = readdir($dir)) {
		if ($file != '.' && $file != '..') {
			$files[] = $file;
		}
	}
	//@todo Support for langages
	if ($options['langSupport'] == 1) {
		
	} else {
		foreach ($files as $file) {
			$name=str_replace('.ctp', '', $file);
			echo '<li>
			<?php echo $this->Html->link('.$this->display(inflector::humanize($name), null, $projectConfig['general']['appBase']).','.$this->url('display', 'pages', "'$name'").');?>
			</li>'."\n";
		}
	}
}