# About this plugin
## Backstory
I created superBake because I was discovering CakePHP in its version 2.1. There was no way for me to easily bake all my models, views and controllers with all the prefixes support, a good internationalization support ( `__d() and __()` depending on the plugin I was working on), no menu generation. So I decided to create something that can do all that, based on a configuration file (yes, I know, Cake is powerfull *without* a configuration file...)

That's why I'm working on this. Slowly. Because it's a hobby and because development isn't my *job*.

But now, with superBake, I can generate on the fly all the skeleton of an entire app, and create small modifications in the config file that will change a lot of things in my app...

**And my website has not yet progressed.**

## Used technology for this plugin
 * Frameworks:
 
  * [CakePHP](http://cakephp.org) framework, of course
  * [Twitter Bootstrap](http://getbootstrap.com) framework
  
 * HTML/CSS:
 
  * [LESSCSS](http://lesscss.org)
  
 * Javascript:
 
  * [AutoTOC](http://fuelyourcoding.com/scripts/toc/)
  * [Syntax.highlighter](http://www.oriontransfer.co.nz/software/jquery-syntax)
	* These two plugins works with [jQuery](http://jquery.com)
  
 * Other:
 
  * [Markdown implementation for CakePHP](http://sime.net.au) for the docs
  * [Markdown extended Lib](https://github.com/egil/php-markdown-extra-extended) for the docs
  * [Spyc library](http://code.google.com/p/spyc/) (for YAML manipulation)
  * [FontAwesome](http://fontawesome.io/) icons
	
 * IDE/Softwares
  * I like to work with [netBeans IDE for PHP](https://netbeans.org/downloads/) and this plugin: [CakePHP for Netbeans](http://plugins.netbeans.org/plugin/44579/php-cakephp-framework)
  * To compare my files, I use [Meld](http://meldmerge.org/)
	* I create my databases using [Mysql Workbench](http://dev.mysql.com/downloads/tools/workbench/)
	* I maintain the gitHub repo using [smartGit](http://www.syntevo.com/smartgithg/) community edition
	
This plugin is developped under Linux, but I try to test is as much as I can under Windows too.
	