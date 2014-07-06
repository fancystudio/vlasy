<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class MainSlider extends Module
{
  public function __construct()
  {
    $this->name = 'mainslider';
    $this->tab = 'front_office_features';
    $this->version = '1.0';
    $this->author = 'Fancystudio';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
    //$this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Royal slider');
    $this->description = $this->l('Hlavny Royal slider');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
    if (!Configuration::get('MYMODULE_NAME'))      
      $this->warning = $this->l('No name provided');
  }
	public function install()
	{
	  if (Shop::isFeatureActive())
    	Shop::setContext(Shop::CONTEXT_ALL);
    	return parent::install() &&
		    $this->registerHook('revSlider') &&
		    Configuration::updateValue('MYMODULE_NAME', 'my friend');
	}
	public function uninstall()
	{
	  if (!parent::uninstall())
	    return false;
	  return true;
	}
		public function hookDisplayRevSlider()
	{
		if($this->context->controller->php_self == "index"){
			$this->context->controller->addCSS($this->_path.'css/royalslider.css', 'all');
			$this->context->controller->addJS($this->_path.'js/jquery.royalslider.min.js');
			return $this->display(__FILE__, 'mainslider.tpl');
		}
		return;
	  	
	}  
}