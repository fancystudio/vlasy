<?php
/**
* 2012-2014 PrestaCS, PrestaCenter - Anatoret plus s.r.o.
*
* PrestaCenter XML Export Pro
*
* Module PrestaCenter XML Export Pro – version for PrestaShop 1.5 and 1.6
* Modul PrestaCenter XML Export Pro – verze pro PrestaShop 1.5 a 1.6
*
* PrestaCenter - modules and customization for PrestaShop
* PrestaCS - moduly, česká lokalizace a úpravy pro PrestaShop
* http://www.prestacs.cz
*
* @author    PrestaCenter <info@prestacenter.com>
* @category  others
* @package   prestacenterxmlexportpro
* @copyright 2012-2014 PrestaCenter - Anatoret plus s.r.o.
* @license   see file licence-prestacenter.html
*/

/**
 * @since 1.5.0
 * @version 1.2.4.2 (2014-07-07)
*/


class PcXmlProController extends ModuleAdminController
{
	const DEFAULT_EXPORT_LIMIT = 500;
	const DEFAULT_IMAGE_SIZE = 'large';
	protected $fields_value_override = array();
	protected $fields_options = array();
	static protected $db;
	protected $currentHelper;
	protected $smartyNS = 'xmlexport';
	protected $isCron = false;
	protected $cronToken = '';
	protected $cronActions = array('export');
	public $isVersion16;
	public function __construct()
	{
		require_once dirname(__FILE__).'/../../classes/PrestaCenterAutoload.php';
		PrestaCenterAutoload::add(dirname(__FILE__).'/../../');
		PrestaCenterAutoload::register();
		$this->table = PcXmlProService::$definition['table'];
		$this->identifier = PcXmlProService::$definition['primary'];
		$this->_defaultOrderBy = PcXmlProService::$definition['primary'];
		$this->className = 'PcXmlProService';
		$this->lang = false;
		$this->multishop_context = Shop::CONTEXT_ALL;
		self::$db = Db::getInstance();
		parent::__construct();
		if (!Configuration::hasKey(PrestaCenterXmlExportPro::CFG_PREFIX.'LAST_CHECKED'))
		{
			$this->updateLastChecked(true);
		}
		$this->isVersion16 = (version_compare(_PS_VERSION_, '1.6') >= 0);
		$this->bootstrap = true;
		$this->tpl_folder = ($this->isVersion16 ? 'xml_export16/' : 'xml_export/');
	}
	public function checkCronToken()
	{
		if (!Tools::getValue('token') && ($cronToken = Tools::getValue('cronToken')))
		{
			if (Tools::substr(_COOKIE_KEY_, 4, 10) === $cronToken)
			{
				if (!in_array(Tools::strtolower(Tools::substr(Tools::getValue('action'), 4)), $this->cronActions))
				{
					die('Access denied.');
				}
				if (!$this->context->cookie)
				{
					$cookie_lifetime = time() + (max((int)Configuration::get('PS_COOKIE_LIFETIME_BO'), 1) * 3600);
					$this->context->cookie = new Cookie('psAdmin', '', $cookie_lifetime);
				}
				$this->context->cookie->disallowWriting();
				$this->context->cookie->id_employee = _PS_ADMIN_PROFILE_;
				$this->context->employee = new Employee($this->context->cookie->id_employee);
				$this->context->cookie->id_lang = (int)$this->context->employee->id_lang;
				$this->context->cookie->passwd = $this->context->employee->passwd;
				$this->token = Tools::getAdminToken($this->controller_name.(int)$this->id.(int)$this->context->employee->id);
				$this->isCron = true;
				$this->cronToken = $cronToken;
				return true;
			}
		}
	}
	public function checkAccess()
	{
		return ($this->isCron && $this->token ? true : parent::checkAccess());
	}
	public function initOverrides()
	{
		static $done = false;
		if (!$done)
		{
			PcXmlLink::_init();
			PcXmlDispatcher::_init();
			$done = true;
		}
	}
	public function init()
	{
		$this->checkCronToken();
		if (!$this->isCron)
		{
		$this->loadMessages();
		}
		$table = PcXmlProFeed::$definition['table'];
		if (Tools::getIsset('add'.$table) || Tools::getIsset('update'.$table) || Tools::getIsset('delete'.$table)
				|| Tools::isSubmit('submitAdd'.$table))
		{
			$this->useXmlFeed();
		}
		elseif (Tools::getIsset('clone'.$table))
		{
			$this->useXmlFeed();
		}
		else
			$this->useXmlService();
		parent::init();
		if ($this->isCron)
		{
			$this->action = Tools::getValue('action');
		}
		elseif (Tools::strtolower(Tools::getValue('action')) === 'bulkexport' && Tools::getIsset('next'))
		{
			$this->action = 'bulkexport';
		}
		elseif (Tools::getIsset('clone'.$table))
		{
			$this->ajax = false;
			$this->action = 'clone';
			$this->display = 'add';
		}
		$this->context->smarty->assign($this->smartyNS, array());
		$this->initOverrides();
	}
	public function initProcess()
	{
		parent::initProcess();
		if (Tools::substr($this->action, 0, 4) === 'bulk')
		{
			$this->useXmlService();
			if (!Tools::getIsset('next'))
			{
				$this->updateLastChecked();
			}
			$this->boxes = $this->getLastChecked('feeds');
		}
		if (!$this->ajax && !$this->isCron && !in_array($this->display, array('edit', 'add', 'view')))
		{
			$this->initOptions();
		}
	}
	public function ajaxPreProcess()
	{
		$this->context->smarty->assign(array(
			'currentIndex' => self::$currentIndex,
			'table' => $this->table,
			'identifier' => $this->identifier,
		));
	}
	public function postProcess()
	{
		if ($this->ajax && Tools::getValue('action') === 'details')
			$this->useXmlFeed();
		parent::postProcess();
	}
	protected function getLastChecked($key = null, $renew = false)
	{
		static $data;
		if (!$data || $renew)
		{
			if (Configuration::hasKey(PrestaCenterXmlExportPro::CFG_PREFIX.'LAST_CHECKED'))
				$data = unserialize(Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'LAST_CHECKED'));
			else
				$data = array('feeds' => array(), 'services' => array(), 'tree' => array());
		}
		if ($key)
			return isset($data[$key]) ? $data[$key] : array();
		else
			return $data;
	}
	public function updateLastChecked($updateFromDb = false)
	{
		$services = array_flip(Tools::getValue(PcXmlProService::$definition['table'].'Box', array()));
		$feeds = array_flip(Tools::getValue(PcXmlProFeed::$definition['table'].'Box', array()));
		if ($updateFromDb || empty($services) && empty($feeds) && Tools::substr($this->action, 0, 4) === 'bulk' && $this->action !== 'bulkremember')
		{
			$services = $this->getLastChecked('services');
			$feeds = $this->getLastChecked('feeds');
		}
		$lastChecked = array('feeds' => array(), 'services' => array(), 'tree' => array());
		foreach (PcXmlProFeed::getAll() as $row)
		{
			if (!isset($lastChecked['tree'][$row['id_service']]))
			{
				$lastChecked['tree'][$row['id_service']] = array();
				$lastChecked[$row['id_service']] = array();
			}
			if ($row['is_exportable'])
			{
				$lastChecked['tree'][$row['id_service']][] = $row['id_feed'];
				if (isset($services[$row['id_service']]))
				{
					$lastChecked['services'][$row['id_service']] = $row['id_service'];
					$lastChecked['feeds'][$row['id_feed']] = $row['id_feed'];
					$lastChecked[$row['id_service']][$row['id_feed']] = $row['id_feed'];
				}
				elseif (isset($feeds[$row['id_feed']]))
				{
					$lastChecked['feeds'][$row['id_feed']] = $row['id_feed'];
					$lastChecked[$row['id_service']][$row['id_feed']] = $row['id_feed'];
				}
			}
		}
		Configuration::updateGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'LAST_CHECKED', serialize($lastChecked));
		$this->getLastChecked(null, true);
	}
	protected function loadMessages()
	{
		static $done = false;
		if ($done)
		{
			return;
		}
		$key = PrestaCenterXmlExportPro::CFG_PREFIX.'MSG'.$this->context->cookie->id_employee;
		if (Configuration::hasKey($key))
		{
			$data = (array)unserialize(Configuration::getGlobalValue($key));
			foreach (array('errors', 'warnings', 'confirmations', 'informations') as $type)
			{
				if (isset($data[$type]))
				{
					$this->$type = array_merge($this->$type, (array)$data[$type]);
				}
			}
			Configuration::updateGlobalValue($key, '');
		}
		$done = true;
	}
	protected function saveMessages()
	{
		$key = PrestaCenterXmlExportPro::CFG_PREFIX.'MSG'.$this->context->cookie->id_employee;
		$data = array();
		foreach (array('errors', 'warnings', 'confirmations', 'informations') as $type)
		{
			$data[$type] = $this->$type;
		}
		Configuration::updateGlobalValue($key, serialize($data));
	}
	protected function redirect()
	{
		$this->saveMessages();
		parent::redirect();
	}
	public function setMedia()
	{
		parent::setMedia();
		$this->addJS($this->module->getModuleUrl().'views/js/xmlexport.js');
	}
	public function setHelperDisplay(Helper $helper)
	{
		$this->currentHelper = $helper;
		parent::setHelperDisplay($helper);
	}
	public function getFieldsValue($obj)
	{
		parent::getFieldsValue($obj);
		$this->fields_value = array_merge($this->fields_value, $this->fields_value_override);
		return $this->fields_value;
	}
	public function initToolbarTitle()
	{
		$bread_extended = $this->breadcrumbs;
		switch ($this->table)
		{
			case PcXmlProService::$definition['table']:
				if ($this->display === 'edit')
					$bread_extended[] = $this->l('Edit service name');
				elseif ($this->display === 'add')
					$bread_extended[] = $this->l('New service');
				break;
			case PcXmlProFeed::$definition['table']:
				if ($this->display === 'edit')
					$bread_extended[] = $this->l('Edit feed');
				elseif ($this->display === 'add')
					$bread_extended[] = $this->l('Add a new feed');
				break;
		}
		$this->toolbar_title = $bread_extended;
	}
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'add':
			case 'edit':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				if ($this->table === PcXmlProFeed::$definition['table'])
			{
					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save and stay in this form'),
						'force_desc' => true,
					);
				}
				$back = self::$currentIndex.'&token='.$this->token;
				$this->toolbar_btn['back'] = array(
					'href' => $back,
					'desc' => $this->l('Back to the list of services')
				);
				break;
			case 'options':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				break;
			default:
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
					'desc' => $this->l('Add a new service'),
				);
				$this->toolbar_btn['newFeed'] = array(
					'href' => self::$currentIndex.'&add'.PcXmlProFeed::$definition['table'].'&token='.$this->token,
					'desc' => $this->l('Add a new feed'),
					'imgclass' => 'new',
				);
		}
	}
	public function initPageHeaderToolbar()
	{
		$this->show_toolbar_options = true;
		if (!$this->action && !$this->display)
		{
			$this->page_header_toolbar_btn = $this->toolbar_btn;
		}
		parent::initPageHeaderToolbar();
	}
	protected function useXmlService()
	{
		$primaryKey = PcXmlProService::$definition['primary'];
		$this->table = PcXmlProService::$definition['table'];
		$this->identifier = $primaryKey;
		$this->_defaultOrderBy = $primaryKey;
		$this->className = 'PcXmlProService';
		$this->fields_list = array(
			$primaryKey => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25,
			),
			'name' => array(
				'title' => $this->l('Service name'),
				'suffix' => '<i class="XmlExportInfo"></i>',
			),
			'count_values' => array(
				'title' => $this->l('Feeds'),
				'width' => 60,
				'align' => 'center',
				'havingFilter' => true,
			),
		);
		$this->bulk_actions = array(
			'export' => array(
				'text' => $this->l('Create XML'),
			),
			'remember' => array(
				'text' => $this->l('Save selected feeds'),
			),
		);
		$fields = PcXmlProService::$definition['fields'];
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Price comparison services:'),
				'image' => $this->module->getModuleUrl().'img/feed_link.png',
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Service name:'),
					'name' => 'name',
					'size' => 30,
					'maxlength' => $fields['name']['size'],
					'required' => $fields['name']['required'],
					'hint' => $this->l('You can use only letters, numbers and hyphens.')
				),
			),
		);
		if ($this->isVersion16)
		{
			$this->fields_form['buttons'] = array(
				'save' => array(
					'title' => $this->l('Save'),
					'type' => 'submit',
					'icon' => 'process-icon-save',
					'class' => 'pull-right',
				),
			);
			$this->fields_form['cancel'] = array(
				'title' => $this->l('Back to the list of services'),
				'icon' => 'process-icon-back',
				'remove_onclick' => true,
			);
		}
		else
		{
			$this->fields_form['submit'] = array(
				'title' => $this->l('Save'),
				'class' => 'button'
			);
		}
	}
	protected function useXmlFeed()
	{
		$primaryKey = PcXmlProFeed::$definition['primary'];
		$this->table = PcXmlProFeed::$definition['table'];
		$this->identifier = $primaryKey;
		$this->_defaultOrderBy = 'name';
		$this->className = 'PcXmlProFeed';
		$this->fields_list = array(
			'filename' => array(
				'title' => $this->l('XML file'),
				'width' => '20%',
			),
			'created' => array(
				'title' => $this->l('Created / updated'),
				'width' => '20%',
			),
			'filesize' => array(
				'title' => $this->l('Size'),
				'remove_onclick' => true,
				'width' => '15%',
			),
			'lang' => array(
				'title' => $this->l('Language'),
				'remove_onclick' => true,
				'width' => '15%',
			),
			'currency' => array(
				'title' => $this->l('Currency'),
				'remove_onclick' => true,
				'width' => '15%',
			),
		);
		$this->bulk_actions = array();
		$services = PcXmlProService::getList();
		$fields = PcXmlProFeed::$definition['fields'];
		$tplDataObject = $this->context->smarty->createData();
		$tplDataObject->assign('module', $this->module->name);
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('XML feed'),
				'image' => $this->module->getModuleUrl().'img/feed.png',
			),
			'description' => $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/feedLegend.tpl', $tplDataObject),
			'input' => array(
				array(
					'name' => PcXmlProService::$definition['primary'],
					'label' => $this->l('Service'),
					'type' => 'select',
					'required' => true,
					'options' => array(
						'query' => $services,
						'id' => 'id',
						'name' => 'name',
					),
					'desc' => $this->l('Select a service for this feed.'),
				),
				array(
					'name' => 'filename',
					'label' => $this->l('Name of the XML file'),
					'type' => 'text',
					'required' => true,
					'size' => 35,
					'maxlength' => $fields['filename']['size'],
				),
				array(
					'name' => 'id_lang',
					'label' => $this->l('Language of the feed'),
					'type' => 'select',
					'required' => true,
					'options' => array(
						'query' => Language::getLanguages(false ),
						'id' => 'id_lang',
						'name' => 'name'
					)
				),
				array(
					'name' => 'id_currency',
					'label' => $this->l('Currency of the feed'),
					'type' => 'select',
					'required' => true,
					'options' => array(
						'query' => Currency::getCurrencies( false, false),
						'id' => 'id_currency',
						'name' => 'name'
					)
				),
				array(
					'name' => 'xml_source',
					'label' => $this->l('XML template'),
					'type' => 'textarea',
					'required' => true,
					'maxlength' => $fields['xml_source']['size'],
					'rows' => 15,
					'cols' => 80,
				),
				array(
					'name' => 'allow_empty_tags',
					'label' => $this->l('Generate empty elements?'),
					'type' => ($this->isVersion16 ? 'switch' : 'radio'),
					'required' => true,
					'default' => 0,
					'desc' => $this->l('Create an empty tag in the feed if the requested data is missing?'),
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'add',
							'value' => 1,
							'label' => $this->l('Create')
						),
						array(
							'id' => 'dont_add',
							'value' => 0,
							'label' => $this->l('Skip')
						)
					)
				),
			),
		);
		if ($this->isVersion16)
		{
			$this->fields_form['buttons'] = array(
				'save' => array(
					'title' => $this->l('Save'),
					'type' => 'submit',
					'icon' => 'process-icon-save',
					'class' => 'pull-right',
				),
				'save-and-stay' => array(
					'title' => $this->l('Save and stay in this form'),
					'type' => 'submit',
					'class' => 'pull-right',
					'name' => 'submitAdd'.$this->table.'AndStay',
					'icon' => 'process-icon-save-and-stay',
				),
			);
			$this->fields_form['cancel'] = array(
				'title' => $this->l('Back to the list of services'),
				'icon' => 'process-icon-back',
				'remove_onclick' => true,
			);
		}
		else
		{
			$this->fields_form['submit'] = array(
				'title' => $this->l('Save feed'),
				'class' => 'button',
			);
		}
	}
	public function initOptions()
	{
		$descr = '';
		$currentImgType = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE');
		if (!$currentImgType)
		{
			$currentImgType = $this->setDefaultImgType(self::DEFAULT_IMAGE_SIZE);
			$descr = sprintf($this->l('You have not selected the image type yet, the default type will be used (%s).'), $currentImgType);
		}
		if (!Configuration::hasKey(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT'))
			Configuration::updateGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT', 1);
		$imageTypes = array();
		foreach (ImageType::getImagesTypes('products') as $row)
		{
			$imageTypes[$row['width']] = array(
				'name' => $row['name'].' ('.$row['width'].' &times; '.$row['height'].' px)',
				'value' => $row['name'],
			);
		}
		ksort($imageTypes);
		$shop = ($this->context->shop->isDefaultShop()
			? $this->context->shop
			: new Shop(Configuration::get('PS_SHOP_DEFAULT')));
		$cronUrl = 'http://'.$shop->domain.$shop->physical_uri.basename(_PS_ADMIN_DIR_).'/'
			.'index.php?controller='.$this->controller_name.'&action=cronExport&cronToken='.Tools::substr(_COOKIE_KEY_, 4, 10);
		$feedIds = implode(',', $this->getLastChecked('feeds'));
		if ($feedIds)
		{
			$cronInfo = $this->l('These feeds will be exported:');
			$sql = 'SELECT f.`filename` 
				FROM `'._DB_PREFIX_.PcXmlProFeed::$definition['table'].'` f '
				.' WHERE `'.PcXmlProFeed::$definition['primary'].'` IN ('.$feedIds.')
					AND f.`id_lang` > 0 AND f.`id_currency` > 0
				ORDER BY `filename`';
			foreach (Db::getInstance()->executeS($sql) as $row)
			{
				$url = htmlspecialchars('http://'.$shop->domain.$shop->physical_uri.'xml/'.$row['filename'], ENT_COMPAT, 'UTF-8');
				$cronInfo .= '<br><a href="'.$url.'" target="_blank">'.$url.'</a>';
			}
		}
		else
		{
			$cronInfo = $this->l('The module remembers which feeds were exported last time. The same feeds will be then exported with CRON. You have no feed selected at the moment.');
		}
		unset($shop);
		$this->fields_options = array(
			'export' => array (
				'title' => $this->l('Settings for data export'),
				'image' => _MODULE_DIR_.$this->module->name.'/img/wrench.png',
				'fields' =>	array(
					PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_LIMIT' => array(
						'title' => $this->l('Export batch processing - how many products are inserted into the feed at once.'),
						'desc' => $this->l('If an export process did not finish successfully (i.e. many products in the e-shop or low time limit for running the script), set it to a lower value. It is recommended that the maximum number of script redirections should be 20.'),
						'cast' => 'intval',
						'type' => 'text',
						'defaultValue' => self::DEFAULT_EXPORT_LIMIT,
					),
					PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE' => array(
						'title' => $this->l('Export image size'),
						'desc' => $descr,
						'value' => $currentImgType,
						'type' => 'select',
						'identifier' => 'value',
						'list' => $imageTypes,
					),
					PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT' => array(
						'title' => $this->l('Export products with a default inactive category'),
						'value' => Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT'),
						'type' => 'bool',
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'icon' => 'process-icon-save',
				),
			),
			'cron' => array(
				'title' => $this->l('Automatic start of export'),
				'image' => _MODULE_DIR_.$this->module->name.'/img/time.png',
				'info' => sprintf($this->l('Use this CRON URL: %s'), htmlspecialchars($cronUrl, ENT_COMPAT, 'UTF-8'))
					.'<br>'.$this->l('Please note: Earlier versions of this module used a different URL. It is still supported, but it may be blocked by certain server security settings. We recommend using the new URL.')
					.'<br><br>'.$cronInfo,
			),
		);
	}
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('details');
		$this->tpl_list_vars[$this->smartyNS]['context'] = 'service';
		$this->tpl_list_vars[$this->smartyNS]['onclick'] = array(
			'type' => 'onclick',
			'name' => 'ajaxDetails',
		);
		$this->tpl_list_vars[$this->smartyNS]['cbx'] = array(
			'dependent' => true,
		);
		$tree = $this->getLastChecked();
		$tmp = $tree['tree'];
		unset($tree['feeds'], $tree['services'], $tree['tree']);
		$tree = array_map('count', $tree);
		$tree['all'] = $tmp;
		$this->tpl_list_vars[$this->smartyNS]['check'] = array(
			'ids' => Tools::jsonEncode($this->getLastChecked('services')),
			'table' => Tools::jsonEncode(PcXmlProService::$definition['table']),
			'parentId' => 0,
			'tree' => Tools::jsonEncode($tree),
			'text' => Tools::jsonEncode($this->l('(Selected feeds: %u)')), 
			'textAll' => Tools::jsonEncode($this->l('(All feeds selected: %u)')), 
			'class' => Tools::jsonEncode('XmlExportInfo'), 
		);
		$this->explicitSelect = true;
		$this->_select = 'a.`name`, COUNT(`'.PcXmlProFeed::$definition['primary'].'`) as count_values';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.PcXmlProFeed::$definition['table'].'` f';
		$this->_join .= ' USING (`'.PcXmlProService::$definition['primary'].'`)';
		$this->_group = 'GROUP BY a.`'.PcXmlProService::$definition['primary'].'`';
		return parent::renderList();
	}
	public function ajaxProcessDetails()
	{
		try
		{
			if (($id = Tools::getValue('id')))
			{
				$this->addRowAction('edit');
				$this->addRowAction('clone');
				$this->addRowAction('delete');
				$this->display = 'list';
				$primaryKey = PcXmlProFeed::$definition['primary'];
				$query = new DbQuery;
				$query->select('SQL_CALC_FOUND_ROWS
						f.`'.$primaryKey.'`, f.`filename`,
						c.`iso_code` as currency, l.`name` as lang, l.`active` as lang_active')
					->from(PcXmlProFeed::$definition['table'], 'f')
					->leftJoin('currency', 'c', 'f.`id_currency` = c.`id_currency`')
					->leftJoin('lang', 'l', 'f.`id_lang` = l.`id_lang`')
					->where('f.`'.PcXmlProService::$definition['primary'].'` = '.(int)$id)
					->orderBy('`'.$primaryKey.'`, `filename`, `lang`');
				$this->_list = self::$db->executeS($query);
				$disabledCbx = array(); 
				foreach ($this->_list as &$item)
				{
					$path = $this->module->getExportDir().$item['filename'];
					if (file_exists($path))
					{
						$item['filesize'] = $this->module->readableFileSize(filesize($path));
						$item['created'] = date('Y-m-d H:i:s', filemtime($path));
					}
					else
					{
						$item['remove_onclick'] = true;
						$item['filesize'] = $this->module->readableFileSize(0);
						$item['created'] = '0000-00-00 00:00:00';
					}
					$disabledCbx[$item[$primaryKey]] = false;
					if (!$item['currency'])
					{
						$item['currency'] = $this->l('undefined');
						$disabledCbx[$item[$primaryKey]] = true;
					}
					if (!$item['lang'])
					{
						$item['lang'] = $this->l('undefined');
						$disabledCbx[$item[$primaryKey]] = true;
					}
					elseif (!$item['lang_active'])
					{
						$item['lang'] .= ' - '.$this->l('inactive');
					}
				}
				unset($item);
				$this->list_no_link = false;
				$this->shopLinkType = '';
				$this->toolbar_scroll = false;
				$this->list_simple_header = true; 
				$this->list_id = $this->table; 
				$this->show_toolbar = false;
				$this->context->smarty->assign($this->smartyNS, array(
					'onclick' => array(
						'type' => 'link',
						'name' => 'xmlLink',
						'key' => 'filename',
						'data' => array(__PS_BASE_URI__.'xml/'),
					),
					'cbx' => array(
						'show' => true, 
						'dependent' => true, 
						'disabled' => $disabledCbx, 
					),
					'check' => array(
						'ids' => Tools::jsonEncode($this->getLastChecked((int)$id)),
						'table' => Tools::jsonEncode(PcXmlProFeed::$definition['table']),
						'parentId' => Tools::jsonEncode((int)$id),
					),
					'ajax' => true,
					'context' => 'feed',
				));
				$this->setHelperDisplay($helper = new HelperList);
				$helper->title = ''; 
				$content = $helper->generateList($this->_list, $this->fields_list);
				die (Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content)));
			}
			else
				die('id missing');
		} catch(Exception $e)
		{
			die((string)$e);
		}
	}
	public function renderForm()
	{
		$this->show_form_cancel_button = true;
		if (($this->display === 'edit' || $this->action === 'clone')
			&& $this->table === PcXmlProFeed::$definition['table'])
		{
			if (version_compare(_PS_VERSION_, '1.5.4', 'ge') && version_compare(_PS_VERSION_, '1.5.5', 'lt'))
			{
				$this->fields_value_override['xml_source'] = htmlspecialchars($this->getFieldValue($this->object, 'xml_source'), ENT_QUOTES, 'UTF-8', true);
			}
		}
		if ($this->action === 'clone')
		{
			parent::renderForm();
			$this->object->id = null;
			$this->currentHelper->id = null;
			$this->currentHelper->fields_value = $this->getFieldsValue($this->object);
			return $this->currentHelper->generateForm($this->fields_form);
		}
		else
		{
			return parent::renderForm();
		}
	}
	public function displayCloneLink($token = null, $id, $name = null)
	{
		$tpl = $this->currentHelper->createTemplate('list_action_clone.tpl');
		$tpl->assign(array(
			'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&clone'.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => $this->l('Clone'),
			'id' => $id
		));
		return $tpl->fetch();
	}
	public function processUpdateOptions()
	{
		parent::processUpdateOptions();
		$this->fields_options['export']['fields'][PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_LIMIT']['value'] = (int)Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_LIMIT');
		$this->fields_options['export']['fields'][PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE']['value'] = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE');
		unset($this->fields_options['export']['fields'][PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE']['desc']);
		$this->fields_options['export']['fields'][PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT']['value'] = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT');
	}
	public function processSave()
	{
		try
		{
			$return = parent::processSave();
			return $return;
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			$this->display = 'edit'; 
			$this->redirect_after = '';
			return false;
		}
	}
	public function processDelete()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if (isset($object->noZeroObject) && count(call_user_func(array($this->className, $object->noZeroObject))) <= 1)
			{
				$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
			}
			elseif (array_key_exists('delete', $this->list_skip_actions) && in_array($object->id, $this->list_skip_actions['delete']))
			{
					$this->errors[] = Tools::displayError('You cannot delete this item.');
			}
			else
			{
				if ($this->deleted)
				{
					if (!empty($this->fieldImageSettings))
					{
						if (!$object->deleteImage())
						{
							$this->errors[] = Tools::displayError('Unable to delete associated images.');
						}
					}
					$object->deleted = 1;
					if ($object->update())
					{
						$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
					}
					else
					{
						$this->errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				elseif ($object->delete())
				{
					$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
				}
				else
				{
					$this->errors[] = Tools::displayError('An error occurred during deletion.');
				}
			}
		}
		else
		{
			$this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
		}
		return $object;
	}
	public function processBulkRemember()
	{
		$this->redirect_after = self::$currentIndex.'&token='.$this->token;
		$this->confirmations[] = $this->l('Update successful');
		return true;
	}
	public function processBulkExport()
	{
		$settings = array();
		$key = PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT'.$this->context->cookie->id_employee;
		$limit = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_LIMIT');
		$imageType = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE');
		if (!empty($this->boxes))
		{
			$settings = array(
				'feedIds' => $this->boxes,
				'startId' => 0,
				'limit' => ($limit ? $limit : self::DEFAULT_EXPORT_LIMIT),
				'shopIds' => null,
				'imgType' => ($imageType ? $imageType : $this->setDefaultImgType(self::DEFAULT_IMAGE_SIZE)),
			);
		}
		elseif (Tools::getIsset('next') && Configuration::hasKey($key))
		{
			$settings = unserialize(Configuration::getGlobalValue($key));
			if (!isset($settings['feedIds']) || empty($settings['feedIds']) || !isset($settings['startId']) || !isset($settings['limit']))
			{
				$settings = array();
				Configuration::deleteByName($key);
			}
		}
		if (empty($settings))
		{
			$this->errors[] = $this->l('You have not selected any feeds or services, XML files cannot be created.');
			return false;
		}
		try
		{
			$this->module->createExportFiles($settings);
			$exportInfo = $this->module->getExportInfo();
			if ($exportInfo['isLastRound'])
			{
				Configuration::deleteByName($key);
			}
			else
			{
				$exportInfo['startId'] = $exportInfo['nextStartId'];
				Configuration::updateGlobalValue($key, serialize($exportInfo));
				$this->redirect_after = self::$currentIndex.'&token='.$this->token.'&action='.$this->action.'&next='.$exportInfo['startId'];
				return true;
			}
		} catch (Exception $e)
		{
			$this->errors[] = sprintf($this->l('Failed to create XML files (error description: %s). XML feeds have been restored to the previous state.'), $e->getMessage());
			return false;
		}
		$this->warnings = $this->module->getErrors();
		$this->confirmations[] = $this->l('The selected XML feeds have been successfully created / updated.');
		$this->redirect_after = self::$currentIndex.'&token='.$this->token;
		return true;
	}
	public function processCronExport($legacyUrlToken = '')
	{
		if ($legacyUrlToken)
		{
			$this->cronToken = $legacyUrlToken;
		}
		if ($this->isCron !== true && !$this->cronToken)
		{
			die('Access denied.');
		}
		$this->initOverrides();
		$key = PrestaCenterXmlExportPro::CFG_PREFIX.'CRON';
		$feedIds = implode(',', $this->getLastChecked('feeds'));
		$settings = array();
		$logFile = @fopen($this->module->getExportDir().'cron_log.txt', 'a');
		@fwrite($logFile, '['.date('d.m.Y H:i:s', time())."] - start\n");
		if (Configuration::hasKey($key))
		{
			$settings = unserialize(Configuration::getGlobalValue($key));
			if (!isset($settings['feedIds']) || empty($settings['feedIds']) || !isset($settings['startId']) || !isset($settings['limit']))
			{
				$settings = array();
				Configuration::deleteByName($key);
			}
		}
		elseif (!empty($feedIds))
		{
			$limit = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_LIMIT');
			$imageType = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE');
			$settings = array(
				'feedIds' => explode(',', $feedIds),
				'startId' => 0,
				'limit' => ($limit ? $limit : self::DEFAULT_EXPORT_LIMIT),
				'shopIds' => null,
				'imgType' => ($imageType ? $imageType : $this->setDefaultImgType(self::DEFAULT_IMAGE_SIZE)),
			);
		}
		if (empty($settings))
		{
			@fwrite($logFile, $this->l('There is no defined feeds for export, cannot create XML files.')."\n\n");
			@fclose($logFile);
			die;
		}
		try
		{
			$files = $this->module->createExportFiles($settings);
			$exportInfo = $this->module->getExportInfo();
			$fileList = '';
			foreach ($files as $feed)
			{
				$fileList .= $feed['filename'].', ';
			}
			@fwrite($logFile, $this->l('Exported XML feeds:').' '.trim($fileList, ', ')."\n");
			@fwrite($logFile, sprintf($this->l('Count of exported items: %1$s, from ID: %2$d'), $exportInfo['numWritten'], $exportInfo['startId'])."\n");
			if ($exportInfo['isLastRound'])
			{
				Configuration::deleteByName($key);
			}
			else
			{
				$exportInfo['startId'] = $exportInfo['nextStartId'];
				Configuration::updateGlobalValue($key, serialize($exportInfo));
				@fwrite($logFile, '['.date('d.m.Y H:i:s', time())."] - redirect\n");
				@fclose($logFile);
				$shop = ($this->context->shop->isDefaultShop()
					? $this->context->shop
					: new Shop(Configuration::get('PS_SHOP_DEFAULT')));
				$redirect = 'http://'.$shop->domain.$shop->physical_uri.basename(_PS_ADMIN_DIR_).'/'
					.'index.php?controller='.$this->controller_name.'&action=cronExport'
					.'&cronToken='.$this->cronToken.'&next='.$exportInfo['startId'];
				Tools::redirectAdmin($redirect);
				die;
			}
		} catch (Exception $e)
		{
			@fwrite($logFile, sprintf('Selhalo vytvoření XML souborů (popis chyby: %s). XML feedy byly obnoveny do stavu před exportem.', $e->getMessage())."\n\n");
			@fclose($logFile);
			die;
		}
		if ($this->module->getErrors())
		{
			@fwrite($logFile, implode("\n\t", $this->module->getErrors())."\n");
		}
		@fwrite($logFile, $this->l('Selected XML feeds have been successfully created / updated.')."\n");
		@fwrite($logFile, '['.date('d.m.Y H:i:s', time())."] - end\n\n");
		@fclose($logFile);
		die;
	}
	protected function _childValidation()
	{
		if ($this->identifier === PcXmlProFeed::$definition['primary'])
		{
			$currencyExists = self::$db->getValue('SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'currency`
				WHERE `id_currency` = '.(int)Tools::getValue('id_currency', 0));
			$languageExists = self::$db->getValue('SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'lang`
				WHERE `id_lang` = '.(int)Tools::getValue('id_lang', 0));
			if (!$currencyExists)
				$this->errors[] = $this->l('You have selected the currency that is not in the database.');
			if (!$languageExists)
				$this->errors[] = $this->l('You have selected the language that is not in the database.');
		}
	}
	protected function setDefaultImgType($size)
	{
		if (($type = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE')))
		{
			return $type;
		}
		$type = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'image_type`
			WHERE (`name` LIKE "'.$size.'%") AND (`products` = 1)');
		Configuration::updateGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'IMAGE_TYPE', $type);
		return $type;
	}
}