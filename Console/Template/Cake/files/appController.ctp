<?php
/**
 * AppController template
 *
 * Options:
 * ========
 *  Options from theme:
 *   - theme.components
 *   - theme.helpers
 *   - theme.components.AuthComponent
 *   - theme.language
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Files
 * @version       0.3
 */
//
// Options
//

// Components list
$appComponents = $this->Sbc->getConfig('theme.components');
// Helpers list
$appHelpers = $this->Sbc->getConfig('theme.helpers');

// Verify is Auth is enabled
$enableAuth = $this->isComponentEnabled('Auth');

// Verify if cache should be used
$enableCache = in_array('Cache', $appHelpers);

// Verify if language support is activated:
$enableLang=$this->Sbc->getConfig('theme.language.useLanguages');
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
<?php
foreach ($appComponents as $component => $compConfig):
	$comp=''; // Component configuration, to be written in file.
	switch ($component):
		// DebugKit
		case 'debugKit':
			$comp="\t'DebugKit.Toolbar',\n";
			break;

		// Auth
		case 'Auth':

			$userStatusField = $compConfig['userStatusField'];
			$validUserStatus = $compConfig['validUserStatus'];
			$userModel = $compConfig['userModel'];
			$userController = Inflector::underscore(Inflector::pluralize($userModel));
			$loginAction = $compConfig['loginAction'];
			// loginRedirect can be prefix::action or action only.
			$tmp = explode('::', $compConfig['loginRedirect']);
			if (count($tmp) > 1):
				$loginRedirect = $tmp[1];
				$loginRedirectPrefix = "'{$tmp[0]}'";
			else:
				$loginRedirect = $compConfig['loginRedirect'];
				$loginRedirectPrefix = null;
			endif;
			$llPlugin = $this->getControllerPluginName($userController);
			$llPlugin = ($llPlugin === null || empty($llPlugin)) ? "null" : "'$llPlugin'";

			$comp.= "\t'Auth' => array(\n";
			$comp.= "\t\t'authenticate' => array(\n";
			$comp.= "\t\t\t'Form' => array(\n";
			$comp.= "\t\t\t\t'fields' => array('username' => 'email'),\n";
			// Field that verify if an account is active or not:
			if ($compConfig['validUserStatus'] === true):
				$comp.= "\t\t\t\t'scope' => array('$userModel.$userStatusField' => $validUserStatus),\n";
			endif;
			$comp.= "\t\t\t),\n"; // closes Form array
			$comp.= "\t\t),\n"; // closes authenticate array

			$comp.= "\t\t'authorize' => array(\n";
			$comp.= "\t\t\t'Actions' => array('actionPath' => 'controllers'),\n";
			$comp.= "\t\t),\n"; // closes authorize array
			// Redirections

			$comp.= "\t\t// Login page:\n";
			$comp.= "\t\t'loginAction' => array('admin' => null, 'plugin' => $llPlugin, 'controller' => '$userController', 'action' => '$loginAction'),\n";
			$comp.= "\t\t// Redirection after logout:\n";
			$comp.= "\t\t'logoutRedirect' => array('admin' => null, 'plugin' => $llPlugin, 'controller' => '$userController', 'action' => '$loginAction'),\n";
			$comp.= "\t\t// Redirection after login:\n";
			$comp.= "\t\t'loginRedirect' => array(".((is_null($loginRedirectPrefix)?("'admin' => null"):("$loginRedirectPrefix => true"))).", 'plugin' => $llPlugin, 'controller' => '$userController', 'action' => '$loginRedirect'),\n";
			$comp.= "\t),\n"; // closes Auth array
			break;

		// other components:
		default:
			$comp= "\t'$component',\n";
			break;

	endswitch;

	// Writing component config
	if(!isset($compConfig['useComponent']) || $compConfig['useComponent'] === true):
		echo $comp;
	endif;
endforeach;
?>
);

