<?php
 /**
 * upraveno z modulu feeder
 * 
 * @var mixed
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/**
*  prvni potencialni funkce aplikovana na vystup z database
*  vhodna pokud je cestina v databasi ulozena jako html entity 
*/
define ("HTML_ENTITY_DECODE", 1);
if($_SERVER['HTTP_HOST'] == 'localhost:8080') {
define ("DEBUG", 1);
$step=10;
$total=20; 

}
else   {
define ("DEBUG", 0);
$step=200;
$total=10000; 
}



/**
* druha potencialni funkce odstrani html tagy jeste pred pripadnym kodovanim entit
* 
*/
define("REMOVE_HTML_TAGS", 1);

/**
* treti funkce pouzita na vystup z database
* 0 .... nic, feed nebude pravdepodobne validni
* 1 .... htmlspecialchars zakoduje specialni html znaky
* 2 .... htmlentities   zakoduje veskere html
*/
define("ENCODE_ENTITIES", 1); 

define("DEF_AVAILABLE_LATER", 10);  // při zapnutém skladu pro zboží které není skladem


define("START_CATEGORY_LEVEL", 2); 
 
define("ZIP_FILE", 1); 
 
 if(DEBUG == 1) {
   ob_start();
 
 }  
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

if(file_exists("zboziplus.php")) 
require_once("zboziplus.php");



 $uniqueId=0;
 $starttime=time();
 // 
 $id_lang=(int)Configuration::get('PS_LANG_DEFAULT');

if(!$id_lang) {
    $sql='SELECT id_lang FROM ps_lang  WHERE iso_code="cs"';
    $id_lang = (int)Db::getInstance()->getValue($sql);
}


$id_shop=(int)Tools::getValue('id_shop')?(int)Tools::getValue('id_shop'):1;
Context::getContext()->shop->id=$id_shop;

if(file_exists(dirname(__FILE__).'/ZboziAttributes.php')) {
    $do_attribudes=1;
    require_once(dirname(__FILE__).'/ZboziAttributes.php');
}
else {
    $do_attribudes=0;
}
require_once(dirname(__FILE__).'/zbozi.php');   

$c= new Zbozi();
$ff=$c->GetSetting("feeds");
foreach($ff as $f) {
$test=Configuration::get('ZBOZI_'.strtoupper($f));
if($test) {
  $feeds[]=$f;  
}
}


$feeddir= '../../'.$c->GetSetting("feeddir");   
 if(!is_dir($feeddir)) {
     mkdir($feeddir);
 }  

$heurekaTree=array();
$sql='SELECT * FROM  '._DB_PREFIX_.'category';
$sql='SHOW COLUMNS FROM  '._DB_PREFIX_.'category LIKE "heureka_category"';
$test= Db::getInstance()->executeS($sql);
if($test && is_array($test) ) {
  $heurekaTree=buildHeurekaTree();   
}

$catTree=array();
$sql='SELECT MAX(level_depth) FROM '._DB_PREFIX_.'category c LEFT JOIN  '._DB_PREFIX_.'category_shop cs ON
    c.id_category=cs.id_category 
      WHERE cs.id_shop='.Context::getContext()->shop->id;
 $maxLevel= Db::getInstance()->getValue($sql);

 $sql='SELECT c.id_category FROM '._DB_PREFIX_.'category c LEFT JOIN  '._DB_PREFIX_.'category_shop cs ON
    c.id_category=cs.id_category 
      WHERE c.is_root_category = 1 AND cs.id_shop='.Context::getContext()->shop->id;
 $root_category = (int)Db::getInstance()->getValue($sql);
 if(empty($root_category))
 $root_category=2;
 


if($maxLevel > 6)
 $maxLevel=6;
 
 
getCategoryTree(START_CATEGORY_LEVEL, $maxLevel, $catTree,'', $root_category);


$forbidden=array();
$zs=Configuration::get("ZBOZI_FORBIDDEN");
if(strlen($zs)) {
  $a=explode(',',$zs);
  foreach($a as $id_product) {
    if((int) $id_product > 0)
    $forbidden[]=$id_product;
  }  
    
}


foreach($feeds as $feed) {

$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {

     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->initFeed( $feeddir);   
    } 
}


set_time_limit (600); 
for($start=0; $start < $total; $start+=$step) {
 
    // static public function getProducts($id_lang, $start,     $limit, $orderBy,    $orderWay, $id_category = false, $only_active = false)
    if(defined("DEBUG") && (int)DEBUG == 1) {
   
    $products =Product::getProducts(      $id_lang,         $start,      $step,  'id_product', 'asc',      false,                    true);
    }
    else {
    $products =Product::getProducts(      $id_lang,         $start,      $step,  'id_product', 'asc',      false,                    true);
    }
    foreach($products as $key=>&$product) {
        if(in_array($product['id_product'], $forbidden)) {
         unset($products[$key] );
        }
        else {
         $product['price'] = Product::getPriceStatic($product['id_product'], true, ((isset($product['id_product_attribute']) AND !empty($product['id_product_attribute'])) ? intval($product['id_product_attribute']) : NULL), 2);
         $product['quantity'] = Product::getQuantity($product['id_product']);
        if($do_attribudes) { 
           $product['attributes'] =ZboziAttributes::getProductAttributes($product['id_product']);// Product::getProductAttributesIds($product['id_product']);
        }  
 
       
        
         if(is_array($heurekaTree) && count($heurekaTree) && ! empty($heurekaTree[$product['id_category_default']]) ) {
          $product['categorytext_heureka'] = $heurekaTree[$product['id_category_default']];
          $product['categorytext_seznam'] = $catTree[$product['id_category_default']];
         
           
         }
         elseif(isset($catTree[$product['id_category_default']])) {
         $product['categorytext_seznam'] = $catTree[$product['id_category_default']];
         $product['categorytext_heureka'] = $catTree[$product['id_category_default']];
         }
         else {
         $product['categorytext_seznam'] ='';
         $product['categorytext_heureka'] ='';
         }
        }  
    }
    
reset ($feeds);    
foreach($feeds as $feed) {

$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {

     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->createFeed($products, $feeddir);  
     unset($Feed); 
    } 
} 
if(DEBUG == 1) {
    echo $start.': '.time()-$starttime.' sec  MEM:'.memory_get_usage()."\n";
    ob_flush();
    flush();
 } 
 unset($products);
} 

