# Help on templates

## Template directory structure

The template used to generate your app is located in the `Sb/Console/Template/` folder. If you want to create yours, the best way is to clone the existing default template.

In earlier versions, superBake was able to handle multiple templates, and the configuration file was outside the template directory, wich was a mistake as the configuration file it tightly tied to a certain template...

### Directory structure is as follow:

<pre class="small">
  <i class="icon-folder-open"></i> Sb/Console/Template
  +--- <i class="icon-folder-open"></i> actions/
  |    +--- <i class="icon-folder-open"></i> actions/
  |    |    +--- <i class="icon-file"></i> [actions templates]
  |    |
  |    +--- <i class="icon-file"></i> controller_actions.ctp - This file includes all the actions for a  controller.
  |
  +--- <i class="icon-folder-open"></i> classes
  |    +--- <i class="icon-file"></i> controller.ctp - <span class="text-info">Controller class template. Calls "controller_actions.ctp" for actions</span>
  |    +--- <i class="icon-file"></i> fixture.ctp - <span class="text-info">Template for fixtures files. Same as Cake's</span>
  |    +--- <i class="icon-file"></i> model.ctp - <span class="text-info">Model class template. Includes model snippets</span>
  |    +--- <i class="icon-file"></i> test.ctp - <span class="text-info">Test file template. Same as Cake's</span>
  |
  +--- <i class="icon-folder-open"></i> common
  |    +--- <i class="icon-folder-open"></i> Licenses/
  |    |    +--- <i class="icon-file"></i> [Licenses templates] <span class="text-info">Licenses to be used in generated files headers</span>
  |    |
  |    +--- <i class="icon-file"></i> [Files used by many templates]
  |
  | +--- <i class="icon-folder-open"></i> docs
  |    +--- <i class="icon-file"></i> [Markdown files] <span class="text-info">Documentation about the current template</span>
  |
  +--- <i class="icon-folder-open"></i> files
  |    +--- <i class="icon-file"></i> [files templates]
  |
  +--- <i class="icon-folder-open"></i> menus
  |    +--- <i class="icon-file"></i> [menus templates]
  |
  +--- <i class="icon-folder-open"></i> models
  |    +--- <i class="icon-file"></i> [models snippets]
  |
  +--- <i class="icon-folder-open"></i> views
  |    +--- <i class="icon-file"></i> [views templates]
  |
  +--- <i class="icon-file"></i> Theme.php - <span class="text-info">Theme class that have methods related to the current template</span>
  +--- <i class="icon-file"></i> superBakeConfig.yml - <span class="text-info">The configuration file</span>
</pre>

## superBake methods
superBake comes with methods you can use in your templates : they are in the SbShell class (`Sb/Console/Command/SbShell.php`). These methods are generic and not related to the template you use, so you can use them in all your templates with `$this->methodName()`.

There is another usefull class to play with the configuration file : `Sbc`, located in `Sb/Lib/Superbake/Sbc.php`. This method is available everywhere in your templates, and accessible through `$this->Sbc->methodName()`.
### Methods from SbShell

#### canDo()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns true if an $action exists for the given $prefix/$controller
 * Use this to check links in templates
 *
 * @param string $action The action to check
 * @param string $prefix The prefix. If null, current prefix will be used
 * @param string $controller The controller. If null, current controller will be used.
 *
 * @return bool
 */
function canDo($action, $prefix = null, $controller = null){}


//
// Examples
//
if($this->canDo('delete', 'user')){
    // Make a link
}

?&gt;
</pre>
<div class="alert alert-warning">
	<i class="icon-warning-sign"></i> This method does not replace ACLs!
</div>

#### cleanPath()

<pre class="syntax brush-html">
&lt;?php
/**
 * Makes a config path value (path::to::file)
 * @param string $path Path::to::file.ext
 * @param boolean $dir If true, a trailing / will be added
 * @return string Good path format, with trailing slash
 */
public function cleanPath($path, $dir = false) {}
?&gt;
</pre>

#### cleanPlugin()

<pre class="syntax brush-html">
&lt;?php
/**
 * Cleans a plugin name: remove the dot and keep the plugin
 *
 * @param string $plugin Plugin to check
 * @return string
 */
public function cleanPlugin($plugin) {}
?&gt;
</pre>

#### getActionName()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns an action name, without its prefix.
 *
 * @param string $action Action name
 *
 * @return string
 */
public function getActionName($action) {}
?&gt;
</pre>

#### getActionPrefix()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns an action's prefix.
 *
 * @param string $action Action to check
 *
 * @return mixed Action prefix, null if none
 */
public function getActionPrefix($action) {}
?&gt;
</pre>

#### getControllerPluginName()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the plugin name for the given $underscored_controller_name
 * Returns null for appBase
 *
 * @param string $underscored_controller_name
 *
 * @return mixed string or null
 */
public function getControllerPluginName($underscored_controller_name) {}
?&gt;
</pre>


#### iString()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns a correct __(...) or __d(...) statement. If plugin is provided, will use
 * plugin as domain; If no plugin is provided, the current plugin will be used.
 *
 * @param string $string String to display.
 * @param string $args String array of args
 * @param string $plugin Plugin name.
 *
 * @return string Ready to use string.
 */
