# Config: defaults
This section holds the default configuration for empty elements in your configuration file. That means if you define elements _without_ all the keys needed, defaults will be used

**Things to know:**

 * All **pathes** must follow this convention: directory separators are `::`.
 * `options` arrays can be simple key/value or multidimensionnal arrays, or both, or a teapot. As you want.
 * `generate` keys are hierarchical: if you set one to false, all the part's child will have them set to false.
 * If you add a key that's not in the defaults list, it will be added to the final config array, making it easy to extend it to suit your needs.

All these parts are in the `defaults` section of your configuration file.

## View

&rarr; [Default views templates](help_templates_views)

---
Views templates are located in the `Sb/Console/Templates/<yourTemplate>/views/` dir.
You can find infos about templates options and configuration on [this help page](help_templates_views)

<pre class="syntax yaml">
##
## This is a default view
##
view:
  ## Path to the template file for the view.
  ## Leave this blank in defaults or all your views will use the same template as default.
  template:
  ## View must be generated
  generate: true
  ## ... And have options
  options: []
</pre>

## Action

&rarr; [Default actions templates](help_templates_controller)

---
Actions are located in the `Sb/Console/Templates/<yourTemplate>/actions/` dir.
You can find more infos about actions templates and their configuration on [this help page](help_templates_actions)

<pre class="syntax yaml">
##
## This is a default action
##
action:
  ## This is the path to the template
  ## Leave this blank in defaults or all your actions will use the same template as default.
  template:
  ## An array of options
  options: []
  ## Action have view. Set it to false if this action don't need a view.
  ## Leave this to true in defaults or all your actions won't have views.
  haveView: true
  ## This section will contain the view configuration
  view: []
  ## Set this to true and the action will be removed from controller.
  ## The difference between this and generate: set this to false and this action
  ## will not be referenced in any case.
  ## This is usefull when you want to discard a specific action, previously 
  ## defined in the default actions list.
  ## Leave this to false in defaults 
  blackListed: false
</pre>

## Actions
These are the default actions that must be created in each controller, listed by prefixes. If you don't want to use prefixes, define everything in the `public` prefix.

If you want to use more prefixes than the default "public" prefix, don't forget to specify them in your app's `Config/core.php`.

For a given plugin/part/controller, if you don't want to have one of the default actions, define it and set the `blackListed` option to `true` in its definition.

You can define options for the specific actions here, using the `action` 'pattern' (if you want to use a custom view or custom template for all the index actions)

<div class="row">
	<div class="col-lg-6 col-md-6">
		<h3>Default actions</h3>
		<pre class="syntax yaml">
##
## This is the default actions list. 
## Here you will define your prefixes and the actions that are available for them.
##
actions:
  ## This is the public prefix.
  ## By default, 'public' users can only index and view things.
  public:
    ## Index action
    index: []
    ## View action
    view: []
  ## This is the admin prefix
  ## By default, 'admin' users can do every CRUD actions.
  admin:
    ## Index action
    index: []
    ## View action
    view: []
    ## Add action
    add: []
    ## Delete action
    delete:
      ## Delete has no view
      haveView: false
    ## Edit action
    edit: []			
		</pre>
	</div>
	<div class="col-lg-6 col-md-6">
		<h3>Example</h3>
		<pre class="syntax yaml">
actions:
  public:
    ## Index action
    ## "Action" structure is used
    index:
      view:
        ## Use views/index/nice_index.ctp
        template: index::nice_index
        options:
          ## According to this template, 
          ## I can hide some fields:
          hiddenFields:
            - password
            - username
            - email
    ## View action
    view:
      view:
        template: view::nice_view
        options:
          hiddenFields:
            - password
            - username
            - email
          toolbar: false
          haveComments: true
    ## A comment action
    commentItem:
      template: comments::add_comment
      haveView: false
  ## This is the admin prefix
  ## By default, 'admin' users can do every CRUD actions.
  admin:
    ## Index action
    index:
      view: index
    ## View action
    view:
      view: view::complete_view
    ## Add action (will use 
    add: []
    ## Delete action
    delete:
      ## Delete has no view
      haveView: false
    ## Edit action
    edit: []
		</pre>
	</div>
</div>

## Controller

&rarr; [Default actions templates](help_templates_controller)

---
This section defines a default controller.

<pre class="syntax yaml">
##
## Default configuration for a controller
##
controller:
  ## Controller name.
  ## It must follows Cake conventions. If empty, model name will be used as base.
  name:
  ## The controller name, user-friendly (for example, a controller named ProjectItemComments 
  ## could have a nicer 'Comments' name displayed). If you leave this blank, a <em>human readable</em> 
  ## name will be created, based on the controller name.
  ## Leave this empty in defaults
  displayName:
  ## Must this be generated ?
  ## Set it to false, and none of your controllers will be generated by default.
  generate: true
  ## List of prefixes/actions
  ## Note that actions defined in controllers will stack with defaults actions,
  ## so if you don't need one of the defaults, re-define it with "blackListed" set to true.
  ## Leave this empty in defaults.
  actions: []
</pre>

## Snippets

&rarr; [Default model snippets](help_templates_models)

---
This section defines a model snippet.

