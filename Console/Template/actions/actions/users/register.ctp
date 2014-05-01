<?php
/**
 * "Register" action for EL-CMS
 *
 * Options:
 * ========
 *  - layout: string, null
 *  - From theme.components.Auth:
 *    - userStatusField: string
 *    - defaultStatus: string|int
 *    - userCanChooseRole: bool
 *    - defaultRoleId: string|int
 *
 * Other:
 * =======
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
 * --------------------------------------------------------------------------*/
// Include common options
include dirname(__FILE__).'/../common/common_options.ctp';

/* ----------------------------------------------------------------------------
 * Current action options:
 */

// User status field
$userStatusField = $this->Sbc->getConfig('theme.components.Auth.userStatusField');
// Default user status
$defaultStatus = $this->Sbc->getConfig('theme.components.Auth.defaultUserStatus');
// Ability for an user to choose his role
$userCanChooseRole = $this->Sbc->getConfig('theme.components.Auth.userCanChooseRole');
// Default role id for new users
$defaultRoleId=$this->Sbc->getConfig('theme.components.Auth.defaultRoleId');

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * --------------------------------------------------------------------------*/
?>

/**
 * <?php echo $admin.$a ?> method
 *
 * @return void
 */
public function <?php echo $admin.$a ?>() {
	<?php
	// Support for a different layout. Look at the snippet for more info.
	include $themePath . 'actions/snippets/layout_support.ctp';
	?>
	if ($this->request->is('post')) {
		$this-><?php echo $currentModelName; ?>->create();
		$this->request->data['<?php echo $currentModelName ?>']['group_id'] = <?php echo $defaultRoleId;?>;
		<?php
		// Updating user status
		if(!empty($userStatusField)):
			echo "\$this->request->data['$currentModelName']['$userStatusField'] = $defaultStatus;\n";
		endif;
		?>
		if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			<?php
			// Success message depends on the user default status, if any:
			// Use status in table and status is true, or don't use status
			if((!empty($defaultStatus) && ($defaultStatus == 1 ||$defaultStatus === true)) || empty($defaultStatus)):
				echo $this->setFlash('Your account has been successfully created. Please log in', 'success', 'login');
			else:
				echo $this->setFlash('Your account has been successfully created and is waiting for moderation.', 'success', 'login');
			endif;
			?>
		} else {
			<?php echo $this->setFlash('Your account could not be created. Please try again', 'error', $a);?>
		}
	}
<?php
/**
 * Fetching associations to make them available for the view
 *
 * @todo add an option in config to hide some associations (ex: groups)
 */
foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
	foreach ($modelObj->{$assoc} as $associationName => $relation):
		if (!empty($associationName)):
			$otherModelName = $this->_modelName($associationName);
			$otherPluralName = $this->_pluralName($associationName);
			// If user can't choose his group, disable it.
			if ($userCanChooseRole === true || ($userCanChooseRole === false && $otherModelName != $this->Sbc->getConfig('theme.acls.roleModel'))):
					echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
					$compact[] = "'{$otherPluralName}'";
				endif;
			endif;
	endforeach;
endforeach;
if (!empty($compact)):
	echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
endif;
?>
	$this->set('title_for_layout', <?php echo $this->iString($a)?>);
}
