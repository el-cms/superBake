# "Standard" menu
<i class="icon-file"></i> **template:** *menus/menu.ctp*
<i class="icon-cogs"></i> **related elements:** [menus::elements::user_menu](../menus.element_user_menu.md/docs:template)

## Description
This menu is a complete menu: it will contains all actions for all plugins/controllers, for a given prefixes list.
## Required config
Nothing

## Options

 * `prefixes:` *array, empty* - List of prefixes to use
 * `hiddenPlugins:` *array, empty* - List of plugins to remove from menu
 * `hiddenControllers:` *array, empty* - List of controllers to remove from menu
 * `hiddenControllersActions:` *array, empty* - List of actions to remove from some controllers
 * `hiddenActions:` *array, empty* - List of actions to remove. Actions should not be prefixed
 * `isPublicMenu:` *bool, false* - Define if this menu is public or not.
 * `haveSfwSwitch:` *bool, false* - Define if the menu must have the SFW switch
 * `haveUserMenu:` *bool, false* - Defines if the menu must have the user menu


## Example:
<div class="row">
<div class="col-sm-6">
An admin menu:
<pre class="syntax yaml">
[menuname]:
	generate: true
  template: menu
  targetPath: Elements::menus::admin.ctp
    options:
      prefixes:
        admin
      haveUserMenu: true
</pre>
</div>
<div class="col-sm-6">
A menu that lists everything in the app, except
<ul>
<li>the `UserController` actions</li>
<li>the Acl plugin</li>
<li>the add and view actions from Post controller, in Blog plugin</li>
</ul>
<pre class="syntax yaml">
[menuname]:
	generate: true
  template: menu
  targetPath: Elements::menus::admin.ctp
    options:
      prefixes:
        public
        admin
      hiddenControllers:
        Users
      hiddenPlugins:
      hiddenControllerActions:
        Posts:
          add
          view
      haveUserMenu: true
</pre>
</div>
</div>
