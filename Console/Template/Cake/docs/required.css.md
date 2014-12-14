# Required files: CSS
<i class="icon-folder-open"></i> **folder:** *required/css*

## Needed files
The only files needed by the theme are the `admin.css` and `public.css`

## Editing styles
If you want to update the style with your own, edit these LESS files:

 * `common-style.less` - Changes for both admin and public styles
 * `admin-bootstrap.less` - Imports of all the different less files (Twitter Bootstrap, FontAwesome, JS plugins, common-style,...)
 * `admin.less` - Changes for admin styles
 * `public-bootstrap.less` - Imports of all the different less files (Twitter Bootstrap, FontAwesome, JS plugins, common-style,...)
 * `public.less` - Changes for public styles

### Note
Don't forget to compile the LESS files before running superBake if you update them.

## How to import these files:
<pre class="syntax yaml">
[...]
  required:
   adminCss:
     type: file
     ## Source
     source: css::admin.css
     ## Target folder
     target: webroot::css::admin.css
   publicCss:
     type: file
     ## Source
     source: css::admin.css
     ## Target folder
     target: webroot::css::admin.css
</pre>