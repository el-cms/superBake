# The configuration file.

The configuration files can be found in `Sb/Console/Template/config`. You can use one config file or split it in multiple files for an easier organisation.

<div class="alert alert-info">
<i class="icon-info icon-3x pull-left"></i> <strong>Hiding files</strong><br />
If you want to remove a file from the final array, name it with an underscore (<code>_</code>) and it won't be loaded.
</div>

The configuration file is segmented in four main sections.
The two first sections are generic values:

 * the **[general](help_config_general.md)** section, which holds general information
 * the **[defaults](help_config_defaults.md)** section, which holds default configurations

Then we have the section that _describe_ your application:

 * the **[plugins](help_config_plugins.md)** section, which actually holds configuration about your plugins and your app.

And finally, the section related to the theme you use:

 * the **[theme](help_config_theme.md)** section, which holds configuration related to the theme (_template_) you use to superBake your app.

Additionnaly, you can add a `description` section where you briefly describe the file. It will be used in the superBake GUI, and will help you organize your files.

For now, everything is in a single file.

## How it works
When the configuration file is loaded by superBake, plugins will be automatically filled with portions from the **[defaults](help_config_defaults.md)** part: incomplete or missing portions in your configuration is not a problem.
This allows you to create light configuration files.

I recommend you to take a look at the [defaults](config_defaults.md) section, to see how it's done.

### Example of a small configuration file
<div class="alert alert-info">Ìn this example, parts defined as <code>[...]</code> are shortened for a better readabilility.</div>
<div class="row-fluid">
<div class="span6">
<h4>Configuration file:</h4>
<pre class="syntax yaml">
description: Sample configuration file.
general: [...]
defaults: [...]
theme: [...]
plugins:
  ## "Plugin that's not one": this must be baked in the app/ subdirs.
  appBase:
    ## parts
    parts:
      ## Users part. Named following the controllers naming convention, as
      ## if no model or controller names are provided, the part will be used as base.
      Users:
      Groups:

  ## A blog plugin
  Blog:
    parts:
      Posts:
      PostCategories:
      PostComments:
</pre>

</div>
<div class="span6">
<h4>Populated result:</h4>
<pre class="syntax yaml">
description: >
  Sample configuration file.
general: [...]
defaults: [...]
theme: [...]
plugins:
  appBase:
    displayName: App Base
    path: app::Plugin
    generate: true;
    haveRoutes: false
    haveBootstrap: false
    parts:
      Users:
        generate: true
        haveModel: true
        model:
          name: User
          generate: true
          displayfield:
          snippets: [ ]
          options: [ ]
        haveController: true
        controller:
          name: Users
          displayName: Users
          generate: true
          actions:
            public:
              index:
                template:
                options: [ ]
                haveView: true
                view:
                  template:
                  generate: true
                  options: [ ]
                blackListed: false
              view:
                [...]
            admin:
              index:
                [...]
              view:
                [...]
              add:
                [...]
              delete:
                template:
                haveView: false
                options: [ ]
                view: [ ]
                blackListed: false
              edit:
                [...]
        options: [ ]
      Groups: [...]
</pre>
</div>
</div>

## How parts are set together
Here is the way a plugin is populated:

### Base configuration
<pre class="syntax yaml">
## general part
general: [...]

## defaults part
defaults: [...]

## plugins part
plugins:[...]

  ## The plugin name
  Plugin1:

   ## Defaults for a plugin
   [...]

   ## Part section
   parts:

     Part1:
       ## Defaults for a part
       [...]

       ## model section of a part:
       model:
         ## Defaults for a model
         [...]

         ## Snippet section for a model
         snippets:

           ## Snippet name (useless, just to organize your work)
           snipetName:
             ## Defaults for a snippet
             [...]

       ## Controller section for a part
       controller:
         ## Defaults for a controller
         [...]

         ## Actions section of a controller
         actions:
           ## Default prefixes/actions
           [...]

           ## Prefixes
           prefix1:

             ## Actions list
             Action1:
               ## Defaults for an action
               [...]

               ## View section of an action
               view:
                 ## Defaults for a view
                 [...]

    ## Menus section of a plugin
    menus:
    ## Files section of a plugin
    files:
    ## Required files section of a plugin
    required:
</pre>
<div class="alert alert-info">
<i class="icon-info icon-3x pull-left"></i> <strong>Overriding defaults</strong><br />
Add a <code>useDefaults: false</code> option in any part if you don't want to use the defaults settings. <span class="text-danger">Be careful as sub-configuration will not use defaults either.</span>
</div>
## Debugging
If you don't understand the way your config file is populated, you can take a look on the <?php echo $this->Html->link('Population log', array('controller'=>'sb', 'action'=>'check'));?>