views_union

README

Description
------------

Views Union allows you to create views which are composed of multiple
"sub-views".  The interesting thing is that the "sub-views" will be combined
with "OR" to create the main view.  Therefore, you can have two views, with
incompatible criteria.

For example, you might have one view that implements "Show me all the events
with dates in this range."  You might have another view which implements "Show me all events which are sticky."

Views Union lets you create a third view, which could give you all the events
within that range, plus all the sticky events, even if they're not in the
range.

Views can also be composed of sub-views which, themselves, have sub-views.

It also implements a couple other features:
1. You can set it to grab only up to a certain number of results from each
   view.  You could implement something like "I want to see all events in this
   date range, plus the first two sticky events."

2. You can set it to grab zero results from a particular view in the case that
   an exposed filter is used.


Installation
------------

Unpack the tarball and install in your modules directory.  Enable it on the
modules admin page.  In order to install modules, though, you'll need to make
the directory you intend to install to writable by the web-server user.
