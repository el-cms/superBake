# Config: files
<div class="alert alert-danger">
	<i class="icon-warning-sign"></i> This section is incomplete
	<!-- todo Fill the views templates help section -->
</div>
Files are what you want: css templates that can be generated with php, layouts, elements, controllers,... Everything, and why not images ?

Templates for files are located in `<yourTemplate>/files`.

## Default templates:

## files/layouts/
Here are template layouts

## files/AppController.ctp
This is a parametric AppController in replacement of the default AppController.

### Options
 * `enableCache` - **Bool** - default false - If set to true, public actions will be cached for one hour
 * `enableDebugKit` - **Bool** - default false - If set to true, the `DebugKit.Toolbar` will be added in components
 * `enableAcl` - **Bool** - default false - If set to true, Auth and Acl components will be loaded, and admin prefix will be limited to logged-in users.