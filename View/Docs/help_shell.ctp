# Shell: Base things to know

## Shells
superBake comes with two shell files:
 
 * `ShellShell.php` - This is the actual shell with menu and methods.
 * `SbShell.php` - This is a class extended by the shell and tasks. The methods from this class are available in the Shell and the tasks.
 
## Tasks
The following tasks are based on the CakePHP's tasks.

### SuperPluginTask
This task handles plugin generation: plugin structure and base files.

### SuperModelTask
This task handles model generation. All the logic for interactive baking has been removed and support for superBake's config has been setup.

### SuperControllerTask
This task handles controller generation.

### SuperViewTask

### SuperMenuTask

### TemplateTask
This task is exactly the same as CakPHP's TemplateTask, but it extends SbShell instead of AppShell. This way, methods from SbShell are available in superBake's templates.