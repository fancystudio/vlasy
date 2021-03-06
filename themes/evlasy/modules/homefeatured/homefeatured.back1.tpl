{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script>
 $(document).ready(function() {
    $('.carousel').carousel({
      interval: 6000
    })
  });
</script>



{counter name=active_ul assign=active_ul}
{if isset($products50) && $products50}
	{assign var="products" value=$products50}
	<div class="products50Homepage">
	
	<div id="myCarousel" class="carousel slide">
	{include file="$tpl_dir./product-list.tpl" class='homefeatured tab-pane' id='homefeatured' active=$active_ul}
	</div>
	
	
	</div>
	{assign var="products" value=$products60}
	<div class="products60Homepage">
	{include file="$tpl_dir./product-list.tpl" class='homefeatured tab-pane' id='homefeatured' active=$active_ul}
	</div>
{else}
<ul id="homefeatured" class="homefeatured tab-pane{if isset($active_ul) && $active_ul == 1} active{/if}">
	<li class="alert alert-info">{l s='No featured products at this time.' mod='homefeatured'}</li>
</ul>
{/if}