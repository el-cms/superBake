<?php
/**
 * This file should be included in each views. It provides support for
 * additionnal JS and CSS.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 *
 */
$out = '';
foreach ($additionnalCSS as $k => $v) {
	if ($v == true) {
		$out.= "\techo \$this->Html->css('" . $this->cleanPath($k) . "');\n";
	}
}
foreach ($additionnalJS as $k => $v) {
	if ($v == true) {
		$out.="\techo \$this->Html->script('" . $this->cleanPath($k) . "');\n";
	}
}
if (!empty($out)) {
	echo "<?php\n $out ?>";
}