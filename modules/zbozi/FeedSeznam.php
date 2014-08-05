<?php
 require_once("cFeed.php");
  class FeedSeznam extends cFeed {
   protected  $feedname='zbozi_seznam.xml';
   protected function StartFeed($fp) {
    fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
    fputs($fp,  "<SHOP>\n"); 
  }      
   protected function CloseFeed($fp) {
      fputs($fp,  "</SHOP>");  
     
 } 
 
 protected function createItem($product, $url, $imgurl) {
    $item= "\t\t<SHOPITEM>\n";
    $item.= "\t\t\t<PRODUCT>".$this->prepareString($product['name'])."</PRODUCT>\n";
    $item.= "\t\t\t<PRODUCTNAME>".$this->prepareString($product['name'])."</PRODUCTNAME>\n";
    $item.=$this->getCategoryText($product['categorytext_seznam']);
  
     
     
      
    
    
    $item.= "\t\t\t<DESCRIPTION>".$this->prepareString($this->getDescription($product))."</DESCRIPTION>\n";
    $item.= "\t\t\t<DESCRIPTION_SHORT>".$this->prepareString($product['description_short'])."</DESCRIPTION_SHORT>\n";
    $item.= "\t\t\t<URL>".$this->prepareString($url)."</URL>\n";
   // $item.= "\t\t\t<SHOP_DEPOTS>dets2908</SHOP_DEPOTS>\n";
 
     if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
    $item.= "\t\t\t<PRICE_VAT>".$this->prepareString($product['price'])."</PRICE_VAT>\n"; 
    $item.= "\t\t\t<DELIVERY_DATE>".$this->getAvailability($product)."</DELIVERY_DATE>\n";

    $item.="\t\t</SHOPITEM>\n";

    return $item;
 
 }    
 
 protected function getItemGroup($product, $url, $cover) {
    $itemgroup='<SHOPITEM>';
    $itemgroup.= "\t\t\t<PRODUCT>".$this->prepareString($product['name'])."</PRODUCT>\n";
    $itemgroup.=$this->getCategoryText($product['categorytext_seznam']);
    
     
     
        
    $itemgroup.= "\t\t\t<DESCRIPTION>".$this->prepareString($this->getDescription($product))."</DESCRIPTION>\n";
    $itemgroup.= "\t\t\t<DESCRIPTION_SHORT>".$this->prepareString($product['description_short'])."</DESCRIPTION_SHORT>\n";
    $itemgroup.= "\t\t\t<URL>".$this->prepareString($url)."</URL>\n";
 //   $itemgroup.= "\t\t\t<SHOP_DEPOTS>dets2908</SHOP_DEPOTS>\n";
 
     if($cover) {
            $itemgroup.=$this->createTag('IMGURL', $this->prepareString($cover));   
      }
    $itemgroup.= "\t\t\t<PRICE_VAT>".$this->prepareString($product['price'])."</PRICE_VAT>\n"; 
    $itemgroup.= "\t\t\t<DELIVERY_DATE>".$this->getAvailability($product)."</DELIVERY_DATE>\n";
    
    
    
    
     foreach($product['attributes'] as $combination) {
              if($this->jen_skladem && $this->stock_management &&  $combination['quantity'] <= 0)
              continue;
            $itemgroup.=$this->createItemCombination($product, $combination, $url, $cover);  
        }
    $itemgroup.='</SHOPITEM>'; 
     return $itemgroup;
}  



   protected function createItemCombination($product, $combination, $url, $imgurl) {
      $item= "\t\t<VARIANT>\n";
    
      $item.=$this->createTag('PRODUCTNAMEEXT', $this->prepareString($product['name'].$this->getCombinationName($combination['attributes'])).' '.$this->prepareString($product['manufacturer_name']).' '.$this->prepareString($product['reference']));
     
       $url.='#'.$this->getCombinationUrl($combination['attributes']);
      if($imgurl) {
            $item.=$this->createTag('IMGURL', $this->prepareString($imgurl));   
      }
      
  
     if(isset( $this->cache[$product['id_product']][$combination['id_product_attribute']]) 
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
      $item.=$this->createTag('DELIVERY_DATE', $this->getAvailability($combination)); 
         
    
      if($product['ean13'])
      $item.=$this->createTag('EAN', $this->prepareString($product['ean13']));
      
      
      $item.="\t\t</VARIANT>\n";
        
        return $item;  
       
   }    
 protected function getCategoryText($categorytext)  {
     $item='';
      if(!empty($categorytext)  && is_array($categorytext)) {
    foreach($categorytext as $category) {
     $item.='<CATEGORYTEXT>'.$category.'</CATEGORYTEXT>';
    }
    } 
    elseif(!empty($categorytext)) {
        $item.='<CATEGORYTEXT>'.$categorytext.'</CATEGORYTEXT>';  
    }
    
    return $item; 
 }
      
  }
?>
