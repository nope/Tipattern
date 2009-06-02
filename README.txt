Tipattern 4.0.9 based on Textpattern 4.0.8-svn r3205

== Recent features ==

* add tag "if_data"
* add tag "section_description"
* add native support to cache
* add description, meta-key, meta-description in section and category
* add native support to import ed export template
* modify layout in write tab
* initial support to "home" section

nb: this mod is ustable

== To do ==

* auto set off cache when site is in test o debug state
* add native support to tag
* add native support to "page"
* more code cleaning
* add new default template whit new tag
* modify table creation to add new features
* add new tag section_meta-key, section_meta-desc, category_desc, category_meta-key, category_meta-desc
* ... e poi non so :)


Textpattern 4.0.8

Released under the Gnu General Public License

== Installation ==

* Extract the files to your site (in the web root, or choose a
  subdirectory). The top-level index.php should reside in this
  directory, as should the /textpattern/ and the /rpc/ directory.
* Create, or establish the existence of, a working mysql database,
  load /textpattern/setup/ (or /subpath/textpattern/setup/ )
  in a browser, and follow the directions.

== Upgrading ==

* Simply replace the two files in your main installation directory
  (index.php and .htaccess), everything in your /rpc/ directory and
  everything in your /textpattern/ directory (except config.php)
  with the corresponding files in this distribution.
* When you login to the admin-side, the relevant upgrade script is
  run automatically. Please take a look into diagnostics to find out
  whether there are any errors and whether the correct version number
  is displayed.

== Getting Started ==

* FAQ is available at http://textpattern.com/faq
* In-Depth Documentation and tag-index is available in the TextBook project
  at http://textbook.textpattern.net/
* You can get support in our forums at http://forum.textpattern.com/

* IMPORTANT: Regularly check back at textpattern.com to see if updates are
  available. 4.0.x is in maintenance mode which means updates are as painless
  as possible, and often fix important bugs or security-related issues.
