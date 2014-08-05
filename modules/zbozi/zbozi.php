<?php
/*
* PrestaHost.cz / PrestaHost.eu
*
*
*  @author prestahost.eu <info@prestahost.cz>
*  @copyright  2014  PrestaHost.eu, Vaclav Mach
*  @license    http://prestahost.eu/prestashop-modules/en/content/3-terms-and-conditions-of-use
*
*  Modul Zbozi pro prestashop 1.5 a 1.6
*/
if (!defined('_PS_VERSION_'))
    exit;
 

class Zbozi extends Module
{

     protected $_html = '';
    protected $_postErrors = array();
    protected $feeds=array("seznam", "heureka");
    protected $feedsUsed=array();
    protected $feeddir= 'xml';    
    protected $availability=0; 
    protected $availability_later=10;
    protected $availability_mode =0;
    protected $text_fields=array('ZBOZI_IMG', 'ZBOZI_DESCRIPTION', 'ZBOZI_ALLOWTAGS',
     'ZBOZI_CPC', 'ZBOZI_FORBIDDEN');
    protected $text_defaults=array('medium', 'description_short', '<b>,<u>,<i>,<p>', '', '','');
    protected $config=array();
    protected $overrides;
    protected $do_attributes;
    

 public function __construct()
    {
          $this->name = 'zbozi';
          $this->do_attributes=0; 
          if(file_exists(dirname(__FILE__).'/ZboziAttributes.php')) {
               $this->do_attributes=1; 
          }
        $this->version = 2.03;
        $this->tab = 'advertising_marketing';
                $this->author = 'PrestaHost.eu)';
        
        $config = Configuration::getMultiple(array(
         'ZBOZI_SEZNAM', 'ZBOZI_HEUREKA', 
        'ZBOZI_GOOGLE', 'ZBOZI_AVAILABILITY', 'ZBOZI_AVAILABILITY_LATER',
         'ZBOZI_IMG', 'ZBOZI_DESCRIPTION', 'ZBOZI_ALLOWTAGS', 'ZBOZI_CPC', 'ZBOZI_AVAILABILITY_MODE', 'ZBOZI_FORBIDDEN',
        'ZBOZI_SKLADEM'));
        $this->feedsUsed["seznam"]=empty($config['ZBOZI_SEZNAM'])?0:1; 
        $this->feedsUsed["heureka"]=empty($config['ZBOZI_HEUREKA'])?0:1;    
      
        $this->feedsUsed["google"]=empty($config['ZBOZI_GOOGLE'])?0:1;   
        $this->availability= intval($config['ZBOZI_AVAILABILITY']);
        if(isset($config['ZBOZI_AVAILABILITY_LATER']))
        $this->availability_later= intval($config['ZBOZI_AVAILABILITY_LATER']);
        
        $this->availability_mode= intval($config['ZBOZI_AVAILABILITY_MODE']);
      
       foreach($this->text_fields as $field) {
           $this->config[$field] = $config[$field];
       }
     

        parent::__construct();

        $this->displayName = 'Zboži';
        if($this->do_attributes)
             $this->displayName .=' s variantami produktů';
        $this->description = 'Modul pro export zboží do služby  zbozi.cz a dalších';
        $this->confirmUninstall ='Odinstalovat ?';
        $val=0;
       
        foreach($this->feedsUsed as $feed) {
         $val+=$feed;   
        }
        
        if(!$val && Module::isEnabled($this->name))
        $this->warning[] = 'není nastavena tvorba  žádného feedu'; 
        
        $this->overrides=array(
  0=>array('source'=>_PS_MODULE_DIR_.$this->name.'/install/AdminCategoriesController.php',
           'target'=>_PS_OVERRIDE_DIR_.'controllers/admin/AdminCategoriesController.php',
           'targetdir'=>_PS_OVERRIDE_DIR_.'controllers/admin/'),
  1=>array('source'=>_PS_MODULE_DIR_.$this->name.'/install/Category.php',
           'target'=>_PS_OVERRIDE_DIR_.'classes/Category.php',
           'targetdir'=>_PS_OVERRIDE_DIR_.'classes/')
  );
        
    }    
    
    
    
public  function GetSetting($key) {
     switch($key) {
         case "feeds":  return $this->feeds; 
         case "feeddir": return $this->feeddir;
         case "avilability": return $this->availability;   
         case "avilability_later": return $this->availability_later;     
     }
       
    }

