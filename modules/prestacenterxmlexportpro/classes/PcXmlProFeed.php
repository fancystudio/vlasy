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


class PcXmlProFeed extends ObjectModel
{
	public $id_pc_xmlpro_service;
	public $id_lang;
	public $id_currency;
	public $xml_source;
	public $allow_empty_tags;
	public $filename;
	public $filesize;
	public $created;
	public static $definition = array(
		'table' => 'pc_xmlpro_feed',
		'primary' => 'id_pc_xmlpro_feed',
		'multilang' => false,
		'fields' => array(
			'id_pc_xmlpro_service' => array(
				'type' => self::TYPE_INT,
				'lang' => false,
				'validate' => 'isUnsignedInt',
				'required' => true,
			),
			'id_lang' => array(
				'type' => self::TYPE_INT,
				'lang' => false,
				'validate' => 'isUnsignedInt',
				'required' => true,
			),
			'id_currency' => array(
				'type' => self::TYPE_INT,
				'lang' => false,
				'validate' => 'isUnsignedInt',
				'required' => true,
			),
			'xml_source' => array(
				'type' => self::TYPE_HTML,
				'lang' => false,
				'validate' => 'isString',
				'required' => true,
				'size' => 2500,
			),
			'allow_empty_tags' => array(
				'type' => self::TYPE_BOOL,
				'lang' => false,
				'validate' => 'isBool',
				'required' => true,
				'default' => 0,
			),
			'filename' => array(
				'type' => self::TYPE_STRING,
				'lang' => false,
				'validate' => 'isFileName', 
				'required' => true,
				'size' => 60,
			),
			'filesize' => array(
				'type' => self::TYPE_INT,
				'lang' => false,
				'required' => false,
				'default' => 0,
			),
			'created' => array(
				'type' => self::TYPE_DATE,
				'lang' => false,
				'validate' => 'isDateFormat',
				'required' => false,
				'default' => '0000-00-00 00:00:00',
			),
		)
	);
	public function update($null_values = false, $autodate = true)
	{
		Hook::exec('actionObjectUpdateBefore', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'UpdateBefore', array('object' => $this));
		$this->clearCache();
		Context::getContext()->controller->module->validateXml($this->xml_source);
		try
		{
			if (!ObjectModel::$db->update($this->def['table'], $this->getFields(), '`'.pSQL($this->def['primary']).'` = '.(int)$this->id, 0, $null_values))
				throw new RuntimeException;
		} catch (Exception $e)
		{
			if (self::$db->getNumberError() == 1062)
			{
				throw new RuntimeException(sprintf($this->l('The name of the specified XML file (%s) already exists.'), $this->filename));
			}
			else
			{
				throw $e;
			}
		}
		Hook::exec('actionObjectUpdateAfter', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'UpdateAfter', array('object' => $this));
		Context::getContext()->controller->updateLastChecked(true);
		return true;
	}
	public function add($autodate = true, $null_values = false)
	{
		Hook::exec('actionObjectAddBefore', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'AddBefore', array('object' => $this));
		Context::getContext()->controller->module->validateXml($this->xml_source);
		try
		{
			if (!self::$db->insert($this->def['table'], $this->getFields(), $null_values))
				throw new RuntimeException;
		} catch (Exception $e)
		{
			if (self::$db->getNumberError() == 1062)
			{
				throw new RuntimeException(sprintf($this->l('The name of the specified XML file (%s) already exists.'), $this->filename));
			}
			else
			{
				throw $e;
			}
		}
		$this->id = ObjectModel::$db->Insert_ID();
		Hook::exec('actionObjectAddAfter', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'AddAfter', array('object' => $this));
		Context::getContext()->controller->updateLastChecked(true);
		return true;
	}
	public function delete()
	{
		$status = parent::delete();
		if ($status)
			Context::getContext()->controller->updateLastChecked(true);
		return $status;
	}
	public function l($string)
	{
		return Context::getContext()->controller->module->l($string);
	}
	public static function getAll()
	{
		$sql = 'SELECT `'.self::$definition['primary'].'` id_feed, ';
		$sql .= ' `'.PcXmlProService::$definition['primary'].'` id_service, (`id_lang` > 0 AND `id_currency` > 0)*1 as is_exportable ';
		$sql .= ' FROM `'._DB_PREFIX_.self::$definition['table'].'`';
		return Db::getInstance()->executeS($sql);
	}
}