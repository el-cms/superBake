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

?&gt;
</pre>

#### cleanPlugin()

<pre class="syntax brush-html">
&lt;?php

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

You can pass the same arguments as for a default __()/__d().

<div class="row">
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
</div>

#### speak()

<pre class="syntax brush-html">
&lt;?php

?&gt;
</pre>

#### url()

<pre class="syntax brush-html">
&lt;?php

?&gt;
</pre>

### From Sbc
Sbc is the library that do stuff around the configuration file (as population, getting values, ...). It's available with `$this->sbc`, and is located in `Sb/Lib/SuperBake/`.

<div class="alert alert-danger">Incomplete list. Waiting for doxygen output.</div>