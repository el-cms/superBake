Version 0.3 is still in alpha and is _not ready_ to use. But you can still test it now with the sample db (in `docs`, sql file or mysql workbench diagram, `superBakeConfig.yml` conf file.)

This readme is in development too.

Feel free to contact me on [g+ EL-CMS](https://plus.google.com/u/0/b/110073171539347252283/) or by [mail](mailto:m.tancoigne@gmail.com) (with "sb" in the beginning of your subject), i'll try to answer quickly.

For now, the configuration file provided with the plugin is the one I work on, so it's not an empty config file. You can test it with the models structure available in `docs/`.

# superBake version 0.3
## What is superBake
superBake is a console Shell for [__cakePHP__](http://cakephp.org/) 2.x. It will help you to generate your application's plugins, models, controllers, views and menus with a configuration file and custom templates.

Basically, it's __bake__ with modified tasks, modified template system and a configuration file.

You can find a demo video on [youtube](https://www.youtube.com/watch?v=sP9WOk7qmwA) (a bit outdated now).

## What superBake isn't

superBake is not a CMS, a blog manager or anything. It will help you to create your apps. That's all.

## Last big changes:

 * added "generation from GUI", accessible at `http://<cakeInstall>/sb/sb/tree`

## Test it in a few simple steps:

 * Download CakePHP [2.4.3](https://github.com/cakephp/cakephp/zipball/2.4.3) (for example) and superBake.
 * Prepare Cake and unzip superBake in a folder named `Sb` in either `app/Plugin` or in `plugin`. Don't forget to load the plugin in `app/bootstrap.php` with this line: `CakePlugin::load('Sb', array('bootstrap' => true, 'routes' => false));`
 * Set up your db and db connection in `app/Config/database.php`
 * Run/upload the sample db model located in `Sb/docs`. There's a SQL file and a Mysql Workbench file for the same db. Choose as you prefer.
 * Open a browser and go to your cake Homepage. In parallel, open a terminal and cd to `PathToCakeInstall/app`
 * You may need to make the `app/Console/cake` file executable, under linux (`chmod +x Console/cake`)
 * You're ready to superBake:
  1. Plugins: `./Console/cake Sb.Shell plugins`. Choose to update the bootstrap file.
  2. Models/Controllers and views: `./Console/cake Sb.Shell mvc`
  3. Menus: `./Console/cake Sb.Shell menus`
  4. Additionnal files (custom layout and AppController): `./Console/cake Sb.Shell files`
  5. Required files (a css file): `./Console/cake Sb.Shell required`
  6. Refresh your browser.

## What if it doesn't work as expected ?

Open an issue, explain the problem, I'll be happy to help.