SuperBake Shell for CakePHP 2.3.x
=================================
Thank you for using the Superbake Shell v0.2

This console shell is designed for CakePHP 2.3.x

Demo
----
As a demo, I finally [took screenshots](https://plus.google.com/b/110073171539347252283/photos/110073171539347252283/albums/5859403751260073329) during tests.

Is this for me ?
----------------
If you're in an early stage of development, this script will help you for
generation of parts of your app.
As superBake is very young, you'll have to edit the superBaked files as you would
have done with bake, but less...

If you're creating a small app (no plugins, some models/controllers,...), this
script can help you creating all that as a batch.

Note, superBake does not use scaffolding, 
Note again, superBake is here for batch generation. If you need to rebuild one
of your models/controllers/view, use Bake.

For the moment, integration of superBake templates with bake isn't good at all.

Note that SuperBake don't bake controllers without models (for now)

Please, backup your app before testing this shell. Thank you !

Directory structure
-------------------
<pre>
<CakeBase>
  /app
		/Console
			/Command
				AppShell.php					Modified AppShell
				SuperBakeShell.php				Shell script
				/Task
					SuperControllerTask.php		Controller generation
					SuperModelTask.php			Model generation
					SuperPluginTask.php			Plugin creation
					SuperViewTask.php			View generation
			/Template
				/superBake 						SuperBake templates (nearly the same as classical bake templates)
					/actions
						controller_actions.php 	File called to create controllers
						/snippets 				Folder with actions samples
							<dirs and files>	
					/classes					Layouts for controllers, fixtures, models and tests.
					/common						Snippets that can be used by both models, controllers and views.
						/licenses				Licenses snippets (used in generated headers)
							gpl3.ctp			GPL v3 licence snippet for headers.
					/models						Snippets for models
						<dirs and files>
					/views 						View samples for view generation
						<dirs and files>

</pre>
Installation guide
------------------
 - To include superBake Shell in your app, just add the archive files in the
app/Console directory.


Usage
-----
 - Then, you have to edit one file. I know that's rude, but I can't guess your app
structure. So, manually edit the app/Console/superBakeConfig.yml. Spend a few minutes
to understand its structure and edit it to fit your needs.

You're almost done !

- Open a console/terminal/whatever you name it
- CD to your <BaseCake>/app dir
- Launch this command: cake superBake
- Follow instructions :D
- Open a browser
- Pray the CakeNoob god not to have let me down during development
- Take a quick look at the generated pages
- If something is wrong, close your eyes quickly, walk backward in any other room
and cry a lot. Sorry.

Sample files
------------
No more sample files. Ask in [github issues](https://github.com/mtancoigne/superBake/issues) and i'll take time to create some.

How it works
------------
SuperBake Shell is almost just modified CakePHP's Bake files, with configuration file to have a easier
baking process.

Contact/help/support
--------------------

For any help, as there is no website for EL-CMS (nor SuperBake, of course) now, feel free to use my [g+ page](https://plus.google.com/b/110073171539347252283/110073171539347252283/posts), or the GitHub issue system...

And if you want to help me to buy coffee: [![Flattr this git repo](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=kure&url=https://github.com/mtancoigne/superBake&title=superBake&language=&tags=github&category=software) 

Todo
----
A (in)complete list of things to do is described on the [roadmap/todo wiki page](https://github.com/mtancoigne/superBake/wiki/Roadmap-todo)
