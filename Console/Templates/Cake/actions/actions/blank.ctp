<?php
/**
 * Empty action with only title and layout option.
 *
 * Options:
 * ========
 *  - layout                   string|null*      Alternative layout to use in order to render the view
 *  - title                    string, null*     Title for layout
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
 *
 * Action
 *
 * -------------------------------------------------------------------------- */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
*
* @return void
*/
public function <?php echo $admin . $a; ?>() {
<?php
// Support for a different layout. Look at the snippet for more info.
include $this->templatePath . 'actions/snippets/layout_support.ctp';
// Title for layout
$fieldToDisplay = (!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey';
if (isset($options['title']) && !empty($options['title'])) {
	$titleForLayout = $this->iString($options['title']);
} else {
	$titleForLayout = $this->iString('Another blank controller');
}
?>
$this->set('title_for_layout', <?php echo $titleForLayout; ?>);
}