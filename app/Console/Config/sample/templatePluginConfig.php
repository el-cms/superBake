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
	/*
	 * Array with the list models that belongs to the plugin.
	 */
	'models' => array(
		/*
		 * Model description
		 */
		// Model name in DB (if your table is named useritems, use UserItems)
		// if you use table prefixes, don't set it here, edit your database.php
		// and Cake will handle it..
		'ModelName' => array(
			// List of actions to be created in controllers
			'whiteList' => array(
				// non prefixed actions
				'public' => array(),
				'user' => array(
					'index' => 'index',
					'view' => 'view',
				),
				'editor' => array(
					//'add' => 'self_add',
					//'edit' => 'self_edit',
					//'delete' => 'self_delete'
				),
				// Admin prefix
				'admin' => array(),
			),
			// This blacklist cancels whitelists
			'blackList' => array(
				'public' => array(),
				'user' => array(),
				'editor' => array(),
				'admin' => array(),
			),
			// Views with their corresponding templates. If not set, superBake
			// will use default views
			// Views are in <templateDir>/views, and all pathes below must be 
			// relative to this dir.
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
			// Parts of code to add in models
			// This is not supported yet.
			'models' => array(
			//'act_as_requester',
			// ...
			),
			// Action equivalences (not used yet)
			// In future versions, this array will use a method as another
			// Or not...
			'equivs' => array(
				// non prefixed actions
				'public' => array(
				// "register" replaces "add"
				//'register'=>'add',
				),
				// Moderator prefix
				//'moderator' =>array(),
				// Admin prefix
				'admin' => array(),
			),
		),
	),
	// Controllers with no models. 
	// SuperBake does not handle them for now.
	'controllers' => array(
		'test' => array(
			'add',
			'edit',
			'delete',
			'update',
			'someAction'
		)
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