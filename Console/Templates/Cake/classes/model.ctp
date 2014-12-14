<?php
/**
 * Model template for EL-CMS baking
 *
 * This file is used during models generation and adds custom snippets to models
 *
 * Options available for model baking:
 *  - actsAs        (array)
 *  - useDbConfig   (string)
 *  - useTable      (string)
 *  - virtualFields (array)
 *  - displayField  (string)
 *  - tablePrefix   (string)
 *  - recursive     (int)
 *  - order         (mixed)
 *  - name          (string)
 *  - cacheQueries  (bool)
 *
 * Models will act as Container if they have associati
 *
 * Options to implement: $data, must check this option.
 *
 * More on model attributes can be found in the CakePHP doc:
 * http://book.cakephp.org/2.0/en/models/model-attributes.html
 *
 * ---
 * This file is an updated file from cakePHP.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Elabs.Classes
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

//
// Preparing the 'actAs' array
if (empty($actsAs)) {
	$actsAs = array();
}

//
// Preparing the 'virtualFields' array
if (empty($virtualFields)) {
	$virtualFields = array();
}

// Containable behaviour
if (!empty($associations['hasMany'])) {
	$actsAs['Containable'] = array();
}


echo "<?php\n";

//
// File headers
//
echo "/**\n"
 . " * {$com_plugin}/Model/$name.php\n"
 . " * File generated on " . date('Y-m-d H:i:s') . " by superBake.\n"
 . " *\n"
 . " * This file contains the $name model.\n"
 . " *\n"
 . " * @copyright     Copyright 2012-" . date('Y') . ", " . $this->Sbc->getConfig('general.editorName') . " (" . $this->Sbc->getConfig('general.editorWebsite') . ")\n"
 . " * @author        " . $this->Sbc->getConfig('general.editorName') . "<" . $this->Sbc->getConfig('general.editorEmail') . ">\n"
 . " * @link          " . $this->Sbc->getConfig('general.editorWebsite') . " " . $this->Sbc->getConfig('general.editorWebsiteName') . "\n"
 . " * @package       " . $this->Sbc->getConfig('general.basePackage') . "/$plugin\n"
 . " *\n\n";

//
// Adding license
//
$licenseTemplate = dirname(dirname(__FILE__)) . DS . 'common' . DS . 'licenses' . DS . $this->Sbc->getConfig('general.editorLicenseTemplate') . '.ctp';
if (file_exists($licenseTemplate)):
	include $licenseTemplate;
else:
	include dirname(dirname(__FILE__)) . DS . 'common' . DS . 'licenses' . DS . 'nolicence' . '.ctp';
	$this->speak(__d('superBake', 'The license template is invalid (%s). A blank one has been used, but you should check the config file.', $projectConfig['general']['editorLicenseTemplate']), 'warning', 1, 2);
endif;
?>
*/

<?php
echo "App::uses('{$plugin}AppModel', '{$pluginPath}Model');\n";

//
// Class description
//
echo "/**\n * $name Model\n *\n";
foreach (array('hasOne', 'belongsTo', 'hasMany', 'hasAndBelongsToMany') as $assocType):
	if (!empty($associations[$assocType])):
		foreach ($associations[$assocType] as $relation):
			echo " * @property {$relation['className']} \${$relation['alias']}\n";
		endforeach;
	endif;
endforeach;
echo " */\n";

//
// Class definition
//
echo "class $name extends {$plugin}AppModel {\n";

//
// ActsAs configuration
//
if (!empty($actsAs)):
	echo "\t/**\n\t * Behaviour modifiers\n\t *\n\t * @var array\n\t */\n";
	echo "\tpublic \$actsAs=array(\n";
	foreach ($actsAs as $behaviour => $behaviourOptions):
		echo "\t\t'$behaviour' => " . $this->displayArray($behaviourOptions) . ",\n";
	endforeach;
	echo ");\n\n";
endif;

//
// useDBConfig
//
if ($useDbConfig !== 'default'):
	echo "\t/**\n\t * Use database config\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$useDbConfig = '$useDbConfig';\n\n";
endif;

//
// useTable
//
if ($useTable && $useTable !== Inflector::tableize($name)):
	$table = "'$useTable'";
	echo "/**\n * Use table\n *\n * @var mixed False or table name\n */\n";
	echo "\tpublic \$useTable = $table;\n\n";
endif;

//
// Primary key
//
if ($primaryKey !== 'id'):
	echo "\t/**\n\t	* Primary key field\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$primaryKey = '$primaryKey';\n\n";
endif;

//
// virtualFields
//
if (!empty($virtualFields)):
	echo "\t/**\n\t * Virtual fields\n\t *\n\t * @var array\n\t */\n";
	echo "\tpublic \$virtualFields=array(\n";
	echo $this->displayArray($virtualFields);
	echo ");\n\n";
endif;

//
// Display field
//
if (!empty($displayField)):
	echo "/**\n\t	* Display field\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$displayField = '$displayField';\n\n";
endif;

//
// Table prefix
//
if (!empty($tablePrefix)):
	echo "/**\n\t	* Table prefix, override the value from 'app/Config/bootstrap.php' for this model only.\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$tablePrefix = '$tablePrefix';\n\n";
endif;

//
// Recursive
//
if (!empty($recursive)):
	echo "/**\n\t	* Fetch association level.\n\t	*\n\t	* @var int\n\t	*/\n";
	echo "\tpublic \$recursive = $recursive;\n\n";
endif;

//
// Model name
//
if (!empty($name)):
	echo "/**\n\t	* Model name.\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$name = '$name';\n\n";
