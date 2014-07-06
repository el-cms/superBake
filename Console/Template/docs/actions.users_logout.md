# "Logout" action
<i class="icon-file"></i> **Template:** *actions/users/logout.ctp*
<i class="icon-cogs"></i> **Related actions:** [users::login](../actions.users_login.md/docs:template), [users::register](../actions.users_register.md/docs:template)
<i class="icon-eye-close"></i> **Related view:** none

## Description
This is a basic logout action.

## Required config
For this action to be baked, the [AuthComponent](../theme_config.component_authComponent.md/docs:template) must be enabled in the config file.

## Options
There's no option for this action.

**Note:** this action have no view. Don't forget to declare it.

## Examples
<pre class="syntax yaml">
[...]
actions:
  public:
    logout:
      template: users::logout
      haveView: false
    [...]
</pre>