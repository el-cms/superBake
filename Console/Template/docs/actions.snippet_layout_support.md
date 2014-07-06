# Layout support - snippet for actions
<i class="icon-file"></i> **template:** *actions/snippets/layout_support.ctp*

## Description
This file is included in each action that have a view and defines an alternative layout.

## Options
 * `layout:` *string, null* - Alternative layout

## Notes
As this is included in other actions, you have to define the options in the actions' options.

## Example
This will change the layout used for the login action.
<pre class="syntax yaml">
[...]
actions:
  public:
    login:
      options:
        layout: login
</pre>
