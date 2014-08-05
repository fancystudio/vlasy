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

{* ---------------- Překrytá šablona z PS 1.6.0.5  --------------- *}

	</table>
</div>
<div class="row">
	<div class="col-lg-8">
		{if $bulk_actions && $has_bulk_actions}
		<div class="btn-group bulk-actions">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions' mod='prestacenterxmlexportpro'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			<li>
				<a href="#" onclick="if (!$('table.table').first().find(':checkbox[name=checkme]').first().prop('checked')) { $('table.table').first().find(':checkbox[name=checkme]').first().click() }; return false;">
					<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='prestacenterxmlexportpro'}
				</a>
			</li>
			<li>
				<a href="#" onclick="if ($('table.table').first().find(':checkbox[name=checkme]').first().prop('checked')) { $('table.table').first().find(':checkbox[name=checkme]').first().click() }; return false;">
					<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='prestacenterxmlexportpro'}
				</a>
			</li>
			<li class="divider"></li>
			{foreach $bulk_actions as $key => $params}
				<li{if $params.text == 'divider'} class="divider"{/if}>
					{if $params.text != 'divider'}
					<a href="#" onclick="{if isset($params.confirm)}if (confirm('{$params.confirm}')){/if}sendBulkAction($(this).closest('form').get(0), 'submitBulk{$key}{$table}');">
						{if isset($params.icon)}<i class="{$params.icon}"></i>{/if}&nbsp;{$params.text}
					</a>
					{/if}
				</li>
			{/foreach}
			</ul>
		</div>
		{/if}
	</div>
	{if !$simple_header && $list_total > $pagination[0]}
	<div class="col-lg-4">
		{* Choose number of results per page *}
		<span class="pagination">
			{l s='Display results:' mod='prestacenterxmlexportpro'}:
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{$selected_pagination|strval}
				<i class="icon-caret-down"></i>
			</button>
			<ul class="dropdown-menu">
			{foreach $pagination AS $value}
				<li>
					<a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id}">{$value}</a>
				</li>
			{/foreach}
			</ul>
			/ {$list_total|strval} {l s='result(s)' mod='prestacenterxmlexportpro'}
			<input type="hidden" id="{$list_id}-pagination-items-page" name="{$list_id}_pagination" value="{$selected_pagination|intval}" />
		</span>
		<script type="text/javascript">
			$('.pagination-items-page').on('click',function(e){
				e.preventDefault();
				$('#'+$(this).data("list-id")+'-pagination-items-page').val($(this).data("items")).closest("form").submit();
			});
		</script>
		<ul class="pagination pull-right">
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id|strval}">
					<i class="icon-double-angle-left"></i>
				</a>
			</li>
			<li {if $page <= 1}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page - 1}" data-list-id="{$list_id|strval}">
					<i class="icon-angle-left"></i>
				</a>
			</li>
			{assign p 0}
			{while $p++ < $total_pages}
				{if $p < $page-2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $page-3}
				{else if $p > $page+2}
					<li class="disabled">
						<a href="javascript:void(0);">&hellip;</a>
					</li>
					{assign p $total_pages}
				{else}
					<li {if $p == $page}class="active"{/if}>
						<a href="javascript:void(0);" class="pagination-link" data-page="{$p|strval}" data-list-id="{$list_id|strval}">{$p|strval}</a>
					</li>
				{/if}
			{/while}
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$page + 1}" data-list-id="{$list_id|strval}">
					<i class="icon-angle-right"></i>
				</a>
			</li>
			<li {if $page >= $total_pages}class="disabled"{/if}>
				<a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages|strval}" data-list-id="{$list_id|strval}">
					<i class="icon-double-angle-right"></i>
				</a>
			</li>
		</ul>
		<script type="text/javascript">
			$('.pagination-link').on('click',function(e){
				e.preventDefault();

				if (!$(this).parent().hasClass('disabled'))
					$('#submitFilter'+$(this).data("list-id")).val($(this).data("page")).closest("form").submit();
			});
		</script>
	</div>
	{/if}
</div>
{block name="footer"}
{foreach from=$toolbar_btn item=btn key=k}
	{if $k == 'back'}
		{assign 'back_button' $btn}
		{break}
	{/if}
{/foreach}
{if isset($back_button)}
<div class="panel-footer">
	<a id="desc-{$table}-{if isset($back_button.imgclass)}{$back_button.imgclass}{else}{$k}{/if}" class="btn btn-default" {if isset($back_button.href)}href="{$back_button.href|escape:'html':'UTF-8'}"{/if} {if isset($back_button.target) && $back_button.target}target="_blank"{/if}{if isset($back_button.js) && $back_button.js}onclick="{$back_button.js}"{/if}>
		<i class="process-icon-back {if isset($back_button.class)}{$back_button.class}{/if}" ></i>
		<span {if isset($back_button.force_desc) && $back_button.force_desc == true} class="locked" {/if}>{$back_button.desc}</span>
	</a>
</div>
{/if}
{/block}
{if !$simple_header}
		<input type="hidden" name="token" value="{$token|strval}" />
	</div>
{else}
	</div>
{/if}
{block name="endForm"}
</form>
{/block}

{* ----------- JavaScript -------------- *}

<script>
{* Vlastni handler pro zavisle checkboxy, volitelne s kaskadovanim *}
{if isset($xmlexport.cbx.dependent) && $xmlexport.cbx.dependent}

