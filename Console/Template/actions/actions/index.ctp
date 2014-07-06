<?php
/**
 * "Index" action for EL-CMS
 *
 *  * Options:
 * ========
 *  - conditions          array|null*         List of conditions for an item to be deleted
 *  - defaultSortOrder    string, 'desc'*     Default sorting order
 *  - defaultSortBy       string, null*       Default column to sort the results on.
 *  - recursiveDepth      int, 0*             Default find depth for associations
 *  - layout              string, null*       Alternative layout
 *  - title               string, null*       Title for layout
 *
 * Other:
 * ======
 *  Nothing
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
// Default sorting field
$defaultSortBy = (!isset($options['defaultSortBy'])) ? null : $options['defaultSortBy'];

// Default sorting order
$defaultSortOrder = (!isset($options['defaultSortOrder'])) ? 'desc' : $options['defaultSortOrder'];

// Default recursive find
$recursiveDepth = (!isset($options['recursiveDepth'])) ? 0 : $options['recursiveDepth'];

//// Internationalized fields (for field selections. If empty, select all fields)
//$languageFields = (!isset($options['languageFields'])) ? array() : $options['languageFields'];
// Conditions (for paginate)
$conditions = (!isset($options['conditions']) || !is_array($options['conditions'])) ? array() : $options['conditions'];

// Title for layout
$titleForLayout = (!isset($options['title'])) ? 'Existing ' . strtolower(Inflector::pluralize(Inflector::humanize(Inflector::underscore($currentModelName)))) : $options['title'];

/* ----------------------------------------------------------------------------
 * Other
 */
// field list
$fields = $modelObj->_schema;

// Pagination options:
$paginateOptions = null;

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * -------------------------------------------------------------------------- */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
*
* Basic index action
*
* @return void
*/
public function <?php echo $admin . $a ?>() {
<?php
// Support for a different layout. Look at the snippet for more info.
include $themePath . 'actions/snippets/layout_support.ctp';
?>
	$this-><?php echo $currentModelName ?>->recursive = <?php echo $recursiveDepth ?>;
	<?php
	//
	// Pagination order
	//
	if (!is_null($defaultSortBy)):
		$paginateOptions.= "\t\t\t'order' => array('$defaultSortBy' => '$defaultSortOrder'),\n";
	endif;

	// Conditions
	if (count($conditions) > 0):
		$paginateOptions.="\t\t\t'conditions' => array(\n";
		foreach ($conditions as $k => $v):
			$paginateOptions.="'$k' => " . $this->c_setFindConditions($v) . ",\n";
		endforeach;
		$paginateOptions.="\t\t\t),\n";
	endif;
	// Pagination options
	if (!empty($paginateOptions)):
		echo "\$this->paginate = array(\n" . $paginateOptions . ");\n";
	endif;
	?>
	$this->set('<?php echo $pluralName ?>', $this->paginate());
	$this->set ('title_for_layout', <?php echo $this->iString($titleForLayout) ?>);
}