<?php
/**
 * Model template for EL-CMS baking
 * 
 * This file is used during models generation and adds custom snippets to models
 * 
 * Useful vars available in model templates :
 * ==========================================
 * $theme				Name of the theme used for generation
 * $name				Current model name
 * $projectConfig		Array of superBake configuration
 * $plugin				Current plugin name
 * $pluginPath			Plugin path (is "Plugin.", for cross plugins models)
 * $currentModelConfig	Array of config from superBake config, for the current model.
 * 
 * 
 * This file is an updated file from cakePHP.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/Console/Controllers
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
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
	$com_plugin = "Plugin/$plugin/";
}
echo "<?php\n";
?>

/**
* app/<?php echo $com_plugin . 'Model/' . $name ?>.php
* File generated on <?php echo date('Y-m-d H:i:s'); ?> by superBake with template "<?php echo $theme; ?>".
*
* This file contains the <?php echo $name ?> model.
* 
* @copyright     Copyright 2012-<?php echo date('Y') ?>, <?php echo $projectConfig['editorName'] ?> (<?php echo $projectConfig['editorWebsite'] ?>)
* @author        <?php echo $projectConfig['editorName'] ?> <?php echo $projectConfig['editorEmail'] . "\n" ?>
* @link          <?php echo $projectConfig['editorWebsite'] ?> <?php echo $projectConfig['editorWebsiteName'] . "\n" ?>
* @package       <?php echo $projectConfig['basePackage'] ?>/<?php echo $plugin . "\n" ?>
*
<?php
$licenseTemplate = dirname(dirname(__FILE__)) . DS . 'common' . DS . 'licenses' . DS . $projectConfig['editorLicenseTemplate'] . '.ctp';
if (file_exists($licenseTemplate)) {
	include($licenseTemplate);
}
?>
*/

<?php echo "App::uses('{$plugin}AppModel', '{$pluginPath}Model');\n"; ?>
/**
* <?php echo $name ?> Model
*
<?php
foreach (array('hasOne', 'belongsTo', 'hasMany', 'hasAndBelongsToMany') as $assocType) {
	if (!empty($associations[$assocType])) {
		foreach ($associations[$assocType] as $relation) {
			echo " * @property {$relation['className']} \${$relation['alias']}\n";
		}
	}
}
?>
*/
class <?php echo $name ?> extends <?php echo $plugin; ?>AppModel {

<?php if ($useDbConfig !== 'default'): ?>
	/**
	* Use database config
	*
	* @var string
	*/
	public $useDbConfig = '<?php echo $useDbConfig; ?>';

	<?php
endif;

if ($useTable && $useTable !== Inflector::tableize($name)):
	$table = "'$useTable'";
	echo "/**\n * Use table\n *\n * @var mixed False or table name\n */\n";
	echo "\tpublic \$useTable = $table;\n\n";
endif;

if ($primaryKey !== 'id'):
	?>
	/**
	* Primary key field
	*
	* @var string
	*/
	public $primaryKey = '<?php echo $primaryKey; ?>';

	<?php
endif;

if ($displayField):
	?>
	/**
	* Display field
	*
	* @var string
	*/
	public $displayField = '<?php echo $displayField; ?>';

	<?php
endif;

if (!empty($validate)):
	echo "/**\n * Validation rules\n *\n * @var array\n */\n";
	echo "\tpublic \$validate = array(\n";
	foreach ($validate as $field => $validations):
		echo "\t\t'$field' => array(\n";
		foreach ($validations as $key => $validator):
			echo "\t\t\t'$key' => array(\n";
			echo "\t\t\t\t'rule' => array('$validator'),\n";
			echo "\t\t\t\t//'message' => 'Your custom message here',\n";
			echo "\t\t\t\t//'allowEmpty' => false,\n";
			echo "\t\t\t\t//'required' => false,\n";
			echo "\t\t\t\t//'last' => false, // Stop validation after this rule\n";
			echo "\t\t\t\t//'on' => 'create', // Limit validation to 'create' or 'update' operations\n";
			echo "\t\t\t),\n";
		endforeach;
		echo "\t\t),\n";
	endforeach;
	echo "\t);\n";
endif;

foreach ($associations as $assoc):
	if (!empty($assoc)):
		?>

