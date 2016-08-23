Tokenize provides configurable, automatic tokenization of CCK widgets using the
Token module. Allows a user to configure the use of Token with specific widgets
in the context of specific node types.

The user can set Token to replace tokens with actual data when the node is 
inserted into the database, or when the node is viewed. In the former case, 
the tokens are replaced, and any subsequent node edit will simply show the 
actual data in their place. In the latter case, the tokens are preserved, but 
any viewing of the content will show the data, rather than the tokens.

** Installation and Usage
1. First install the Token module and the cck module (Available from 
http://drupal.org/project/token and http://drupal.org/project/cck)

2. Extract the Tokenize tarball into your sites/all/modules directory (or
  another suitable location).

3. Visit Administer > Site building > Modules and enable the Tokenize module.

4. Create or edit a cck field.  You should now see settings related to the 
Tokenize module.


** Credits:
Tokenize was developed by Morris Singer http://drupal.org/user/91393
