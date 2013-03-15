<?php

/**
 * PHP file for EL-CMS
 * 
 * This is the sample plugin configuration file for superBake generation.
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version 0.1
 * ----
 * 
 *  This file is part of EL-CMS.
 *
 *  EL-CMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  EL-CMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *
 *  You should have received a copy of the GNU General Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
$PluginConfig = array(
	'models' => array(
		'Users' => array(
			'whiteList' => array(
				'public' => array( // add Register method
					'register' => 'users'.DS.'register',
				),
				'user' => array(
					'logout'=>'users'.DS.'logout',
				),
				'editor' => array(), // Nothing more as editor
				'admin' => array('lock'=>'users'.DS.'lock'), //Already full powers, add Lock
			),
			'blackList' => array(
				'public' => array(),
				'user' => array(),
				'editor' => array(),
				'admin' => array(),
			),
			'views' => array(
				'public' => array(
					'index' => 'index' . DS . 'simple_divs',
				),
				'user' => array(
					'index' => 'index' . DS . 'simple_divs',
				),
				'editor' => array(),
				'admin' => array(),
			),
		),
		'Groups' => array(
			'whiteList' => array(
				'public' => array(),
				'user' => array(),
				'editor' => array(),
				'admin' => array(),
			),
			'blackList' => array(
				'public' => array('index', 'view'), // No listing/view for public
				'user' => array(),
				'editor' => array(),
				'admin' => array(),
			),
			'views' => array(
				'public' => array(
					'index' => 'index' . DS . 'simple_divs',
				),
				'user' => array(
					'index' => 'index' . DS . 'simple_divs',
				),
				'editor' => array(),
				'admin' => array(),
			),
		),
	),
	// Dir where the plugin must be baked in (Plugin or app/Plugin
	// 'plugin_dir' => 'Plugin',
	'plugin_dir' => 'app' . DS . 'Plugin',
	// Set to true if the plugin needs a bootstrap file
	'have_bootstrap' => false,
	// Set to true if the plugin needs a routes file
	'have_routes' => false,
);
?>