endif;

//
// Queries cache
//
if (!empty($cacheQueries)):
	echo "/**\n\t	* Cache the queries if set to true.\n\t	*\n\t	* @var string\n\t	*/\n";
	echo "\tpublic \$cacheQueries = '$cacheQueries';\n\n";
endif;

//
// Order
//
if (!empty($order)):
	echo "/**\n\t	* Default ordering.\n\t	*\n\t	* @var mixed (string|array)\n\t	*/\n";
	if (is_array($order)) {
		echo "\tpublic \$order = " . $this->displayArray($order) . ";\n\n";
	} else {
		echo "\tpublic \$order = '$order';\n\n";
	}
endif;

//
// Validate array
//
if (!empty($validate)):
	echo "/**\n * Validation rules\n *\n * @var array\n */\n";
	echo "\tpublic \$validate = array(\n";
	foreach ($validate as $field => $validations):
		echo "\t\t'$field' => array(\n";
		if ($name === 'User'):
			switch ($field):
				case 'username':
					// Unique
					echo "\t\t\t'isUnique' => array(\n";
					echo "\t\t\t\t'rule' => array('isUnique'),\n";
					echo "\t\t\t\t'message' => 'Username already taken. Please choose another one.',\n";
					echo "\t\t\t),\n";
					break;

				case 'password':
					// Same passwords
					echo "\t\t\t'same' => array(\n";
					echo "\t\t\t\t'rule' => array('comparePwd'),\n";
					echo "\t\t\t\t'message' => 'Passwords missmatch.',\n";
					echo "\t\t\t),\n";
					break;

				case 'email':
					// Unique
					echo "\t\t\t'isUnique' => array(\n";
					echo "\t\t\t\t'rule' => array('isUnique'),\n";
					echo "\t\t\t\t'message' => 'Email address already in use. Please choose another one.',\n";
					echo "\t\t\t),\n";
				default:
					break;
			endswitch;
		endif;
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
?>

//
// The Associations below have been created with all possible keys, those that are not needed can be removed
//
<?php
foreach (array('hasOne', 'belongsTo') as $assocType):
	if (!empty($associations[$assocType])):
		$typeCount = count($associations[$assocType]);
		echo "\n/**\n * $assocType associations\n *\n * @var array\n */";
		echo "\n\tpublic \$$assocType = array(";
		foreach ($associations[$assocType] as $i => $relation):
			$relationPlugin = $this->Sbc->getModelPlugin($relation['className']);
			if ($relationPlugin != $this->Sbc->getAppBase() && !empty($relationPlugin)):
				$relationFullName = $relationPlugin . '.' . $relation['className'];
			else:
				$relationFullName = $relation['className'];
			endif;
			$out = "\n\t\t'{$relation['alias']}' => array(\n";
			$out .= "\t\t\t'className' => '$relationFullName',\n";
			$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
			$out .= "\t\t\t'conditions' => '',\n";
			$out .= "\t\t\t'fields' => '',\n";
			$out .= "\t\t\t'order' => ''\n";
			$out .= "\t\t)";
			if ($i + 1 < $typeCount):
				$out .= ",";
			endif;
			echo $out;
		endforeach;
		echo "\n\t);\n";
	endif;
endforeach;

//Has many
if (!empty($associations['hasMany'])):
	$belongsToCount = count($associations['hasMany']);
	echo "\n/**\n * hasMany associations\n *\n * @var array\n */";
	echo "\n\tpublic \$hasMany = array(";
	foreach ($associations['hasMany'] as $i => $relation):
		$relationPlugin = $this->Sbc->getModelPlugin($relation['className']);
		if ($relationPlugin != $this->Sbc->getAppBase()):
			$relationFullName = $relationPlugin . '.' . $relation['className'];
		else:
			$relationFullName = $relation['className'];
		endif;
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '$relationFullName',\n";
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
		if ($i + 1 < $belongsToCount):
			$out .= ",";
		endif;
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;

if (!empty($associations['hasAndBelongsToMany'])):
	$habtmCount = count($associations['hasAndBelongsToMany']);
	echo "\n/**\n * hasAndBelongsToMany associations\n *\n * @var array\n */";
	echo "\n\tpublic \$hasAndBelongsToMany = array(";
	foreach ($associations['hasAndBelongsToMany'] as $i => $relation):
		$relationPlugin = $this->Sbc->getModelPlugin($relation['className']);
		if ($relationPlugin != $this->Sbc->getAppBase()):
			$relationFullName = $relationPlugin . '.' . $relation['className'];
		else:
			$relationFullName = $relation['className'];
		endif;
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '$relationFullName',\n";
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
		if ($i + 1 < $habtmCount):
			$out .= ",";
		endif;
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;

echo ("//\n// Your snippets are included below\n//\n");
/**
 * Additional snippets are handled here.
 */
foreach ($modelConfig['snippets'] as $snippet => $snippetConfig):
	echo "\n//Snippet \"$snippet\":\n";
	// making options available:
	$options = $snippetConfig['options'];
	// Loading the file
	$additionnalCode = dirname(dirname(__FILE__)) . DS . 'models' . DS . $this->cleanPath($snippetConfig['path']) . '.ctp';
	// Checking
	if (file_exists($additionnalCode)):
		include $additionnalCode;
	else:
		include dirname(dirname(__FILE__)) . DS . 'models' . DS . 'missing_code.ctp';
		$this->speak(__d('superBake', 'Snippet file "%s" was not found. Default code has been set as replacement.', $additionnalCode), 'warning', 0);
	endif;
endforeach;
?>
}