public $helpers = array(
<?php
foreach ($appHelpers as $helper):
	echo "\t\t'$helper',\n";
endforeach;
?>
);

	public function beforeFilter() {
		parent::beforeFilter();

		//
		// Making the current URL parameters accessible in view
		//
		$baseURL = array();
		// Current prefix:
		if(isset($this->request->params['prefix'])){
			<?php
			$prefixes=$this->Sbc->getPrefixesList();
			$i=0;
			foreach($prefixes as $p):
				if($p!='public'):
					$begin=($i===0)?'':"\t\t\telse";
					echo "{$begin}if(isset(\$this->request->params['$p']) && \$this->request->params['$p']===true){\n";
					echo "\t\t\t\t\$baseUrl['prefix'] = '$p';\n";
					echo "\t\t\t}\n";
					$i++;
				endif;
			endforeach;
			?>
		}else{
			$baseURL['prefix'] = 'public';
		}
		// Current plugin:
		$baseURL['plugin'] = $this->request->params['plugin'];
		// Current controller:
		$baseURL['controller'] = $this->request->params['controller'];
		// Current action:
		$baseURL['action'] = $this->request->params['action'];
		// Named parameters:
		foreach ($this->request->pass as $named) {
			$baseURL[] = $named;
		}
		// Passing array to the view:
		$this->set('baseURL', $baseURL);

		//
		// Prefixes changes
		//
		<?php
		$prefixes = $this->Sbc->getPrefixesList();
		// Counting prefixes
		$prefixesNb=count($prefixes);
		$i=0;

		if($prefixesNb>1):
			// Public prefix is not yet encountered
			$havePublicPrefix=false;
			// Walking through prefixes
			foreach($prefixes as $prefix):
				// preparing the proper if/elseif/else
				$begin='';
				if($i>0):
					$begin='else';
				endif;
				// public prefix should be treated at the end of the if/else statement.
				if($prefix==='public'):
					$havePublicPrefix=true;

				// Not the public prefix, so we set it up
				else:
					echo "\n\t\t// Changes for the $prefix prefix\n";

					// Starting if startment
					echo "\t\t{$begin}if (isset(\$this->request->params['prefix']) && isset(\$this->request->params['$prefix']) && \$this->request->params['$prefix']  === true".(($enableAuth) ? ' && $this->Auth->loggedIn()' : '') .") {\n";
					//
					// Layout changes depending on the prefix
					if($this->Sbc->getConfig('theme.useLayoutsForPrefixes')===true):
						echo "\t\t\t// Layout change\n";
						echo "\t\t\t\$this->layout = '$prefix';\n";
					endif;

					//
					// Allow everything to admins
					if($prefix===$this->Sbc->getConfig('general.adminPrefix')):
						echo ($enableAuth) ? "\t\t\t// Default for admins: allow everything\n"
								. "\t\t\t\$this->Auth->allow();\n" : "\t\t\t// Acls are disabled. Look at the theme.components.Auth section in config file.\n";
					endif;
					// End if statment
					echo "\t\t}\n";
					$i++;
				endif;

			endforeach;
			// Public prefix
			if($havePublicPrefix===true):
				echo "\n\t\t// Public prefix\n";
				echo "\t\telse{\n";
				echo ($enableCache) ? "\t\t\t//Cache all action for 1 hour\n"
						. "\t\t\t\$this->cacheAction = '1 hour';\n" : "\t\t\t// Cache disabled. Set 'enableCache: true' in options for this file if you want it\n";
				echo "\t\t}\n";
			endif;
		else:
			echo "\n\n//\n// You have only one prefix... I don\'t handle this now...\n//\n\n";
		endif;


		/* ************************************************************************
		 * Language support: define current page language.
		 */
		if ($enableLang):
			?>
			//
			// Making the different date formats available in views
			//
			$langDateFormats = array(
			<?php
			foreach($this->Sbc->getConfig('theme.language.dateFormats') as $lang=>$format):
				echo "'$lang'=>'$format',\n";
			endforeach;
			?>
				);
			$this->set('langDateFormats', $langDateFormats);

			// Current language
			$curr_lang = $this->_setLanguage();
			// Change language
			$this->set('lang', $curr_lang);
			$this->set('lang_fallback', DEFAULT_LANGUAGE);
			<?php
		endif;
		?>
	}
<?php

/* * ***********************************************************************
 * Language support : _setLanguage method
 */
if ($enableLang):
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
