# "Admin" layout
<i class="icon-file"></i> **template:** *files/layouts/admin.ctp*
<i class="icon-cogs"></i> **related files:** [menus::menu](../menus.menu.md/docs:template)

## Description
Default layout template used to generate prefix-specific layouts.

 * Based on CakePHP default layout
 * A red bar appears under the menu to differenciate from the default layout

## Required config


## Options

## Example:
An admin layout:
<pre class="syntax yaml">
[...]
files:
  adminLayout:
    targetPath: View::Layouts::admin.ctp
    template: layouts::admin
</pre>
