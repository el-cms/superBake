<?php
/**
 * Controllers actions template for EL-CMS baking
 *
 * This file is used during controllers generation and adds basic CRUD actions
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

// Default recursive find
$recursiveDepth = (!isset($options['recursiveDepth'])) ? 0 : $options['recursiveDepth'];

// Internationalized fields (for field selections. If empty, select all fields)
$languageFields = (!isset($options['languageFields'])) ? array() : $options['languageFields'];

// Conditions (for paginate)
$conditions = (!isset($options['conditions'])) ? array() : $options['conditions'];

/* ----------------------------------------------------------------------------
 * Actual action
 */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
*
* @throws NotFoundException
* @param string $id
* @return void
*/
public function <?php echo $admin . $a ?>($id = null) {
if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
throw new NotFoundException(<?php echo $this->iString('Invalid ' . strtolower($singularHumanName)) ?>);
}
<?php
// Fields
$findFields='';
if ($this->Sbc->getConfig('theme.language.useLanguages') === true) {
	// Language fields should be set in config, so we check
	if (count($languageFields) === 0) {
		$this->speak(__d('superBake', ' - No languageField defined. All fields will be returned.'), 'warning');
	} else {
		?>
		$lang = Configure::read('Config.language');
		$fallback = Configure::read('website.defaultLang');
		<?php
		$findFields.="\t\t\t'fields' => array(\n\t\t\t\t// Fallback\n";
		foreach ($languageFields as $l) {
			'_' . $this->Sbc->getConfig('theme.language.fallback');
			$findFields.="\t\t\t\t'$currentModelName.{$l}_' . \$fallback . ' as {$l}_default',\n";
			$findFields.="\t\t\t\t'$currentModelName.{$l}_' . \$lang . ' as $l',\n";
			foreach ($this->Sbc->getConfig('theme.language.available') as $lang) {
				unset($fields[$l . '_' . $lang]);
			}
		}
		// Other fields:
		$findFields.="\t\t\t\t// Other fields\n";
		foreach ($fields as $f => $fConfig) {
			$findFields.="\t\t\t\t'$currentModelName.$f',\n";
		}
		// Related fields
		$findFields.="\t\t\t// BelongsTo fields\n";
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

			$findFields.="\t\t\t\t'$fModel.$primaryKey',\n";
			$findFields.="\t\t\t\t'$fModel.$displayField',\n";
		}
		$findFields.="\t\t\t),\n";
	}
}
// Conditions
$findConditions = '';
if (count($conditions) > 0) {
	foreach ($conditions as $k => $v) {
		$findConditions.="'$k' => " . stheme::c_indexConditions($v) . ",\n";
	}
}
?>
$options = array(
	'conditions' => array(
		'<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id,
		<?php echo $findConditions?>
		),
		<?php echo $findFields;?>);
$<?php echo lcfirst($currentModelName); ?>Data = $this-><?php echo $currentModelName; ?>->find('first', $options);
$this->set('<?php echo $singularName; ?>', $<?php echo lcfirst($currentModelName); ?>Data);
$this->set('title_for_layout', <?php echo $this->iString(ucfirst(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) . ': %s', '$' . lcfirst($currentModelName) . "Data['$currentModelName'][\$this->${currentModelName}->" . ((!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey') . "]"); ?>);
}