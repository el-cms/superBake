<?php
/**
 * "edit" action for EL-CMS
 *
 * Options:
 * ========
 *  - layout                   string|null*      Alternative layout to use in order to render the view
 *  - hiddenAssociations       array             BelongsTo associations to hide (as user id)
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
 * --------------------------------------------------------------------------*/
// Include common options
include 'common/common_options.ctp';

/* ----------------------------------------------------------------------------
 * Current action options:
 */

// Hidden belongsTo associations
if(!isset($hiddenAssociations)){
	$hiddenAssociations=array();
}

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * --------------------------------------------------------------------------*/

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
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(<?php echo $this->iString('Invalid ' . strtolower($singularHumanName)) ?>);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			<?php
			// Check if the added item should be assigned to a given user
			if($this->isComponentEnabled('Auth')):
				$userId=Inflector::singularize(Inflector::tableize($this->Sbc->getConfig('theme.components.Auth.userModel'))).'_'.$this->Sbc->getConfig('theme.components.Auth.userModelPK');
				if(isset($conditions[$userId]) && $conditions[$userId]==='%self%'):
//					$itemIsForSelf=true;
					$this->speak(__d('superBake', '  - Edited items belongs to the logged in user.'), 'comment');
					echo "// Assigning user Id to the new ".strtolower($currentModelName)."\n";
					echo "\$this->request->data['$currentModelName']['$userId'] = \$this->Session->read('Auth.".$this->Sbc->getConfig('theme.components.Auth.userModel').".".$this->Sbc->getConfig('theme.components.Auth.userModelPK')."');\n";
				else:
					$this->speak(__d('superBake', '  - Edited items can be reassigned to a given user.'), 'comment');
				endif;
			endif;
			?>
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
				<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' has been saved', 'success', 'index'); ?>
			} else {
				<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)) . ' could not be saved', 'success', 'index'); ?>
			}
		} else {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$<?php echo lcfirst($currentModelName); ?>Data = $this-><?php echo $currentModelName; ?>->find('first', $options);
			$this->request->data = $<?php echo lcfirst($currentModelName); ?>Data;
		}
		<?php
		$fieldToDisplay = (!empty($modelObj->displayField)) ? 'displayField' : 'primaryKey';
		?>
		$this->set('title_for_layout', <?php echo $this->iString(
						'Edit ' . strtolower(Inflector::singularize(Inflector::humanize(Inflector::underscore($currentModelName)))) . ' %s',
						'$' . lcfirst($currentModelName) . "Data['$currentModelName'][\$this->${currentModelName}->$fieldToDisplay]"); ?>);
<?php
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