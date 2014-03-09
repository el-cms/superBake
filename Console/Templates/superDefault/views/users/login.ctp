<?php
/**
 * Login view
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 */

/* ----------------------------------------------------------------------------
 * Some options from theme : Acls
 */
$userNameField = $this->Sbc->getConfig('theme.acls.userNameField');
$userPassField = $this->Sbc->getConfig('theme.acls.userPassField');
$userModel = $this->Sbc->getConfig('theme.acls.userModel');
?>

<?php echo "<?php echo \$this->Form->create('$userModel'); ?>"; ?>
	<fieldset>
		<?php echo "<?php
		\techo \$this->Form->input('$userNameField', array('placeholder' => ".$this->iString(ucfirst(inflector::humanize($userNameField)))."));
		\techo \$this->Form->input('$userPassField', array('placeholder' => ".$this->iString(ucfirst(inflector::humanize($userPassField)))."));
		?>\n";?>
	</fieldset>
<?php echo "<?php echo \$this->Form->end(array('label' => ".$this->iString('Log me in').")); ?>\n" ?>
