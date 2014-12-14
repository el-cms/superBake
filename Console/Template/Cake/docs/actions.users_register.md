# "Register" action
<i class="icon-file"></i> **Template:** *actions/users/register.ctp*
<i class="icon-cogs"></i> **Related actions:** [users::logout](../actions.users_logout.md/docs:template), [users::login](../actions.users_login.md/docs:template)
<i class="icon-eye-open"></i> **Related view:** [users::register](../views.users_register.md/docs:template)

## Description
Basic "register" action that allows people to register on the site.
There's no special mechanisms here (no email validation, password recovery,...) but if you write one, i'll be glad to use it.

## Required config
For this action to be baked, the [AuthComponent](../theme_config.component_authComponent.md/docs:template) must be enabled in the config file.

## Options
This action uses options from the [AuthComponent](../theme_config.component_authComponent.md/docs:template) config:

 * **userStatusField:** *string* User status field
 * **defaultUserStatus:** *string|int|bool* Default status for new users
 * **userCanChooseRole:** *bool* determines if an user can choose its role
 * **defaultRoleId:** *string|int* Default role Id for new users

Additionnally, you have these options:

 * **layout:** *string, null* - Alternative layout to use for this action

## Examples
<pre class="syntax yaml">
[...]
actions:
  public:
    register:
      template: users::register
      options:
        [...]
</pre>
