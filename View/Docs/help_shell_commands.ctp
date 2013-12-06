# Command list
superBake is a Shell for CakePHP. That means it must be used from command line. All the actions are launched from the `<cakeDir>/app` directory and starts with `Console/cake Sb.Shell` command.

You can run commands with arguments, or step by step, using the menu. The menu is accessible with `Sb.Shell` command, without any arguments. If you launch a command without its arguments, or if you make a typo in an argument, you'll be guided step by step.

If you think that superBake is too speaky, close its mouth with the additionnal `-q` (quiet) parameter. You'll only have success/warning and error messages. You can also make it *even more* speaky with the verbose mode: `-v`.

Launching `$ ./Console/cake Sb.Shell` will bring you to this menu:

<pre id="shellMenu">


  <em>Sometimes, cake throws errors about unwritable files.
  That's ok, that's just a file permissions problem when you run the script whith a different
  user as the webserver.</em>


<span class="text-info">Welcome to CakePHP v2.4.1 Console</span>
---------------------------------------------------------------
App : app
Path: <em>pathToCake/</em>app/
---------------------------------------------------------------

+--[ SuperBake ]------------------------------------------------+
|                                           |   ___             |
|                                           |   | | <span class="text-success">Experiments</span> |
|  This script generates plugins, models,   |  /   \    <span class="text-success">Labs</span>    |
|  views and controllers for them.          | (____')           |
|                                           |                   |
+---------------------------------------------------------------+
|                                                               |
|  <span class="text-info">Welcome to the SuperBake Shell.</span>                              |
|  <span class="text-info">This Shell can be quieter if launched with the -q option.</span>    |
|                                                               |
|  <span class="text-warning" data-toggle="tooltip" title="Seriously, do it!"><i class="icon-info-sign"></i> Read the doc before things turns bad</span>                       |
|                                                               |
+---------------------------------------------------------------+
| <span class="text-info" data-toggle="tooltip" title="Current configuration file. No way to change this for now."><i class="icon-info-sign"></i> Config file: "superBakeConfig.yml".</span>
| <span class="text-warning" data-toggle="tooltip" title="This info will appear if you have errors in your configuration file."><i class="icon-info-sign"></i> --> This file contains errors. Check it.</span>
+--[ <span class="text-danger"><a href="#plugin_generation_section">Plugin creation</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell plugins"></i> [<strong>P</strong>]lugins (Creates all plugins structures)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell pluginMVC <PluginName>"></i> [<strong>S</strong>]pecific entire plugin (M/C/V)
|
+--[ <span class="text-danger"><a href="#batch_generation_section">Batch generation</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell models"></i> [<strong>M</strong>]odels (Generates all models)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell controllers"></i> [<strong>C</strong>]ontrollers (Generates all controllers)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell views"></i> [<strong>V</strong>]iews (Generates all views)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell mvc"></i> [<strong>A</strong>]ll (Models, Controllers and Views)
|                                                               
+--[ <span class="text-danger"><a href="#model_generation_section">Model generation</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell pluginModels <PluginName>"></i> One plugin mo[<strong>D</strong>]els (All models for a specific plugin)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell model <PluginName>.<ModelName>"></i> [<strong>B</strong>]ake one model
|                                                               
+--[ <span class="text-danger"><a href="#controller_generation_section">Controller generation</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell pluginControllers <PluginName>"></i> [<strong>O</strong>]ne plugin controllers (All controllers for a specific plugin)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell controller <PluginName>.<ControllerName>"></i> Bake one contro[<strong>L</strong>]ler
|                                                               
+--[ <span class="text-danger"><a href="#view_generation_section">View generation</a></span> ]
|  <span class="text-info">View generation is based on the configuration file, and not</span>
|  <span class="text-info">from the existing controller. That means that if you have modified your</span>
|  <span class="text-info">controllers, the new actions will not be available.</span>
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell pluginViews <PluginName>"></i> O[<strong>N</strong>]e plugin views (All views for a specific plugin)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell view <PluginName>.<ControllerName>.<ActionName>"></i> Bake a view for one [<strong>G</strong>]iven action (plugin/controller specific)
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell controllerView <PluginName>.<ControllerName>"></i> Views fo[<strong>R</strong>] one given controller
|                                                               
+--[ <span class="text-danger"><a href="#menu_generation_section">Menus</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell menus"></i> M[<strong>E</strong>]nus (Generates menus)
|                                                               
+--[ <span class="text-danger"><a href="#file_generation_section">Files</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell files"></i> [<strong>F</strong>]Iles (Generates files)
|                                                               
+--[ <span class="text-danger"><a href="#required_generation_section">Required files</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell files"></i> Required f[<strong>I</strong>]les (Copies files and dirs)
|                                                               
+--[ <span class="text-danger"><a href="#misc_generation_section">Misc</a></span> ]
|      <i class="icon-info-sign" data-toggle="tooltip" title="Sb.Shell janitor"></i> Config [<strong>J</strong>]anitor (Cleans and fills your config. Outputs the result)
|      <i class="icon-info-sign" data-toggle="tooltip" title=":D"></i> [<strong>Q</strong>]uit
|   
</pre>
<a name="plugin_generation_section"></a>
## Plugin generation
Actions in this section are related to plugin generation and actions related to plugins.

 * `Sb.Shell plugins` will generate plugins structures. By default, all plugins defined in your configuration file will be created (See the [default part](config_defaults) from the config file)
 * `Sb.Shell pluginMVC <PluginName>` will generate models, views and controllers for the specified plugin.

<a name="batch_generation_section"></a>
## Batch generation
Actions in this section creates things for every plugins:

* `Sb.Shell models` will generate every models in their respective plugin.
* `Sb.Shell controllers` will generate every controllers in their respective plugin.
* `Sb.Shell views` will generate every views for every actions in every controllers, in their respective plugin.
* `Sb.Shell mvc` will generate models, controllers and views in all plugins.

<a name="model_generation_section"></a>
## Model generation

 
 * `Sb.Shell model <PluginName>.<ModelName>` will generate the *ModelName* model in the *PluginName* it belongs to. Useful when you work on a specific model. If you launch this command without arguments, or with wrong ones, you'll be guided step by step.
 * `Sb.Shell pluginModels <PluginName>` will generate all models for the specified plugin.

<a name="controller_generation_section"></a>
## Controller generation
 
 * `Sb.Shell controller <PluginName>.<ControllerName>` will generate the *ControllerName* controller in the *PluginName* it belongs to. Useful when you work on a specific controller. If you launch this command without arguments, or with wrong ones, you'll be guided step by step.
 * `Sb.Shell pluginControllers <PluginName>` will generate all controllers in the given plugin.

<a name="view_generation_section"></a>
## View generation

 
 * `Sb.Shell view <PluginName>.<ControllerName>.<actionName>` will generate the view for the given action. If you make a mistake, you'll be guided.
 * `Sb.Shell pluginViews <PluginName>` will generate all views for the given plugin.
 * `Sb.Shell controllerViews <PluginName>.<ControllerName>` will generate all the views for a given plugin/controller.

<a name="menu_generation_section"></a>
## Menus generation		 

 * `Sb.Shell menus` will generate all the menus.
 
<a name="file_generation_section"></a>
## Standalone file generation

 * `Sb.Shell files` will generate all the standalone files.

<a name="required_generation_section"></a>
## Copies files and folders

 * `Sb.Shell required` will copy all the required files/folders.

<a name="misc_generation_section"></a>
## Misc

* `Sb.Shell janitor` will output the YAML array of your config file, fully populated. You can also find this in [the superBake GUI](../../sb/check). Lanched with the `-q` option and outputed to a file with the `> filename.yml` direction can be usefull to keep tracks of your different configurations and take parts from them.
 