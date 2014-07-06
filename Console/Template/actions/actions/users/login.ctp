<?php
/**
 * "Login" action for EL-CMS
 *
 * Options:
 * ========
 *  - layout: string, null - Custom layout to be used for this action.
 *  - title               string, null*       Title for layout
 *
 * Other:
 * ======
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

// Title for layout
$titleForLayout = (!isset($options['title'])) ? ucfirst(Inflector::humanize(Inflector::underscore($a))) : $options['title'];
/* ----------------------------------------------------------------------------
 *
 * Action
 *
 * --------------------------------------------------------------------------*/
?>

/**
* This method logs an user in the ACL system or displays a form to log an user in.
*/
public function <?php echo $admin . $a ?>() {
<?php
// Support for a different layout. Look at the snippet for more info.
include $themePath . 'actions/snippets/layout_support.ctp';

// 'theme.enableAcl' should be true to enable this action.
if($enableAuth): ?>
	if($this->Auth->loggedIn()){
		<?php echo $this->setFlash('You are already logged in', 'info',  "'/'", array('specialUrl' => true)); ?>
	}
	if ($this->request->is('post')) {
		if ($this->Auth->login()) {
			<?php echo $this->setFlash('You are now connected', 'success', '$this->Auth->redirect()', array('specialUrl' => true)); ?>
		} else {
			<?php echo $this->setFlash('Your username or password was incorrect', 'error', $a, array('controllerName' => $controllerName)); ?>
			}
	}
<?php
else:
	// Acls not enabled, so a flash message is displayed upon the form.
	echo $this->setFlash('Acls are not enabled, you can\\\'t use this action.'
			. ' To enable Acls, set the <code>theme.components.Auth.useComponent</code> to true in your config file,'
			. ' and run superBake again.', 'error', "'/'", array('specialUrl' => true));
endif;
?>
	$this->set('title_for_layout', <?php echo $this->iString($titleForLayout)?>);
}