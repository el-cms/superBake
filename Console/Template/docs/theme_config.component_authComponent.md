# AuthComponent

## Description
This component should be enabled if you need a user authentication system.

## Configuration
All the configuration for this component is in the `theme` section of the configuration file:
<pre class="syntax yaml">
theme:
  components:
   Auth:
</pre>

### Configuration values

 * **roleModel:** *string* The model that stores the roles (groups)
 * **rolePk:** *string* The primary key of the role model
 * **userModel:** *string* The model that stores the users
 * **userModelPk:** *string* Primary key for the users model
 * **userCanChooseRole:** *bool* Determines if the user can choose his role during registration
 * **defaultRoleId:** *string/number* Default role for new users
 * **validUserStatus:** *bool* If true, you have a column in the user table that determines if an user is enabled or not
 * **userStatusField:** *string* The field that determines if an user is enabled and can login
 * **userDefaultStatus:** *string* The default content of the status field for new users to auto approve new users)
 * **validUserStatus:** *string/number* Content of the status field to allow an user to login
 * **userNameField:** *string* Field that represent the user's username (for login). Usually it's an email field.
 * **userPassField:** *string* Password field
 * **loginRedirect:** *string* Action where the user should be redirected on a successfull login. You have to write it like `controller::action`
 * **loginAction:** *string* Action handling the login (for redirections too)

### Configuration example
<pre class="syntax yaml">
theme:
  components:
    Auth:
			# Groups model name
      roleModel: Group
			# Groups primary key
      roleModelPK: id

			# Users model name
      userModel: User
			# Users primary key
      userModelPK: id

			# A new user can't choose his role
      userCanChooseRole: false
			# The default role is 2 (which should correspond to the users group)
      defaultRoleId: 2

      # Users may be disabled
      validUserStatus: true
      # The field is "status"
      userStatusField: status
      # A new user is enabled by default
      defaultUserStatus: 1

      # Field used to login an user
      userNameField: email
      # Password field name
      userPassField: password

      # Redierction after a successfull login
      loginRedirect: user::dashboard
      # The login action name
      loginAction: login
</pre>
