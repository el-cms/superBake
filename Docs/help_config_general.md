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
  appBase: AppBase
  ## Use routing prefixes
  usePrefixes: true
  ## Prefix that have all rights:
  ## leave empty if you don't use one.
  adminPrefix: admin
  ## By default, update the bootstrap file on plugin generation ?
  updateBootstrap: true
  ## Default DB connection to use (see your `app/Config/database.php` file)
  dbConnection: default

  ##
  ## Personnal informations, mostly here to use in generated headers.
  ##

  ## Your name
  editorName: John Doe
  ## Your email adress
  editorEmail: john@example.com
  ## Your website
  editorWebsite: http://example.com
  ## Your website name
  editorWebsiteName: I remember nothing.
  ## Your license template (find out all licenses in template/commons/licenses/)
  ## This license will be added in generated files
  editorLicenseTemplate: gpl3
  ## Package name (for comments. If someone can explain the use of Packages in files, that would be great:)
  basePackage: IREMEMBER

  ##
  ## Other options for superBake
  ##

  ## Language support: if set to true, app and plugin string will use the
  ## internationalization methods for strings (`__()`, `__d()`). Set it to false,
  ## and strings will be plain text.
  useInternationalizedStrings: true

  ## If set to false, session-related methods will not be included in generated files.
  ## This option is not in the `theme` section, as methods like `Sb::setFlash()` use
  ## its value.
  useSessions: true
</pre>
