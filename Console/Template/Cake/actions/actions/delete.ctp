<?php
/**
 * "delete" action for El-CMS
 *
 * Options:
 * ========
 *  - conditions          array|null*         List of conditions for an item to be deleted
 *
 * Other:
 * ======
 *   This action has no view.
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
 *
 * Action
 *
 * --------------------------------------------------------------------------*/

?>

	/**
	 * <?php echo $admin.$a ?> method from snippet <?php echo __FILE__ ?>
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin.$a; ?>($id = null) {
		<?php
		if($this->isComponentEnabled('Auth')):
			$userId=Inflector::singularize(Inflector::tableize($this->Sbc->getConfig('theme.components.Auth.userModel'))).'_'.$this->Sbc->getConfig('theme.components.Auth.userModelPK');
			if(isset($conditions[$userId]) && $conditions[$userId]==='%self%'):
				$this->speak(__d('superBake', '  - Only the logged in user will be able to delete an item.'), 'comment');
				// Condition for a valid post:
				echo "if (\$this->{$currentModelName}->hasAny(array(\n";
				echo "\t\t\t'$currentModelName.$userId' => \$this->Session->read('Auth.".$this->Sbc->getConfig('theme.components.Auth.userModel').".".$this->Sbc->getConfig('theme.components.Auth.userModelPK')."'),\n";
				echo "\t\t\t'$currentModelName.$primaryKey'=>\$id\n";
				echo "\t\t))) {\n";
				echo "\t\t\t\$this->{$currentModelName}->$primaryKey = \$id;\n";
				echo "\t\t}\n";
			else:
				$this->speak(__d('superBake', '  - The logged in user can delete items from everyone.'), 'comment');
			endif;
		else:
			echo "\$this->$currentModelName->$primaryKey = \$id;\n";
		endif;
		?>
		if (!$this-><?php echo $currentModelName; ?>->exists()) {
			throw new NotFoundException(<?php echo $this->iString('Invalid '.strtolower($singularHumanName))?>);
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this-><?php echo $currentModelName; ?>->delete()) {
			<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)).' deleted', 'success', 'index');?>
		}else{
			<?php echo $this->setFlash(ucfirst(strtolower($singularHumanName)).' was not deleted', 'error', 'index');?>
		}
	}