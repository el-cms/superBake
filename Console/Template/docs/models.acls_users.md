# User model, ACLs snippet
<i class="icon-file"></i> **template:** *models/acls/users.ctp*

## Description
This file contains methods and vars used in the User model to make it works as requester (for ACLs), hash and compare passwords.

## Required config

 * [AuthComponent](../theme_config.component_authComponent.md/docs:template) should be enabled in config.

## Options:
None

## Example
Example for a user model named "Users":
<pre class="syntax yaml">
[...]
  Users:
    model:
      snippets:
        acls:
          path: acls::users
</pre>

