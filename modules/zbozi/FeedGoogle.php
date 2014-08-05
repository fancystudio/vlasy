<?php
 require_once("cFeed.php");
  class FeedGoogle extends cFeed {
  protected  $feedname='zbozi_google.xml';
  
  protected function StartFeed($fp) {
        fputs($fp,  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n
        <feed xmlns=\"http://www.w3.org/2005/Atom\" xmlns:g=\"http://base.google.com/ns/1.0\">\n
        <title>".Configuration::get(PS_SHOP_DOMAIN)."</title>\n
        <link href=\"http://".Configuration::get(PS_SHOP_DOMAIN)."/$feeddir/google.xml\" rel=\"alternate\" type=\"text/html\" />
        <updated>".date('Y-m-d H:m:i')."</updated>\n
        <id>tag:".Configuration::get(PS_SHOP_DOMAIN).",".date('Y-m-d')."</id>\n");
  }     
   protected function CloseFeed($fp) {
     fputs($fp,  "</feed>");
     fclose($fp);  
 }       

protected function createItem($product, $url, $imgurl) {
       $item= "\t\t<entry>\n";
         $item.= "\t\t\t<g:id>".$this->prepareString($product['reference'])."</g:id>\n";
         $item.= "\t\t\t<title>".$this->prepareString($product['name'])."</title>\n";
         $item.= "\t\t\t<description>".$this->prepareString($this->getDescription($product))."</description>\n";
         $item.= "\t\t\t<g:product_type>".$this->getCategoryText($product['categorytext'])."</g:product_type>\n";
         
         $item.= "\t\t\t<link>".$this->prepareString($url)."</link>\n";
       
         $item.= "\t\t\t<updated>".$product['date_upd']."</updated>\n";
         
  
         
         $item.="\t\t\t<g:image_link>".$this->prepareString($imgurl)."</g:image_link>";
      
         $item.= "\t\t\t<g:condition>new</g:condition>\n";
      
      $availability='in stock';
      if(empty($product['available_now'])) 
       $availability='out of stock';
      $item.= "\t\t\t<g:availability>$availability</g:availability>\n";
      
      $item.= "\t\t\t<g:price>".$this->prepareString($product['price'])." CZK</g:price>\n"; 
      $item.= "\t\t\t<g:brand>".$this->prepareString($product['manufacturer_name'])."</g:brand>\n";  
       $item.= "\t\t\t<g:mpn>".$this->prepareString($product['reference'])."</g:mpn>\n"; 
       
     $item.="  <g:shipping>
   <g:country>CZ</g:country>
   <g:service>Standard</g:service>
   <g:price>49</g:price>
</g:shipping>";
       
         
     //    $item.= "\t\t\t<DELIVERY_DATE>".$this->getAvailability($product)."</DELIVERY_DATE>\n";
     //    $item.= "\t\t\t<CATEGORYTEXT>".$this->prepareString($this->get_categorytext($product))."</CATEGORYTEXT>\n";   
        $item.="\t\t</entry>\n";
     return $item;
}
      

         
  protected function getCategoryText($categorytext)  {
     $item='';
      if(!empty($categorytext)  && is_array($categorytext)) {
    foreach($categorytext as $category) {
     $item.=$category['name'].' ';
    }
    } 
    return $item; 
 } 
 
         
  }
?>
