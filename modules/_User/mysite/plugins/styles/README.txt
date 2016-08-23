// $Id: README.txt,v 1.5 2008/04/06 23:08:27 agentken Exp $

/**
 * @file
 * Notes regarding the Style files used with MySite..
 */

MySite Styles
----------------
Style plugins are used by the MySite module to control the display of individual
elements with a user's content collection.

Style plugins are standard CSS files that use ids and classes specific to the MySite module.

MySite ships with six Style files:

 -- default.css
 -- fire.css
 -- forest.css
 -- midnight.css
 -- sky.css
 -- sunrise.css
 -- winter.css

Of these, only 'default.css' is required.  MySite will not work without it.


Options
---------
You may create new Style files by following default.css file as a guide.  New style files
must be named *.css and placed within the 'mysite/plugins/styles' directory.  Be sure
to include the #mysite-*-edit definitions, as they control the presentation of selection options.

If you remove all theme files except 'default.css' your site users will not be presented
any style selection options.

If you wish to use a different default style, simply rename the appropriate file to 'default.css' and
replace the original default file.


Internationalization
---------------------
By design, styles are named based on their filenames.  If you would like to modify the filenames
to reflect a specific language other than English, you may do so.

However, if you change the name of "default.css" you must change the default setting in the {mysite} database
table.  Here is a sample SQL statement for altering the proper table and column.

  ALTER TABLE {mysite} ALTER COLUMN style SET DEFAULT '$string';

For example, if you use Italian as your default language, rename "default.css" to "difetto.css" and run the query:

  ALTER TABLE {mysite} ALTER COLUMN style SET DEFAULT 'difetto';

This naming issue only affects css files, since the other plugins have translation functions that control
the text displayed to users.

Contributing
--------------
If you have created a Style file and wish to share it, simply attach the file as a new issue at

http://drupal.org/project/issues/mysite

You may need to change the file extension to .txt to attach the file.
