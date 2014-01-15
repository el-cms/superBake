<?php
/**
 * Controllers actions template for EL-CMS baking
 *
 * This file is used during controllers generation and adds basic "index" action
 * to the controllers.
 *
 * This file is an updated file from cakePHP.
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
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
/* ----------------------------------------------------------------------------
 * Current template options
 */
// Default sorting field
$defaultSortBy = (!isset($options['defaultSortBy'])) ? null : $options['defaultSortBy'];

// Default sorting order
$defaultSortOrder = (!isset($options['defaultSortOrder'])) ? 'desc' : $options['defaultSortOrder'];

// Default recursive find
$recursiveDepth = (!isset($options['recursiveDepth'])) ? 0 : $options['recursiveDepth'];

// Internationalized fields (for field selections. If empty, select all fields)
$languageFields = (!isset($options['languageFields'])) ? array() : $options['languageFields'];

// Conditions (for paginate)
$conditions = (!isset($options['conditions']) || !is_array($options['conditions'])) ? array() : $options['conditions'];

/* ----------------------------------------------------------------------------
 * Other
 */
// field list
$fields = $modelObj->_schema;

// Pagination options:
$paginateOptions = null;

/* ----------------------------------------------------------------------------
 * Actual action
 */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
*
* Basic index action
*
* @return void
*/
public function <?php echo $admin . $a ?>() {
$this-><?php echo $currentModelName ?>->recursive = <?php echo $recursiveDepth ?>;
<?php
if ($this->Sbc->getConfig('theme.language.useLanguages') === true) {
	// Language fields should be set in config, so we check
	if (count($languageFields) === 0) {
		$this->speak(__d('superBake', ' - No languageField defined. All fields will be returned.'), 'warning');
	} else {
		?>
		$lang = Configure::read('Config.language');
		$fallback = Configure::read('website.defaultLang');
		<?php
		$paginateOptions.="\t\t\t'fields' => array(\n\t\t\t\t// Fallback\n";
		foreach ($languageFields as $l) {
			'_' . $this->Sbc->getConfig('theme.language.fallback');
			$paginateOptions.="\t\t\t\t'$currentModelName.{$l}_' . \$fallback . ' as {$l}_default',\n";
			$paginateOptions.="\t\t\t\t'$currentModelName.{$l}_' . \$lang . ' as $l',\n";
			foreach ($this->Sbc->getConfig('theme.language.available') as $lang) {
				unset($fields[$l . '_' . $lang]);
			}
		}
		// Other fields:
		$paginateOptions.="\t\t\t\t// Other fields\n";
		foreach ($fields as $f => $fConfig) {
			$paginateOptions.="\t\t\t\t'$currentModelName.$f',\n";
		}
		// Related fields
		$paginateOptions.="\t\t\t// BelongsTo fields\n";
		foreach ($modelObj->belongsTo as $f => $fConfig) {
			// Finding fields params:
			$fElements = explode('.', $fConfig['className']);
			// Plugin and model
			if (count($fElements) > 1) {
				$fPlugin = $fElements[0] . '.';
				$fModel = $fElements[1];
			} else {
				$fPlugin = null;
				$fModel = $fElements[0];
			}

			// Fields
			App::uses($fModel, $fPlugin . 'Model');
			if (!class_exists($fModel)) {
				$this->err(__d('superBake', 'You should already have baked the controller dependencies (linked models) to build this method with the current options set. Please try again.'));
				$this->_stop();
			}
			$lModel = ClassRegistry::init($fModel);
			$displayField = $lModel->displayField;
			$primaryKey = $lModel->primaryKey;

			$paginateOptions.="\t\t\t\t'$fModel.$primaryKey',\n";
			$paginateOptions.="\t\t\t\t'$fModel.$displayField',\n";
		}
		$paginateOptions.="\t\t\t),\n";
	}
}
// Pagination order
if (!is_null($defaultSortBy)) {
	$paginateOptions.= "\t\t\t'order'=>array('$defaultSortBy' => '$defaultSortOrder'),\n";
}

// Conditions
if (count($conditions) > 0) {
	$paginateOptions.="\t\t\t'conditions'=>array(\n";
	foreach ($conditions as $k => $v) {
		$paginateOptions.="'$k' => " . stheme::c_indexConditions($v) . ",\n";
	}
	$paginateOptions.="\t\t\t),\n";
}
// Pagination options
if (!empty($paginateOptions)) {
	echo "\$this->paginate=array(\n" . $paginateOptions . ");\n";
}
?>
$this->set('<?php echo $pluralName ?>', $this->paginate());
$this->set('title_for_layout', <?php echo $this->iString('Existing ' . strtolower(Inflector::pluralize(Inflector::humanize(Inflector::underscore($currentModelName))))) ?>);
}