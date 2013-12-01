# The configuration file.

The configuration file can be found in `Sb/Console/Configurations`. You can store all your configurations here, but remember that superBake will **only use** the one named `superBakeConfig.yml`.

The configuration file is segmented in three main sections.
The two first sections are generic values:

 * the **[general](help_config_general)** section, which holds general information
 * the **[defaults](help_config_defaults)** section, which holds default configurations

Then we have the section that _describe_ your application:

 * the **[plugins](help_config_plugins)** section, which actually holds configuration about your plugins and your app.
 
Additionnaly, you can add a `description` section where you briefly describe the file. It will be used in the sb web interface, and will help you organize your files.

For now, everything is in a single file.

## Basics
To be clear with the words I use in this help, i'll use _section_ for the main sections (general, defaults, plugins and additionnaly _description_), _parts_ will represent the differents parts in a plugin, and _fragments_ or _portions_ will be chunks of configuration.
 
When the configuration file is loaded by superBake, plugins will be automatically filled with portions from the **[defaults](help_config_defaults)** part: incomplete or missing portions in your configuration is not a problem. 
This allows you to create light configuration files.

I recommend you to take a look at the [defaults](config_defaults) section, to see how it's done.

## Examples
<div class="alert alert-info">Ìn this example, parts defined as <code>[...]</code> are shortened for a readabilility issue only.</div>
<div class="row">
<div class="col-lg-6 col-md-6">
<h3>Simple example:</h3>
<pre class="syntax yaml">
description: Sample configuration file.
general: [...]
defaults: [...]
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
<br />
<p>That is a very simple configuration file, isn't it ? Now, look at the populated configuration array:</p>


</div>
<div class="col-lg-6 col-md-6">
<h3>Populated result:</h3>
<pre class="syntax yaml">
description: >
  Sample configuration file.
general: [...]
defaults: [...]
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

Parts have been stripped to make a smaller display, but as you can see, everything is populated

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

## Debugging
If you don't understand the way your config file is populated, you can take a look on the <?php echo $this->Html->link('Population log', array('controller'=>'sb', 'action'=>'check'));?>