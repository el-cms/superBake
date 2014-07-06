# Config: views

## Base

To configure a view for an action, define it as follow:

<pre class="syntax yaml">
[...]
  [actionName]:
    ## defines if the action have a view. Useless if defined in defaults.
    haveView: true
    view:
      [view definition]
</pre>


Note that you can define defaults for the views in the defaults section.