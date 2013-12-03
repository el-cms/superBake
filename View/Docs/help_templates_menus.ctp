# Config: menus
Templates for menus are located in `<yourTemplate>/menus`.

<div class="alert alert-danger">
	<i class="icon-warning-sign"></i> This section is incomplete
	<!-- todo Fill the views templates help section -->
</div>

If you look a default values for a menu file, it's the same as a "File" section. That's because both menus and files generation use the same task: superFileTask, wich is a modified viewTask.

## Default menu
The default menu comes with these options:

 * `hiddenPlugins` - **Array, default none** - List of plugins to exclude from the menu.
 * `prefixes` - **Array, default none** - List of prefixes to include in the menu.
 * `hiddenController` - **Array, default none** - List of controllers to exclude from the menu. 
 * `hiddenActions` - **Array, default none** - List of actions to exclude from the menu. In each controllers.
 * `` - **** - 
 * `` - **** - 

## Examples

### Simple admin menu

<pre class="syntax yaml">
[...]
  menus:
    admin:
      WRITE HERE !
      template: menu
</pre>

### Simple public menu