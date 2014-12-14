# "Default" layout
<i class="icon-file"></i> **template:** *files/layouts/default.ctp*
<i class="icon-cogs"></i> **related files:**[menus::menu](../menus.menu.md/docs:template)

## Description
Default layout template used to generate prefix-specific layouts.

 * Based on CakePHP default layout

## Required config


## Options

## Example:
An admin layout:
<pre class="syntax yaml">
[...]
files:
  adminLayout:
    targetPath: View::Layouts::default.ctp
    template: layouts::default
</pre>