  public  function install()
    {   
 
   if(! $this->check_feeddir()) {
          return false;
   } 
  
$sql='SELECT column_name
  FROM information_schema.columns 
 WHERE table_schema = "'._DB_NAME_.'" 
   AND table_name   = "'._DB_PREFIX_.'category"
   AND column_name  = "heureka_category"';
 $column_exists=Db::getInstance()->getValue($sql);
  
 if($column_exists == false) {
  
 $sql='ALTER TABLE '._DB_PREFIX_.'category ADD heureka_category VARCHAR (250) COLLATE utf8_czech_ci DEFAULT NULL';
  Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
 }
  $sql='UPDATE   '._DB_PREFIX_.'category SET heureka_category="" WHERE id_category = 1';
$test=Db::getInstance()->Execute($sql); 

   
     if(!$test) {
         $this->warning[]='Nepodařilo se přidat pole Heureka Category, proto nebyly instalovány soubory pro práci s tímto polem';   
     }
     else { 
   foreach($this->overrides as $override) {
       if(file_exists($override['target']) && filesize($override['target']) > 86 ) {
            if(crc32(file_get_contents($override['target'])) != crc32(file_get_contents($override['source'])))
                $this->warning[]='Soubor '.$override['target'].' již existuje a namůže být nahrazen, prosím upravte jej ručně'; 
       }
       if(!is_writable($override['targetdir'])) { 
           $this->warning[]='Adresář '.$override['target'].' není zapisovatelný'; 
      }
      elseif(!copy($override['source'],$override['target'])) {
        $this->warning[]='Nepodařilo se překopírovat '.$override['source'].' do '.$override['target'];   
      }
   } 
     }
  
   
   
    if(file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
      unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
    else  {
        $this->warning[]=_PS_ROOT_DIR_.'/cache/class_index.php not found';   
    }
    
    if(is_array($this->warning) && count($this->warning) ) {
       $protocol =implode("\n", $this->warning);    
        Configuration::updateValue('ZBOZI_PROTOCOL', $protocol);
    }
   Configuration::updateValue(  'ZBOZI_SEZNAM', 1);
    Configuration::updateValue(  'ZBOZI_HEUREKA', 1);
    
      Configuration::updateValue(  'ZBOZI_PARTIAL_UNISTALL', 1);
      Configuration::updateValue(  'ZBOZI_ROUND_PRICES', 1);
  
    for($i=0; $i<count($this->text_fields); $i++) {
      Configuration::updateValue($this->text_fields[$i], $this->text_defaults[$i], true);    
    }
        if (!parent::install())
            return false;
        return true;
   }

   
   
   public function uninstall()
    {
        
         for($i=0; $i<count($this->text_fields); $i++) {
             if (!Configuration::deleteByName($this->text_fields[$i]))
               return false; 
          }
   if((int)Configuration::get("ZBOZI_PARTIAL_UNISTALL") == 0) {       
        $sql='SELECT column_name
  FROM information_schema.columns 
 WHERE table_schema = "'._DB_NAME_.'" 
   AND table_name   = "'._DB_PREFIX_.'category"
   AND column_name  = "heureka_category"';
 $column_exists=Db::getInstance()->getValue($sql);
 if($column_exists == "heureka_category") {
 $sql='ALTER TABLE '._DB_PREFIX_.'category DROP COLUMN heureka_category';
  Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
 
 } 
   }          
                          
        if (!Configuration::deleteByName('ZBOZI_SEZNAM') 
            OR !Configuration::deleteByName('ZBOZI_HEUREKA') 
            OR !Configuration::deleteByName('ZBOZI_GOOGLE')
            OR !Configuration::deleteByName('ZBOZI_AVAILABILITY')
            OR !Configuration::deleteByName('ZBOZI_AVAILABILITY_LATER')
            OR !Configuration::deleteByName('ZBOZI_AVAILABILITY_MODE') 
            OR !Configuration::deleteByName('ZBOZI_FORBIDDEN')
            OR !Configuration::deleteByName('ZBOZI_SKLADEM')
            OR !Configuration::deleteByName('ZBOZI_PARTIAL_UNISTALL')
            OR !Configuration::deleteByName('ZBOZI_ROUND_PRICES')
            OR !parent::uninstall())
            return false;
    $removed=0;        
     foreach($this->overrides as $override) {
        if(file_exists($override['target'])) {
       if(crc32(file_get_contents($override['target'])) == crc32(file_get_contents($override['source'])))
         unlink($override['target']);
         $removed++; 
       }
     }        
    
    if(file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
      unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
   
  if($removed==count($this->overrides) ) {     
  $sql='ALTER TABLE ps_category DROP COLUMN heureka_category';   
 //  Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);   
 // the column is no removed   
        return true;
    }
    }
    protected function _postValidation()
    {
        if (isset($_POST['btnSubmit']))
        {
         ;
        }
    }

    protected function _postProcess()
    {
        if (isset($_POST['btnSubmit']))
        {
            reset ($this->feeds);
            
            foreach($this->feeds as $feed) {
               $this->feedsUsed[$feed]= intval(Tools::getValue($feed));
               $key="ZBOZI_".strtoupper($feed);
               Configuration::updateValue($key, $this->feedsUsed[$feed]);  
            }
            Configuration::updateValue('ZBOZI_AVAILABILITY', intval($_POST['ZBOZI_AVAILABILITY'])); 
            $this->availability =intval($_POST['ZBOZI_AVAILABILITY']);  
          
            Configuration::updateValue('ZBOZI_AVAILABILITY_LATER', intval($_POST['ZBOZI_AVAILABILITY_LATER'])); 
            $this->availability_later =intval($_POST['ZBOZI_AVAILABILITY_LATER']);  
          
            Configuration::updateValue('ZBOZI_AVAILABILITY_MODE', intval($_POST['ZBOZI_AVAILABILITY_MODE']));  
             $this->availability_mode= intval($_POST['ZBOZI_AVAILABILITY_MODE']); 
           
             Configuration::updateValue('ZBOZI_SKLADEM', intval(Tools::getValue('ZBOZI_SKLADEM')));  
             $this->availability_mode= intval(Tools::getValue('ZBOZI_SKLADEM')); 
             Configuration::updateValue('ZBOZI_ROUND_PRICES', intval(Tools::getValue('ZBOZI_ROUND_PRICES')));  
               Configuration::updateValue('ZBOZI_PARTIAL_UNISTALL', intval(Tools::getValue('ZBOZI_PARTIAL_UNISTALL')));  
         
                 
          
             foreach($this->text_fields as $field) {
               Configuration::updateValue($field, $_POST[$field], true);  
               $this->config[$field]= $_POST[$field];
            }
        }
        if(Tools::getValue('cmd_clear')) {
            Configuration::updateValue('ZBOZI_PROTOCOL', '');  
        }
        $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" />Změna byla uložena</div>';
    }

    protected function _displayZbozi()
    {
        
        $this->_html .= '<img src="../modules/zbozi/zbozi.jpg" style="float:left; margin-right:15px;"><b>Modul zboží</b><br /><br />
        
        Modul pro export zboží do služby  heureka.cz zbozi.cz.  Tyto feedy jsou bez úprav použitelné i pro většinu dalších vyhledávačů.<br />  <br />
        <ul>
        <li>použitelný i pro velké eshopy s tisíci kusů zboží (na vhodném hostingu)</li>
        <li>podpora multishop</li>
        <li>vynechání vybraných produktů</li>
        <li>přesné párování Heureka</li>
        <li>podrobné nastavení dostupnosti</li>';
        
        if($this->do_attributes==1)
        $this->_html .= '<li><b>varianty zboží</b></li>  </ul> ';
        else
         $this->_html .= '<li style="color:red"><b>nelze exportovat varianty zboží. 
        <br /> Pro export variant je potřeba modul 
       <b> <a href="http://prestahost.eu/prestashop-modules/cs/import-export/20-export-heureka-zbozi-varianty-produktu.html" target="_blank" style="color:blue">Zbožíplus</a></b></li>  </ul> ';
        
          $this->_html .='<br /><br /><a href="http://www.prestahost.cz" target="_blank"><img src="../modules/'.$this->name.'/prestahost.gif"></a> 
       <br />  
       <b><a href="http://www.prestahost.cz" target="_blank">Prestahost.cz</a> </b>: česká podpora Prestashopu, specializovaný hosting, moduly na míru, import xml feedů. ';  
 
    }
               
    protected function _displayForm()
    {   
          $stock_management = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        $url=$_SERVER['HTTP_HOST']."/modules/".$this->name."/feeds.php";
        $this->_html .=
        '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>';
          $protocol=Configuration::get('ZBOZI_PROTOCOL');
          if($protocol && strlen($protocol)){
               $this->_html .= '<span style="color:red"><h4>Instalace není úplná</h4>'.nl2br($protocol).'</span></br>
               Více informací najdete v přiloženém readme.pdf </br>
               <input type="submit" name="cmd_clear" value="Vyčistit instalační protokol"><br /><br />';
               
          }  
            $this->_html .= '<legend><img src="../img/admin/contact.gif" />'.$this->l('Feedy').'</legend>
             
               Pro vytvoření feedu je potřeba spouštět skript <a href="http://'.$url.' " target="_blank" style="color:blue">'.$url.'</a> .
               Požádejte svého poskytovatele o instalaci kronu spouštějícího 1x za den uvedené url  
                <br />   
               Pokud využíváte multishop, přidejte do kronu  parametr id_shop tj. např.<br />  
               '.$url.'?id_shop=1<br />'.$url.'?id_shop=2<br />
               
                 <br /> 
                Pokud hostujete na Prestahost.cz, zapněte si kron v záložce Hosting (hlavní lišta záložek)  <br />   
                <br />   
                
               Pokud je nějaký feed již vytvořen, zobrazuje se odkaz níže, kliknutím na něj se feed otevře v prohlížeči. 
               Tím zjistíte url které je potřeba zadat do administrace zboží nebo heureka.<br />  
                 ';
                
        if(!$this->check_feeddir()) {    
           $this->_html .= "<b>POZOR adresář pro skladování feedů " .$_SERVER['HTTP_HOST']."/".$this->feeddir." nelze vytvořit nebo není zapisovatelný</b>";
        }        
        $this->_html .= '<br />';
              
           reset($this->feeds);
           foreach($this->feeds as $feed) {
            $file="zbozi_".$feed.".xml";
            $path="../".$this->feeddir."/$file";
            
            $checkbox="$feed - <input type=\"checkbox\" name=\"$feed\" value='1' style=\"color:blue\"";
            if($this->feedsUsed[$feed])
                 $checkbox.=" checked=\"checked\" ";
            $checkbox.="/>"; 
          
              $this->_html .=$checkbox."<br />"; 
           }
           
           
           
           
             $this->_html .=  '<h4>Existující feedy</h4>';
             
              $feedscreated=scandir(_PS_ROOT_DIR_.'/'.$this->feeddir);  
              
              foreach($feedscreated as $created) {
                //  $this->_html.=$created;
                  if(strpos($created, '.xml') > 0) {
                    $url="http://".$_SERVER['HTTP_HOST']."/".$this->feeddir."/".$created;
                    $this->_html.="<a href=\"$url\" target='_blank' style='color:blue'>$url</a> ".date('d.m.Y H:i', filemtime("../".$this->feeddir.'/'.$created))."<br />";     
                  }
              } 
      
              
            $this->_html.='</fieldset><br />';  
    /*        
      $this->_html .=' <fieldset><legend>Detailní nastavení feedů</legend>';  
           $this->_html.='<table>
       <tr><td>Zboží</td><td><a href="http://sluzby.heureka.cz/napoveda/xml-feed/" target="_blank">Heureka</a></td></tr>
        <tr>
        <td></td>
        <td>
        PRODUCTNAME: name + manufacturer_name<input type="checkbox" name="FEED_HEUOPT1" />  + reference<input type="checkbox" name="FEED_HEUOPT2" /> <br />
        PRODUCT: name + manufacturer_name<input type="checkbox" name="FEED_HEUOPT3" />  + reference<input type="checkbox" name="FEED_HEUOPT4" />
        </td>
        </tr>
       </table>';      
       $this->_html.='</fieldset><br />';      
     */         
        $this->_html .=' <fieldset><legend>Dostupnost</legend>'; 
        
        $this->_html .='Některé srovnávače cen, například Heuréka manuálně kontrolují obchody a
         porovnávají dostupnost z feedu s údaji u zboží nebo v dodacích podmínkách. Určitě se tedy vyplatí
         věnovat se správnému nastavení v této sekci<br /><br />';
        
        
                                              
          
          $this->_html .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="0" '.$this->optAvailabilityMode(0).'/> 
           Zohlednit řízení skladu: Modul bude respektovat zda je nastaveno řízení skladu.
          
           <br />';
           
           if($stock_management) {
             $this->_html .=' <small> Řízení skladu je zapnuto   použije se číslovka z textu zobrazovaného pokud <b>je nebo respektive není zboží skladem</b>. Pokud v textu není číslovka, použije se výchozí hodnota </b></small></br>';   
           }
           else {
              $this->_html .=' <small> Řízení skladu je vypnuto   použije se vždy  výchozí hodnota. Modul se nebude pokoušet hledat čísla v textech o dostupnosti.  Rízení skladu lze zapnout v Nastavení - Produkty </small></br>';   
           }
           
          $this->_html .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="1" '.$this->optAvailabilityMode(1).'/> Vždy hledat číslo.
          <small>
           bez ohledu na řízení je hledána číselná hodnota v textu. V závislosti na počtu kusů
           se 
            zjišťuje se číslo z textu zadávaného <b> pokud je nebo není zboží skladem</b>. Pokud  v textu chybí číslo, použije se výchozí hodnota. Aby bylo možné texty dopsat (Detail produktu - Množství), je potřeba řízení skladu
           na chvíli zapnout</small><br />';
          $this->_html .='<input type="radio" name="ZBOZI_AVAILABILITY_MODE" value="2" '.$this->optAvailabilityMode(2).'/> Nikdy nehledat číslo.  
           <small>Vždy se použije výchozí hodnota.</small><br /><br />';
        
        
        $this->_html .=' Výchozí hodnota  <input  type="text" name="ZBOZI_AVAILABILITY" value="'.$this->availability.'" />    dnů (0 = skladem)  
          použije se pokud je nenulový počet kusů, nebo je zvoleno "Nikdy nehledat číslo".
         <br />   <br />';
         $this->_html .=' Výchozí hodnota 2 <input  type="text" name="ZBOZI_AVAILABILITY_LATER" value="'.$this->availability_later.'" />    dnů  
           použije se pokud je nula kusů (nikoliv ale při nastavení "Nikdy nehledat číslo")
         <br />   <br />';
         
         
        
       
       
         $this->_html .='Výjimky pro zpracování textu  pro zboží skladem, které se vyhodnotí jako okamžitě k odběru:<br /> 
         <ul>
         <li>text je roven "skladem"</li>
         <li>obsahuje "ihned" a neobsahuje číslovku</li> 
         <li>obsahuje číslovku 24</li>
         </ul>';
       
      /* 
           $this->_html .=' Přepočet dostupnosti <input type="checkbox" value="1" name="ZBOZI_AVAILABILITY_RENUM"';
           
           if($this->availability_renum==1)  $this->_html .='  checked="checked"';
           $this->_html='/>
       */    
       
       $this->_html .=' </fieldset><br /><br />';
       $this->_html .=' <fieldset><legend>Další nastavení</legend>';  
       
                 
            if($stock_management) {
                $checkbox="<br /><input type=\"checkbox\" name=\"ZBOZI_SKLADEM\" value='1' style=\"color:blue\"";
            if(Configuration::get("ZBOZI_SKLADEM"))
                 $checkbox.=" checked=\"checked\" ";
            $checkbox.="/> Exportovat pouze zboží skladem"; 
          
            } 
            else {
                 $checkbox="<input type=\"checkbox\" name=\"ZBOZI_SKLADEM\" value='1'  disabled=\"disabled\" style=\"color:blue\" />
                 Exportovat pouze zboží skladem NELZE - NEJPRVE ZAPŇETE ŘÍZENÍ SKLADU
                 "; 
            } 
                   $this->_html .=$checkbox." &nbsp;";
                   
            $checkbox="<input type=\"checkbox\" name=\"ZBOZI_PARTIAL_UNISTALL\" value='1' style=\"color:blue\"";
            if(Configuration::get("ZBOZI_PARTIAL_UNISTALL"))
                 $checkbox.=" checked=\"checked\" ";
            $checkbox.="/>Při odinstalaci zachovat pole Heureka Category &nbsp;"; 
            $this->_html .=$checkbox." &nbsp;";
               $checkbox="<input type=\"checkbox\" name=\"ZBOZI_ROUND_PRICES\" value='1' style=\"color:blue\"";
            if(Configuration::get("ZBOZI_ROUND_PRICES"))
                 $checkbox.=" checked=\"checked\" ";
            $checkbox.="/>Ceny zaokrouhlovat jako v eshopu"; 
           $this->_html .=$checkbox."<br /><br />";
               
       
       
         $this->_html .='
         Velikosti obrázků: <select name ="ZBOZI_IMG">';
         
       $images=ImageType::getImagesTypes('products');
       if(empty($this->config["ZBOZI_IMG"]))
         $this->config["ZBOZI_IMG"]=$images[0]['name'];
        foreach($images as $image) {
            if($image['height'] < 800 &&  $image['width'] < 800) {
              $this->_html .="<option value=\"".$image['name']."\"";
              if($image['name'] == $this->config["ZBOZI_IMG"])
                $this->_html.=" selected=\"selected\"";
              $this->_html.=">".$image['name'].' ('.$image['width'].'x'.$image['height'].")</option>";
            }
        }    
        $this->_html .='</select>
        vyberte jakou velikost obrázků ve feedu chcete použít
        <br /><br />';
        
     $this->_html .='
        Pole pro popis: <select name ="ZBOZI_DESCRIPTION">';
        $keys=array('description_short', 'description');
        foreach($keys as $key) {
              $this->_html .="<option value=\"$key\"";
               if($key == $this->config["ZBOZI_DESCRIPTION"])
                $this->_html.=" selected=\"selected\"";
              $this->_html.=">$key</option>";
        }    
        $this->_html .='</select><br />';
        
       $this->_html .='
        Dovolené html tagy:   <input  type="text" name="ZBOZI_ALLOWTAGS" value="'.$this->config['ZBOZI_ALLOWTAGS'].'" />     <br />';
      
         $this->_html .='Vynechané produkty - ID produktů oddělené čárkou: <input type="text"  size="50" name="ZBOZI_FORBIDDEN" value="'.$this->config["ZBOZI_FORBIDDEN"].'"><br /><br />';
      
      
       $this->_html .='
        Heureka CPC: <select name ="ZBOZI_CPC">';
        $keys=array('', 'wholesale_price' , 'manufacturer_reference', 'reference', 'ean13' , 'upc');
        $vals=array('nic', 'nákupní cena ' , 'kód zboží dodavatele', 'kód zboží', 'EAN13 nebo JAN', 'UPC');
        $counter=0;
        foreach($keys as $key) {
              $this->_html .="<option value=\"$key\"";
               if($key == $this->config["ZBOZI_CPC"])
                $this->_html.=" selected=\"selected\"";
              $this->_html.=">{$vals[$counter]}</option>";
        $counter++;
        }    
        $this->_html .='</select>  <br />
        Pokud používáte heureka CPC, můžete pro ně využít některé z polí určených pro jiné účely, pokud to neovlivní funkci eshopu.
        Pokud je dané pole u produktu vyplněno, bude hodnota dosazena za <a href="http://sluzby.heureka.cz/napoveda/xml-feed/">HEUREKA_CPC</a>.
        
        <br />  <br />';   
        
        
         $this->_html .='
        <h4>Párování kategorií heureka</h4>: Tato verze modulu páruje feed heureka na základě nastavení kategorií.
        <ol>
        <li>
         Otevřete   <a href="http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml" target="_blank">Specifikaci Heureka</a>
        </li>
        
        <li>
        Ve specifikaci vyhledáte vhodné CATEGORY_FULLNAME tedy například: "Heureka.cz | Oblečení a móda | Dětské oblečení | Dětské plavky"
        </li>
        <li> 
         V administraci svého eshopu otevřete příslušnou kategorii a do pole "Heureka kategorie" okopírujete celou cestu (CATEGORY_FULLNAME)
       </li> 
          '
          
          ;
        
        $this->_html .='
        <input class="button" name="btnSubmit" value="Uložit změny" type="submit" /> 
            </fieldset>
        </form>';
    }

   protected function optAvailabilityMode($value) {
       if($value==0 && ($value=$this->availability_mode || empty($this->availability_mode)))
         return " checked='checked'";
         
       if($value==$this->availability_mode)
           return " checked='checked'";
           
   }
    
    protected function check_feeddir() {
      $dir="../".$this->feeddir;
      if(!is_dir($dir)) {
         mkdir($dir);
      } 
     if(!is_dir($dir)) {
         $this->_errors[]=$this->l('Důvod selhání: nepodařilo se vytvořit adresář ').$this->feeddir .'.';
         return 0;
     } 
     if(!is_writable($dir)) {
         chmod($dir, 0755);
     }
     if(!is_writable($dir)) {
                $this->_errors[]=$this->l('Důvod selhání: nelze zapisovat do adresáře ').$this->feeddir.'.';
            return 0;
     }
        return 1;
    /* chmod(
     $fp=fopen($dir."/test.txt", "w+");
     if(!
    */    
    }
    
 public   function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';

        if (!empty($_POST))
        {
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                 foreach ($this->_postErrors AS $err)
                    $this->_html .= '<div class="alert error">'. $err .'</div>';
        }
        else
            $this->_html .= '<br />';

        $this->_displayZbozi();
        $this->_displayForm();

        return $this->_html;
    }    
    

	

}
