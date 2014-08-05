Postup instalace modulu PrestaCenter XML Export Pro
===============================================================================

Všechny cesty jsou relativní ke kořenovému adresáři PrestaShopu,
tedy "/modules" znamená "[cesta k vaší instalaci prestashopu]/modules".

Instalace modulu:
* V instalačním archívu modulu prestacenterxmlexportproXXXX.zip
  jsou 3 soubory se šablonami feedů. Pokud  nechcete exportovat varianty pro
  všechny feedy můžete překopírovat soubor sql-simple.xml na sql.xml (výchozí
  soubor sql.xml obsahuje varianty produktů a je stejný jako sql-variants.xml).
  Změněné soubory je nutné znova sbalit do původního archivu.
* V administraci modulů zvolit "Přidat nový modul" a vybrat soubor
  prestacenterxmlexportproXXXX.zip z počítače.
* Pokud již máte modul nainstalován a nechcete přepisovat původní feedy,
  další kroky můžete přeskočit.
* Pokud již máte modul nainstalován a chcete přepsat původní feedy, klikněte
  na odkaz "Obnovit původní".
* Pokud instalujete modul poprvé, vyberte ze skupiny "Další moduly" modul
  "PrestaCenter XML Export Pro" a klikněte na tlačítko "Nainstalovat".
* V hlavním menu administrace najdete modul jako poslední položku v nabídce
  "Katalog".

-------------------------------------------------------------------------------
Modul vyžaduje, aby některé adresáře byly zapisovatelné (chmod 755 nebo
chmod 775). Někdy je nutné přístupové práva nastavit ručně, jde o tyto
adresáře:
/override/classes
/modules/prestacenterxmlexportpro/templates
/xml
Adresář /xml je nutné vytvořit, pokud nebyl vytvořen automaticky při instalaci.

Spolu s modulem se automaticky nainstalují šablony XML feedů pro služby
Google merchant a obecný feed (General) vhodný například i pro export produktů
pro další odběratele.

Pro použitou češtinu nebo slovenštinu v PrestaShopu jsou nainstalováný i feedy
heureka.cz, zbozi.cz, resp. heureka.sk, pricemania.sk.

Ke každému feedu lze přidat libovolný počet jeho variant, které budou mít
stejnou strukturu feedu, ale liší se zvoleným jazykem a měnou, v níž se
budou přidávat názvy, popisy a ceny zboží. Pro jednotlivé varianty se musí
lišit i název cílového souboru.

Je možné definovat vlatní šablony XML feedů pro jiné srovnávače zboží. Popis
XML šablon se vám zobrazí u formuláře pro úpravu feedu (Zobrazit nápovědu).

Všechny XML feedy se budou generovat do adresáře /xml – takže jejich URL
bude vypadat např. takhle:
http://www.mujobchod.cz/xml/heureka-cs-czk.xml

Pokud by váš hosting posílal XML soubor se špatnou MIME hlavičkou, můžete pro
URL feedu použít i tvar:
http://www.mujobchod.cz/xml/getfeed.php?file=heureka-cs-czk.xml
Skript getfeed.php posílá správnou HTTP hlavičku (Content-Type: application/xml)
a čas, kdy byl feed naposledy změněn.
