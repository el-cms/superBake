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
if(!isset($enableAcl)){
	$enableAcl=false;
}

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
		<?php if($enableAcl){ ?>
		'Acl',
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email'),
					'scope' => array('User.status' => 1)
				)
			),
			'authorize' => array(
				'Actions' => array('actionPath' => 'controllers')
			),
			// Login page
			'loginAction' => array('admin' => null, 'plugin' => null, 'controller' => 'users', 'action' => 'login'),
			// Logout page
			'logoutRedirect' => array('admin' => null, 'plugin' => null, 'controller' => 'users', 'action' => 'login'),
			// After login page
			'loginRedirect' => array('admin' => 'admin', 'plugin' => 'blog', 'controller' => 'posts', 'action' => 'index'),
		),
		<?php
		}
		?>);
	public $helpers = array(
		'Form',
		'Html',
		<?php if($enableCache){echo "'Cache',";}?>
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
		if($this->sbc->getConfig('theme.language.useLanguages')==true){
			?>
			// Language
			$curr_lang = $this->_setLanguage();
			// Change language
			$this->set('lang', $curr_lang);
			$this->set('lang_fallback', DEFAULT_LANGUAGE);
		<?php
		}
		?>
	}
	<?php
	/* ************************************************************************
	 * Language support : _setLanguage method
	 */
		if($this->sbc->getConfig('theme.language.useLanguages')==true){
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
		} // end _setLanguage
	?>
}