{if isset($xmlexport.cbx.cascade)}XmlExportModule.useCascade = {if $xmlexport.cbx.cascade}true{else}false{/if};{/if}
/* handler se nastaví jen jednou pro celou tabulku */
{if $xmlexport.context == 'service'}
	$('table.table')
	  .off('change.xmlexport', ':checkbox', XmlExportModule.checkboxHandler)
	  .on('change.xmlexport', ':checkbox', XmlExportModule.checkboxHandler);
{/if}

{if isset($xmlexport.check) && isset($xmlexport.check.table)}
XmlExportModule.lastChecked.push( { 'table' : {$xmlexport.check.table|strval}, 'ids' : {$xmlexport.check.ids|strval}, 'parentId' : {$xmlexport.check.parentId|strval} } );
{if empty($xmlexport.ajax)}
$('table.table').ready(XmlExportModule.checkLastUsed);
$('table.table').ajaxSuccess(XmlExportModule.checkLastUsed);
XmlExportModule.info.init( { 'table' : {$xmlexport.check.table|strval}, 'tree' : {$xmlexport.check.tree|strval}, 'msg' : {$xmlexport.check.text|strval}, 'msgAll' : {$xmlexport.check.textAll|strval}, 'class' : {$xmlexport.check.class|strval} } );

{/if}
{/if}

{/if} {* ---- konec handleru pro checkboxy *}

{* Oprava blbě napsaného selektoru ve funkci PS (admin.js) *}
{if $xmlexport.context == 'service'}
window.display_action_details = function(row_id, controller, token, action, params) {
	var id = action+'_'+row_id;
	var current_element = $('#details_'+id);
	var parentRow = current_element.closest('tr');

	if (!current_element.data('dataMaped')) {
		var ajax_params = {
			'id': row_id,
			'controller': controller,
			'token': token,
			'action': action,
			'ajax': true
		};

		$.each(params, function(k, v) {
			ajax_params[k] = v;
		});

		$.ajax({
			url: 'index.php',
			data: ajax_params,
			dataType: 'json',
			cache: false,
			context: current_element,
			async: false,
			success: function(data) {
				if (typeof(data.use_parent_structure) == 'undefined' || (data.use_parent_structure == true))
				{
					if (parentRow.hasClass('alt_row'))
						var alt_row = true;
					else
						var alt_row = false;
					parentRow.after($('<tr class="details_'+id+' small '+(alt_row ? 'alt_row' : '')+'"></tr>')
						.append($('<td style="border:none!important;" class="empty"></td>')
						.attr('colspan', parentRow.find('td').length)));
					$.each(data.data, function(it, row)
					{
						var bg_color = ''; // Color
						if (row.color)
							bg_color = 'style="background:' + row.color +';"';

						var content = $('<tr class="action_details details_'+id+' '+(alt_row ? 'alt_row' : '')+'"></tr>');
						content.append($('<td class="empty"></td>'));
						var first = true;
						var count = 0; // Number of non-empty collum
						$.each(row, function(it)
						{
							if(typeof(data.fields_display[it]) != 'undefined')
								count++;
						});
						$.each(data.fields_display, function(it, line)
						{
							if (typeof(row[it]) == 'undefined')
							{
								if (first || count == 0)
									content.append($('<td class="'+current_element.align+' empty"' + bg_color + '></td>'));
								else
									content.append($('<td class="'+current_element.align+'"' + bg_color + '></td>'));
							}
							else
							{
								count--;
								if (first)
								{
									first = false;
									content.append($('<td class="'+current_element.align+' first"' + bg_color + '>'+row[it]+'</td>'));
								}
								else if (count == 0)
									content.append($('<td class="'+current_element.align+' last"' + bg_color + '>'+row[it]+'</td>'));
								else
									content.append($('<td class="'+current_element.align+' '+count+'"' + bg_color + '>'+row[it]+'</td>'));
							}
						});
						content.append($('<td class="empty"></td>'));
						parentRow.after(content.show('slow'));
					});
				}
				else
				{
					if (parentRow.hasClass('alt_row'))
						var content = $('<tr class="details_'+id+' alt_row"></tr>');
					else
						var content = $('<tr class="details_'+id+'"></tr>');
					content.append($('<td style="border:none!important;">'+data.data+'</td>').attr('colspan', parentRow.find('td').length));
					parentRow.after(content);
					parentRow.parent().find('.details_'+id).hide();
				}
				current_element.data('dataMaped', true);
				current_element.data('opened', false);

				if (typeof(initTableDnD) != 'undefined')
					initTableDnD('.details_'+id+' table.tableDnD');
			}
		});
	}

	if (current_element.data('opened'))
	{
		current_element.find('i.icon-collapse-top').attr('class', 'icon-collapse');
		parentRow.parent().find('.details_'+id).hide('fast');
		current_element.data('opened', false);
	}
	else
	{
		current_element.find('i.icon-collapse').attr('class', 'icon-collapse-top');
		parentRow.parent().find('.details_'+id).show('fast');
		current_element.data('opened', true);
	}
};
{/if}
</script>

{* ----------------  Hooky --------------- *}

{hook h='displayAdminListAfter'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListAfter{/capture}
	{hook h=$hookName}
{/if}


{block name="after"}{/block}
