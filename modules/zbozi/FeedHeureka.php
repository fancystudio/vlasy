<?php
 require_once("cFeed.php");
  class FeedHeureka extends cFeed {
protected  $feedname='zbozi_heureka.xml';
protected $heureka_categories;

 public function __construct() {
   
   parent::__construct();  
   if(strlen($this->heureka_category))
      $this->heureka_categories=$this->getHeurekaCategories();
   
 }
 
 protected function StartFeed($fp) {
    fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
    fputs($fp,  "<SHOP>\n"); 
  }         
      
 protected function CloseFeed($fp) {
      fputs($fp,  "</SHOP>");  
 }     
 protected function createItem($product, $url, $imgurl) {
   
      $item= "\t\t<SHOPITEM>\n";
      $item.=$this->createTag('ITEM_ID', $this->getUniqueId());
      $item.=$this->createTag('PRODUCTNAME', $this->prepareString($product['name']).' '.$this->prepareString($product['manufacturer_name']).' '.$this->prepareString($product['reference']));
      $item.=$this->createTag('PRODUCT', $this->prepareString($product['name']).' '.$this->prepareString($product['manufacturer_name']).' '.$this->prepareString($product['reference']));
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product)));
      $item.=$this->createTag('URL', $this->prepareString($url));  
      if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      $item.=$this->createTag('PRICE_VAT', $product['price']); 
                                                                                              
       $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_heureka'])));  
      
    
          
      $item.=$this->createTag('PRICE_VAT', $product['price']);
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($product)); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      if($product['ean13'])
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
      if($this->cpc && $product[$this->cpc]) { // defaultne manufacturer_reference
         $item .=$this->createTag('HEUREKA_CPC', str_replace('.', ',', (float)($product[$this->cpc])));
      }
      
      $item.="\t\t</SHOPITEM>\n";
       
        return $item;
 }

protected function getItemGroup($product, $url, $cover) {
 
    $itemgroup='';
     foreach($product['attributes'] as $combination) {
             if($this->jen_skladem && $this->stock_management &&  $combination['quantity'] <=0)
              continue;
              
            $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover);  
        }
     return $itemgroup;
}
 
 protected function createItemCombination($product, $combination, $url, $imgurl) {
      $item= "\t\t<SHOPITEM>\n";
    
       $item.=$this->createTag('ITEM_ID', $this->getUniqueId());
       $item.=$this->createTag('ITEMGROUP_ID', $product['id_product']); 
      $item.=$this->createTag('PRODUCTNAME', $this->prepareString($product['name'].$this->getCombinationName($combination['attributes'])).' '.$this->prepareString($product['manufacturer_name']).' '.$this->prepareString($product['reference']));
      $item.=$this->createTag('PRODUCT', $this->prepareString($product['name'].$this->getCombinationName($combination['attributes'])).' '.$this->prepareString($product['manufacturer_name']).' '.$this->prepareString($product['reference']));
      
      $item.=$this->createTag('DESCRIPTION', $this->prepareString($this->getDescription($product)));
      $url.='#'.$this->getCombinationUrl($combination['attributes']);
      $item.=$this->createTag('URL', $this->prepareString($url));  
      
      if($combination['id_image']) {
             $name=$this->toUrl($product['name']);
             global $link;
             $imgurl=$link->getImageLink($name, $product['id_product'].'-'.$combination['id_image'], $this->imagetype);  
             $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      elseif($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
     if(isset($this->cache[$product['id_product']][$combination['id_product_attribute']]) 
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd'] == $product['date_upd']
       &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price'] == $product['price'] 
         &&  $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price'] == $combination['price'] 
     ) {
        $price=$this->cache[$product['id_product']][$combination['id_product_attribute']]['price'];  
     }
     else {
      $price=Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute'],2);
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['price']=$price;
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['date_upd']=$product['date_upd']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['product_price']=$product['price']; 
        $this->cache[$product['id_product']][$combination['id_product_attribute']]['attribute_price']=$combination['price']; 
     } 
    
      $item.=$this->createTag('PRICE_VAT', $price); 
      $item.=$this->createTag('CATEGORYTEXT', $this->prepareString($this->getCategoryText($product['categorytext_heureka'])));  
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($combination)); 
         
      if($product['manufacturer_name'])
      $item.=$this->createTag('MANUFACTURER', $this->prepareString($product['manufacturer_name']));
      if($product['ean13'])
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
      if($this->cpc && $product[$this->cpc]) { // defaultne manufacturer_reference
         $item .=$this->createTag('HEUREKA_CPC', str_replace('.', ',', (float)($product[$this->cpc])));
      }
      
      $item.="\t\t</SHOPITEM>\n";
        
        return $item;  
       
   }   

 protected function getUniqueId() {
   global $uniqueId;
   return ++$uniqueId;   
 }
 
 protected function getCategoryText($categorytext)  {
 //echo  $categorytext;  
  return $categorytext;
 }         
 
 private function cantor($x, $y)
{
    // ((x + y) * (x + y + 1)) / 2 + y;
    if(function_exists('bcadd'))
    return bcadd(bcdiv(bcmul(bcadd($x, $y), bcadd(bcadd($x, $y), 1)), 2), $y);
    
    return (($x + $y) * ($x + $y + 1)) / 2 + $y;
}
 
     protected function getHeurekaCategories() {
        $retval=array();
    //   $xml=simplexml_load_file("http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml");
     $s= file_get_contents("http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml");  
     $arr=explode("<CATEGORY_ID>", $s);
     foreach($arr as $chunk) {
         if((int)$chunk > 0) {
               $chunk=str_replace('</CATEGORY>', '' , $chunk);
               $chunk=str_replace('<CATEGORY>', '' , $chunk);
               $chunk=str_replace('</HEUREKA>', '' , $chunk);
               $xml=simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><CHUNK><CATEGORY_ID>".$chunk.'</CHUNK>');
               if($xml && strlen((string)$xml->CATEGORY_FULLNAME) > 6   && (int)$xml->CATEGORY_ID ) {
                 $retval[ (int)$xml->CATEGORY_ID] =(string)$xml->CATEGORY_FULLNAME; 
               }
         }
     }
     return $retval;
  }
  }
?>
