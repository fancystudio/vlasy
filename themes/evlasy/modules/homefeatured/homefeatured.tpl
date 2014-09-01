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
<link href="modules/homefeatured/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
(function($) {
    $(function() {
        var jcarousel50 = $('.jcarousel50');
        var jcarousel60 = $('.jcarousel60');
        
        jcarousel50
            .on('jcarousel:reload jcarousel:create', function () {
                var width = jcarousel50.innerWidth();

                if (width >= 600) {
                    width = width / 3;
                } else if (width >= 350) {
                    width = width / 2;
                }

                jcarousel50.jcarousel('items').css('width', width + 'px');
            })
            .jcarousel();
        
        jcarousel60
	        .on('jcarousel:reload jcarousel:create', function () {
	            var width = jcarousel60.innerWidth();
	
	            if (width >= 600) {
	                width = width / 4;
	            } else if (width >= 350) {
	                width = width / 2;
	            }
	
	            jcarousel60.jcarousel('items').css('width', width + 'px');
	        })
	        .jcarousel();
        
        $('.jcarousel50-control-prev, .jcarousel60-control-prev')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '-=1'
        });

    $('.jcarousel50-control-next, .jcarousel60-control-next')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '+=1'
        });
    });
})(jQuery);
</script>
{counter name=active_ul assign=active_ul}
{if isset($products50) && $products50}
	{assign var="products" value=$products50}
	<div class="jcarousel50">
	{include file="$tpl_dir./product-list.tpl" class='homefeatured tab-pane' id='homefeatured' active=$active_ul}
	</div>
	<a href="#" class="jcarousel50-control-prev">&lsaquo;</a>
    <a href="#" class="jcarousel50-control-next">&rsaquo;</a>           
	{assign var="products" value=$products60}
	<div class="jcarousel60">
	{include file="$tpl_dir./product-list.tpl" class='homefeatured tab-pane' id='homefeatured' active=$active_ul}
	</div>
	<a href="#" class="jcarousel60-control-prev">&lsaquo;</a>
    <a href="#" class="jcarousel60-control-next">&rsaquo;</a> 
{else}
<ul id="homefeatured" class="homefeatured tab-pane{if isset($active_ul) && $active_ul == 1} active{/if}">
	<li class="alert alert-info">{l s='No featured products at this time.' mod='homefeatured'}</li>
</ul>
{/if}