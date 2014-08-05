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

require_once './../config/config.inc.php';
Autoload::getInstance()->generateIndex();
$data = array('type' => 'display');
if (($name = Tools::getValue('file')))
{
	$data['filename'] = dirname(__FILE__).'/'.basename($name);
	if (file_exists($data['filename']) && is_readable($data['filename']))
	{
		$data['status'] = 'ok';
		$data['lastmod'] = gmdate(DATE_RFC2822, filemtime($data['filename']));
		$data['size'] = filesize($data['filename']);
	}
	else
	{
		$data['status'] = 'notfound';
	}
}
else
{
	$data['status'] = 'empty';
}
require './index.php';