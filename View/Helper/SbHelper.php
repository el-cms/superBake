<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class SbHelper extends Helper {

	public function execBtn($command, $description = null, $class = null) {
		if (Configure::read('Sb.executeTroughGUI') === true) {
			$class = (is_null($class)) ? 'default' : $class;
			$link = '<button class="btn btn-xs btn-' . $class . '"';
			$link.=' onClick="run_cmd(\'' . $command . '\')"';
			$link.=' title="' . 'Execute `cake Sb.Shell ' . $command . '`" data-toggle="tooltip"';
			$link.='>';
			$link.='<i class="icon-cogs"></i> ' . $description;
			$link.='</button>';
			return $link;
		}
	}

}
