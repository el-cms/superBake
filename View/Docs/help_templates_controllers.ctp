# Templates: controllers
Controllers are classes made of actions (the methods), and variables that define used components, helpers,...

## Controller class template
 * `<yourTemplate>/classes/controller.ctp` - This file is a controller skeleton: it handles everything that's not an action in controllers (helpers, components,...).
 * `<yourTemplate>/actions/controller_actions.ctp` -  This file handles the controller skeleton.
 
### Skeleton's options
These options are handled by the skeleton (defined in you controller's `options` section):
If all your controllers need one of these, define it in the defaults.

---

**`libraries`** - Array of additionnal libraries (path: library)
**Example:**
   
<pre class="syntax yaml">
##
## you need to use the Spyc library (Yaml operations) located in the lib/Yaml folder :
##
[...]
  controller:
    options:
      libraries:
        Yaml: Spyc
        ## this will generate
        ## app::uses('Spyc', 'Yaml');
        ## in the controller
</pre>

## Actions templates
Actions templates are located in the `<yourTemplate>/actions/actions/` directory.
All action templates have access to these variables:

 * **Vars from superBake:**
 
  * `$sbc` - an instance of the Sbc object.
  * `$currentPart` - The current part name
  * `$currentController` - The current controller name
  * `$prefix` - Prefix for the current action ("public" for no prefixes) 
  * All config keys from the current action, as variables:
  
   * `$template`,
   * `$options`,
   * `$haveView`,
   * ... and other keys you would have defined.
 
 * **Vars from CakePHP:** (examples for a "Users" controller)
 
  * `$pluralVar` - "users"
  * `$plugin` - The plugin name
  * `$admin` - Current prefix.
  * `$controllerPath` - "users"
  * `$pluralName` - "users"
  * `$singularName` - "user"
  * `$singularHumanName` - "User"
  * `$pluralHumanName` - "users"
  * `$modelObj` - The model object
  * `$wannaUseSession` - True if session should be used
  * `$currentModelName` - "User" - The model name
  * `$displayField` - The display field
  * `$primaryKey` - The primary key
  
 * **Other vars:**
 
  * `$a` - Action name
  * `$actionConfig` - Action configuration array
	
 * **Methods:** see the [basics template documentation](help_templates)

### Templates:

#### **`actions/actions/add.ctp`**
An _add_ action template. Options for this action:

 * <i class="icon-star-empty text-success" data-toggle="tooltip" title="Optionnal"></i> `fileField` - If set, the action will include mechanics to upload a file. The file name will be stored in the `fileField.name` field. **I recommand to define this in the part section, so it's available for the views to.**
 
  * `name` - Name of the file field
  * `allowedExts` - An array of allowed file extensions (extensions are without the '.')
  * `maximumSize` - Maximum file size. Default is the server's max upload size
  * `path` - Destination folder, relative to your `app/webroot` directory.
  * `type` - File type (_file_ or _image_). If image is set, thumbnails creation mechanics will be created.
  * `thumb` - _optionnal_ - If true, will generate a thumbnail
  
   * `path` - Thumbnail path

<div class="row">
    <div class="col-lg-6 col-md-6">
    Example for a file field:
    <pre class="syntax yaml">
[...]
  options:
    filefield:
      name: file
      allowedExts:
        - zip
        - pdf
        - odt
      maximumSize: 200kb
      path: uploads::documents
</pre>
    </div>
    <div class="col-lg-6 col-md-6">
    Example for an image field:
    <pre class="syntax yaml">
[...]
  options:
    filefield:
      name: pic
      allowedExts:
        - jpg
        - jpeg
        - gif
        - png
      maximumSize: 200kb
      path: uploads::images
</pre>
    </div>
</div>

#### **`actions/actions/delete.ctp`**
This action have no options, but don't forget **it has no view too.**

#### **`actions/actions/edit.ctp`**

Options for this action:

#### **`actions/actions/index.ctp`**

Options for this action:
 
 * <i class="icon-star-empty text-success" data-toggle="tooltip" title="Optionnal"></i> `defaultSortBy` - _fieldname - default null_ - A field name to sort the results.
 * <i class="icon-star-empty text-success" data-toggle="tooltip" title="Optionnal"></i> `defaultSortOrder` - _'desc' | 'asc' - Default 'asc'_ - Default sorting order
 * <i class="icon-star-empty text-success" data-toggle="tooltip" title="Optionnal"></i> `defaultRecursiveDepth` - _Int - Default 0_ - If set to 1, data associated with db entries will be returned too.
 
**Example:**
<pre class="syntax yaml">
[...]
  People:
    controller:
     actions:
       public:
         index:
           options:
             defaultSortBy: birthdate
             defaultSortOrder: asc
             defaultRecursiveDepth: 0
</pre>

#### **`actions/actions/view.ctp`**

Options for this action:
 
 * <i class="icon-star-empty text-success" data-toggle="tooltip" title="Optionnal"></i> `layout` - _string - default empty_ - If set, the given layou will be used for this action. This is usefull when you have multiple layouts for your prefixes.
 
#### **`actions/actions/missing_action.ctp`**

This templates creates an empty action if you declare one that doesn't exists in the config file. This action contains a `@todo` element in its comments and throws an exception to remind you of this non-existant action.

#### **`actions/actions/users/login.ctp`**

Options for this action:

#### **`actions/actions/users/logout.ctp`**

Options for this action:

#### **`actions/actions/users/register.ctp`**

Options for this action:
