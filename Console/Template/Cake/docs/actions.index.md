# "Index" action
<i class="icon-file"></i> **Template:** *actions/index.ctp*
<i class="icon-cogs"></i> **Related actions:** None
<i class="icon-eye-open"></i> **Related views:** [index](../views.index.md/docs:template), [index::article](../views.index_article.md/docs:template), [index::gallery](../views.index_gallery.md/docs:template)

## Description
Simple index action.

## Required config
There's no prerequisites for this action

## Options
General options:

 * `defaultSortOrder:` *string, 'desc'* - Default sorting order
 * `defaultSortBy:` *string, null* - Default column to sort the results on.
 * `recursiveDepth:` *int, 0* - Default find depth for associations
 * `layout:` *string, null* - Alternative layout to use for this action

Auth-specific options:

 * `conditions:` **array** - List of [conditions](../theme_class.model_conditions.md/docs:template) for listing elements that allows to restrict users

### Notes

## Examples

List of items that should belong to the logged-in user.

<pre class="syntax yaml">
[...]
  options:
    conditions:
      # user_id is the foreign key linking to users in the table
      user_id: %self%
</pre>