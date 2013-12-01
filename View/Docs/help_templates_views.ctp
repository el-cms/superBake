# Templates: views
All the views templates are located under the `<yourTemplate>/views`.

superBake comes with one default template: **superDefault**, wich is based on Cake's default template.

The following options are available:

## Available vars:
The following variables are accessible in any view: (use `var_export($this);` for a complete and more comprehensive list)

### Model variables
These variables are related to the current model.
 
 * `modelClass` - Model class name
 * `schema` - Array of information about the table fields:
 * `primaryKey` - The primary key name 
 * `displayField` - Field to display
 * `fields` - List of fields
 * `associations` - List of associations
  
  * `hasOne` - List of related associations
  * `belongsTo` - List of related associations
  * `hasmany` - List of related associations
   
### Controller variables
These variables are related to the current controller.

 * `singularVar` - Singular name of class
 * `pluralVar` - Plural name of class
 * `singularHumanName` - Singular name, human readable
 * `pluralHumanName` - Plural name, human readable
 
### View options
All the options defined in your view's section part are directly available.
<div class="alert alert-warning"><i class="icon-warning-sign"></i> Be carefull when choosing your options, they must not conflict with cake variables !</div>

 * `template` - Current template file name.
 
 
### Access to superBake:

 * Both `$sbc` and `$this->sbc` refers to Sbc.
 * `currentPart` - Current part name.
 * `plugin` - Current plugin name. Null for _appBase_.
 * `action` - Current action name with prefix, if any.
 * `admin` - Current prefi. Null for _public_.

## Common options
The following options are available and valid for all the actions:

 * **Toolbar options:** each view can have a toolbar with actions related to the current controller, which are:
  
  * `noToolbar` - **BOOL** default `false` - If set to true, no toolbar will be created in the view.
  * `toolbarHiddenControllers` - **Array** default empty - List of related controller that must be hidden from toolbar.
  * `viewIsAnItem` - **BOOL** default false - If set to true, actions for edit/delete will be added for the current item. Use it only on "view" actions, or views that display only one item.
  
 * **Fields options:** options concerning data fields
  
  * `hiddenFields` - List of fields that must not be shown on the view. Default is an empty array.
  * `sortableFields` - List of fields that can be sorted out. Default is an empty array.
 
 * **Other options:**
  * `additionnalCSS` - List of custom CSS to include in the view.
  * `additionnalJS` - List of custom javascript files to include.

## Custom options
Following views have these specific options:

### index
These options applies to the `views/index.ctp` template.

 * `hideItemActions` - **BOOL** default `true` - Create actions links for each items (action are view/edit/delete) if current prefix has them.
 * `` - 
 * `` - 
 * `` - 
 * `` - 

### edit/add
As add and edit forms are quite the same, their template is the same view (as Cake do in its default template). If you need to customize your add or edit actions, simply create a template named `add.ctp` or `view.ctp`: if one of these files exists, suprBake will use it instead of the original `form.ctp`

Options related to `view/form.ctp`

 * **Special case:** for an hypothetic _User_ model (or whatever you named it), you should define these two options in the part.options section as they are used by both model and view:
 
  * `passField` - **String** default 'password' - Name of the password field in database.
  * `passCheckField` - **String** default 'password2' - Name of the password verification text input.
  
 * `fileField` - **Array** default empty array - See the [Add action](help_templates_controllers#actionsactionsaddctp) in controller documentation
 * `` - 
 * `` - 
### view
Options related to `view/view.vtp`:

 * `relationsHiddenFields` - array of relation/controllers/fieldnames
 * `relationsHiddenControllers` - array of relation/ControllerName
 * `` - 
 * `` - 
 
### users::register
 
### users::login