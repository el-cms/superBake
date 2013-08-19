Version 0.3 is still in alpha and is __really__ not ready to use.
This readme is in development.

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
*Coming soon*
### Additionnal libs
 - `app/Libs/Yaml` is a library made to play with YAML data. It's used by superBake to read the configuraiton file and generate Yaml from arrays.

### Additionnal files
Here is a list of files and folders that may be different from an empty CakePHP project

 - `app/Console/Templates/superCakeStrap` is the default bunch of templates and snippets used by superBake. It uses the Twitter Bootstrap Framework (v3).
 - `app/Console/Commands/super*Task.php` are the tasks used by superBake to generate plugins, models,...
 - `app/Console/superBakeShell.php` is the superBake shell
 - `app/Console/superBakeConfig.yml` is the default configuration file

As the theme is using Twitter Bootstrap Framework, I kept its LESS files in the `app/webroot/less` folder. It will help you customize your CSS, and update the framework when it'll be stable.

## Commands
*Coming soon*
## How to ?
*Coming soon*
### Create my own template
*Coming soon*
## Other
*Coming soon*