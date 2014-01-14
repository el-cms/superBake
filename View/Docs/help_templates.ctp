# Base template options

## Template directory structure

Templates can be found in the `Sb/Console/Templates/` folder. If you want to create yours, the best way is to clone the existing default template.
Directory structure is as follow:

  <template dir>
  +---actions/
  |   |   
  |   |---controller_actions.ctp - This file includes all the actions for a  controller.
  |   +---actions/
  |       |---<actions templates>
  |
  +---classes
  |   |---controller.ctp - Controller class template. Calls "controller_actions.ctp" for actions
  |   |---fixture.ctp
  |   |---model.ctp - Model class template. Includes model snippets
  |   |---test.ctp
  |
  +---common
  |   |---<Files used by many templates>
  |   +---Licenses/
  |       |---<Licenses templates>
  |
  +---files
  |   |---<files templates>
  |
  +---menus
  |   |---<menus templates>
  |
  +---models
  |   |---<models snippets>
  |
  +---views
      |---<views templates>

## Common elements:
Common elements such as the licenses templates for the generated files can be found in `common/` directory.

### Base licenses:
superBake's default model is shipped with three license to protect your generated files: 

* **`licenses/gpl3.ctp`** - GPL 3 license files headers.
* **`licenses/mit.ctp`** - MIT license files headers.
* **`licenses/nolicense.ctp`** - No license. Be careful with this one.

**All licenses use data from the `general` part of the [configuration file](config_general)**


## superBake methods
Methods from SbShell are available in every template:

### From SbShell

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
 * @param bool $dir If true, a trailing / will be added
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
	<div class="col-lg-6 col-md-6">

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
	<div class="col-lg-6 col-md-6">

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
	<div class="col-lg-6 col-md-6">
		<em>In a User Controller template:</em>
		<pre class="syntax brush-html">
$this->Session->setFlash(&lt;?php echo $this->display('Your account could not be created. Please, try again.')?&gt;, 'flash_error');
		</pre>
	</div>
	<div class="col-lg-6 col-md-6">
		<em>will write this in the generated controller:</em>
		<pre class="syntax brush-html">
$this->Session->setFlash(__('Your account could not be created. Please, try again.'), 'flash_error');
		</pre>
	</div>
</div>

You can pass the same arguments as for a default `__()` or `__d()`.

<!--<div class="row">
	<div class="col-lg-6 col-md-6">
		<em></em>
		<pre class="syntax brush-html">
			
		</pre>
	</div>
	<div class="col-lg-6 col-md-6">
		<em></em>
		<pre class="syntax brush-html">
			
		</pre>
	</div>
</div> -->

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

### From Sbc
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

#### prefixesList()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the list of prefixes in default actions list.
 * @return array
 */
public function prefixesList(){}
?&gt;
</pre>