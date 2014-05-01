# "Delete" action
<i class="icon-file"></i> **Template:** *actions/delete.ctp*
<i class="icon-cogs"></i> **Related actions:** None
<i class="icon-eye-close"></i> **Related view:** None

## Description
This method will delete data from the database

## Required config

There's no prerequisites for this action

## Options
No general options


Auth-specific options:

 * `conditions:` **array** - List of [conditions](../theme_class.model_conditions.md/docs:template) for new/edited elements that allows to restrict users

### Notes

 * This action have no view.

## Examples
<div class="row">
    <div class="col-lg-6 col-md-6">
    Example: delete one thing
    <pre class="syntax yaml">
[...]
  delete:
    haveView: false
</pre>
    </div>
    <div class="col-lg-6 col-md-6">
    Example: delete currently logged-id user's thing:
    <pre class="syntax yaml">
[...]
  delete:
    haveView: false
    options:
      conditions:
        # user_id is the foreign key linking to users in the table
        user_id: %self%
</pre>
    </div>
</div>