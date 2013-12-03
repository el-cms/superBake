<?php
/**
 * Controller template for EL-CMS baking
 * 
 * This file is used during controllers generation and adds helpers and components
 * to the controller, plus the controller headers
 * 
 * This file is an updated file from cakePHP.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Classes
 * @version       0.3
 * 
 * ----
 * 
 *  This file is part of EL-CMS.
 *
 *  EL-CMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  EL-CMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *
 *  You should have received a copy of the GNU General Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
//Plugin name used in headers, with trailing slash
$com_plugin = '';
if (!empty($plugin)) {
	$com_plugin = "$pluginPath/$plugin/";
}

echo "<?php\n";
?>

/**
* app/<?php echo $com_plugin . 'Controller/' . $controllerName ?>.php
* File generated on <?php echo date('Y-m-d H:i:s'); ?> by superBake with template "<?php echo $sbc->getConfig('general.defaultTemplate'); ?>".
*
* This file contains the <?php echo $currentController ?> controller.
* 
* @copyright     Copyright 2012-<?php echo date('Y') ?>, <?php echo $sbc->getConfig('general.editorName') ?> (<?php echo $sbc->getConfig('general.editorWebsite') ?>)
* @author        <?php echo $sbc->getConfig('general.editorName') ?> <<?php echo $sbc->getConfig('general.editorEmail') ?>>
* @link          <?php echo $sbc->getConfig('general.editorWebsite') ?> <?php echo $sbc->getConfig('general.editorWebsiteName') . "\n" ?>
* @package       <?php echo $sbc->getConfig('general.basePackage') ?>/<?php echo $plugin . "\n" ?>
*
<?php
$licenseTemplate = dirname(dirname(__FILE__)) . DS . 'common' . DS . 'licenses' . DS . $sbc->getConfig('general.editorLicenseTemplate') . '.ctp';
if (file_exists($licenseTemplate)) {
	include($licenseTemplate);
} else {
	include(dirname(dirname(__FILE__)) . DS . 'common' . DS . 'licenses' . DS . 'nolicence' . '.ctp');
	$this->speak(__d('superBake', 'The license template is invalid (%s). A blank one has been used, but you should check the config file.', $sbc->getConfig('general.editorLicenseTemplate')), 'error', 0, 1);
}
?>
*/

<?php
// AppController file
echo "App::uses('{$plugin}AppController', '{$pluginPath}Controller');\n";
// Additionnal libraries
foreach ($currentControllerConfig['libraries'] as $folder => $lib) {
	echo "App::uses('$lib', '$folder');\n";
}
?>
/**
* <?php echo $controllerName; ?> Controller
*
<?php
$defaultModel = Inflector::singularize($controllerName);
echo " * @property {$defaultModel} \${$defaultModel}\n";
if (!empty($components)) {
	foreach ($components as $component) {
		echo " * @property {$component}Component \${$component}\n";
	}
}
?>
*/
class <?php echo $controllerName; ?>Controller extends <?php echo $plugin; ?>AppController {

	<?php
	// Model
	if($sbc->getConfig('plugins.'.$sbc->pluginName($plugin).".parts.$currentPart.haveModel")==false){
		echo "\t/**\n\t * Model to use\n\t * @var string\n\t */\n\tpublic \$uses=null;\n\n";
	}
	
	//Helpers
	if (count($helpers)):
		echo "/**\n * Helpers\n *\n * @var array\n */\n";
		echo "\tpublic \$helpers = array(";
		for ($i = 0, $len = count($helpers); $i < $len; $i++):
			if ($i != $len - 1):
				echo "'" . Inflector::camelize($helpers[$i]) . "', ";
			else:
				echo "'" . Inflector::camelize($helpers[$i]) . "'";
			endif;
		endfor;
		echo ");\n\n";
	endif;

	if (count($components)):
		echo "/**\n * Components\n *\n * @var array\n */\n";
		echo "\tpublic \$components = array(";
		for ($i = 0, $len = count($components); $i < $len; $i++):
			if ($i != $len - 1):
				echo "'" . Inflector::camelize($components[$i]) . "', ";
			else:
				echo "'" . Inflector::camelize($components[$i]) . "'";
			endif;
		endfor;
		echo ");\n\n";
	endif;

	echo "\t" . trim($actions) . "\n";

?>
}
