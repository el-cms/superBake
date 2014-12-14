<?php
/**
 * "Logout" action for EL-CMS
 *
 * Options:
 * ========
 * No options
 *
 * Other:
 * =======
 *  - You must use the Auth component for this action to work
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

// Checks if Auth is enabled:
$enableAuth = $this->isComponentEnabled('Auth');

/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * --------------------------------------------------------------------------*/
?>

	/**
	 * This methods logs an user out of the system
	 */
	public function <?php echo $admin.$a ?>() {
<?php if($enableAuth):?>
		<?php echo $this->setFlash('You are now disconnected', 'info', "'/'", array('specialUrl' => true, 'redirect'=>false));?>
		$this->redirect($this->Auth->logout('/'));
<?php else:
		// 'theme.enableAcl' set to false, so the methods will display a flash
		// message and do nothing.
		echo "\n\t\t".$this->setFlash('Acls are not enabled, you can\\\'t use this action.'
				. ' To enable Auth, set the <code>theme.components.Auth.useComponent</code> to true in your config file,'
				. ' and run superBake again.', 'error',  "'/'", array('specialUrl' => true));
endif;?>
	}