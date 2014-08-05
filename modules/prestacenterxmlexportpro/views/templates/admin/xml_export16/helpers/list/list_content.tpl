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

{* překrytá šablona list_content.tpl z PS 1.6.0.5 *}

{* ----------- vlastní onclick akce, nastavení *}

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
	{if $position_identifier}id="tr_{$position_group_identifier}_{$tr.$identifier}_{if isset($tr.position['position'])}{$tr.position['position']}{else}0{/if}"{/if}
	class="{if isset($tr.class)} {$tr.class}{/if} {if $tr@iteration is odd by 1}odd{/if}"
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}

	>
		<td class="text-center">
			{if $bulk_actions && $has_bulk_actions || isset($xmlexport.cbx.show) && $xmlexport.cbx.show}
				{* změna orig. šablony - obě podmínky s totožným obsahem sloučeny *}
				{if !isset($list_skip_actions.delete) || !in_array($tr.$identifier, $list_skip_actions.delete)}
					<input type="checkbox" name="{$list_id|strval}Box[]" value="{$tr.$identifier|strval}" class="noborder"
					{if isset($checked_boxes) && is_array($checked_boxes) && in_array({$tr.$identifier}, $checked_boxes)}
						checked="checked"
					{/if}
					{if !empty($xmlexport.cbx.disabled)	&& $xmlexport.cbx.disabled[$tr.$identifier]}
						disabled="disabled"
					{/if}
					/>
				{/if}
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			{* donastavení proměnných pro onclick akce/vsprintf *}
			{$onclick_data.4 = $tr.$identifier}
			{$onclick_data.5 = $tr.$key}
			{if isset($xmlexport.onclick) && isset($xmlexport.onclick.key)}{$onclick_data.6 = $tr[$xmlexport.onclick.key]}{/if}

			{block name="open_td"}
				<td
					{if isset($params.position)}
						id="td_{if !empty($position_group_identifier)}{$position_group_identifier}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{if !isset($params.remove_onclick) && !isset($tr.remove_onclick) && ($xmlexport.onclick || !$no_link)}pointer{/if}
					{if isset($params.position) && $order_by == 'position' && $order_way != 'DESC'} dragHandle{/if}
					{if isset($params.class)} {$params.class}{/if}
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
				{if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}<span class="badge badge-success">{/if}
				{if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}<span class="badge badge-warning">{/if}
				{if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}<span class="badge badge-danger">{/if}

				{* NB: Tady bylo v 1.5 opraveno na $tr.color, které použili i ve spanu, ale zřejmě takto to
					má asi být správně. (?)
				*}
				{if isset($params.color) && isset($tr[$params.color])}
					<span class="label color_field" style="background-color:{$tr[$params.color]};color:{if Tools::getBrightness($tr[$params.color]) < 128}white{else}#383838{/if}">
				{/if}
				{if isset($tr.$key)}
					{if isset($params.active)}
						{$tr.$key|strval}
					{elseif isset($params.activeVisu)}
						{if $tr.$key}
							<i class="icon-check-ok"></i> {l s='Enabled' mod='prestacenterxmlexportpro'}
						{else}
							<i class="icon-remove"></i> {l s='Disabled' mod='prestacenterxmlexportpro'}
						{/if}

					{elseif isset($params.position)}
						{if $order_by == 'position' && $order_way != 'DESC'}
							<div class="dragGroup">
								<div class="positions">
									{$tr.$key.position|strval}
								</div>
								<div class="btn-group">
									<a class="btn btn-default btn-xs" href="{$tr.$key.position_url_down|escape:'html':'UTF-8'}" {if !($tr.$key.position != $positions[count($positions) - 1])}disabled{/if}>
										<i class="icon-chevron-{if $order_way == 'ASC'}down{else}up{/if}"></i>
									</a>
									<a class="btn btn-default btn-xs" href="{$tr.$key.position_url_up|escape:'html':'UTF-8'}" {if !($tr.$key.position != $positions.0)}disabled{/if}>
										<i class="icon-chevron-{if $order_way == 'ASC'}up{else}down{/if}"></i>
									</a>
								</div>
							</div>
						{else}
							{$tr.$key.position + 1|escape:'intval'}
						{/if}
					{elseif isset($params.image)}
						{$tr.$key|strval}
					{elseif isset($params.icon)}
						{if is_array($tr[$key])}
							{if isset($tr[$key]['class'])}
								<i class="{$tr[$key]['class']|strval}"></i>
							{else}
								<img src="../img/admin/{$tr[$key]['src']|strval}" alt="{$tr[$key]['alt']|strval}" title="{$tr[$key]['alt']|strval}" />
							{/if}
                        {else}
                            <i class="{$tr[$key]|strval}"></i>
						{/if}
					{elseif isset($params.type) && $params.type == 'price'}
						{displayPrice price=$tr.$key}
					{elseif isset($params.float)}
						{$tr.$key|strval}
					{elseif isset($params.type) && $params.type == 'date'}
						{dateFormat date=$tr.$key full=0}
					{elseif isset($params.type) && $params.type == 'datetime'}
						{dateFormat date=$tr.$key full=1}
					{elseif isset($params.type) && $params.type == 'decimal'}
						{$tr.$key|string_format:"%.2f"}
					{elseif isset($params.type) && $params.type == 'percent'}
						{$tr.$key|strval} {l s='%' mod='prestacenterxmlexportpro'}
					{* If type is 'editable', an input is created *}
					{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
						<input type="text" name="{$key}_{$tr.id}" value="{$tr.$key|escape:'html':'UTF-8'}" class="{$key}" />
					{elseif isset($params.callback)}
						{if isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
							<span title="{$tr.$key}">{$tr.$key|truncate:$params.maxlength:'...'}</span>
						{else}
							{$tr.$key|strval}
						{/if}
					{elseif $key == 'color'}
						{if !is_array($tr.$key)}
						<div style="background-color: {$tr.$key|strval};" class="attributes-color-container"></div>
						{else} {*TEXTURE*}
						<img src="{$tr.$key.texture|strval}" alt="{$tr.name|strval}" class="attributes-color-container" />
						{/if}
					{elseif isset($params.maxlength) && Tools::strlen($tr.$key) > $params.maxlength}
						<span title="{$tr.$key|escape:'html':'UTF-8'}">{$tr.$key|truncate:$params.maxlength:'...'|escape:'html':'UTF-8'}</span>
					{else}
						{$tr.$key|escape:'html':'UTF-8'}
					{/if}
				{else}
					{block name="default_field"}--{/block}
				{/if}
				{if isset($params.suffix)}{$params.suffix}{/if}
				{if isset($params.color) && isset($tr.color)}
					</span>
				{/if}
				{if isset($params.badge_danger) && $params.badge_danger && isset($tr.badge_danger) && $tr.badge_danger == $params.badge_danger}</span>{/if}
				{if isset($params.badge_warning) && $params.badge_warning && isset($tr.badge_warning) && $tr.badge_warning == $params.badge_warning}</span>{/if}
				{if isset($params.badge_success) && $params.badge_success && isset($tr.badge_success) && $tr.badge_success == $params.badge_success}</span>{/if}
				{* vlastní onclick akce (std. odkaz) - uzavření prvku *}
				{if isset($xmlexport.onclick) && $xmlexport.onclick.type == 'link' && !isset($params.remove_onclick) && !isset($tr.remove_onclick)}
					</a>
				{/if}
			{/block}
			{block name="close_td"}
				</td>
			{/block}
		{/foreach}

	{if $shop_link_type}
		<td title="{$tr.shop_name|strval}">
			{if isset($tr.shop_short_name)}
				{$tr.shop_short_name|strval}
			{else}
				{$tr.shop_name|strval}
			{/if}
		</td>
	{/if}
	{if $has_actions}
		<td class="text-right">
			{assign var='compiled_actions' value=array()}
			{foreach $actions AS $key => $action}
				{if isset($tr.$action)}
					{if $key == 0}
						{assign var='action' value=$action}
					{/if}
					{$compiled_actions[] = $tr.$action}
				{/if}
			{/foreach}
			{if $compiled_actions|count > 0}
				{if $compiled_actions|count > 1}<div class="btn-group-action">{/if}
				<div class="btn-group pull-right">
					{$compiled_actions[0]|regex_replace:'/class\s*=\s*"(\w*)"/':'class="$1 btn btn-default"'}
					{if $compiled_actions|count > 1}
					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<i class="icon-caret-down"></i>&nbsp;
					</button>
						<ul class="dropdown-menu">
						{foreach $compiled_actions AS $key => $action}
							{if $key != 0}
							<li>
								{$action|strval}
							</li>
							{/if}
						{/foreach}
						</ul>
					{/if}
				</div>
				{if $compiled_actions|count > 1}</div>{/if}
			{/if}
		</td>
	{/if}
	</tr>
{/foreach}
{else}
	<tr>
		<td class="list-empty" colspan="{count($fields_display) + 2|escape:'intval'}">
			<div class="list-empty-msg">
				<i class="icon-warning-sign list-empty-icon"></i>
				{l s='No items found' mod='prestacenterxmlexportpro'}
			</div>
		</td>
	</tr>
{/if}
</tbody>
