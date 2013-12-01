# Templates: models:
## Model class template
The model skeleton is `<yourTemplate>/classes/model.ctp`. It contains all the logic to create a model file with snippets and so.
Possible options for this are :

 * _Hum... sorry, I have done nothing yet_ <i class="icon-meh"></i>

## Snippets
Model snippets are located in `<yourTemplate>/models/`.

All model snippets have access to these variables:

 * **Vars from superBake:**
 
  * `$sbc` - A copy of the superBake object. You have now access to the whole config file and the methods to play with it.
  * `$options` - An array of options defined in your snippets + the options from the part.
  * `$displayField` - The display field name - If empty, CakePHP will use the primary key.
  * `$part` - Part name
  * `$modelConfig` - The whole model configuraiton from config file, for an easy access.
  
 * **Vars from CakePHP:**
 
  * `$name` - The current model name
  * `$theme` - Current template name
  * `$plugin` - Current plugin name. Can be empty if it's appBase.
  * `$pluginPath` - Plugin name with a '.', for cross plugins links
  * `$associations` - Array of possible associations
  * `$validate` - The validation array
  * `$primaryKey` - The primary key
  * `$useTable` - Used table name
  * `$useDbConfig` - Used database configuration

## Default snippets

 * **`models/missing_code.ctp`** - This snippet is used when a defined snippet is not found. It contains PHP comments to explain the situation and a `@todo` statement.
 
  * _There is no option for this snippet._
  
 * **`models/acls/roles.ctp`** - This snippets adds the mechanics used to define a certain table as the _role_ model. I'ts used for Acls and is based on the cakePHP tutorial on how to set up Acls.
 
  * _There is no option for this snippet._
  
 * **`models/acls/users.ctp`**
This snippets adds the mechanics used to define a certain table as the _users_ model. I'ts used for Acls and is based on the cakePHP tutorial on how to set up Acls. Options are:

  * `passField` - **Default:** password - Database password field name.
  * `passCheckField` - **Default:** password2 - Name of the confirmation field, to use in forms (create/update)
  * **I recommand to define these two values in the part options as they are needed by the other users actions and views**
