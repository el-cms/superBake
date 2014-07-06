<?php
/**
 * "View" action for El-CMS
 *
 * Options:
 * ========
 *  - recursiveDepth      int, 0*             Default find depth for associations
 *  - conditions          array|null*         List of conditions for an item to be deleted
 *  - layout              string, null*       Alternative layout
 *  - title               string, null*       Title for layout
 *
 * Other:
 * ======
 *  Have support for containers
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Actions
 * @version       0.3
 */
/* ----------------------------------------------------------------------------
 *
 * Options
 *
 * -------------------------------------------------------------------------- */
// Include common options
include 'common/common_options.ctp';

/* ----------------------------------------------------------------------------
 * Current action options:
 */
// Default recursive find
$recursiveDepth = (!isset($options['recursiveDepth'])) ? 0 : $options['recursiveDepth'];

// Hidden associations
if (!isset($hiddenAssociations) || !is_array($hiddenAssociations)) {
	$hiddenAssociations = array();
}

// Conditions (for paginate)
$conditions = (!isset($options['conditions'])) ? array() : $options['conditions'];

// Containable conditions:
$containsConditions = (!isset($options['containConditions'])) ? array() : $options['containConditions'];
$contain = $this->c_findContains($modelObj, array(
		'conditions' => $containsConditions,
		'hiddenAssociations' => $hiddenAssociations,
				));

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * -------------------------------------------------------------------------- */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>.
*
* @throws NotFoundException
* @param string $id
* @return void
*/
public function <?php echo $admin . $a ?>($id = null) {
<?php
// Support for a different layout. Look at the snippet for more info.
include $themePath . 'actions/snippets/layout_support.ctp';

	// Fields
	$findFields = '';
	// Conditions
	$findConditions = '';
	if (count($conditions) > 0):
		foreach ($conditions as $k => $v):
			$findConditions.="'$k' => " . $this->c_setFindConditions($v) . ",\n";
		endforeach;
	endif;

	// "Contain" section:
	if(count($contain) > 0){
		$containString = "'contain' => ".$this->displayArray($contain).',';
	}
?>
$options = array(
'conditions' => array(
'<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id,
<?php echo $findConditions ?>
),
<?php echo $containString; ?>
<?php echo $findFields; ?>);
$<?php echo lcfirst($currentModelName); ?>Data = $this-><?php echo $currentModelName; ?>->find('first', $options);
if (empty($<?php echo lcfirst($currentModelName); ?>Data)) {
throw new NotFoundException(<?php echo $this->iString('Invalid ' . strtolower($singularHumanName)) ?>);
}
$this->set('<?php echo $singularName; ?>', $<?php echo lcfirst($currentModelName); ?>Data);
<?php
$fieldToDisplay = (!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey';
// Title for layout
if (isset($options['title']) && !empty($options['title'])) {
	$titleForLayout = $this->iString($options['title']);
} else {
	$titleForLayout = $this->iString(ucfirst(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) . ": %s", "$" . lcfirst($currentModelName) . "Data['$currentModelName'][\$this->${currentModelName}->$fieldToDisplay]");
}
?>
$this->set('title_for_layout', <?php echo $titleForLayout; ?>);
}
