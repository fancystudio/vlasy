<?php

/**
  *
  * @copyright  (c)2012 Ireneusz Kierkowski
  * @package    ShipToPay PrestaShop Module   
  * @version    1.01
  * @date       17-12-2012
  * 
  */

abstract class Module extends ModuleCore
{
	public static function getPaymentModules()
	{
        global $cookie;
       
		$context = Context::getContext();
		if (isset($context->cart))
			$billing = new Address((int)$context->cart->id_address_invoice);

		$frontend = true;
		$groups = array();
		if (isset($context->employee))
			$frontend = false;
		elseif (isset($context->customer))
		{
			$groups = $context->customer->getGroups();
			if (empty($groups))
				$groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
		}

		$hookPayment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hookPayment = 'displayPayment';

		$paypal_condition = '';
		$iso_code = Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		$paypal_countries = array('ES', 'FR', 'PL', 'IT');
		if (Context::getContext()->getMobileDevice() && Context::getContext()->shop->getTheme() == 'default' && in_array($iso_code, $paypal_countries))
			$paypal_condition = ' AND m.`name` = \'paypal\'';

        $shiptopay_active = Configuration::get('SHIPTOPAY_ACTIVE') && $context->cart && !$context->cart->isVirtualCart() ? true : false;

		$list = Shop::getContextListShopID();
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT '.($shiptopay_active ? '(stp.id_carrier), h.`id_hook`, ' : '').'m.`name`, hm.`position`
		FROM `'._DB_PREFIX_.'module` m
		'.($frontend ? 'LEFT JOIN `'._DB_PREFIX_.'module_country` mc ON (m.`id_module` = mc.`id_module` AND mc.id_shop = '.(int)$context->shop->id.')' : '').'
		'.($frontend ? 'INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module` AND mg.id_shop = '.(int)$context->shop->id.')' : '').'
		'.($frontend && isset($context->customer) ? 'INNER JOIN `'._DB_PREFIX_.'customer_group` cg on (cg.`id_group` = mg.`id_group`AND cg.`id_customer` = '.(int)$context->customer->id.')' : '').'
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook` 
		'.($shiptopay_active ? 'LEFT JOIN `'._DB_PREFIX_.'shiptopay` stp ON hm.`id_module` = stp.`id_payment`' : '').' 
		WHERE h.`name` = \''.pSQL($hookPayment).'\'
		'.(isset($billing) && $frontend ? 'AND mc.id_country = '.(int)$billing->id_country : '').'
		AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
        '.($shiptopay_active ? 'AND stp.id_carrier = '.(int)$context->cart->id_carrier : '').'  
		AND hm.id_shop IN('.implode(', ', $list).')
		'.(count($groups) && $frontend ? 'AND (mg.`id_group` IN('.implode(', ', $groups).'))' : '').$paypal_condition.'
		GROUP BY hm.id_hook, hm.id_module
		ORDER BY hm.`position`, m.`name` DESC');
	}
}
