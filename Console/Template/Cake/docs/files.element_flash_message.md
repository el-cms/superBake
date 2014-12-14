# "Flash message" element file
<i class="icon-file"></i> **template:** *files/elements/flash_message.ctp*

## Description
"Flash message" element, used in views to display flash messages.

This template can be used to generate specific flash messages elements.

## Required config


## Options

 * `alertClass:` *string, null* - CSS class of the generated element.

## Examples
Creating an "error" flash message, Twitter bootstrap style:
<pre class="syntax yaml">
[...]
files:
  flash_error:
    targetPath: View::Elements::flash_error.ctp
    template: elements::flash_message
    options:
      alertClass: danger
</pre>