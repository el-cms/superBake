# Common options handling
<i class="icon-file"></i> **Template:** *actions/common/common_options.ctp*
<i class="icon-cogs"></i> **Related actions:** [users::logout](../actions.users_logout.md/docs:template), [users::login](../actions.users_login.md/docs:template)
<i class="icon-eye-close"></i> **Related view:** None

## Description
This is a file meant to be included in all your actions, and where you should store logic
common to all actions

## Required config

## Options
This file handles no options for now.
## Examples

### Inclusion in actions
<pre class="syntax php-script">
&lt;?php
// We assume the current action is in Template/actions.

// Include common options
include "common/common_options.ctp";

// Rest of the action...
</pre>