		//The Associations below have been created with all possible keys, those that are not needed can be removed
		<?php
		break;
	endif;
endforeach;

foreach (array('hasOne', 'belongsTo') as $assocType):
	if (!empty($associations[$assocType])):
		$typeCount = count($associations[$assocType]);
		echo "\n/**\n * $assocType associations\n *\n * @var array\n */";
		echo "\n\tpublic \$$assocType = array(";
		foreach ($associations[$assocType] as $i => $relation):
			$out = "\n\t\t'{$relation['alias']}' => array(\n";
			$out .= "\t\t\t'className' => '{$relation['className']}',\n";
			$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
			$out .= "\t\t\t'conditions' => '',\n";
			$out .= "\t\t\t'fields' => '',\n";
			$out .= "\t\t\t'order' => ''\n";
			$out .= "\t\t)";
			if ($i + 1 < $typeCount) {
				$out .= ",";
			}
			echo $out;
		endforeach;
		echo "\n\t);\n";
	endif;
endforeach;

if (!empty($associations['hasMany'])):
	$belongsToCount = count($associations['hasMany']);
	echo "\n/**\n * hasMany associations\n *\n * @var array\n */";
	echo "\n\tpublic \$hasMany = array(";
	foreach ($associations['hasMany'] as $i => $relation):
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '{$relation['className']}',\n";
		$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
		$out .= "\t\t\t'dependent' => false,\n";
		$out .= "\t\t\t'conditions' => '',\n";
		$out .= "\t\t\t'fields' => '',\n";
		$out .= "\t\t\t'order' => '',\n";
		$out .= "\t\t\t'limit' => '',\n";
		$out .= "\t\t\t'offset' => '',\n";
		$out .= "\t\t\t'exclusive' => '',\n";
		$out .= "\t\t\t'finderQuery' => '',\n";
		$out .= "\t\t\t'counterQuery' => ''\n";
		$out .= "\t\t)";
		if ($i + 1 < $belongsToCount) {
			$out .= ",";
		}
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;

if (!empty($associations['hasAndBelongsToMany'])):
	$habtmCount = count($associations['hasAndBelongsToMany']);
	echo "\n/**\n * hasAndBelongsToMany associations\n *\n * @var array\n */";
	echo "\n\tpublic \$hasAndBelongsToMany = array(";
	foreach ($associations['hasAndBelongsToMany'] as $i => $relation):
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '{$relation['className']}',\n";
		$out .= "\t\t\t'joinTable' => '{$relation['joinTable']}',\n";
		$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
		$out .= "\t\t\t'associationForeignKey' => '{$relation['associationForeignKey']}',\n";
		$out .= "\t\t\t'unique' => 'keepExisting',\n";
		$out .= "\t\t\t'conditions' => '',\n";
		$out .= "\t\t\t'fields' => '',\n";
		$out .= "\t\t\t'order' => '',\n";
		$out .= "\t\t\t'limit' => '',\n";
		$out .= "\t\t\t'offset' => '',\n";
		$out .= "\t\t\t'finderQuery' => '',\n";
		$out .= "\t\t\t'deleteQuery' => '',\n";
		$out .= "\t\t\t'insertQuery' => ''\n";
		$out .= "\t\t)";
		if ($i + 1 < $habtmCount) {
			$out .= ",";
		}
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;


/**
 * Additional are handled here.
 */
$nb = 0;
if (!empty($currentModelConfig['models'])) {
	$nb = count($currentModelConfig['models']);
}
if ($nb > 0) {
	$this->out('Number of snippets: ' . $nb);
	foreach ($currentModelConfig['models'] as $k => $v) {
		$additionnalCode = dirname(dirname(__FILE__)) . DS . 'models' . DS . str_replace('::', DS, $v) . '.ctp';
		if (file_exists($additionnalCode)) {
			include($additionnalCode);
		} else {
			include(dirname(dirname(__FILE__)) . DS . 'models' . DS . 'missing_code.ctp');
			$this->out(__d('superBake', '<warning>Snippet file "%s" was not found. Default code has been set as replacement.</warning>', $additionnalCode),1, Shell::QUIET);
		}
	}
}
?>
}
