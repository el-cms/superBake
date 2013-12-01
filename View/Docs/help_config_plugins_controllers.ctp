# Config: controllers

&rarr; [Configuration: views](help_config_plugins_views)
&rarr; [Templates: controllers actions](help_templates_controllers)

---
## Base

To configure a controller in a plugin's part, define it as follow:

<pre class="syntax yaml">
<em>PluginName:</em>
  <em>parts:</em>
    <em>partName:</em>
      controller:
        [controller configuration]
</pre>

You can base your configuration on the [default configuration for a controller](help_config_defaults#controller)

## Actions list
The actions list in a controller (`actions` section) should contain the actions for the different prefixes used by your app. Don't forget to activate them in the `app/Config/core.php`.

The actions list is based on the [defaults actions](help_config_defaults#actions), that means that ALL actions defined in the default section are added to a controller's actions list.
If you want to disable one of the default actions for a specific controller, do it this way:

<pre class="syntax yaml">
## Default action list with an annoying action in it
defaults:
  actions:
    someprefix:
      annoyingAction:
        [...]

## Plugins
plugins:
  SomePlugin:
    parts:
      People:
        controller:
          actions:
            someprefix:
              annoyingAction:
                ## That will do the job !
                blackListed: true
</pre>