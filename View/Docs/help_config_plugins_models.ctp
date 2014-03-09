# Config: models

&rarr; [Configuration: controllers](help_config_plugins_controllers)
&rarr; [Templates: model snippets](help_templates_models)

---
## Base

To configure a model in a plugin's part, define it as follow:

<pre class="syntax yaml">
<em>PluginName:</em>
  <em>parts:</em>
    <em>partName:</em>
      model:
        [model configuration]
</pre>

You can base your configuration on the [default configuration for a model](help_config_defaults#model)

## Snippets
In a model definition, snippets are defined as follow:

<pre class="syntax yaml">
[...]
  <em>model:</em>
    [...]
    snippets:
      ## Use what you want to identify your snippet.
      ## If a snippet template isn't found, a comment using this name will be set in the generaed model.
      snippet1:
        path: path::to::snippet1
        options:
          option1: value1
          option2: value2
      snippet2:
        path: path::to::snippet2
        options:
          option1: value1
          option2: value2
</pre>

### Snippet options:
Snippets options consists of the options you defined yor the snippet _plus_ the part's options.
Part options are overriden by snippets options:
<div class="row">
	<div class="col-lg-4 col-md-4">
		<strong>This:</strong>
		<pre class="syntax yaml">
parts:
  Users:
    options:
      option1: Value1
      Option2: Value2
    model:
      snippet:
        randomSnippet:
          options:
            Option2: SomeVal
            Option3: Value3
</pre>
	</div>
	<div class="col-lg-4 col-md-4">
		<strong>Is the same as this:</strong>
		<pre class="syntax yaml">
parts:
  Users:
    options:
      Option1: Value1
      Option2: Value2
    model:
      snippet:
        randomSnippet:
          options:
            Option1: Value1
            Option2: SomeVal
            Option3: Value3
</pre>
	</div>
	<div class="col-lg-4 col-md-4">
		<strong>Or this:</strong>
		<pre class="syntax yaml">
parts:
  Users:
    options:
    model:
      snippet:
        randomSnippet:
          options:
            Option1: Value1
            Option2: SomeVal
            Option3: Value3
</pre>
	</div>
</div>

But in the last example, controllers and views won't have access to the part options.