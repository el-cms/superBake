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
	}

}
