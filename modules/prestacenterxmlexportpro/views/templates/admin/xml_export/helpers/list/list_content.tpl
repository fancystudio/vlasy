{**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{* formátovací řetězce a data pro (v)sprintf a $xmlexport.onclick.
Hodnota atributu onclick je v uvozovkách, hodnoty pro location.href nebo window.open v apostrofech *}
{if isset($xmlexport.onclick)}
	{* pozice parametrů pro vsprintf (o jednu vyšší než skutečné klíče pole!):
	5 = $tr.$identifier ( = ID řádku ), 6 = hodnota aktuální buňky,
	7 = hodnota zvolené buňky podle $xmlexport.onclick.key. Od klíče 8 začínají data předaná z kontroleru. *}

	{$onclick_data = array($current, $token, $table, $identifier, '', '', '')}
	{if isset($xmlexport.onclick.data)}{$onclick_data = array_merge($onclick_data, $xmlexport.onclick.data)}{/if}
{/if}
{$onclick_format.xmlLink = 'href="%8$s%7$s" target="_blank"'}
{$onclick_format.xmlPopup = '%8$s%7$s'}
{$onclick_format.ajaxDetails = 'document.getElementById(\'details_details_\'+\'%5$s\').click()'}

<tbody>
{if count($list)}
{foreach $list AS $index => $tr}
	<tr
	{if $position_identifier}id="tr_{$id_category}_{$tr.$identifier}_{if isset($tr.position['position'])}{$tr.position['position']}{else}0{/if}"{/if}
	class="{if $index is odd}alt_row{/if} {if $row_hover}row_hover{/if}"
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}
	>
		<td class="center">
			{if {$has_bulk_actions} || (isset($xmlexport.cbx.show) && $xmlexport.cbx.show)}
				{if isset($list_skip_actions.delete)}
					{if !in_array($tr.$identifier, $list_skip_actions.delete)}
						<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier|escape}"
						{if !empty($xmlexport.cbx.disabled)	&& $xmlexport.cbx.disabled[$tr.$identifier]}
							disabled="disabled"
						{/if}
						class="noborder" />
					{/if}
				{else}
					<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier|escape}"
					{if !empty($xmlexport.cbx.disabled)	&& $xmlexport.cbx.disabled[$tr.$identifier]}
						disabled="disabled"
					{/if}
					class="noborder" />
				{/if}
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			{$onclick_data.4 = $tr.$identifier}
			{$onclick_data.5 = $tr.$key}
			{if isset($xmlexport.onclick) && isset($xmlexport.onclick.key)}{$onclick_data.6 = $tr[$xmlexport.onclick.key]}{/if}

			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($id_category)}{$id_category}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{if !isset($params.remove_onclick) && !isset($tr.remove_onclick) && ($xmlexport.onclick || !$no_link)}pointer{/if}
					{if isset($params.position) && $order_by == 'position' && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.align)} {$params.align}{/if}"

					{* onclick akce (jsou-li povoleny): *}
					{* Vlastní. Platí pro celý řádek, dá se pro jednotlivá pole vypnout nastavením remove_onclick *}
					{if isset($xmlexport.onclick) && !isset($params.remove_onclick) && !isset($tr.remove_onclick)}

						{* - jakýkoli onclick javascript *}
						{if $xmlexport.onclick.type == 'onclick'}
							onclick="{$onclick_format[$xmlexport.onclick.name]|vsprintf:$onclick_data}"
						{* - přesměrování ve stejném okně *}
						{elseif $xmlexport.onclick.type == 'redir'}
							onclick="document.location = '{$onclick_format[$xmlexport.onclick.name]|vsprintf:$onclick_data}';"
						{* - otevření odkazu v novém okně *}
						{elseif $xmlexport.onclick.type == 'popup'}
							onclick="window.open('{$onclick_format[$xmlexport.onclick.name]|vsprintf:$onclick_data}');"
						{/if}
					{* původní PS akce *}
					{elseif !$no_link && !isset($params.remove_onclick) && !isset($tr.remove_onclick) && !isset($params.position)}
							onclick="document.location = '{$current_index}&{$identifier}={$tr.$identifier}{if $view}&view{else}&update{/if}{$table}&token={$token}'"
					{/if}
					>
			{/block}
			{block name="td_content"}
				{* Vlastní onclick akce - standardní odkaz. *}
				{if isset($xmlexport.onclick) && $xmlexport.onclick.type == 'link' && !isset($params.remove_onclick) && !isset($tr.remove_onclick)}
					<a {$onclick_format[$xmlexport.onclick.name]|vsprintf:$onclick_data}>
				{/if}
				{if isset($params.prefix)}{$params.prefix}{/if}
				{if isset($params.color) && isset($tr.color)}
					<span class="color_field" style="background-color:{$tr.color};color:{if Tools::getBrightness($tr.color) < 128}white{else}#383838{/if}">
				{/if}

				{if isset($params.active)}
					{$tr.$key|strval}
				{elseif isset($params.activeVisu)}
					<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
					alt="{if $tr.$key}{l s='Enabled' mod='prestacenterxmlexportpro'}{else}{l s='Disabled' mod='prestacenterxmlexportpro'}{/if}" title="{if $tr.$key}{l s='Enabled' mod='prestacenterxmlexportpro'}{else}{l s='Disabled' mod='prestacenterxmlexportpro'}{/if}" />
				{elseif isset($params.position)}
					{if $order_by == 'position' && $order_way != 'DESC'}
						<a href="{$tr.$key.position_url_down|escape:'html':'UTF-8'}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down' mod='prestacenterxmlexportpro'}" title="{l s='Down' mod='prestacenterxmlexportpro'}" />
						</a>

						<a href="{$tr.$key.position_url_up|escape:'html':'UTF-8'}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up' mod='prestacenterxmlexportpro'}" title="{l s='Up' mod='prestacenterxmlexportpro'}" />
						</a>
					{else}
						{$tr.$key.position + 1|escape:'intval'}
					{/if}
				{elseif isset($params.image)}
					{$tr.$key|strval}
				{elseif isset($params.icon)}
					<img src="../img/admin/{$tr[$key]['src']|strval}" alt="{$tr[$key]['alt']|strval}" title="{$tr[$key]['alt']|strval}" />
				{elseif isset($params.price)}
					{$tr.$key|strval}
				{elseif isset($params.float)}
					{$tr.$key|strval}
				{elseif isset($params.type) && $params.type == 'date'}
					{$tr.$key|strval}
				{elseif isset($params.type) && $params.type == 'datetime'}
					{$tr.$key|strval}
				{elseif isset($params.type) && $params.type == 'decimal'}
					{$tr.$key|string_format:"%.2f"}
				{elseif isset($params.type) && $params.type == 'percent'}
					{$tr.$key|strval} {l s='%' mod='prestacenterxmlexportpro'}
				{* If type is 'editable', an input is created *}
				{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
					<input type="text" name="{$key}_{$tr.id}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key}" />
				{elseif isset($params.callback)}
					{$tr.$key|strval}
				{elseif isset($tr.$key) && $key == 'color'}
					<div style="float: left; width: 18px; height: 12px; border: 1px solid #996633; background-color: {$tr.$key|strval}; margin-right: 4px;"></div>
				{elseif isset($tr.$key)}
					{if isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
						<span title="{$tr.$key|escape:'htmlall':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'htmlall':'UTF-8'}</span>
					{else}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{/if}
				{else}
					{block name="default_field"}--{/block}
				{/if}
				{if isset($params.suffix)}{$params.suffix}{/if}
				{if isset($params.color) && isset($tr.color)}
					</span>
				{/if}
				{if isset($xmlexport.onclick) && $xmlexport.onclick.type == 'link' && !isset($params.remove_onclick) && !isset($tr.remove_onclick)}
					</a>
				{/if}
				</td>
			{/block}
		{/foreach}

	{if $shop_link_type}
		<td class="center" title="{$tr.shop_name|escape}">
			{if isset($tr.shop_short_name)}
				{$tr.shop_short_name|escape}
			{else}
				{$tr.shop_name|escape}
			{/if}</td>
	{/if}
	{if $has_actions}
		<td class="center" style="white-space: nowrap;">
			{foreach $actions AS $action}
				{if isset($tr.$action)}
					{$tr.$action|strval}
				{/if}
			{/foreach}
		</td>
	{/if}
	</tr>
{/foreach}
{else}
	<tr><td class="center" colspan="{count($fields_display) + 2|escape:'intval'}">{l s='No items found' mod='prestacenterxmlexportpro'}</td></tr>
{/if}
</tbody>
