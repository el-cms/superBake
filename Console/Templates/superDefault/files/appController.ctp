<?php
/**
 * AppController template
 */

//
// Options
//

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
//		'DebugKit.Toolbar',
		'Session',
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
			'logoutRedirect' => array('admin' => 'admin', 'plugin' => null, 'controller' => 'users', 'action' => 'login'),
			// After login page
			'loginRedirect' => array('admin' => 'admin', 'plugin' => 'blog', 'controller' => 'posts', 'action' => 'index'),
		),);
	public $helpers = array(
		'Form',
		'Html',
		//'Cache',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		// website config
		//$this->set('_website', Configure::read('website'));
		// current plugin
		$this->set('current_plugin', $this->request->params['plugin']);
		// Current controller
		$this->set('current_controller', $this->request->params['controller']);

		// Layout change for admin
		if (isset($this->request->params['prefix']) && $this->request->params['prefix'] === 'admin' && $this->Auth->loggedIn()) {
			$this->layout = 'admin';
			// Message count for message board
			//$this->loadModel('Messages');
			//$this->set('messageCount', $this->Messages->find('count', array('conditions' => array('read' => 0))));
			$this->Auth->allow();
		} else {
			//Cache all action for 1 hour
			//$this->cacheAction = "1 hour";
			//$this->Auth->allow('index', 'view', 'login', 'display');
			$this->Auth->allow();
		}


		//// Language
		//$curr_lang = $this->_setLanguage();
		//// Change language
		//$this->set('lang', $curr_lang);
		//$this->set('lang_fallback', DEFAULT_LANGUAGE);
	}

	/**
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
	}*/

}


