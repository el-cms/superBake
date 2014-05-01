# Config: theme

The theme section holds configuration related to the current template. Here are the minimum subsections to add in a theme definition.

For more informations on the available options, check the template documentation.

## Base
A theme section should at least have these sections:

<pre class="syntax yaml">
theme:
  ## List of helpers
  helpers: []

  ## List of components with their config:
  components:
    ## Component name:
    SomeComponent:
      ## Use this component or not
      useComponent: false
      ## Component configuration
      [...]

  ## other options
  [...]
</pre>