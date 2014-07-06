<?php
/**
 * "edit" action for EL-CMS
 *
 * Options:
 * ========
 *  - layout                   string|null*      Alternative layout to use in order to render the view
 *  - hiddenAssociations       array             BelongsTo associations to hide (as user id)
 *  - title               string, null*       Title for layout
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

// Hidden belongsTo associations
if (!isset($hiddenAssociations)) {
	$hiddenAssociations = array();
}

// Conditions (for paginate)
$conditions = (!isset($options['conditions']) || !is_array($options['conditions'])) ? array() : $options['conditions'];

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * -------------------------------------------------------------------------- */
?>

/**
* <?php echo $admin . $a ?> method from snippet <?php echo __FILE__ ?>
*
* @throws NotFoundException
* @param string $id
* @return void
*/
public function <?php echo $admin . $a; ?>($id = null) {
<?php
// Support for a different layout. Look at the snippet for more info.
include $themePath . 'actions/snippets/layout_support.ctp';
?>
	// Searching for the <?php echo "$currentModelName\n"; ?>
	if ($this->request->is('post') || $this->request->is('put')) {
		$id=$this->request->data['<?php echo $currentModelName; ?>']['<?php echo $primaryKey; ?>'];
	}

	// Building array of conditions
	$options = array(
			'conditions' => array(
					'Post.<?php echo $primaryKey?>' => $id,
	<?php
	foreach ($conditions as $k => $v):
		echo "\t\t\t\t\t'$k' => " . $this->c_setFindConditions($v) . ",\n";
	endforeach;
	?>
					)
			);

	// Counting valid data
	if (!$this-><?php echo $currentModelName; ?>->find('count', $options)==1) {
		throw new NotFoundException(<?php echo $this->iString('Invalid ' . strtolower($singularHumanName)) ?>);
	}

	// Updating the post
	if ($this->request->is('post') || $this->request->is('put')) {
		if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' has been saved', 'success', 'index'); ?>
		} else {
			<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' could not be saved', 'success', 'index'); ?>
		}
	} else {
		$<?php echo lcfirst($currentModelName); ?>Data = $this-><?php echo $currentModelName; ?>->find('first', $options);
		$this->request->data = $<?php echo lcfirst($currentModelName); ?>Data;
	}
<?php
// Title for layout
$fieldToDisplay = (!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey';
if (isset($options['title']) && !empty($options['title'])) {
	$titleForLayout = $this->iString($options['title']);
} else {
	$titleForLayout = $this->iString('Edit ' . strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) . ' %s', '$' . lcfirst($currentModelName) . "Data['$currentModelName'][\$this->${currentModelName}->$fieldToDisplay]");
}
?>
$this->set('title_for_layout', <?php echo $titleForLayout; ?>);
<?php
// List of linked models
$compact=array();

foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
	foreach ($modelObj->{$assoc} as $associationName => $relation):
		if (!empty($associationName) && !in_array($this->_modelName($associationName), $hiddenAssociations)):
			$otherModelName = $this->_modelName($associationName);
			$otherPluralName = $this->_pluralName($associationName);
			echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
			$compact[] = "'$otherPluralName'";
		endif;
	endforeach;
endforeach;
if (!empty($compact)):
	echo "\t\t\$this->set(compact(" . join(', ', $compact) . "));\n";
endif;
?>
}