# "bootstrap" file
<i class="icon-file"></i> **template:** *files/bootstrap.ctp*

## Description
Bootstrap file to be created in `app/Config`.

 * Website config
 * Language support
 * Plugin list

## Options
Configuration from theme:

 * `theme.language`

Configuration from general:

 * Almost all general options...

## Example:
<pre class="syntax yaml">
[...]
files:
  bootstrap:
    targetPath: Config::bootstrap.php
    template: bootstrap
</pre>
