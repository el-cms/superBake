<?php

App::uses('AppController', 'Controller');

class SbAppController extends AppController {

	/**
	 * Statements to execute before doing the action.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Allow all actions.
		if (in_array('Acl', $this->components)) {
			$this->Auth->allow();
		}

		// Search for documentation in Template dir:
		$dir = CakePlugin::path('Sb') . 'Console' . DS . 'Template' . DS . 'docs' . DS;
		$docDir = opendir($dir);
		$files = array();
		$menuLinks = array();
		while ($file = readdir($docDir)) {
			if (!is_dir($dir . $file)) {
				$files[] = $file;
			}
		}

		sort($files);

		foreach ($files as $file) {
			$tmp = explode('.', $file);
			//Only use files named something.something.ext and adding entry to the menu
			if (isset($tmp[count($tmp) - 1]) && count($tmp) > 2) {
				// Removing extension
				unset($tmp[count($tmp) - 1]);
				$menuLinks[ucfirst(str_replace('_', ' ', $tmp[0]))][] = array(
						'title' => ucfirst(str_replace('_', ' ', $tmp[1])),
						'file' => $file
				);
			}
		}
		$this->set('templateLinks', $menuLinks);

	}

}
