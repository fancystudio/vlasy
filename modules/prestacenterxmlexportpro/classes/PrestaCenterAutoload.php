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


if (!defined('T_NAMESPACE'))
{ define('T_NAMESPACE', -1); }
if (!defined('T_NS_C'))
{ define('T_NS_C', -1); }
if (!defined('T_NS_SEPARATOR'))
{ define('T_NS_SEPARATOR', -1); }
class PrestaCenterAutoload
{
	protected static $dirs = array();
	protected static $index = array();
	protected static $scanned = false;
	protected static $cacheFile = '';
	public static function register()
	{
		self::$cacheFile = dirname(__FILE__).'/autoload.cache';
		spl_autoload_register('PrestaCenterAutoload::load', false);
		self::loadCache();
	}
	public static function load($className)
	{
		if (empty(self::$index) || !isset(self::$index[$className]) || !is_readable(self::$index[$className]))
		{
			self::createIndex();
		}
		if (isset(self::$index[$className]) && is_readable(self::$index[$className]))
		{
			require_once self::$index[$className];
		}
	}
	public static function add($dir)
	{
		$dir = realpath($dir);
		if (!file_exists($dir))
			throw new InvalidArgumentException("Folder $dir doesn't exist.");
		elseif (!is_dir($dir) || !is_readable($dir))
			throw new InvalidArgumentException("$dir is not a folder or is not readable.");
		self::$dirs[] = $dir;
	}
	protected static function createIndex()
	{
		if (self::$scanned || empty(self::$dirs))
		{
			return;
		}
		$index = array();
		foreach (self::$dirs as $path)
		{
			foreach (Tools::scandir($path, 'php', '', true) as $file)
			{
				$code = Tools::file_get_contents($path.DS.$file);
				$expected = false;
				$namespace = $name = '';
				$level = $minLevel = 0;
				foreach (@token_get_all($code) as $token)
				{ 					if (is_array($token))
					{
						switch ($token[0])
						{
							case T_COMMENT:
							case T_DOC_COMMENT:
							case T_WHITESPACE:
								continue 2;
							case T_NS_SEPARATOR:
							case T_STRING:
								if ($expected)
							{
									$name .= $token[1];
								}
								continue 2;
							case T_NAMESPACE:
							case T_CLASS:
							case T_INTERFACE:
								$expected = $token[0];
								$name = '';
								continue 2;
							case T_CURLY_OPEN:
							case T_DOLLAR_OPEN_CURLY_BRACES:
								$level++;
						}
					}
					if ($expected)
					{
						switch ($expected)
						{
							case T_CLASS:
							case T_INTERFACE:
								if ($level === $minLevel)
							{
									$index[$namespace.$name] = $path.DS.$file;
								}
								break;
							case T_NAMESPACE:
								$namespace = $name ? $name.'\\' : '';
								$minLevel = $token === '{' ? 1 : 0;
						}
						$expected = null;
					}
					if ($token === '{')
					{
						$level++;
					}
					elseif ($token === '}')
					{
						$level--;
					}
				}
			}
		}
		self::$index = $index;
		self::$scanned = true;
		self::saveCache();
	}
	protected static function loadCache()
	{
		if (file_exists(self::$cacheFile) && ($data = unserialize(Tools::file_get_contents(self::$cacheFile))))
		{
			self::$index = $data;
		}
	}
	protected static function saveCache()
	{
		if (!empty(self::$index) && is_dir(dirname(self::$cacheFile)) && is_writable(dirname(self::$cacheFile)))
		{
			file_put_contents(self::$cacheFile, serialize(self::$index), LOCK_EX);
		}
	}
}