# "Login" action
<i class="icon-file"></i> **Template:** *actions/users/login.ctp*
<i class="icon-cogs"></i> **Related actions:** [users::logout](../actions.users_logout.md/docs:template), [users::register](../actions.users_register.md/docs:template)
<i class="icon-eye-open"></i> **Related view:** [users::login](../views.users_login.md/docs:template)

## Description
This is a basic login action.

## Required config
For this action to be baked, the [AuthComponent](../theme_config.component_authComponent.md/docs:template) must be enabled in the config file.

## Options
 * **layout:** *string, null* - Alternative layout to use for this action

## Examples
<pre class="syntax yaml">
[...]
  actions:
    public:
      login:
        template: users::login
          options:
            layout: login
          [...]
</pre>