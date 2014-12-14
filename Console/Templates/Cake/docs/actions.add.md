# "Add" action
<i class="icon-file"></i> **Template:** *actions/add.ctp*
<i class="icon-cogs"></i> **Related actions:** [edit](../actions.edit.md/docs:template)
<i class="icon-eye-open"></i> **Related view:** [add](../views.add.md/docs:template)

## Description
This method will add data in the database.
It can handle file upload and thumbnail generation for images files

## Required config

 * If you plan to use image upload with thumbnail creation, add the [SimpleImage class](../required.lib_SimpleImage.md/docs:template) to the required files.

## Options
General options:

 * `hiddenAssociations:` *array* - List of belongsTo associations to remove from the form.
 * `layout:` *string, null* - Alternative layout to use for this action

File-upload-specific options:

 * `fileField:` *array, null* - Options for file field.

   * `type:` *string* - File type, can be **image** or **file**
   * `name:` *string* - Name of the field that stores the file name
   * `allowedExts:` *array* - An array of allowed file extensions (extensions are without the '.')
   * `maximumSize:` *string* - Maximum file size (ex: 10Mb, 120Kb,...). Default is the server's max upload size
   * `path:` *string* - Destination folder, relative to your `app/webroot` directory.
   * `thumb:` *bool* - _optionnal_ - If true, will generate a thumbnail

     * `path` - Thumbnail path

Auth-specific options:

 * `conditions:` **array** - List of [conditions](../theme_class.model_conditions.md/docs:template) for new/edited elements that allows to restrict users

### Notes

 * You should define these options in the part section directly, so they are available in edit actions and views too.
 * If you don't specify the field name, `file` will be used.
 * For a quick file field definition, you can do something like this: `fileField: fieldName`. Defaults will be used (look at the template)



## Examples
<div class="row">
    <div class="col-lg-6 col-md-6">
    Example for an item with a file field. The item should be assigned to the logged-in user.
    <pre class="syntax yaml">
[...]
  options:
    conditions:
      # user_id is the foreign key linking to users in the table
      user_id: %self%
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
    Example for an item with an image field with thumbnail creation and no user-specific links:
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
      thumbnails:
        path: uploads::images::tmb
</pre>
    </div>
</div>