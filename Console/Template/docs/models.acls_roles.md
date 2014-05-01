# Role model, ACLs snippet
<i class="icon-file"></i> **template:** *models/acls/roles.ctp*

## Description
This snippet should be included in your role model definition: it makes it acts as a requester.

## Options
None

## Example
Example for a role model named "Groups":
<pre class="syntax yaml">
[...]
  Groups:
    model:
      snippets:
        acls:
          path: acls::roles
</pre>