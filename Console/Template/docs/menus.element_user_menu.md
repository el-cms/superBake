# "User menu" dropdown for menus
<i class="icon-file"></i> **template:** *menus/elements/user_menu.ctp*
<i class="icon-cogs"></i> **related menus:** [menus::composed](../menus.composed.md/docs:template), [menus::menu](../menus.menu.md/docs:template)

## Description
Dropdown menu for logged-in users. This template is meant to be included in your menus.

## Required config
 * [AuthComponent](../theme_config.component_authComponent.md/docs:template) should be enabled.

## Options

 * `isPublicMenu:` *bool, false* - If set to true, the element will be 'protected' with a check to see if an user is logged in or not.