Snippets are located in the `Sb/Console/Templates/<yourTemplate>/models/` dir.
You can find more infos about model snippets and their configuration on [this help page](help_templates_models)

<pre class="syntax yaml">
##
## Default snippet configuration
##
snippet:
  ## Path to the snippet.
  ## If a snippet doesn't exists, a comment will be inserted in model or file as a reminder.
  path:
  ## Options for this snippet.
  options: []
</pre>

## Model

&rarr; [Default model snippets](help_templates_models)

---
This sections defines defaults values for a model.

Snippets for models are located in the `Sb/Console/Templates/<yourTemplate>/models/` dir.
You can find more infos about model snippets and their configuration on [this help page](help_templates_models)
<div class="row">
	<div class="col-lg-6 col-md-6">
		<h3>Default model</h3>
			<pre class="syntax yaml">
##
## Default model configuration
##
model:
  ## Model name.
  ## Follow the cake naming conventions.
  ## If empty, part name will be used as base.
  name:
  ## Must this model be generated ?
  generate: true
  ## Field to display in order to identify an item.
  ## If blank, primary keys will be used.
  displayField:
  ## Snippets list. A snippet is a bit of code to be 
  ## included in your model.
  snippets: []
  ## Options passed to template for generation
  options: []
</pre>
	</div>
	<div class="col-lg-6 col-md-6">
		<h3>Example of model definition</h3>
		<pre class="syntax yaml">
model:
  name: Users
  displayField: pseudo
  ## Snippets list.
  snippets:
    ## Snippet name (use what you want)
    acl:
      ## Path
      path: acl::users
      ## no options.
    otherSnippet:
      path: ...
      options:
        - ...
        - ...
		</pre>
	</div>
</div>
	
## Part
This section defines a part configuration.

When you define a part, use the **Cake controller naming conventions** (TableName, CamelCased and plural form). This way, nameless models/controllers will base their names on it.
<pre class="syntax yaml">
##  
## Default part configuration
##
part:
  ## Generate this part ?
  ## This overrides under-levels "generate", as model:generate, controller:generate
  ## ...
  generate: true
  ## Have a model ?
  haveModel: true
  ## Model configuration
  model: []
  ## Have controller ?
  haveController: true
  ## Controller configuration
  controller: []
  ## Options that must be available in both model, controllers and views generation
  options: []
</pre>

Note that `haveModel` and `haveController` options are _really_ differents from the `generate` options in models/controllers definitions : if set to false, superBake will **NOT** reference any model or controller from this part in **ANY**.generated files. It must be set to false only if the part should NEVER have a model or a controller.

## Plugin
This is a default plugin configuration.

<div class="row">
	<div class="col-lg-6 col-md-6">
		<h3>Default plugin</h3>
		<pre class="syntax yaml">
##  
## Default plugin configuration
##
plugin:
  ## Plugin name, human readable
  displayName:
  ## Path where the plugin must be built.
  ## Path can be app::Plugin or Plugins
  path: app::Plugin
  ## Generate this plugin ?
  ## Setting this to false will make this plugin not to be built, but all items
  ## in it (models, controllers, ...) can be used as a reference.
  generate: true;
  ## Do the plugin must have routes ?
  ## (will be created in <em>path</em>/Config)
  haveRoutes: false
  ## Do the plugin must have a bootstrap file ?
  ## (will be created in <em>path</em>/Config
  haveBootstrap: false
  ## Parts of the plugin
  parts: []
  ## Menus for this plugin
  menus: []
  ## Files for this plugin
  files: []
  ## required files/folders for this plugin
  required: []
</pre>
	</div>
	<div class="col-lg-6 col-md-6">
		<h3>Minimal example</h3>
		<pre class="syntax yaml">
## Blog plugin with categories,
## posts and comments
Blog:
  parts:
    Posts:
    PostCategories:
    PostComments:
</pre>
	</div>
</div>

## File

&rarr; [Default files templates](help_templates_files)

---
This section describes the configuration for standalone files. These files can be everything (a layout, a css file,...) since you have the right template.

Templates for files are located in the `Sb/Console/Templates/<yourTemplate>/files/` dir.
You can find more infos about files templates and their configuration on [this help page](help_templates_files)

<pre class="syntax yaml">
##  
## Default configuration for a file
##
file:
  ## (a list can be found in 'Templates/<templateName>/files', and path must
  ## be relative to this folder.)
  template:
  ## Folder where the file must be copied (relative to final plugin directory)
  targetPath:
  ## Target filename. If none is provided, file name will be based on "file" name.
  targetName:
  ## Options for the file templates
  options: []
</pre>

## Menu

&rarr; [Default menus templates](help_templates_menus)

---
This section describes the configuration for a menu.

Templates for menus are located in the `Sb/Console/Templates/<yourTemplate>/menus/` dir.
You can find more infos about menus templates and their configuration on [this help page](help_templates_menus)

<pre class="syntax yaml">
##
## Default configuration for a menu
##
menu:
  ## Template path
  template:
  ## Target folder (relative to plugins' View/ folder)
  targetPath: elements::menus
  ## Target filename. If none is provided, file name will be based on menu name.
  targetName:
  ## Options
  options: []
</pre>

## Required
This section is empty for now. Required files are a concept and I must work on it