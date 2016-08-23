Installation:
-------------
Installation Instructions:

1) Copy this entire directory to the modules directory of your drupal installation. 
2) Download the file whizzywig.js from the Whizzywig download page (see Resources below) and put it in the folder 'whizzywig' within the module folder.
   This means the file will most likely be at /sites/all/modules/whizzywig/whizzywig/whizzywig.js
3) Download any addition files you want from the Whizzywig download page (see Resources below) and put them also in the 'whizzywig' folder. 
   Please check out the README.txt file in that folder for more information. 
   Recommended files to be downloaded additionally are xhtml.js and buttons.zip
4) Enable the whizzywig module in Drupal (admin/build/modules).
5) Go to Administer > User Management > Access Control (admin/user/access) and specify which roles are allowed to use the Whizzywig editor, and which roles are allowed to administer the Whizzywig settings.
6) For the Whizzywig editor to work as it should, it is recommended to change your filter settings (admin/settings/filters), and configure the 'Allowed HTML tags' in the HTML filter configuration, to allow the tags that match the toolbar buttons you have enabled. 
   
 Resources:
 ----------
 Whizzywig homepage: http://www.unverse.net/whizzywig-cross-browser-html-editor.html
 Whizzywig download page: http://www.unverse.net/whizzywig-download.html

Configuration:
--------------
Go to Administer > Site Configuration > Whizzywig (admin/settings/whizzywig) to change the Whizzywig configuration.

On this page you can:
- specify whether you want to use the xHTML addon (requires xhtml.js - see README.txt in folder 'whizzywig').
- choose if you want to integrate IMCE as picture browser.
- specify the width & height of the editor
- specify which buttons and/or lists to show on the editor toolbar, and their order.
- specify which textareas should not/should only show the editor based on textarea ID or path.
- specify the stylesheet to be used for the editor.
- specify the language to be used for the editor.


Settings examples:
------------------
Below are some recommended settings for the Whizzywig editor that may be useful to most users:

- Editor width: setting this to '100%' will make sure the editor covers the whole content area.
- You might want to add the following textarea IDs, when you have selected "Don't show Whizzywig editor on textareas with IDs mentioned below" in the "Filter visibility based on textarea IDs" settings:
    edit-help
    edit-description
    edit-log
- You can use wildcards for the "Filter visibility based on Drupal paths" setting. If you want to disable the Whizzywig editor on admin page for example, add:
    admin/*
- If you want to use 'Custom buttons' in the editor, you will have to select 'As specified below:' in the 'Toolbar Settings'. 
  If you add custom buttons on the appropriate tab, they will also be selectable to be in the toolbar buttons list.
- Some commonly used features in other WYSIWYG editors that are not in Whizzywig, can be added by creating 'Custom buttons':
  DESCRIPTION          -  TAG code to use
  Drupal break         -  <!--break-->  
  Justify text         -  <div align="justify">
  Select All           -  js:oW.document.execCommand("selectall",false,"");
  Strike through       -  <strike>
  ...
  (if you have code for more commonly used buttons, send them to me - see contact below - so i can add it to the this list)
  You will most likely need to change your HTML input filter to allow the custom tags you have specified.

Issues & Support:
-----------------
Please use the issue tracker at drupal.org to report any issues or ask for support.
Please note however that this module only provides Whizzywig support in Drupal: 
issues or feature requests for the editor itself should be reported to the Whizzywig author (see Whizzywig homepage or forum).

Support Whizzywig:
------------------
If you like the Whizzywig editor, please show your appreciation to John Goodman/unverse.net:
- rate the script on hotscripts.com: http://www.hotscripts.com/rate/54358.html?RID=%20N425785
- make a donation: http://www.unverse.net/whizzywig-faq.html

Contact:
--------
Sven Decabooter <sven@svendecabooter.be>