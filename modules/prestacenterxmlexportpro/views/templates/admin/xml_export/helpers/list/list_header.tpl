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

{* Překrytá šablona.
 * - pokud proměnná $xmlexport.cbx.dependent je TRUE, překryje se výchozí handler pro závislé checkboxy vlastním
 * - nastavení $xmlexport.cbx.cascade (default TRUE) povoluje propojení checkboxů v hlavní tabulce a podtabulkách
 *}

{if !$simple_header}

	<script type="text/javascript">
		$(document).ready(function() {
			$('table.{$table|strval} .filter').keypress(function(event){
				formSubmit(event, 'submitFilterButton{$table|strval}')
			})
		});
	</script>
	{* Display column names and arrows for ordering (ASC, DESC) *}
	{if $is_order_position}
		<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
		<script type="text/javascript">
			var token = '{$token|strval}';
			var come_from = '{$table|strval}';
			var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
	{/if}

	<script type="text/javascript">
		$(function() {
			if ($("table.{$table|strval} .datepicker").length > 0)
				$("table.{$table|strval} .datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
		});
	</script>


{/if}{* End if simple_header *}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}

{if !$simple_header}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

{block name="override_header"}{/block}


{hook h='displayAdminListBefore'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}


{if !$simple_header}
<form method="post" action="{$action|strval}" class="form">
	<input type="hidden" id="submitFilter{$table|strval}" name="submitFilter{$table|strval}" value="0"/>
{/if}
	<table class="table_grid" name="list_table">
		{if !$simple_header}
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">
						{if $page > 1}
							<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter{$table|strval}').value=1"/>&nbsp;
							<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter{$table|strval}').value={$page - 1}"/>
						{/if}
						{l s='Page' mod='prestacenterxmlexportpro'} <b>{$page|escape}</b> / {$total_pages|escape}
						{if $page < $total_pages}
							<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter{$table|strval}').value={$page + 1}"/>&nbsp;
							<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter{$table|strval}').value={$total_pages|strval}"/>
						{/if}
						| {l s='Display results:' mod='prestacenterxmlexportpro'}
						<select name="pagination" onchange="submit()">
							{* Choose number of results per page *}
							{foreach $pagination AS $value}
								<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
							{/foreach}
						</select>
						/ {$list_total|escape} {l s='result(s)' mod='prestacenterxmlexportpro'}
					</span>
					<span style="float: right;">
						<input type="submit" name="submitReset{$table|strval}" value="{l s='Reset' mod='prestacenterxmlexportpro'}" class="button" />
						<input type="submit" id="submitFilterButton{$table|strval}" name="submitFilter" value="{l s='Filter' mod='prestacenterxmlexportpro'}" class="button" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
		{/if}
		<tr>
			<td{if $simple_header} style="border:none;"{/if}>
				<table
				{if $table_id} id={$table_id}{/if}
				class="table {if $table_dnd}tableDnD{/if} {$table}"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;"
				>
					<col width="10px" />
					{foreach $fields_display AS $key => $params}
						<col {if isset($params.width) && $params.width != 'auto'}width="{$params.width}px"{/if}/>
					{/foreach}
					{if $shop_link_type}
						<col width="80px" />
					{/if}
					{if $has_actions}
						<col width="52px" />
					{/if}
					<thead>
						<tr class="nodrag nodrop" style="height: 40px">
							<th class="center">
								{if $has_bulk_actions || (isset($xmlexport.cbx.show) && $xmlexport.cbx.show)}
									<input type="checkbox" name="checkme" class="noborder"
									{if !isset($xmlexport.cbx.dependent) && !$xmlexport.cbx.dependent}
										onclick="checkDelBoxes(this.form, '{$table|strval}Box[]', this.checked)"
									{/if} />
								{/if}
							</th>
							{foreach $fields_display AS $key => $params}
								<th {if isset($params.align)} class="{$params.align}"{/if}
									{if isset($params.width)} style="width: {$params.width};"{/if}>
									{if isset($params.hint)}<span class="hint" name="help_box">{$params.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
									<span class="title_box">
										{$params.title|strval}
									</span>
									{if (!isset($params.orderby) || $params.orderby) && !$simple_header}
										<br />
										<a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$table}Orderby={$key|urlencode}&amp;{$table}Orderway=desc&amp;token={$token}"><img border="0" src="../img/admin/down{if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}_d{/if}.gif" /></a>
										<a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$table}Orderby={$key|urlencode}&amp;{$table}Orderway=asc&amp;token={$token}"><img border="0" src="../img/admin/up{if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}_d{/if}.gif" /></a>
									{elseif !$simple_header}
										<br />&nbsp;
									{/if}
								</th>
							{/foreach}
							{if $shop_link_type}
								<th>
									{if $shop_link_type == 'shop'}
										{l s='Shop' mod='prestacenterxmlexportpro'}
									{else}
										{l s='Shop group' mod='prestacenterxmlexportpro'}
									{/if}
									<br />&nbsp;
								</th>
							{/if}
							{if $has_actions}
								<th class="center">{l s='Action' mod='prestacenterxmlexportpro'}{if !$simple_header}<br />&nbsp;{/if}</th>
							{/if}
						</tr>
 						{if !$simple_header}
						<tr class="nodrag nodrop filter {if $row_hover}row_hover{/if}" style="height: 35px;">
							<td class="center">
								{if $has_bulk_actions}
									--
								{/if}
							</td>

							{* Filters (input, select, date or bool) *}
							{foreach $fields_display AS $key => $params}
								<td {if isset($params.align)} class="{$params.align}" {/if}>
									{if isset($params.search) && !$params.search}
										--
									{else}
										{if $params.type == 'bool'}
											<select onchange="$('#submitFilterButton{$table|strval}').focus();$('#submitFilterButton{$table|strval}').click();" name="{$table|strval}Filter_{$key|strval}">
												<option value="">--</option>
												<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'  mod='prestacenterxmlexportpro'}</option>
												<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No' mod='prestacenterxmlexportpro'}</option>
											</select>
										{elseif $params.type == 'date' || $params.type == 'datetime'}
											{l s='From' mod='prestacenterxmlexportpro'} <input type="text" class="filter datepicker" id="{$params.id_date}_0" name="{$params.name_date}[0]" value="{if isset($value.0)}$value.0{/if}"{if isset($params.width)} style="width:70px"{/if}/><br />
											{l s='To' mod='prestacenterxmlexportpro'} <input type="text" class="filter datepicker" id="{$params.id_date}_1" name="{$params.name_date}[1]" value="{if isset($value.1)}$value.1{/if}"{if isset($params.width)} style="width:70px"{/if}/>
										{elseif $params.type == 'select'}
											{if isset($params.filter_key)}
												<select onchange="$('#submitFilterButton{$table}').focus();$('#submitFilterButton{$table}').click();" name="{$table}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
													<option value="" {if $params.value == ''} selected="selected" {/if}>--</option>
													{if isset($params.list) && is_array($params.list)}
														{foreach $params.list AS $option_value => $option_display}
															<option value="{$option_value}" {if $option_display == $params.value ||  $option_value == $params.value} selected="selected"{/if}>{$option_display}</option>
														{/foreach}
													{/if}
												</select>
											{/if}
										{else}
											<input type="text" class="filter" name="{$table}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{else}style="width:95%"{/if} />
										{/if}
									{/if}
								</td>
							{/foreach}

							{if $shop_link_type}
								<td>--</td>
							{/if}
							{if $has_actions}
								<td class="center">--</td>
							{/if}
							</tr>
						{/if}
						</thead>
