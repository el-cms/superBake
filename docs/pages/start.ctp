<h3>And now ?</h3>
<i class="icon-arrow-left"></i> Try to review warnings and errors on the left column.<br/>
Basically, you should at least:
<ul>
	<li>Change the value of <code>Security.salt</code> and <code>Security.cipherSeed</code> in the <code>app/Config/core.php</code> file.</li>
	<li>Set up your database configuration.</li>
</ul>
<h3>Then ?</h3>
Start using superBake. If you don't know where to start, look at the <code>/docs</code> folder. It contains a sample db script and some testing configuration files for superBake.<br />
<ul>
	<li><code>superBakeConfig.min.yml</code> contains a minimum configuration file to work with the DB sample.</li>
	<li><code>superBakeConfig.full.yml</code> contains a more complete configuration file to work with the DB sample.</li>
</ul>
Create a database, execute the script in it, and copy one of the config file in the <code>app/Console</code> directory. You can backup the existing config file if you want to keep an empty one.<br />
Edit the <code>app/Config/core.php</code> file and uncomment line 124 (<code>Configure::write('Routing.prefixes', array('admin'));</code>) to enable routing prefixes. You will just use 'admin' and 'public' prefixes in this example, and public is the default prefix.

<h3>Sample superBake generation</h3>
<div class="alert alert-info">
	<i class="icon-info-sign"></i> <strong>Tip:</strong><br />
	If superBake or your browser throws you errors about non-writable cache files, clear your cache. You use the console as an user, and files created in the cache are yours, and your server (maybe ~www-data) cant overwrite them, creating permissions problems. Or vice versa.
</div>
To generate the sample application, open a console/terminal and go to the <code>app/</code> dir, as you would have done for <code>Bake</code>.<br />
<ol>
	<li>Your are going to create your plugins' structures first. Type in your Terminal:
		<ul>
			<li><code>./Console/cake superBake plugins</code></li>
			<li>When the script asks for a bootstrap update, select 'yes', to update the bootstrap file with the new plugins</li>
			<li>Now, look at the directory structure: plugins folders have been created in <code>app/Plugins</code>
		</ul>
	</li>
	<li>Now, you're going to create models in each plugins. Type this:
		<ul>
			<li><code>./Console/cake superBake models</code></li>
			<li>And models are generated.</li>
		</ul>
	</li>
	<li>Next step is controllers:
		<ul>
			<li><code>./Console/cake superBake controllers</code></li>
		</ul>
	</li>
	<li>...Views
		<ul>
			<li><code>./Console/cake superBake views</code></li>
		</ul>
	</li>
	<li>...and menus
		<ul>
			<li><code>./Console/cake superBake menus</code></li>
		</ul>
	</li>
	<li>Menus are not included in the default layout, as the files doesn't exists on a fresh install.<br />
		You have to manually edit the <code>app/View/Layouts/default.ctp</code> and remove the comments on lines 41/42 (menus elements)<br />
	</li>
	<li>
		Open your browser, and go to your cake fresh install. 
	</li>
</ol>
<div class="alert alert-info">
	<i class="icon-info-sign"></i> <strong>Tip:</strong><br />
	Every superBake command can be launched in quiet mode (just add <code> -q</code> on the command line). Even the menu is smaller in quiet mode.
</div>

