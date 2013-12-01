Version 0.3 is still in alpha and is __really__ not ready to use. But you can still test it now with the sample db (in `docs`, sql file or mysql workbench diagram, `superBakeConfig.yml` conf file.)

This readme is in development too.

Feel free to contact me on [g+ EL-CMS](https://plus.google.com/u/0/b/110073171539347252283/) or by [mail](mailto:m.tancoigne@gmail.com) (with "sb" in the beginning of your subject), i'll try to answer quickly.

# superBake version 0.3
## What is superBake
superBake is a console Shell for __cakePHP__ 2.x. It will help you to generate your application's plugins, models, controllers, views and menus with a configuration file and custom templates.

Basically, it's __bake__ with modified tasks and modified template system.

## Installation
In order to install superBake:
 * create a new cakePHP project
 * Create a directory named Sb in /plugin or app/Plugins
 * Extract the superBake files in this dir
 * Enable the plugin in bootstrap file, adding `CakePlugin::load('Sb', array('bootstrap' => true, 'routes' => false));`
 * Start using superBake.
  * Alternatively, you can read the documentation here: http://path/to/cake/sb/docs/display/help