<?php
/**
 * Register view
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 *
 * Available options from config file:
 * ===================================
 * theme.acls options:
 * -------------------
 * 	roleModel         : Role model name
 * 	roleModelPK       : Role model primary key
 * 	userModel         : User model name
 * 	userModelPK       : User model primary key
 * 	userCanChooseRole : BOOL, define if user might be able to choose his own role.
 * 	defaultRoleId     : Default role is for new users
 * 	userStatusField   : Status field
 * 	defaultUserStatus : Default status
 * 	userNameField     : Field used for authentication
 * 	userPassField     : Password field in user model
 */

/* ----------------------------------------------------------------------------
 * Options from theme : Acls
 */
$roleModel = null;
$roleModelPK = null;
$userModel = null;
$userModelPK = null;
$userCanChooseRole = null;
$defaultRoleId = null;
$userStatusField = null;
$defaultUserStatus = null;
$userNameField = null;
$userPassField = null;
// Assigning values
$themeAcl = $this->Sbc->getConfig('theme.components.Auth');
if (is_array($themeAcl)) {
	foreach ($themeAcl as $k => $v) {
		${$k} = $v;
	}
}
/* ----------------------------------------------------------------------------
 * Current template options
 */

//
// Hidden fields
if (!isset($hiddenFields) || !is_array($hiddenFields)) {
	$hiddenFields = array();
}
// Group field
if ($userCanChooseRole === false) {
	$hiddenFields[] = Inflector::underscore($roleModel) . "_$roleModelPK";
}
// Status field:
if(!empty($userStatusField)){
	$hiddenFields[] = $userStatusField;
}
// Adding password field to hidden fields, as they are "manually" added at the end
$hiddenFields[] = $userPassField;
?>

	<?php
	echo "<?php echo \$this->Form->create('$userModel'); ?>\n";
	?>
	<fieldset>
		<legend><?php echo "<?php echo ".$this->iString('General information')."; ?>"; ?></legend>
		<?php
		echo "\t<?php\n";
		foreach ($fields as $field):
			//Skipping primary key
			if (($field === $primaryKey) || in_array($field, $hiddenFields)):
				continue;
			elseif (!in_array($field, array('created', 'modified', 'updated'))):
				echo "\t\techo \$this->Form->input('{$field}');\n";
			endif;
		endforeach;
		echo "?>\n";
		?>
	</fieldset>
	<fieldset>
		<legend><?php echo "<?php echo ".$this->iString('Password')."; ?>"; ?></legend>
		<?php
		/*
		 * Password fields
		 */
		echo "<?php\n";
		echo "\t\techo \$this->Form->input('$userPassField');\n";
		echo "\t\techo \$this->Form->input('{$userPassField}_verify');\n";
		echo "?>\n";
		?>
	</fieldset>
	<?php
	echo "<?php echo \$this->Form->end(" . $this->iString('Register') . "); ?>\n";
	?>
