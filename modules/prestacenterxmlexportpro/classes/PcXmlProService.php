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


class PcXmlProService extends ObjectModel
{
	public $name;
	public static $definition = array(
		'table' => 'pc_xmlpro_service',
		'primary' => 'id_pc_xmlpro_service',
		'multilang' => false,
		'fields' => array(
			'name' => array(
				'type' => self::TYPE_STRING,
				'lang' => false,
				'validate' => 'isGenericName',
				'required' => true,
				'size' => 50,
			),
		)
	);
	public function delete()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.PcXmlProFeed::$definition['table'].'` WHERE `'.$this->def['primary'].'` = '.(int)$this->id;
		$status = self::$db->execute($sql) && parent::delete();
		if ($status)
			Context::getContext()->controller->updateLastChecked(true);
		return $status;
	}
	public function add($autodate = true, $null_values = false)
	{
		$status = parent::add($autodate, $null_values);
		if ($status)
			Context::getContext()->controller->updateLastChecked(true);
		return $status;
	}
	public function update($null_values = false, $autodate = true)
	{
		$status = parent::update($null_values, $autodate);
		if ($status)
			Context::getContext()->controller->updateLastChecked(true);
		return $status;
	}
	public static function getList()
	{
		$query = new DbQuery;
		$sql = $query
			->select('`'.self::$definition['primary'].'` id')
			->select('`name`')
			->from(self::$definition['table'])
			->orderBy('id')
			->build();
		return self::$db->executeS($sql);
	}
	public static function getFeedIds($ids)
	{
		if (empty($ids))
			return array();
		$query = new DbQuery;
		$query->select('GROUP_CONCAT(`'.PcXmlProFeed::$definition['primary'].'`) ids')
			->from(PcXmlProFeed::$definition['table']);
		if (is_array($ids))
		{
			$ids = array_map('intval', $ids);
			$query->where('`'.self::$definition['primary'].'` IN ('.implode(',', $ids).')');
		}
		else
		{
			$query->where('`'.self::$definition['primary'].'` = '.(int)$ids);
		}
		return explode(',', self::$db->getValue($query));
	}
}