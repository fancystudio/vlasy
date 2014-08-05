PrestaCenter XML Export Pro
===============================================================================

All paths are relative to the PrestaShop root directory,
so "/modules " means "[path to your Prestashop]/modules".

Installing the module:
* In the installation archive of module prestacenterxmlexportproXXXX.zip
  are 3 files with feed templates. If you do not want export variants of products,
  you can copy (and override) the file sql-cs-simple.xml to sql.xml (default
  sql.xml file contains product variants and is the same as sql-cs-variants.xml).
  If you use standard module installation process, changed files must be repacked
  into the original zip archive.
* In the administration of module click on "Add a new module" and select the file
  prestacenterxmlexportproXXXX.zip from your computer.
* If you are installing the module for the first time, select from the
  "Other modules" module "PrestaCenter XML Export Pro" and click on "Install".
* If you already have a module installed and you want not overwrite
  the installed feeds, a further step can be skipped.
* If you already have a module installed and you want to overwrite the original
  feeds, click on the "Reset".
* The module PrestaCenter XML Export Pro is the last item of the menu "Catalog"
  (main BO menu).

-------------------------------------------------- -----------------------------
The module requires some writeable directories (chmod 755 or chmod 775).
Should it be necessary manually set the access rights for this
directories:
/override/classes
/modules/prestacenterxmlexportpro/templates
/xml
If directory /xml is not created automatically during installation,
is necessary to create it.

Along with the module are automatically installed XML feed templates for
services Google Merchant and "General" service for public use.

For Czech and Slovak language are installed services heureka.cz, zbozi.cz and/or
heureka.sk, pricemania.sk.

For each feed can be added to any number of options which will have the same
structure of the feed, but varies with the chosen language and currency.
Individual variations must have a different name of the destination file.

It is possible to define a custom XML feed template for other price comparison
services. Description of XML templates will be displayed in the form for
feed editing (See help).

All XML feeds will be generated in the directory /xml - so that the URL of feed
will look like this:
http://www.myshop.com/xml/google-en-eur.xml

If your webserver sent XML file with the wrong MIME header, you can
also use this form:
http://www.myshop.com/xml/getfeed.php?file=google-en-eur.xml
File getfeed.php sends the correct HTTP header (Content-Type: application/xml)
and the time of the last file modification.
