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

			</table>
			{if $bulk_actions}
				<p>
					{foreach $bulk_actions as $key => $params}
						<input type="submit" class="button" name="submitBulk{$key}{$table}" value="{$params.text}" {if isset($params.confirm)}onclick="return confirm('{$params.confirm}');"{/if} />
					{/foreach}
				</p>
			{/if}
		</td>
	</tr>
</table>
{if !$simple_header}
	<input type="hidden" name="token" value="{$token|escape}" />
	</form>
{/if}

{* Vlastni handler pro zavisle checkboxy, volitelne s kaskadovanim *}
{if isset($xmlexport.cbx.dependent) && $xmlexport.cbx.dependent}
<script>
{if isset($xmlexport.cbx.cascade)}XmlExportModule.useCascade = {if $xmlexport.cbx.cascade}true{else}false{/if};{/if}
/* zabranuje duplicitnim handlerum */
$('table.table')
  .off('click.xmlexport', ':checkbox', XmlExportModule.checkboxHandler)
  .on('click.xmlexport', ':checkbox', XmlExportModule.checkboxHandler);

{if isset($xmlexport.check) && isset($xmlexport.check.table)}
XmlExportModule.lastChecked.push( { 'table' : {$xmlexport.check.table|strval}, 'ids' : {$xmlexport.check.ids|strval}, 'parentId' : {$xmlexport.check.parentId|strval} } );
{if empty($xmlexport.ajax)}
$('table.table').ready(XmlExportModule.checkLastUsed);
$('table.table').ajaxSuccess(XmlExportModule.checkLastUsed);
XmlExportModule.info.init( { 'table' : {$xmlexport.check.table|strval}, 'tree' : {$xmlexport.check.tree|strval}, 'msg' : {$xmlexport.check.text|strval}, 'msgAll' : {$xmlexport.check.textAll|strval}, 'class' : {$xmlexport.check.class|strval} } );

{/if}
{/if}

</script>
{/if}

{hook h='displayAdminListAfter'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListAfter{/capture}
	{hook h=$hookName}
{/if}


{block name="after"}{/block}