<?php
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	$Subpackage
 * @copyright	Copyright (C) May 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @website 	htt://landofcoder.com
 * @license		GNU General Public License version 2
 */
if (!defined('_CAN_LOAD_FILES_')){
    define('_CAN_LOAD_FILES_',1);
}
if( !class_exists('LofFeaturedProductDataSource', false) ){  
	class LofFeaturedProductDataSource extends LofFeaturedDataSourceBase{
	   	
		
        function getListFeatured( $params ){
            global $cookie, $link;
           	$id_lang = intval($cookie->id_lang);
			if(_PS_VERSION_ >= "1.5")
				$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
			else
				$category = new Category(1, Configuration::get('PS_LANG_DEFAULT'));
			
            $maxDesc = $params->get( 'des_max_chars',100);
			$limit_items = $params->get( 'limit_items',10);
    		$listFeatured = $category->getProducts((int)(intval($cookie->id_lang)), 1, $limit_items);
			$listFeatured = Product::getProductsProperties($id_lang, $listFeatured);

            foreach( $listFeatured as &$product ){
                $product['description']=substr(trim(strip_tags($product['description_short'])),0, $maxDesc);
                $product['price']=Tools::displayPrice($product['price']);                 
                $product = $this->parseImages( $product,$params );                
                $product = $this->generateImages( $product, $params );
            }
            return $listFeatured;
        }
        /**
		 * get main image and thumb
		 *
		 * @param poiter $row .
		 * @return void
		 */
		public  function parseImages( $product, $params ){
			global $link;
            
            $isRenderedMainImage = 	$params->get("cre_main_size",0);
			if(_PS_VERSION_ <= "1.5.0.17")
				$mainImageSize       =  $params->get("main_img_size",'thickbox');
			else
				$mainImageSize       =  $params->get("main_img_size",'thickbox_default');
            
            if( $isRenderedMainImage ) { 
				if((int)Configuration::get('PS_REWRITING_SETTINGS') == 1){
					$product["mainImge"] = $this->getImageLink($product["link_rewrite"], $product["id_image"] );
				}else{
					$product["mainImge"] = $link->getImageLink($product["link_rewrite"], $product["id_image"] );
				}
	        } else{
	        	$product["mainImge"] = $link->getImageLink($product["link_rewrite"], $product["id_image"], $mainImageSize ); 
	        }
            $product["thumbImge"] = $product["mainImge"];

            return $product; 
		}                                   			
	}
}
?>