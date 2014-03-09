# Config: plugins

## appBase
The **appBase** plugin isn't really a plugin: it correspond to the `app/` folder. That means that exerything defined here will be generated in `app/`:

 * models in `app/Model`
 * controllers in `app/Controller`
 * views in `app/View`
 * and all the menus/files will be generated from this path.

This is set this way to avoid a special section in the configuration file that would contains the same structure as other plugins.

You can define global menus and files here, as global required files (when it will be available).

The default name is appBase, but if you want to change it in order to create a plugin named "appBase", change the name in the plugin list and change the value of `general.appBase` in the configuration file.

## Structure:

To be valid, a plugin needs at least a "part" section with at least one part in it.

### Part options

Parts options are available in model options, models snippets options, views options and controllers options.

## Examples
As the configuration file is populated with default values, you can define your plugins in many ways:

In all the examples below, i'll define a "blog" plugin, a "forum" plugin and the "appBase"

### Simple plugin section
<pre class="syntax yaml">
## Config file's "plugins" section
plugins:
  ## The "appBase"
  appBase:
    parts:
      ## Users part
      Users:
      ## Groups part
      Groups:

  ## The blog plugin
  Blog:
    parts:
      ## The posts
      Posts:
      ## Their category
      PostCategories:
      ## Their comments
      PostComments:

  ## Forum plugin
  Forum:
    parts:
      ## Differents rooms
      Rooms:
      ## Rooms belongs to categories
      RoomCategories:
      ## There are topics in rooms
      RoomTopics:
      ## Topic have answers
      RoomTopicAnswers:
      ## Topics may be "sticky", "closed",...
      RoomTopicStates:
</pre>


### More complete plugin section

<pre class="syntax yaml">
## Config file's "plugins" section
plugins:
  ## The "appBase"
  appBase:
    parts:
      ## Users part
      Users:
        ## Model name on model definition line
        model: Users
        controller:
          name: Users
          actions:
            ## Some more actions for users
            public:
              login:
              register:
            ## Some additionnal actions for admin
            admin:
              logout:

      ## Groups part
      Groups:
        model:
          ## Model name is different than part name
          name: Roles
          displayField: name
        ## empty controller, name will be "Role"
        controller:

  ## The blog plugin
  Blog:
    parts:
      ## The posts
      Posts:
        model:
          displayField: title
      ## Their category
      PostCategories:
        model:
          displayField: name
      ## Their comments
      PostComments:
        model:
          displayField: id

  ## Forum plugin
  Forum:
    parts:
      ## Differents rooms
      Rooms:
      ## Rooms belongs to categories
      RoomCategories:
      ## There are topics in rooms
      RoomTopics:
      ## Topic have answers
      RoomTopicAnswers:
      ## Topics may be "sticky", "closed",...
      RoomTopicStates:
</pre>