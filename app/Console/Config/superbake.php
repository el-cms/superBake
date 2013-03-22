<?php

/**
 * PHP file for EL-CMS
 * 
 * This file contains configuration variables for EL-CMS baking.
 * The main part of this script is the blacklist, wich corresponds to actions
 * not to bake, depending on plugn/controller/routing prefixes.
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
$projectConfig = array(
	// notPlugin is the name of a "virtual" plugin : it corresponds to the
	// configuration of models, controllers,... which must be baked in app/
	// You can change this name if you want to create a plugin with it.
	// If you do so, don't forget to rename the appropriate file in the 
	// superBake Config/ dir
	'notPlugin' => 'App',
	//'notPlugin' => 'AnotherName',
	// Plugins list. All plugin listed here must have a configuration file in the
	// superBake Config/ dir. Use the templateFile.config.php file as example.
	'plugins' => array(
		'Blog' => array(),
		'Gallery' => array(),
		'Licenses' => array(),
		'Liked' => array(),
		'Links' => array(),
		'Projects' => array(),
		'Tags' => array(),
	),
	// The default console template to use for generation. If you want to build
	// your own, take this one as an example.
	'defaultTemplate' => 'superBake',
	// If set to true, superBake will ask you for the template to use, overriding 
	// the previously given defaultTemplate
	'askForTemplate' => 'false',
	// The default whitelist adds itself to the plugin-specific whitelists.
	// It define which actions must be created for given routing prefix.
	// 
	// You can specify the controller snippet to use for the specific action doing like this :
	// 'index'=>'<path_to_snippet_dir>'.DS.'<snippet>' with no extension
	// path to snippet dir is relative to <themedir>/actions
	// 
	// To disable an action defined in the default whitelist, add it to the plugin's
	// specific blacklist, as it works that way : add all whitelists, then remove blacklists.
	'defaultWhiteList' => array(
		// 'prefix' => array(
		// 'action',),
		// The public prefix corresponds to non prefixed actions
		'public' => array(
			'index' => 'index',
			'view' => 'view',
		),
		// Registered users
		'user' => array(
			'index' => 'index',
			'view' => 'view',
			//Add pecific add/edit/delete in models configs
		),
		//Editors
		'editor' => array(
			'index' => 'index',
			'view' => 'view',
			//Add pecific add/edit/delete in models configs
		),
		// Admins
		'admin' => array(
			'index' => 'index',
			'view' => 'view',
			'add' => 'add',
			'edit' => 'edit',
			'delete' => 'delete'),
	),
	// Define if plugin generation must update the bootstrap.php file.
	// This value is defined during plugin generation, so no need to set something here.
	'updateBootstrap' => null,
	// If you don't use routing prefixes (see your core.php file), set this to false.
	'usePrefixes' => true,
	'rolesModel' => 'Groups',
	'rolesPK' => 'id',
	'usersModel' => 'Users',
	'usersPK' => 'id',
);
?>