# "Edit" action
<i class="icon-file"></i> **Template:** *actions/edit.ctp*
<i class="icon-cogs"></i> **Related actions:** [add](../actions.add.md/docs:template)
<i class="icon-eye-open"></i> **Related view:** [edit](../views.edit.md/docs:template)

## Description
Simple edit form for databasse items.

## Required config
There's no prerequisites for this action

## Options
General options:

 * `hiddenAssociations:` *array* - List of belongsTo associations to remove from the form.
 * `layout:` *string, null* - Alternative layout to use for this action

File-upload-specific options: *nothing yet*

Auth-specific options:

 * `conditions:` **array** - List of [conditions](../theme_class.model_conditions.md/docs:template) for new/edited elements that allows to restrict users

### Notes

## Examples

The item should belong to the logged-in user.

<pre class="syntax yaml">
[...]
  options:
    conditions:
      # user_id is the foreign key linking to users in the table
      user_id: %self%
</pre>