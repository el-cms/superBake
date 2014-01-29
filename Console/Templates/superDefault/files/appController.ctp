<?php
/**
 * AppController template
 */

//
// Options
//

// Enable the cache : Default is false
if(!isset($enableCache)){
	$enableCache=false;
}

// Enable Acls: default is false
$enableAcl=$this->Sbc->getConfig('theme.enableAcl');

// enableDebugKit: default is false
if(!isset($enableDebugKit)){
	$enableDebugKit=false;
}
// Components

?>

<?php echo "<?php\n"; ?>
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 */
class AppController extends Controller {

	public $cacheAction;
	public $components = array(
		<?php echo ($enableDebugKit)?"'DebugKit.Toolbar',\n":"// DebugKit is disabled\n\t\t//'DebugKit.Toolbar',\n";?>
		'Session',
		<?php if($enableAcl): ?>
		'Acl',
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email'),
					<?php
					$userStatusField=$this->Sbc->getConfig('theme.acls.userStatusField');
					if(!empty($userStatusField)):
						?>
					'scope' => array('<?php echo $this->Sbc->getConfig('theme.acls.userModel').".$userStatusField"?>' => <?php echo $this->Sbc->getConfig('theme.acls.validUserStatus'); ?>)
					<?php
					endif;
					?>

				)
			),
			'authorize' => array(
				'Actions' => array('actionPath' => 'controllers')
			),
			// Login page
			<?php
				// Infos on the login page:
				$userModel = $this->Sbc->getConfig('theme.acl.userModel');
				$userController = Inflector::underscore($this->Sbc->getConfig('theme.acl.userModel'));
				$userPlugin = $this->getControllerPluginName($userController);
				$userPlugin = ($userPlugin === null) ? "null" : "'$userPlugin'";
				?>
			'loginAction' => array('admin' => null, 'plugin' => <?php echo $userPlugin;?>, 'controller' => '<?php echo $userController; ?>', 'action' => 'login'),
			// Logout page
			//'logoutRedirect' => array('admin' => null, 'plugin' => null, 'controller' => 'users', 'action' => 'login'),
			'logoutRedirect' => '/',
			// After login page
			//'loginRedirect' => array('admin' => 'users', 'plugin' => 'blog', 'controller' => 'posts', 'action' => 'index'),
			'loginRedirect' => '/',
		),
		<?php
		endif;
		?>);
	public $helpers = array(
		'Form',
		'Html',
		<?php if($enableCache):
			echo "'Cache',";
		endif;?>
	);

	public function beforeFilter() {
		parent::beforeFilter();

		// Making current plugin available in view
		$this->set('current_plugin', $this->request->params['plugin']);
		// Making current controller available in view
		$this->set('current_controller', $this->request->params['controller']);

		// Changes for admin
		if (isset($this->request->params['prefix']) && $this->request->params['prefix'] === 'admin'<?php echo ($enableAcl)?' && $this->Auth->loggedIn()':''?>) {
			// Layout change
			$this->layout = 'admin';
			<?php
			echo ($enableAcl)?"\t\t\t// Default for admins: allow everything\n"
				. "\t\t\t\$this->Auth->allow();\n"
							:"// Acls are disabled. Set 'enableAcl: true' in options for this file.\n";
			?>
		} else {
			<?php
				echo ($enableCache)?"\t\t\t\t//Cache all action for 1 hour\n"
				. "\t\t\t\$this->cacheAction = '1 hour';\n"
								:"// Cache disabled. Set 'enableCache: true' in options for this file if you want it\n";
			echo ($enableAcl)?"\t\t\t// Some available actions for public prefix.\n"
				. "\t\t\t\$this->Auth->allow('index', 'view', 'login', 'logout', 'display', 'register');\n"
							:"\t\t\t// Acls are disabled. Set 'enableAcl: true' in options for this file.\n";
			?>
		}
		<?php
		/* ************************************************************************
		 * Language support: define current page language.
		 */
		if($this->Sbc->getConfig('theme.language.useLanguages') === true):
			?>
			// Language
			$curr_lang = $this->_setLanguage();
			// Change language
			$this->set('lang', $curr_lang);
			$this->set('lang_fallback', DEFAULT_LANGUAGE);
		<?php
		endif;
		?>
	}
	<?php
	/* ************************************************************************
	 * Language support : _setLanguage method
	 */
		if($this->Sbc->getConfig('theme.language.useLanguages') === true):
			?>
			private function _setLanguage() {
				// Available languages
				$langs = array_keys(Configure::read('Config.languages'));
				$newLang = DEFAULT_LANGUAGE;
				// Checking if new language has been set
				if (isset($this->request['language'])) {
					$newLang = $this->request['language'];
					if (!in_array($newLang, $langs)) {
						$newLang = DEFAULT_LANGUAGE;
					}
				}
				// Checking for passed parameters
				if (isset($this->request->params['named']['language'])) {
					$newLang = $this->request->params['named']['language'];
					if (!in_array($newLang, $langs)) {
						$newLang = DEFAULT_LANGUAGE;
					}
				}

				Configure::write('Config.language', $newLang);
				$this->Session->write('Config.language', $newLang);
				return $newLang;
			}
		<?php
		endif; // end _setLanguage
	?>
}


