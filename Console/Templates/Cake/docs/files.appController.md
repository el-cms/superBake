# "AppController" file
<i class="icon-file"></i> **template:** *files/appController.ctp*

## Description
AppController file to be created in `app/Controller`.

 * Adds custom components/helpers
 * Layout change depending on prefixes
 * Prepares some variables for views (as the current URL, the SFW state, current language,...)
 * Language support

## Options
Options from theme:

 * `theme.components`
 * `theme.helpers`
 * [AuthComponent](../theme_config.component_authComponent.md/docs:template)
 * `theme.language`

## Example:
<pre class="syntax yaml">
[...]
files:
  appController:
    targetPath: Controller::AppController.php
    template: appController
</pre>
