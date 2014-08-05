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


function getAdminDir($admin_file)
{
	$root_dir = realpath(dirname(__FILE__).'/../../');
	$files = scandir($root_dir);
	$full_name = '';
	foreach ($files as $file)
	{
		$full_name = $root_dir.'/'.$file;
		if (is_dir($full_name))
		{
			if (is_file($full_name.'/'.$admin_file))
			{
				return $full_name;
			}
		}
	}
	return false;
}
$ad = getAdminDir('get-file-admin.php');
if ($ad)
	define('_PS_ADMIN_DIR_', $ad);
else
	die('Could not locate admin directory!');
require_once dirname(__FILE__).'/../../config/config.inc.php';
Autoload::getInstance()->generateIndex();
require_once dirname(__FILE__).'/controllers/admin/PcXmlProController.php';
require_once dirname(__FILE__).'/prestacenterxmlexportpro.php';
if (empty(Context::getContext()->link))
{
	$protocol = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
	Context::getContext()->link = new Link($protocol, $protocol);
}
$cronToken = Tools::getValue('token');
if (Tools::substr(_COOKIE_KEY_, 4, 10) != $cronToken)
	die('Access denied.');
Context::getContext()->shop->setContext(Shop::CONTEXT_ALL);
$controller = Controller::getController('PcXmlProController');
$controller->processCronExport($cronToken);