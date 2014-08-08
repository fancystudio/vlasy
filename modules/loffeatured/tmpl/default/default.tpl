<div class="clearfix clear clr"></div>
<div class="lof-featured {$theme}">
	<section class="featured-widget">
		<header>
			<h3 class="featured-title">50 CM</h3>
		</header>
		<div class="list-featured responsive">
			<ul id="loffeatured-{$moduleId}" class="featured-news clearfix">
				{foreach from=$listFeature item=item}
					{if $item.category_default == '50CM'}
					<li>
						<article>
							<div class="featured-item box-hover clearfix">
								<div class="entry-content">
									<div class="video-thumb">
										<a href="{$item.link}" title="{$item.name}" class="product_image" target="{$target}">
										<img class="responsive-img" src="{$item.mainImge}" alt="{$item.name}"/>
										</a>
									</div>
									{if $show_title eq '1'}
									<h4 class="entry-title">
										<a href="{$item.link}" target="{$target}" title="{$item.name}">{$item.name}</a>
									</h4>
									{/if}
									{if is_array($item.specific_prices) AND ($priceSpecial  eq '1')}
									<div class="entry-price entry-price-discount"><strike>{displayWtPrice p=$item.price_without_reduction}</strike></div>
									{/if}
									{if $show_price eq '1'}<div class="entry-price">{$item.price}</div>{/if}
									{if $show_desc eq '1'}<p>{$item.description}</p>{/if}
	
									{if (($item.quantity > 0 OR $item.allow_oosp))}
									<div class="lof-main-puplic">
										<a class="lof-add-cart ajax_add_to_cart_button" rel="ajax_id_product_{$item.id_product}" href="{$site_url}cart.php?add&amp;id_product={$item.id_product}&amp;token={$token}"><span>{l s='Add to cart' mod='loffeatured'}</span></a>
									</div>
									{else}
										<div class="lof-main-puplic"><a><span class="lof-add-cart">{l s='Add to cart' mod='loffeatured'}</span></a></div>
									{/if}
								</div>
							</div>
						</article>				
					</li>
					{/if}
				{/foreach}
			</ul>
			{if $show_button eq '1'}
			<div class="featured-nav">
				<a id="loffprev-{$moduleId}" class="prev" href="#">&nbsp;</a>
				<a id="loffnext-{$moduleId}" class="next" href="#">&nbsp;</a>
			</div>
			{/if}
			{if $show_pager eq '1'}<div id="loffpager-{$moduleId}" class="lof-pager"></div>{/if}
		</div>
	</section>
</div>
<div class="clearfix clear clr"></div>
<div class="lof-featured {$theme}">
	<section class="featured-widget">
		<header>
			<h3 class="featured-title">60 CM</h3>
		</header>
		<div class="list-featured responsive">
			<ul id="loffeatured2-{$moduleId}" class="featured-news clearfix">
				{foreach from=$listFeature item=item}    
					{if $item.category_default == '60CM'}
					<li>
						<article>
							<div class="featured-item box-hover clearfix">
								<div class="entry-content">
									<div class="video-thumb">
										<a href="{$item.link}" title="{$item.name}" class="product_image" target="{$target}">
										<img class="responsive-img" src="{$item.mainImge}" alt="{$item.name}"/>
										</a>
									</div>
									{if $show_title eq '1'}
									<h4 class="entry-title">
										<a href="{$item.link}" target="{$target}" title="{$item.name}">{$item.name}</a>
									</h4>
									{/if}
									{if is_array($item.specific_prices) AND ($priceSpecial  eq '1')}
									<div class="entry-price entry-price-discount"><strike>{displayWtPrice p=$item.price_without_reduction}</strike></div>
									{/if}
									{if $show_price eq '1'}<div class="entry-price">{$item.price}</div>{/if}
									{if $show_desc eq '1'}<p>{$item.description}</p>{/if}
	
									{if (($item.quantity > 0 OR $item.allow_oosp))}
									<div class="lof-main-puplic">
										<a class="lof-add-cart ajax_add_to_cart_button" rel="ajax_id_product_{$item.id_product}" href="{$site_url}cart.php?add&amp;id_product={$item.id_product}&amp;token={$token}"><span>{l s='Add to cart' mod='loffeatured'}</span></a>
									</div>
									{else}
										<div class="lof-main-puplic"><a><span class="lof-add-cart">{l s='Add to cart' mod='loffeatured'}</span></a></div>
									{/if}
								</div>
							</div>
						</article>				
					</li>
					{/if}
				{/foreach}
			</ul>
			{if $show_button eq '1'}
			<div class="featured-nav">
				<a id="loffprev2-{$moduleId}" class="prev" href="#">&nbsp;</a>
				<a id="loffnext2-{$moduleId}" class="next" href="#">&nbsp;</a>
			</div>
			{/if}
			{if $show_pager eq '1'}<div id="loffpager2-{$moduleId}" class="lof-pager"></div>{/if}
		</div>
	</section>
</div> 
  <script type="text/javascript">
// <![CDATA[
			$('#loffeatured-{$moduleId}').carouFredSel({ldelim}
				responsive:true,
				circular: false,
			    infinite: false,
				prev: '#loffprev-{$moduleId}',
				next: '#loffnext-{$moduleId}',
				pagination: "#loffpager-{$moduleId}",
				auto: {$auto_play},
				width: {$slide_width},
				height: {$slide_height},
				scroll: {$scroll_items},
				items:{ldelim}
					width:200,
					visible:{ldelim}
						min:1,
						max:{$limit_cols}
					{rdelim}
				{rdelim}
			{rdelim});	
			$('#loffeatured2-{$moduleId}').carouFredSel({ldelim}
			responsive:true,
			circular: false,
		    infinite: false,
			prev: '#loffprev2-{$moduleId}',
			next: '#loffnext2-{$moduleId}',
			pagination: "#loffpager2-{$moduleId}",
			auto: {$auto_play},
			width: {$slide_width},
			height: {$slide_height},
			scroll: {$scroll_items},
			items:{ldelim}
				width:200,
				visible:{ldelim}
					min:1,
					max:{$limit_cols}
				{rdelim}
			{rdelim}
		{rdelim});	
		 
// ]]>

</script>
