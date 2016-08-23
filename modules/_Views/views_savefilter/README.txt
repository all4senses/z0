$Id: README.txt,v 1.1.2.4 2007/05/29 20:37:11 avf Exp $

Views save filter module README

This module saves filter values for views with exposed filters.  This
means that if you have a view with exposed filters, the filter values
that the user chooses will be saved and restored the next time the user
returns to the page provided by the view.

A side effect of this is that it is possible to define views providing
blocks with exposed filters.  The filter values can be changed by going
to the page provided by the same view and selecting the filter values
there.  The selected values will then also be used for the block, and
will be saved when the user logs out.

See INSTALL.txt for instructions on how to install the module.

This module has no settings --- simply enable it, and it will save any
exposed filter values.  Note that the saved values are cleared if the
view is edited.

This module is in development, so beware of bugs.  In particular, it has
not been tested with PostgreSQL.

