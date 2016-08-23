monthgroup

README

Description
------------

This module defines several views style plugin which allows you to group
entires.  You can group them by Month, or by Taxonomy Term.

When enabled, this module will provide a new "View Type" inside the "Page" and
"Block" groups of a view's edit page.  Rather than "List View" or "Table View",
you can set it to "Grouped by month, Full Nodes" or "Grouped by month,
Teasers" (or similar for Taxonomy Term). 

It only works if the view is sorted first by the appropriate field.  For
example, to group by month, you must sort first by a cck datefield.  To group
by taxonomy term, you must sort first by taxonomy term.  

When it works, you should see results in the view that are like this:
    July 2007
    Event 1 - July 21, 2007
       yadda yadda
    Event 2 - July 22, 2007
       blah blah

    August 2007
    Event 3 - August 3, 2007
       event event

    etc.

Similarly, for Taxonomy Term:
    Category A
    post 1 (about category A)
    post 2 (about category A)

    Category B
    post 3 (about category B)

    etc.


Installation
------------

Unpack the tarball and install in your modules directory.  Enable "Views Group-By Pack" on the modules admin page.  

Make sure your view is sorted first by the appropriate field, and then set the
"View Type" to "Grouped By X".
