<?php
/**
 * $ModDesc
 * 
 * @version		$Id: file.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) December 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
if (!defined('_CAN_LOAD_FILES_')){
	define('_CAN_LOAD_FILES_',1);
}    
class loffeatured extends Module
{
	private $_params = '';	
	private $_postErrors = array();		

	function __construct()
	{
		$this->name = 'loffeatured';
		parent::__construct();			
		$this->tab = 'LandOfCoder';
		$this->author = 'LandOfCoder';		
		$this->version = '1.1';
		$this->displayName = $this->l('Lof Featured Products Module');
		$this->description = $this->l('Display featured products using carouFredSel plugin jquery');
		if( file_exists( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/params.php' ) && !class_exists("LofParams", false) ){
			if( !defined("LOF_LOAD_LIB_PARAMS") ){				
				require( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/params.php' );
				define("LOF_LOAD_LIB_PARAMS",true);
			}
		}		
		$this->_params = new LofParams( $this->name );		   
	}
  
   /**
    * process installing 
    */
	function install(){		
		if (!parent::install())
			return false;
		if(!$this->registerHook('header')) return false;
		//if(!$this->registerHook('top')) return false;
		if(!$this->registerHook('displayHomeTabContent')) return false;
		return true;
	}
	
	function hooktop($params){		
		return $this->processHook( $params,"top");
	}

	function hookHome($params) {
		return $this->processHook( $params,"home");
	}
	public function hookDisplayHomeTabContent($params)
	{
		return $this->processHook( $params,"top");
	} 
	function hookHeader($params)
	{
		if(_PS_VERSION_ <="1.4"){							
			$header = '
			<link type="text/css" rel="stylesheet" href="'.($this->_path).'tmpl/assets/style.css'.'" />
			<link type="text/css" rel="stylesheet" href="'.($this->_path).'tmpl/'. $this->getParamValue('module_theme','default').'/assets/style.css'.'" />			
            <script type="text/javascript" src="'.($this->_path).'assets/script.js'.'"></script>';			
			return $header;			
		}else{
			if( !defined("_LOF_FEATURE_PRODUCT_") ){
				if(_PS_VERSION_ >= "1.5"){  
					//$this->context->controller->addCSS(($this->_path).'tmpl/assets/style.css', 'all');
					$this->context->controller->addJS(($this->_path).'assets/script.js', 'all');
				}else{
					Tools::addJS( ($this->_path).'assets/script.js', 'all');
					//Tools::addJS( ($this->_path).'assets/jquery.tools.min.js', 'all');
				}
                define('_LOF_FEATURE_PRODUCT_', 1);         
            } 
			if(_PS_VERSION_ >= "1.5"){
				$this->context->controller->addCSS(($this->_path).'tmpl/assets/style.css', 'all');
				$this->context->controller->addCSS(($this->_path).'tmpl/'. $this->getParamValue('module_theme','default').'/assets/style.css', 'all');
			}else{
				Tools::addCSS( ($this->_path).'tmpl/assets/style.css', 'all');
				Tools::addCSS( ($this->_path).'tmpl/'. $this->getParamValue('module_theme','default').'/assets/style.css', 'all');  
			}
		}		
	}
	/**
    * Proccess module by hook
    * $pparams: param of module
    * $pos: position call
    */
	function processHook( $mparams, $pos="home" ){
		
        global $cookie, $link, $smarty;
		
       	$id_lang = intval($cookie->id_lang);               
		//load param
		$params = $this->_params;
		$site_url = Tools::htmlentitiesutf8('http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
		
		if(_PS_VERSION_ <="1.4"){
			// create thumbnail folder	 						
			$thumbPath = _PS_IMG_DIR_.$this->name;
			
			if( !file_exists($thumbPath) ) {
				mkdir( $thumbPath, 0777 );			
			};
			$thumbUrl = $site_url."img/".$this->name;
		}else{			
			// create thumbnail folder	 			
			$thumbPath = _PS_CACHEFS_DIRECTORY_.$this->name;
			if( !file_exists(_PS_CACHEFS_DIRECTORY_) ) {
				mkdir( _PS_CACHEFS_DIRECTORY_, 0777 );  			
			}; 
			if( !file_exists($thumbPath) ) {
				mkdir( $thumbPath, 0777 );			
			};
			$thumbUrl = $site_url."cache/cachefs/".$this->name;			
		}
		
		if( file_exists( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/group_base.php' ) && !class_exists("LofFeaturedDataSourceBase", false) ){
			if( !defined("LOF_FEATURED_LOAD_LIB_GROUP") ) {
				require_once( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/group_base.php' );
				define("LOF_FEATURED_LOAD_LIB_GROUP",true);
			}
		}
		if( file_exists( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/phpthumb/ThumbLib.inc.php' ) && !class_exists('PhpThumbFactory', false)){						
			if( !defined("LOF_FEATURED_LOAD_LIB_PHPTHUMB") ) {
				require( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/phpthumb/ThumbLib.inc.php' );	
				define("LOF_FEATURED_LOAD_LIB_PHPTHUMB",true);
			}			
		}                
        
        //config
		$moduleId = rand().time();
        $params = $this->_params;
		$params->set( 'auto_renderthumb',0);
        $theme = $params->get("module_theme","default");
		
		$sh = $params->get( 'slide_height', '\'auto\'' );
		$slide_height = ( $sh=='auto' ) ? '\'auto\'' : (int)$sh;
		$sh = $params->get( 'slide_width', '\'auto\'' );
		$slide_width = ( $sh=='auto') ? '\'auto\'': (int)$sh;
		$auto_play = $params->get('auto_play', 1);
		$auto_play = ($auto_play == 1) ? 'true' : 'false';
		$scroll_items = $params->get('scroll_items', '1');
		
		$main_height = $params->get('main_height', '100');
		$main_width = $params->get('main_width', '100');
		$limit_cols = $params->get('limit_cols', '4');
        
		$show_price	= $params->get('show_price',1);
		$priceSpecial   = $params->get('price_special',1);
		$show_desc	= $params->get('show_desc',1);
		
		$show_title	= $params->get('show_title',1);
		$show_image	= $params->get('show_image',1);
		$show_pager	= $params->get('show_pager',1);
		$show_button = $params->get('show_button',1);
		$target = $params->get('target','_self');
		
		
		$source =  	  'product';        
		$path = dirname(__FILE__).'/libs/groups/'.strtolower($source).'/product.php'; 				
		if( !file_exists($path) ){
			return array();	
		}
        require_once $path;
		
        $objectName = "LofFeatured".ucfirst($source)."DataSource";
	 	$object = new $objectName();
		
  		
        $object->setThumbPathInfo($thumbPath,$thumbUrl)
               ->setImagesRendered( array( 'mainImage' => array( (int)$params->get( 'main_width', 150 ), (int)$params->get( 'main_height', 100 )) ) );
        
		$homeFeature =  $this->getProFeature();
		$listFeature = $object->getListFeatured( $params );   
        
		// template asignment variables
		$smarty->assign( array(
								'moduleId' 		=> $moduleId,
								'site_url'      => $site_url,
								'theme'	        => $theme,
								'slide_height' 	=> $slide_height,
								'slide_width'	=> $slide_width,
								'auto_play' 	=> $auto_play,
								'scroll_items'	=> $scroll_items,
								'main_height' 	=> $main_height,
								'main_width'	=> $main_width,
								'limit_cols' 	=> $limit_cols,
							  
								'show_price'	=> $show_price,
								'priceSpecial'  => $priceSpecial,
								'show_desc'		=> $show_desc,
								'show_title'	=> $show_title,
								'show_image'	=> $show_image,
								'show_pager'	=> $show_pager,
								'show_button'	=> $show_button,
								'target'	=> $target,
							  
								'listFeature'		=> $listFeature,
							));
							
		
		$layout = $this->getLayoutPath($theme);
		
		return $this->display(__FILE__, $layout); 				
	}
	
    public function getLayoutPath( $theme="default" ){
        $layout = 'tmpl/'.$theme.'/default.tpl';
        return $layout;
    }
    
    public function splitingCols ( $products ){
        return $output; 
    }  
    
     	   	
   /**
    * Get list of sub folder's name 
    */
	public function getFolderList( $path ) {
		$items = array();
		$handle = opendir($path);
		if (! $handle) {
			return $items;
		}
		while (false !== ($file = readdir($handle))) {
			if (is_dir($path . $file))
				$items[$file] = $file;
		}
		unset($items['.'], $items['..'], $items['.svn']);
		
		return $items;
	}
	
   /**
    * Render processing form && process saving data.
    */	
	public function getContent()
	{
		$html = "";
		if (Tools::isSubmit('submit'))
		{
			$this->_postValidation();

			if (!sizeof($this->_postErrors))
			{													
		        $definedConfigs = array(
		          /* general config */
		          'module_theme'      => '',
		          'order_by'      => '',
		          'limit_items'      => '',
		          'scroll_items'      => '',
		          'slide_height'      => '',
		          'slide_width'      => '',
		          'auto_play'      => '',
		          'target'      => '',
		          'des_max_chars'      => '',
		          'cre_main_size'      => '',
		          'main_img_size'      => '',
		          'main_height'      => '',
		          'main_width'      => '',
		          'limit_cols'      => '',
		          'show_desc'      => '',
		          'show_price'      => '',
		          'price_special'      => '',
		          'show_title'      => '',
		          'show_image'      => '',
		          'show_button'      => '',
		          'show_pager'      => ''
		        );
				
		        foreach( $definedConfigs as $config => $key ){
		      		Configuration::updateValue($this->name.'_'.$config, Tools::getValue($config), true);
		    	}
                
		        $html .= '<div class="conf confirm">'.$this->l('Settings updated').$this->l('Update success').'</div>';
			}
			else
			{
				foreach ($this->_postErrors AS $err)
				{
					$html .= '<div class="alert error">'.$err.'</div>';
				}
			}
			// reset current values.
			$this->_params = new LofParams( $this->name );	
		}
			
		return $html.$this->_getFormConfig();
	}
	
	/**
	 * Render Configuration From for user making settings.
	 *
	 * @return context
	 */
	private function _getFormConfig(){		
		$html = '';
		 
	    $formats = ImageType::getImagesTypes( 'products' );
	    $themes=$this->getFolderList( dirname(__FILE__)."/tmpl/" );
        $groups=$this->getFolderList( dirname(__FILE__)."/libs/groups/" );

	    ob_start();
	    include_once dirname(__FILE__).'/config/loffeatured.php'; 
	    $html .= ob_get_contents();
	    ob_end_clean(); 
		return $html;
	}
    
	/**
     * Process vadiation before saving data 
     */
	private function _postValidation()
	{
		if (!Validate::isCleanHtml(Tools::getValue('module_height')))
			$this->_postErrors[] = $this->l('The module height you entered was not allowed, sorry');
		if (!Validate::isCleanHtml(Tools::getValue('module_width')))
			$this->_postErrors[] = $this->l('The module width you entered was not allowed, sorry');
		if (!Validate::isCleanHtml(Tools::getValue('des_max_chars')) || !is_numeric(Tools::getValue('des_max_chars')))
			$this->_postErrors[] = $this->l('The description max chars you entered was not allowed, sorry');
		
		if (!Validate::isCleanHtml(Tools::getValue('main_height')) || !is_numeric(Tools::getValue('main_height')))
			$this->_postErrors[] = $this->l('The Main Image Height you entered was not allowed, sorry');
		if (!Validate::isCleanHtml(Tools::getValue('main_width')) || !is_numeric(Tools::getValue('main_width')))
			$this->_postErrors[] = $this->l('The Main Image Width you entered was not allowed, sorry');
 
                      							
	}
	
	public function getProFeature(){
		$sql = 'SELECT DISTINCT p.id_product FROM `'._DB_PREFIX_.'category_product` cp '
		 . 'LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product` '
		 . 'WHERE cp.`id_category` =1';    
		return Db::getInstance()->ExecuteS($sql);
	} 	 
        
	
   /**
    * Get value of parameter following to its name.
    * 
	* @return string is value of parameter.
	*/
	public function getParamValue($name, $default=''){
		return $this->_params->get( $name, $default );	
	}	  	  		
} 