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
 * index method
 *
 * @return void
 */
	public function index() {

	}

/**
 * This method will load a config file submited in POST.
 * If nothing is set, it will use the default config file.
 *
 * Some vars from sbc will be available in views.
 *
 *
 * @access private
 * @return void
 */
	private function _selectConfigFile() {
		$sbc = new Sbc();

		if ($this->request->is('post')) {
			$fileToLoad = $this->request->data['configFile'];
		} else {
			$fileToLoad = Configure::read('Sb.defaultConfig');
		}
		// Find the different configuration files
		$configFolder = new Folder($sbc->getConfigPath());

		// Loads the file
		$sbc->loadFile($fileToLoad);

		// Giving the array to view
		$this->set('configFiles', $configFolder->find('(.*)\.yml', true));
		$this->set('configFile', $fileToLoad);
		$this->set('configFileDescription', $sbc->getConfig('description'));
		$this->set('log', $sbc->displayLog());
		$this->set('logErrors', $sbc->getErrors());
		$this->set('logWarnings', $sbc->getWarnings());

		return $sbc;
	}

/**
 * This action will display the final configuration file, after population.
 * In addition, the view displays the logs from sbc::populate() to check for errors.
 *
 * @return void
 */
	public function check() {
		$sbc = $this->_selectConfigFile();
		$this->set('completeConfig', Spyc::YAMLDump($sbc->getConfig()));
	}

/**
 * Displays the configuration file in a more readable way.
 * (Named 'tree' because the very first version was a tree-like render)
 *
 * @return void
 */
	public function tree() {
		$sbc = $this->_selectConfigFile();
		// Prefixes and actions list:
		$defaults_prefixes_list = '';
		foreach ($sbc->getConfig('defaults.actions') as $prefix => $action) {
			$defaults_prefixes_list.=$prefix . ', ';
		}
		$this->set('defaults_prefixes_list', rtrim($defaults_prefixes_list, ', '));
		$this->set('completeConfig', $sbc->getConfig());
	}

/**
 * Method to test the sbc::arrayMerge() method.
 * Here for testing only.
 *
 * @return void
 */
	public function arraymerge() {
		$result = '';
		if ($this->request->is('post')) {
			$sbc = new Sbc();
			$spyc = new Spyc();
			$default = $this->request->data['default'];
			$defined = $this->request->data['defined'];
			$keep = (isset($this->request->data['keepRest']) && $this->request->data['keepRest'] == 'keep') ? true : false;
			$result = $spyc->YAMLDump($sbc->updateArray($spyc->YAMLLoadString($default), $spyc->YAMLLoadString($defined), $keep));
		} else {
			$default = null;
			$defined = null;
			$keep = null;
			$result = null;
		}
		$this->set('result', $result);
		$this->set('default', $default);
		$this->set('defined', $defined);
		$this->set('keepRest', $keep);
	}

}
