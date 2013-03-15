Thank you for using the Superbake Shell v0.1

This console shell is designed for CakePHP 2.3.x

MAY I USE THE SCRIPT ?
======================
If you're in an early stage of development, this script will help you for
generation of parts of your app.
As superBake is very young, you'll have to edit the superBaked files as you would
have done with bake, but less...

If you're creating a small app (no plugins, some models/controllers,...), this
script can help you creating all that as a batch.

Note, superBake does not use scaffolding, 
Note again, superBake is here for batch generation. If you need to rebuild one
of your models/controllers/view, use Bake.

Note that SuperBake don't bake controllers without models (for now)

================================================================================
Please, backup your app before testing this shell. Thank you !
================================================================================

Please, take a look at the Directory structure before going on.

DIRECTORY STRUCTURE
===================
<CakeBase>
  /app
		/Console
			/Command
				AppShell.php					Modified AppShell
				SuperBakeShell.php				Shell script
				/Task
					SuperControllerTask.php		Controller generation
					SuperModelTask.php			Model generation
					SuperPluginTask.php			Plugin creation
					SuperViewTask.php			View generation
			/Config
				PublicConfig.php				Config file for models in app/Model
				superbake.php					Main configuration file
				templatePluginConfig.php		Template file for plugin configuration
				example/
					<files>						Sample configuration, on which I did my tests
			/Template
				/superBake 						SuperBake templates (nearly the same as classical bake templates)
					/actions
						controller_actions.php 	File called to create controllers
						/snippets 				Folder with actions samples
							<dirs and files>	
					/classes					Layouts for controllers, fixtures, models and tests.
					/views 						View samples for view generation
						<dirs and files>


INSTALLATION GUIDE
==================
 - To include superBake Shell in your app, just add the archive files in the
app/Console directory. I have to write a entire plugin for it.


USAGE
=====
 - Then, you have to edit files. I know that's rude, but I can't guess your app
structure. So, manually edit the app/Console/Config/SuperBakeConfig.php,
 - Then, edit the RootConfig.php file
 - For each plugin you want to create, create a file named <PluginName>Config.php,
based on the templatePluginConfig.php (it's an empty config file with comments on values)

You're almost done !

- Open a console/terminal/whatever you name it
- CD to your <BaseCake>/app dir
- Launch this command: cake superBake
- Follow instructions :D
- Open a browser
- Pray the CakeNoob god not to have let me down during development
- Take a quick look at the generated pages
- If something is wrong, close your eyes quickly, walk backward in any other room
and cry a lot. Sorry.


HOW IT WORKS
============
SuperBake Shell is just modified CakePHP's Bake files, with configuration files to have a easier
baking process.


TODO
====
 - Work on generation of controllers with no models
 - Work to be able to generate just one entire plugin
 - Use the actions equivalences (defined in plugins config files) during controller generation
 - ...

LICENSE : GPL
=============
This file is part of EL-CMS.

EL-CMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
EL-CMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 

You should have received a copy of the GNU General Public License
along with Foobar. If not, see <http://www.gnu.org/licenses/>

