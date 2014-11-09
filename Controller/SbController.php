<?php

App::uses('Spyc', 'Sb.Yaml');
App::uses('Sbc', 'Sb.Superbake');
App::uses('Folder', 'Utility');

/**
 * Sb Controller
 *
 * @property Sb $Sb
 */
class SbController extends SbAppController {

	/**
	 *
	 * @var Sbc object
	 */
	public $Sbc;

	public function beforeFilter() {
		parent::beforeFilter();

		// Layout
		if (Configure::read('Sb.Croogo')) {
			$this->layout = 'default_croogo';
		}
	}

	/**
	 * Index method
	 * Only returns the view.
	 *
	 * @return void
	 */
	public function admin_index() {

	}

	/**
	 * Redirection to admin_index()
	 */
	public function index() {
		$this->redirect(array('admin' => true, 'plugin' => 'sb', 'controller' => 'Sb', 'action' => 'index'));
	}

	/**
	 * This method will load a config file submited in POST.
	 * If nothing is set, it will use the default config file.
	 *
	 * Some vars from Sbc will be available in views.
	 *
	 *
	 * @access private
	 * @return void
	 */
	private function _loadConfigFile() {
		$this->Sbc = new Sbc();

		// Loads the file
		$this->Sbc->loadConfig();

		// Checking routing prefix
		$this->set('routingPrefixError', ((count($this->Sbc->getPrefixesList()) != (count(Configure::read('Routing.prefixes')) + 1)) ? 1 : 0));

		// Giving the array to view
		$this->set('configFileDescription', $this->Sbc->getConfig('description'));
		$this->set('log', $this->Sbc->displayLog());
		$this->set('logErrors', $this->Sbc->getErrors());
		$this->set('logWarnings', $this->Sbc->getWarnings());

		return $this->Sbc;
	}

	/**
	 * This action will display the final configuration file, after population.
	 * In addition, the view displays the logs from Sbc::populate() to check for errors.
	 *
	 * @return void
	 */
	public function admin_check() {
		$this->_loadConfigFile();
		$this->set('completeConfig', Spyc::YAMLDump($this->Sbc->getConfig()));
	}

	/**
	 * Displays the configuration file in a more readable way.
	 * (Named 'tree' because the very first version was a tree-like render)
	 *
	 * @return void
	 */
	public function admin_tree() {
		$this->helpers[] = 'Sb.Sb'; // For execution buttons
		$this->_loadConfigFile();
		// Prefixes and actions list:
		$defaults_prefixes_list = '';
		foreach ($this->Sbc->getConfig('defaults.actions') as $prefix => $action) {
			$defaults_prefixes_list.=$prefix . ', ';
		}
		$this->set('defaults_prefixes_list', rtrim($defaults_prefixes_list, ', '));
		$this->set('completeConfig', $this->Sbc->getConfig());
	}

	/**
	 * Executes `Sb.Shell $command` and echoes the result.
	 *
	 * @param string $command
	 *
	 * @return void
	 */
	public function admin_execute_cmd($command = null) {
		$this->helpers[] = 'Sb.Sb';
//		die('test');
		if (Configure::read('Sb.executeTroughGUI') === false) {
			die("Execution through GUI is disabled. To enable it, please change the value of `Sb.executeTroughGUI` to `true`.\n");
		}
		if (is_null($command)) {
			die('Hello, dear ! You gave me no argument, so I can\'t process to the delightful execution.');
		}
		$cmd = 'php ' . APP . "Console" . DS . "cake.php Sb.Shell $command";

		$output = shell_exec($cmd);
		if (DS === '\\') { // Windows, direct output
			echo $output;
		} else { // Linux, mac,... Strips ANSI codes
			echo preg_replace('@\[(\d{1,2})m@', '', $output);
		}
		die();
	}

}
