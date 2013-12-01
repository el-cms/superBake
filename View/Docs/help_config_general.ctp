# Config: General section

This section is used to define general configuration keys and values, used by both superBake Shell and templates.

## Default keys:
<pre class="syntax yaml">
#--------------------------------------------------------------------------
#
# General information
#
#--------------------------------------------------------------------------
general:
  ## Name of the "plugin that isn't one". This is a very special plugin:
  ## Everything in it will be created in the app/ dir, not in a plugin.
  ## Change this value if you want to name one of your plugin "appBase"
  appBase: appBase
  ## SuperBake template to use
  template: superCakeStrap
  ## Use prefixes ?
  usePrefixes: true
  ## By default, update the bootstrap file on plugin generation ?
  updateBootstrap: true
  ## Default DB connection to use
  dbConnection: default
#  ## Empty parts must have a model ?
#  ## If set to false, you will have to define each of your models yourself
#  partsHaveModel: true
#  ## Empty parts must have a controller ?
#  ## If set to false, you will have to define each of your controllers yourself
#  partsHaveController: true
  
  ##
  ## Personnal informations, mostly here to use in generated headers.
  ##

  ## Your name
  editorName: John Doe
  ## Your email adress
  editorEmail: john@mem-recovery.com
  ## Your website
  editorWebsite: http://i-remember.com
  ## Your website name
  editorWebsiteName: I remember nothing.
  ## Your license template (find out all licenses in templates/<defaultTemplate>/commons/licenses/)
  editorLicenseTemplate: gpl3
  ## Package name (for comments. If someone can explain the use of Packages in files, that would be great:)
  basePackage: IREMEMBER
</pre>

## Next ?
[Next part: Defaults section](help_config_defaults)