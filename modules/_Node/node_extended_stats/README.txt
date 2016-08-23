// $Id: README.txt,v 1.1.2.1 2008/11/20 06:34:42 jaydub Exp $

Module description
==================

Drupal via the core Statistics module allows you to collect node
view counts for the current day (daycount) and for 'all-time' (totalcount).
This module was developed to allow you to define additional timeperiods
in addition to the ones provided by the Statistics module. By enabling
this module you can track node view and comment counts for shorter
timeperiods such as 4 hours or for longer timeperiods such as 1 week
or 1 month.

In order to make the collection of this data more useful I have also
included simple Views integration that allows you to present a node's
view count or comment count as a Views field and/or use the node's
view count or comment count as a Views sort.

Installation and Configuration
==============================

1) Copy the Node Extended Stats module files to your Drupal modules 
   directory (e.g. /sites/all/modules)

2) To install, enable the Node Extended Stats module on the Drupal
   modules page /admin/build/modules

3) Once the Node Extended Stats module has been installed you can configure
   the settings at /admin/settings/node_extended_stats

  a) Select the timeperiods that you would like to collect
     node view counts and comment counts for.

  b) Select how many nodes to recheck view and comment counts
     for on each cron run.

4) To view the most frequently viewed or commented on content go to
   the reports page at /admin/logs/node_extended_stats


Views Integration
=================

If you wish to utilize a node's view or comment count in Views you
can enable the Node Extended Stats Views module.

Installation and Configuration
==============================

1) To install, enable the Node Extended Stats Views module on the Drupal
   modules page /admin/build/modules

2) Create a new view or edit an existing view. If your view uses Table
   or List view you can add fields to your view for a node's view or
   comment count for each timeperiod you specified in the settings
   page for the Node Extended Stats module.

  a) Example: In the drop down for Views fields you would see the
     following field options if you have a defined timeperiod of 1 week.

     'Node Extended Stats: Hist in last 1 week'
     'Node Extended Stats: Comments in last 1 week'

   If you wish to use a node' view or comment count as a Views sort
   option you can add the sort option to your view.


Important Notes
===============

If you have a high traffic site and/or use long time periods for
view and comment counts then understand that the raw data tables
for collecting the view and comment count data could grow quite
large. 

It's highly recommended that high traffic sites that are using
MySQL use the InnoDB engine for the tables in this module as
the row-level locking feature of InnoDB will allow for better
performance than the table-level locking of MyIASM tables 
in MySQL.


Credits
=======

Project Owner
Jeff Warrington http://drupal.org/user/46257
This module was developed in part for the Beijinger website
http://www.thebeijinger.com