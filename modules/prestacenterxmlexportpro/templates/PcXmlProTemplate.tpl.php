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


class PcXmlProTemplate
{
	protected $common = array();
	protected $feedVars = array();
	protected $helperData = array();
	protected $id_combination = 0;
	public function __construct()
	{ }
	public function setCommonData(array $common)
	{
		$this->common = $common;
		return $this;
	}
	public function set($name, $value)
	{
		$this->feedVars[$name] = $value;
		return $this;
	}
	public function reset()
	{
		$this->feedVars = array();
		return $this;
	}
	protected function parseCustomAvailability($custom)
	{
		$opts = array(0, '#', '#', '#', 'Y-m-d', '#skipProduct');
		$custom = array_map('trim', (is_array($custom) ? $custom : explode(',', $custom)));
		if (isset($custom[0]) && $custom[0] !== '')
		{
			$tmp = array_map('trim', explode(':', $custom[0]));
			if (count($tmp) === 1)
			{  
				$opts[0] = 0;
				$opts[1] = $opts[2] = $tmp[0];
			}
			elseif (count($tmp) === 3 && ctype_digit($tmp[0]))
			{ 
				$opts[0] = (int)$tmp[0];
				$opts[1] = $tmp[1];
				$opts[2] = $tmp[2];
			}
		}
		if (isset($custom[1]) && $custom[1] !== '')
		{
			$tmp = array_map('trim', explode(':', $custom[1]));
			if (count($tmp) === 1)
			{   
				$opts[3] = $tmp[0];
			}
			elseif ($tmp[0] === '#')
			{  
				$opts[3] = '#';
				$opts[4] = !empty($tmp[1]) ? $tmp[1] : $opts[4];
			}
		}
		if (isset($custom[2]) && $custom[2] !== '')
		{
			$opts[5] = $custom[2];
		}
		return $opts;
	}
	protected function generatorAvailability($uid, $value, $modifiers = array(), $custom = '')
	{
		if (empty($this->helperData['order_default']))
		{
			$this->helperData['order_default'] = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
		}
		if (empty($this->helperData['availability'][$uid]))
		{
			$this->helperData['availability'][$uid] = $this->parseCustomAvailability($custom);
		}
		$opts = $this->helperData['availability'][$uid];
		$product = $this->feedVars['product'];
		if (!$this->id_combination && $product['quantity'] > 0 || $this->id_combination && $product['variant'][$this->id_combination]['quantity'] > 0)
		{
			$days = preg_match('~(\\d)~', $product['available_now'][$this->feedVars['id_lang']], $m) ? $m[1] : 0;
			if ($opts[0] === 0 || $days <= $opts[0])
			{
				return $opts[1] === '#' ? $days : $opts[1];
			}
			else
			{
				return $opts[2] === '#' ? $days : $opts[2];
			}
		}
		elseif ($product['out_of_stock'] == 1 || $product['out_of_stock'] == 2 && $this->helperData['order_default'])
		{
			if ($opts[3] === '#skipProduct')
			{
				throw new PcXmlProExportException;
			}
			elseif ($opts[3] !== '#')
			{
				return $opts[3] === '#skipTag' ? '' : $opts[3];
			}
			else
			{
				if ($product['available_later'][$this->feedVars['id_lang']] === '')
					return '';
				if (preg_match('~(\d{4}-\d{1,2}-\d{1,2}|\d{1,2}\.\s*\d{1,2}\.\s*\d{4})~',
						$product['available_later'][$this->feedVars['id_lang']], $m))
				{
					return date($opts[4], strtotime(str_replace(' ', '', $m[1])));
				}
				elseif (preg_match('~(\d+)~', $product['available_later'][$this->feedVars['id_lang']], $m))
				{
					return (int)$m[1];
				}
				else
				{
					return '';
				}
			}
		}
		else
		{
			if ($opts[5] === '#skipProduct')
				throw new PcXmlProExportException;
			return $opts[5] === '#skipTag' ? '' : $opts[5];
		}
		return ''; 
	}
	protected function generatorAvailability_text($uid, $value, $modifiers = array(), $custom = '')
	{
		if (is_array($custom))
		{
			$custom = $custom[0];
		}
		if (empty($this->helperData['order_default']))
		{
			$this->helperData['order_default'] = (int)Configuration::get('PS_ORDER_OUT_OF_STOCK');
		}
		$key = '';
		if (!$this->id_combination && $this->feedVars['product']['quantity'] > 0)
		{
			$key = 'available_now';
		}
		elseif ($this->id_combination && $this->feedVars['product']['variant'][$this->id_combination]['quantity'] > 0)
		{
			$key = 'available_now';
		}
		elseif ($this->feedVars['product']['out_of_stock'] == 1 ||
				$this->feedVars['product']['out_of_stock'] == 2 && $this->helperData['order_default'])
		{
			$key = 'available_later';
		}
		return $key && $this->feedVars['product'][$key][$this->feedVars['id_lang']] !== ''
				? $this->feedVars['product'][$key][$this->feedVars['id_lang']]
				: trim($custom);
	}
	protected function generatorActiveCatOnly($uid, $value, $modifiers = array(), $custom = '')
	{
		static $allow;
		if (is_null($allow))
		{
			$allow = Configuration::getGlobalValue(PrestaCenterXmlExportPro::CFG_PREFIX.'EXPORT_INACTIVE_CAT');
		}
		if (!$allow && !$this->feedVars['product']['defaultCatActive'])
		{
			throw new PcXmlProExportException;
		}
		return $value;
	}
	protected function helperCondition($uid, $condition, $modifiers = array(), $custom = '')
	{
		if (empty($this->helperData['condition'][$uid]))
		{
			$values = array_map('trim', (is_array($custom) ? $custom : explode(',', $custom)));
			$fromDb = array('new', 'bazaar', 'refurbished');
			foreach ($values as $key => $translation)
			{
				if (isset($fromDb[$key]))
					$this->helperData['condition'][$uid][$fromDb[$key]] = $translation;
			}
		}
		return isset($this->helperData['condition'][$uid][$condition]) ? $this->helperData['condition'][$uid][$condition] : $condition;
	}
	protected function helperFtime($uid, $time, $modifiers = array(), $format = 'Y-m-d\TH:i:sP')
	{
		if (is_array($format))
		{
			$format = $format[0];
		}
		$func = 'date';
		if (isset($modifiers['TIMEZONE']) && $modifiers['TIMEZONE'] === 'GMT')
		{
			$func = 'gmdate';
		}
		return $func(trim($format), $time);
	}
	protected function helperCategories($uid, $categories, $modifiers = array(), $delim = '|')
	{
		if (is_array($delim))
		{
			$delim = $delim[0];
		}
		return str_replace('|', (string)$delim, $categories);
	}
	protected function helperTruncate($uid, $input, $modifiers = array(), $custom = '')
	{
		if (is_array($custom))
		{
			$custom = $custom[0];
		}
		if ((int)$custom < 1)
		{ return $input; }
		return Tools::substr($input, 0, (int)$custom, 'UTF-8');
	}
	protected function generatorFeature_block($uid, $features, $modifiers = array(), $custom = '')
	{
		$output = '';
		if (empty($features))
		{
			return $output;
		}
		elseif (is_string($features))
		{
			return $features;
		}
		$opts = array('- ', ': ', '; ');
		if (is_string($custom))
		{
			$opts = explode(',', $custom) + $opts;
		}
		elseif (is_array($custom))
		{
			$opts = $custom + $opts;
		}
		foreach ($features as $name => $value)
		{
			$output .= $opts[0].$name.$opts[1].$value.$opts[2];
		}
		return $output;
	}
	protected function helperDecimalDot($uid, $price, $modifiers = array(), $custom = '')
	{
		if (is_array($custom))
			$custom = $custom[0];
		return str_replace('.', (string)$custom, (string)$price);
	}
	protected function helperEscape($uid, $input, $modifiers = array(), $custom = '')
	{
		return htmlspecialchars($input, ENT_QUOTES, 'UTF-8', false);
	}
	protected function helperStrip($uid, $input, $modifiers = array(), $custom = '')
	{
		return preg_replace('~\s+~u', ' ', $input);
	}
	protected function helperClean($uid, $input, $modifiers = array(), $custom = '')
	{
		$input = preg_replace('~(?:\s|&nbsp;|\<br\>|\<br /\>|\<br/\>)+~ui', ' ', $input, -1);
		$input = trim(strip_tags($input));
		return $input;
	}
	protected function generatorNotEmpty($uid, $input, $modifiers = array(), $custom)
	{ }
	protected function helperDebug_time_elapsed($uid, $input, $modifiers = array(), $units = 'us')
	{
		static $last = 0.0, $supported = null;
		if (is_null($supported))
			$supported = function_exists('microtime');
		$units = Tools::strtolower($units);
		$now = ($supported ? microtime(true) : time());
		if ($last === 0.0)
		{
			$last = $now;
			return 0;
		}
		$return = ($now - $last) * ($units === 's' ? 1 : ($units === 'ms' ? 1000 : 1000000));
		$last = $now;
		return $return;
	}
	protected function helperDebug_memory($uid, $input, $modifiers = array(), $units = 'b')
	{
		$units = Tools::strtolower($units);
		return memory_get_usage() / ($units === 'kb' ? 1024 : ($units === 'mb' ? 1048576 : 1));
	}
	protected function helperDebug_memory_peak($uid, $input, $modifiers = array(), $units = 'b')
	{
		$units = Tools::strtolower($units);
		return memory_get_peak_usage() / ($units === 'kb' ? 1024 : ($units === 'mb' ? 1048576 : 1));
	}
	/* @methods@ */
}