public function iString($string, $args = null, $plugin = null) {}
?&gt;
</pre>

**Examples:**
<div class="row">
	<div class="span6 col-md-6">

		<pre class="syntax brush-html">
// in a view, in appBase
&lt;div class="someClass"&gt;
	&lt;?php echo "&lt;?php echo ".$this->iString('Some stuff')."; ?&gt;
&lt;/div&gt;

// Will create
&lt;div class="someClass"&gt;
	&lt;?php echo __('Some stuff'); ?&gt;
&lt;/div&gt;
</pre>
	</div>
	<div class="span6 col-md-6">

		<pre class="syntax brush-html">
// In a plugin named "Projects"
&lt;div class="someClass"&gt;
	&lt;?php echo "&lt;?php echo ".$this->iString('Some stuff')."; ?&gt;
&lt;/div&gt;

// will create :
&lt;div class="someClass"&gt;
	&lt;?php echo __d('Projects', 'Some stuff'); ?&gt;
&lt;/div&gt;


</pre>
	</div>
</div>

---

<div class="row">
	<div class="span6 col-md-6">
		<em>In a User Controller template:</em>
<pre class="syntax brush-html">
&lt;?php
echo $this->setFlash($this->iString('Your account could not be created. Please, try again.'), 'flash_error');
?&gt;
</pre>
	</div>
	<div class="span6 col-md-6">
		<em>will write this in the generated controller:</em>
<pre class="syntax brush-php">
&lt;?php
$this->Session->setFlash(__('Your account could not be created. Please, try again.'), 'flash_error');
?&gt;
</pre>
	</div>
</div>

You can pass the same arguments as for a default `__()` or `__d()`.


#### isComponentEnabled()

<pre class="syntax brush-html">
&lt;?php
/**
 * Return true if a given component is enabled in the <code>theme.components</code> section.
 * @param type $component
 * @return boolean
 */
public function isComponentEnabled($component) {}
?&gt;
</pre>


#### setFlash()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns a string to create Flash messages with correct flash message element
 * The presence of flash message elements is defined in the config file.
 *
 * To enable setFlash messages using sessions, 'general.useSessions' must be true
 * in config file.
 *
 * @param string $content Message content
 * @param string $class Message class: error/succes/... Must match a valid flash message element
 * @param string $action Action to redirect the user to (in controllers, use $a
 * 		for current action)
 * @param array $options Array of options: controllerName, useSession, redirect, specialUrl
 *
 * Options:
 * 		- controllerName (String, default NULL) Target controller name
 * 		- redirect (Bool, default true) Redirect the user after flash or not.
 * 		//- useSession (Bool, default false) Forces the use of setFlash or not
 * 		- specialUrl (Bool, default false) If set to true, target action will be used as the
 * 			target url, so it will not be passed to $this->url()
 *
 * @return string setFlash()+redirect, setFlash() only or flash() if session is disabled.
 */
public function setFlash($content, $class, $action, $options = array()) {}
?&gt;
</pre>

#### speak()

<pre class="syntax brush-html">
&lt;?php
/**
 * Creates a pretty output for shell messages
 *
 * If $decorations > 0, output will have an opening HR
 * If $decorations >= 2, output will have a closing HR
 *
 * @param mixed $text String to output, or array of strings.
 * @param string $class Class (info|warning|error|success|comment|bold)
 * @param integer $force 1 for Normal, 2 for Verbose only and 0 for Quiet shells.
 * @param integer $decorations If >0, text will be surrounded by hr and $decorations new lines.
 * @param integer $newLines Number of empty lines to insert before and after text.
 *
 * @return void
 */
public function speak($text, $class = null, $force = 1, $decorations = 0, $newLines = 0) {}
?&gt;
</pre>

#### url()

<pre class="syntax brush-html">
&lt;?php
/**
 * This function create a link array for controllers/views, taking in account of
 * the admin state and if the controller is in a plugin or not (and wich).
 * Behavior:
 *  - if $prefix is empty, current routing prefix will be used
 *
 * @param string $action	The target action
 * @param string $controller	Target controller (MUST be given to find good plugin)
 * @param array  $options		An array of options
 * @return string Like "array('admin'=>'string|false', 'plugin'=>'string', 'controller'=>'controller', 'action'=>'action', 'options')"
 */
function url($action, $controller = null, $prefix = null, $options = null) {}
?&gt;
</pre>

### Methods from Sbc
Sbc is the library that do stuff around the configuration file (as population, getting values, ...). It's available with `$this->Sbc`, and is located in `Sb/Lib/SuperBake/`.

#### actionAddPrefix()

<pre class="syntax brush-html">
&lt;?php
/**
 * Creates a prefix_action string, and returns only action if prefix is public.
 * @param string $action Action name
 * @param string $prefix Prefix
 * @return string
 */
public function actionAddPrefix($action, $prefix = null) {}
?&gt;
</pre>

