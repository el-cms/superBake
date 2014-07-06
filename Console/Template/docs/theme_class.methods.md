# Methods from the Theme class

## Controller related methods
### c_indexConditions($condition)
Outputs a condition for a find/paginate call by replacing vars by actual code.


### c_parseSize($size)
Parses a given byte count.
Function from Drupal, found [here](https://api.drupal.org/api/drupal/includes!common.inc/function/parse_size/6)

### c_getFileUploadMaxSize()
Determine the maximum file upload size by querying the PHP settings.
Function from Drupal, found [here](https://api.drupal.org/api/drupal/includes!file.inc/function/file_upload_max_size/6)


## Schema/models related methods

### s_prepareSchemaFields()
Updates the fields list considering the fields to be hidden

Don't forget to update the `$schema` and `$fields` vars in your template after this method call :
<pre class="syntax brush-php">
&lt;?php
$schema=$this-&gt;templateVars['schema'];
$fields=$this-&gt;templateVars['fields'];
?&gt;</pre>


### s_prepareSchemaRelatedFields($model, $relation, $hasOne)
Returns the different string/fields to display for the linked fields of a given schema.

## Views related methods

### v_prepareField($field, $config)
Prepares strings to display for a given field.

### v_prepareRelatedField($field, $config, $originalFieldsList, $hasOne)
Prepares string to display for a given field in associated models.


### v_prepareFieldForeignKey($field, $key, $config)
Prepares a string to use in views top display a foreign key. If the current prefix is allowed to view the action, a link will be made.


### v_formInput($field, $options)
Creates a string for form input in views.


### v_alert($content, $class, $options)
Creates an alert div with given class and content.

**Options:**

 * haveCloseButton true/false*, If true, alert will have a close button.


### v_newDropdownButton($title, $content, $btnSize$style)
Creates a new dropdown buttons group


### v_newButtonGroup($content)
Creates a new buttons group


### v_row($content, $options)
Creates a new row HTML element with $content in it.


### v_dateField($name, $data_format)

### v_formOpenGroup($field, $humanFieldName)
Opens a form group.


### v_formCloseGroup()
Closes a form group.


### v_isFieldKey($field, $associations)
Checks if a field is a foreign key. If true, returns an `array( 'alias' => ;modelName, 'field' => ;displayField|primaryKey, 'details' => ; array(Association details) )` else, returns false.


### v_paginatorField($field, $unsortableFields)
Returns a field name with pagination link if any.


### v_fieldName($field)
Returns an human readable field name.
ex: "user_id" becomes "user id"

## Other methods

### displayArray($array)
Replacement of var_export, output is on one line, strings are protected and variables kepts as variables.
This method is recursive.

### themeInfos()
Returns some infos on the current theme