<?php
  class cFeed {
      protected $imagetype;
      protected $descrition_field;
      protected $allow_tags=0;
      protected $availability=0;
      protected $availability_later=10;
      protected $availability_mode=0;
      protected $stock_management=0;
      protected $cpc; // nazev pole ktere bude brano jako cena pro cpc
      protected $heureka_category;
      protected $cache;
      protected $cache_path;
      protected $jen_skladem;
      protected $decimals = null;
  
   
      public function __construct() {
       $config = Configuration::getMultiple(array('ZBOZI_IMG', 'ZBOZI_SKLADEM','ZBOZI_DESCRIPTION', 'ZBOZI_ALLOWTAGS', 'ZBOZI_AVAILABILITY','ZBOZI_AVAILABILITY_LATER',  'PS_STOCK_MANAGEMENT', 'ZBOZI_CPC', 'ZBOZI_AVAILABILITY_MODE', 'ZBOZI_HEUREKA_CATEGORY'));
       $this->imagetype=$config['ZBOZI_IMG'];
      
 
       if(empty($this->imagetype) )   
            $this->imagetype='medium';
            
       if(Configuration::get('ZBOZI_ROUND_PRICES')) {
           $currency=new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
           $this->decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
       }    
            
      $this->descrition_field = $config['ZBOZI_DESCRIPTION'];  
      $this->allow_tags = (int)$config['ZBOZI_ALLOWTAGS'];  
      $this->availability = (int)$config['ZBOZI_AVAILABILITY'];   
      $this->availability_later = (int)$config['ZBOZI_AVAILABILITY_LATER'];    
      $this->stock_management = (int)$config['PS_STOCK_MANAGEMENT'];  
      $this->jen_skladem  = (int)$config['ZBOZI_SKLADEM'];  
       $this->availability_mode= (int)$config['ZBOZI_AVAILABILITY_MODE'];     
      $this->cpc = $config['ZBOZI_CPC'];  
      $this->heureka_category  = $config['ZBOZI_HEUREKA_CATEGORY'];
      
       if(Tools::getValue('id_shop')) {
          $sql='SELECT name FROM `'._DB_PREFIX_.'shop` WHERE id_shop='.(int)Tools::getValue('id_shop');
          $shop=  Db::getInstance()->getValue($sql);
          $this->feedname=$shop.'_'.$this->feedname;
      }
     
      $this->cache_path=dirname(__FILE__).'/cache/'.Context::getContext()->shop->id;
      
      $this->cache=$this->loadCache();
      }
      
   public function __destruct() {
       if(is_array($this->cache)) {
            if(file_exists($this->cache_path))
              unlink($this->cache_path);
             file_put_contents($this->cache_path, json_encode($this->cache));
      }
   }   
      
    protected function getAvailability($product) {
        
          // respektuji rizeni skladu
          if($this->availability_mode == 0 || empty($this->availability_mode)) {
               if($this->stock_management) {
                     if($product["quantity"] > 0) {
                        if(isset($product['available_now']))
                            return $this->parseAvailability($product['available_now'], 'available_now'); 
                        else
                          return (int) $this->availability;   
                     }
                     else {
                       if(isset($product['available_later']))
                         return $this->parseAvailability($product['available_later'], 'available_later');   
                       else
                       return (int) $this->availability_later; 
                     }
               }
                  else
                 return (int) $this->availability; 
             }
          // parsuje text 
          elseif($this->availability_mode==1) {
               return $this->parseAvailability($product['available_now'], 'available_now'); 
          }
          elseif($this->availability_mode==2) {
            return (int) $this->availability;   
          }
   }
      
        
     

        
 private function parseAvailability($text, $key) {
    
     $c=preg_replace('/[^0-9-]/', '', $text);
     $c=trim($c);
     
     if(strlen($c) && strpos($c, '-')) {
         return (int)$c;
     } 
    $c=str_replace('-','', $c);
    
    if($c==24) // napr do 24 hodin
      return 0;
       
    if((int)($c) > 0 )
     return (int) $c;
    
    if($c===0)
       return (int) $c;
    
    
    if($key=='available_now') {
     $text=strtolower($text);
     $pos=strpos($text, 'ihned');
      if($pos===0 || (int) $pos > 0)
        return 0;
        
     if($text  == 'skladem')
       return 0;
    return (int) $this->availability;     
    }    
    
    return (int) $this->availability_later;     
    
 }   
 
public function initFeed($feeddir) {
 $feedpath =$feeddir.'/'.$this->feedname;

if(file_exists($feedpath))
unlink($feedpath);
$fp=fopen($feedpath, "a+");
if(!$fp) {
  echo "failed to open ".$feedpath;  
}
$this->StartFeed($fp);
fclose($fp); 
} 

public function finishFeed($feeddir) {
 $feedpath =$feeddir.'/'.$this->feedname;  
 $fp=fopen($feedpath, "a+");
  $this->CloseFeed($fp);
 if($fp)
  fclose($fp); 
  
  if(defined("ZIP_FILE") &&   ZIP_FILE == 1) {
      $this->toZip($feeddir, $this->feedname);
  }
}

public function toZip($feeddir, $feedname) {
    $feedpath =$feeddir.'/'.$feedname;  
    $zip = new ZipArchive();
    if(file_exists($feedpath.'.zip'))
      unlink ($feedpath.'.zip');
      
    if ($zip->open($feedpath.'.zip', ZIPARCHIVE::CREATE)!==TRUE) {
        exit("cannot open <$filename>\n");
    }
   if( !$zip->addFile($feedpath,$feedname)) {
     echo 'failed to zip '.$feedname;   
   }
    $zip->close();                 
 }  
        
public function createFeed($products, $feeddir) {
  global $link;    
  global $id_lang; 
   
$feedpath =$feeddir.'/'.$this->feedname;
 $fp=fopen($feedpath, "a+");

 
    foreach ($products AS $product)
    {  
     if($this->jen_skladem && $this->stock_management &&  $product['quantity'] <=0)
       continue;
       
       $cover=$this->getCoverUrl($product['id_product'], $id_lang, $product);
       $url=$link->getproductLink($product['id_product'], $product['link_rewrite'], Tools::getValue('id_category'));
       if($product['attributes'] && count($product['attributes'])) {
          $itemgroup=$this->getItemGroup($product, $url, $cover);
         fputs ($fp, $itemgroup);   
      }
      else {
        fputs ($fp, $this->createItem($product, $url, $cover));   
      }
       
       
      
  
    }
    fclose($fp); 

    }
    
   protected function getCombinationName($attributes) {
   $retval='';
 foreach($attributes as $attribute)  {
       $retval.=', '.$attribute[0].'  '.$attribute[1];
  }
 // $combination['group_name'].'-'.$combination['attribute_name'];
 return $retval;
 }
  protected function getCombinationUrl($attributes) {
     $retval='';
  foreach($attributes as $attribute)  {
       $retval.='/'.$attribute[2].'-'.$attribute[3];
  }
 // $combination['group_name'].'-'.$combination['attribute_name'];
 return $retval;
     
 }  
  
  
  protected function getCoverUrl($id_product, $id_lang, $product) {
   global $link;    
           $images= Image::getImages(intval($id_lang),$id_product);
         foreach($images as $image) {
           $name=$this->toUrl(empty($image['legend'])?$product['name']:$image['legend']);
            if($image['cover']) {   
                  $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$image['id_image'], $this->imagetype);
                   break;
            }     
         }
         
        if(empty($imgurl)) {
             $name=$this->toUrl(empty($images[0]['legend'])?$product['name']:$images[0]['legend']);
           $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$images[0]['id_image'], $this->imagetype);  
        }
       return $imgurl;
  }    
  
  protected function getDescription($product) {
     $key=$this->descrition_field=='description'?'description':'description_short';
     $s=$product[$key];
     if(mb_strlen($s, 'utf-8') > 510) {
        $s=mb_substr($s,0, 510, 'utf-8'); 
     }
     return $s;
  }   
  
  
      protected function prepareString($s) {
            if(intval(HTML_ENTITY_DECODE)==1) {
               $s=html_entity_decode($s, null, 'UTF-8'); 
            }
            
            if(intval(REMOVE_HTML_TAGS)==1) {
                if($this->allow_tags && strlen($this->allow_tags) > 3)
                   $s=strip_tags($s, $this->allow_tags);
                else
                   $s=strip_tags($s);  
            }
            
            if(intval(ENCODE_ENTITIES)==1) {
                $s=htmlspecialchars($s); 
            }
            elseif(intval(ENCODE_ENTITIES)==2) {
               $s=htmlentities($s); 
            }
           // august 2012 
           $s = preg_replace('/[\x00-\x1F]/', '', $s);
           return $s; 
        }
    
      protected function  getCategoryText($product) {
      global $id_lang; 
         $cats= Db::getInstance()->ExecuteS('
        SELECT '._DB_PREFIX_.'category_lang.name  
          
        FROM
          '._DB_PREFIX_.'category_product LEFT JOIN  '._DB_PREFIX_.'category_lang ON
        '._DB_PREFIX_.'category_product.id_category =  '._DB_PREFIX_.'category_lang.id_category
        LEFT JOIN  '._DB_PREFIX_.'category ON
        '._DB_PREFIX_.'category_product.id_category =  '._DB_PREFIX_.'category.id_category
        
            WHERE '._DB_PREFIX_.'category_product.id_product = '.intval($product["id_product"])
          .' AND    '._DB_PREFIX_.'category_lang.id_lang= '.$id_lang
           .' AND    '._DB_PREFIX_.'category.active= 1 ORDER BY  '._DB_PREFIX_.'category.level_depth ASC '
            ); 
      $retval="";
      foreach($cats as $cat) {
        $retval.=$cat["name"]." ";  
      }
     return $retval;
  } 
  
  protected function createTag($key, $value){
        if($key == 'PRICE_VAT' && !is_null($this->decimals)) {
           $value = Tools::ps_round($value, $this->decimals); 
        }
        return "\t\t\t<$key>$value</$key>\n";  
  }
  

   protected function toUrl($s) {
            if(empty($s))
              return '';
             $s=$this->cs_utf2ascii($s);
             $s=strtolower($s);
             $s= preg_replace('~[^-a-z0-9_ ]+~', '', $s);
             return str_replace(" ", "-", $s);
       }
       
     private function cs_utf2ascii($s) { 
        static $tbl = array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z"); 
        return strtr($s, $tbl); 
        }  
        
      protected function loadCache(){
         if(file_exists($this->cache_path)){
            $s=file_get_contents($this->cache_path);
            if(strlen($s))
              return json_decode($s,true); 
         }
         return array();
      }        
  }
?>