#### getActionsAll()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of all actions for all controllers, whatever is the "generate" state of the controller
 * array(plugin=>controllerName=>prefix=>action)
 * @return array
 */
public function getActionsAll() {}
?&gt;
</pre>

#### getActionsToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of actions to bake for a given plugin/part/prefix.
 *
 * @param string $plugin
 * @param string $part
 * @param string $prefix
 * @return array
 */
public function getActionsToBake($plugin, $part, $prefix) {}
?&gt;
</pre>

#### getAppBase()

<pre class="syntax brush-html">
&lt;?php
	/**
 * Returns the appBase value
 *
 * @return string
 */
	 public function getAppBase() {}
?&gt;
</pre>

#### getConfig()

<pre class="syntax brush-html">
&lt;?php
/**
 * Searches for the value of the given key in the config array.
 * Key must be in the format of "key.subKey.subSubKey", as for Configure::read()
 *
 * @param string $key
 * @return mixed Key's value
 */
public function getConfig($key = null) {}
?&gt;
</pre>

#### getControllerPart()

<pre class="syntax brush-html">
&lt;?php
/**
 * This will search for a controller in plugins parts, and RETURN THE FIRST RESULT.
 *
 * @param string $controller Controller name to search for
 * @return string or false
 */
public function getControllerPart($controller) {}
?&gt;
</pre>

#### getControllerPlugin()

<pre class="syntax brush-html">
&lt;?php
/**
 * This will search for a controller in plugins, and RETURN THE FIRST RESULT.
 *
 * @param string $controller Controller name to search for
 * @return string or false
 */
public function getControllerPlugin($controller) {}
?&gt;
</pre>

#### getControllersList()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns a list of controllers that must be generated in a given plugin.
 *
 * @param string $plugin Plugin name
 * @return array
 */
public function getControllersList($plugin = null) {}
?&gt;
</pre>

#### getControllersToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the array of controllers to bake
 *
 * @return array List of controllers to bake: array(controllerName=>array(part, plugin))
 */
public function getControllersToBake() {}
?&gt;
</pre>

#### getFilesToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of files to bake with "generate" set to true.
 * @return array
 */
public function getFilesToBake() {}
?&gt;
</pre>

#### getMenusToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of menus with "generate" set to true.
 *
 * @return array
 */
public function getMenusToBake() {}
?&gt;
</pre>

#### getModelPart()

<pre class="syntax brush-html">
&lt;?php
/**
 * This will search for a model in plugins parts, and RETURN THE FIRST RESULT.
 *
 * @param string $model Model name to search for
 * @return string or false
 */
public function getModelPart($model) {}
?&gt;
</pre>

#### getModelPlugin()

<pre class="syntax brush-html">
&lt;?php
/**
 * This will search for a model in plugins, and RETURN THE FIRST RESULT.
 *
 * @param string $model Model name to search for
 * @return string or false
 */
public function getModelPlugin($model) {}
?&gt;
</pre>

#### getModelsList()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns a list of models that must be generated in a given plugin.
 *
 * @param string $plugin Plugin name
 * @return array
 */
public function getModelsList($plugin = null) {}
?&gt;
</pre>

#### getModelsToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the array of models to bake
 *
 * @return array List of models to bake: array(modelName=>array(part, plugin))
 */
public function getModelsToBake() {}
?&gt;
</pre>

#### getPluginsList()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of plugins whatever is their 'generate' state.
 *
 * @return array
 */
public function getPluginsList() {}
?&gt;
</pre>

#### getPluginsToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the array of plugins to bake
 *
 * @return array List of plugins to bake: array(pluginName))
 */
public function getPluginsToBake() {}
?&gt;
</pre>

#### getViewsToBake()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the array of views to bake
 *
 * @param string $plugin Plugin name.
 * @param string $controller Controller name
 *
 * @return array List of views to bake: array(plugin=> part=> prefix=>array(action))
 */
public function getViewsToBake($plugin = null, $controller = null) {}
?&gt;
</pre>

#### isActionnable()

<div class="alert alert-info">
    This method is callet by SbShell::canDo(). You should use canDo() instead of isActionnable.
</div>
<pre class="syntax brush-html">
&lt;?php
/**
 * Returns true if the controller/prefix/action exists in config (that means
 * the current prefix have access to this action).
 *
 * @param string $prefix Prefix to check
 * @param string $controller Controller to check
 * @param string $action Action to check
 * @return boolean
 */
public function isActionnable($prefix, $controller, $action) {}
?&gt;
</pre>

#### pluginName()

<pre class="syntax brush-html">
&lt;?php
/**
 * returns the appBase value if $plugin is null or empty
 *
 * @param string $plugin Plugin name
 * @return string Plugin name
 */
public function pluginName($plugin = null) {}
?&gt;
</pre>

#### prefixName()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns public if $prefix is null, or $prefix.
 * @param string $prefix Prefix to test
 * @return string
 */
public function prefixName($prefix) {}
?&gt;
</pre>

#### getPrefixesList()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of prefixes in default actions list.
 * @return array
 */
public function getPrefixesList(){}
?&gt;
</pre>