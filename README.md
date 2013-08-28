Version 0.3 is still in alpha and is __really__ not ready to use. But you can still test it now with the sample db (in `docs`, sql file or mysql workbench diagram, `superBakeConfig.yml` conf file.)

This readme is in development too.

Feel free to contact me on [g+ EL-CMS](https://plus.google.com/u/0/b/110073171539347252283/) or by [mail](mailto:m.tancoigne@gmail.com) (with "sb" in the beginning of your subject), i'll try to answer quickly.

# superBake version 0.3
## What is superBake
superBake is a console Shell for __cakePHP__ 2.3.x. It will help you to generate your application's plugins, models, controllers, views and menus with a configuration file and custom templates.

Basically, it's __bake__ with modified tasks and modified template system.

## Installation
In order to install superBake, create a new cakePHP project, and extract the superBake files

### Modified files
superBake has some modified files, compared to a fresh cakePHP install :

 - `app/console/appShell.php` contains some methods used by superBake Shell and superBake tasks
 - `app/View/Layouts/default.ctp` is the default layout. As superBakes comes with a template using the Twitter Bootstrap Framework (v3), the default layout has changed

### Additionnal plugin
 - I provide superBake with the Alaxos ACL management Plugin, as it's an easy way to manage your app's ACLs. The plugin has been modified to work with version 2.3.x and the design has been modified too.

### Additionnal libs
 - `app/Libs/Yaml` is a library made to play with YAML data. It's used by superBake to read the configuraiton file and generate Yaml from arrays.

### Additionnal files
Here is a list of files and folders that may be different from an empty CakePHP project

 - `app/Console/Templates/superCakeStrap` is the default bunch of templates and snippets used by superBake. It uses the Twitter Bootstrap Framework (v3).
 - `app/Console/Commands/super*Task.php` are the tasks used by superBake to generate plugins, models,...
 - `app/Console/superBakeShell.php` is the superBake shell
 - `app/Console/superBakeConfig.yml` is the default configuration file

As the theme is using Twitter Bootstrap Framework, I kept its LESS files in the `app/webroot/less` folder. It will help you customize your CSS, and update the framework when it'll be stable.

### About superCakeStrap template
The default template shipped with superBake is named __superCakeStrap__ as everything here is _super_ and it uses the Twitter Bootstrap Framework (version 3 RC2 for now)
As TBF does not comes with glyphicons by default, I prefer the use of 
## Commands
*Coming soon*

## How to ?
*Coming soon*

### Create my own template
*Coming soon*

## Other
*Coming soon*