reset ($feeds);
foreach($feeds as $feed) {
 echo 'finishing';
$classname= "Feed".ucfirst($feed);

    if(file_exists($classname.".php")) {

     require_once($classname.".php");
     $Feed=new  $classname;
     $Feed->finishFeed( $feeddir);   
    } 
}


 

 


function  getCategoryTree($level, $maxlevel, &$catTree, $name='', $parent=2) {
    global $id_lang;
    if($level > $maxlevel) {
      return;
    }
 
    $sql='SELECT cl.name,   c.`id_category`
         FROM 
        `'._DB_PREFIX_.'category` c LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
        ON c.id_category = cl.id_category
           LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
        ON c.id_category =  cs.id_category
        WHERE cl.id_lang='.(int)$id_lang.' AND c.level_depth='.(int)$level.
        ' AND cs.id_shop='.(int)Context::getContext()->shop->id.' 
        AND c.id_parent='.(int)$parent.' GROUP BY  c.`id_category`
        ';
  
    
    $ct= Db::getInstance()->ExecuteS($sql);
    foreach($ct as $cat) {
        $catname=$name.$cat['name'].' | ';
        $catTree[$cat['id_category']]= $catname;
        getCategoryTree($level+1,$maxlevel, $catTree, $catname, $cat['id_category']); 
    }
     
}

 function buildHeurekaTree() {
      $retval=array();
        $sql='SELECT c.heureka_category,   c.`id_category`
         FROM 
        `'._DB_PREFIX_.'category` c 
           LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
        ON c.id_category =  cs.id_category
        WHERE cs.id_shop='.(int)Context::getContext()->shop->id.' 
         GROUP BY  c.`id_category`
        '; 
     $ct= Db::getInstance()->ExecuteS($sql);
    foreach($ct as $cat) {
        $retval[$cat['id_category']]= $cat['heureka_category']; 
    }
   return $retval;    
 }
  function friendly_url($nadpis) {
    $prevodni_tabulka = Array(
  'ä'=>'a',
  'Ä'=>'A',
  'á'=>'a',
  'Á'=>'A',
  'à'=>'a',
  'À'=>'A',
  'ã'=>'a',
  'Ã'=>'A',
  'â'=>'a',
  'Â'=>'A',
  'č'=>'c',
  'Č'=>'C',
  'ć'=>'c',
  'Ć'=>'C',
  'ď'=>'d',
  'Ď'=>'D',
  'ě'=>'e',
  'Ě'=>'E',
  'é'=>'e',
  'É'=>'E',
  'ë'=>'e',
  'Ë'=>'E',
  'è'=>'e',
  'È'=>'E',
  'ê'=>'e',
  'Ê'=>'E',
  'í'=>'i',
  'Í'=>'I',
  'ï'=>'i',
  'Ï'=>'I',
  'ì'=>'i',
  'Ì'=>'I',
  'î'=>'i',
  'Î'=>'I',
  'ľ'=>'l',
  'Ľ'=>'L',
  'ĺ'=>'l',
  'Ĺ'=>'L',
  'ń'=>'n',
  'Ń'=>'N',
  'ň'=>'n',
  'Ň'=>'N',
  'ñ'=>'n',
  'Ñ'=>'N',
  'ó'=>'o',
  'Ó'=>'O',
  'ö'=>'o',
  'Ö'=>'O',
  'ô'=>'o',
  'Ô'=>'O',
  'ò'=>'o',
  'Ò'=>'O',
  'õ'=>'o',
  'Õ'=>'O',
  'ő'=>'o',
  'Ő'=>'O',
  'ř'=>'r',
  'Ř'=>'R',
  'ŕ'=>'r',
  'Ŕ'=>'R',
  'š'=>'s',
  'Š'=>'S',
  'ś'=>'s',
  'Ś'=>'S',
  'ť'=>'t',
  'Ť'=>'T',
  'ú'=>'u',
  'Ú'=>'U',
  'ů'=>'u',
  'Ů'=>'U',
  'ü'=>'u',
  'Ü'=>'U',
  'ù'=>'u',
  'Ù'=>'U',
  'ũ'=>'u',
  'Ũ'=>'U',
  'û'=>'u',
  'Û'=>'U',
  'ý'=>'y',
  'Ý'=>'Y',
  'ž'=>'z',
  'Ž'=>'Z',
  'ź'=>'z',
  'Ź'=>'Z'
  
);
    
    $nadpis =strtolower(( strtr($nadpis, $prevodni_tabulka)));   
    $url = $nadpis;
    $url=str_replace(' ', '_', $url);
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}
?>

