{* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### *}
{assign var="product_tax_brDown" value=0}
{foreach $product_tax_breakdown as $rate => $product_tax_infos}
	{assign var="product_tax_brDown" value=$product_tax_brDown+round($product_tax_infos.total_price_tax_excl*{$rate}/100, 2)}
{/foreach}

{assign var="shipping_tax_brDown" value=0}
{foreach $shipping_tax_breakdown as $shipping_tax_infos}
	{assign var="shipping_tax_brDown" value=$shipping_tax_brDown+$shipping_tax_infos.total_amount}
{/foreach}

{assign var="reduction_amnt" value=0}
{foreach $order_details as $order_detail}
	{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
		{assign var="reduction_amnt" value=1}
	{/if}
	{if (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
		{assign var="reduction_amnt" value=1}
	{/if}
{/foreach}

{assign var="sirka" value=43}
{if $tax_excluded_display}
	{assign var="sirka" value=$sirka+27}
{/if}
{if $reduction_amnt}
	{assign var="sirka" value=$sirka-7}
{/if}
{if !$reduction_amnt}
	{assign var="sirka" value=$sirka+11}
{/if}

{assign var="Price_excl" value=11}
{assign var="Tax_rate" value=7}
{assign var="Tax" value=9}
{assign var="Price_incl" value=11}
{assign var="Discount" value=7}
{assign var="Qty" value=8}
{assign var="Total" value=11}

<table cellpadding="1" cellspacing="0" align="left" style="width: 100%; font-size:9pt;">

<!-- ///////////////   dodavatel   ///////////////   -->
	<tr>
		<td rowspan="3" style="border:0px solid #000;">
			<table cellpadding="5" style="width: 100%;">
				<tr>
		   			<td style=" font-size:14pt; font-weight:bold;">{l s='Supplier:' pdf='true'}</td>
		   			<td rowspan="2" style="text-align:right">
						{if $logo_path}
		  <!-- ///////////////   logo firmy nahrajte v Konfigurace -*->šablony velikost upravte podle potřeby /////////////// -->  
							<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
						{/if}
					</td>
				</tr>
				<tr>
		   			<td style="height:60px">
					{$fa_name_shop}<br />
					{$fa_address}<br />
					{$fa_city}<br />
					{$fa_zipcode}<br />
					{$fa_country}
					</td>
				</tr>
				<tr>
		   			<td colspan="2" style="height:80px">
					{l s='DNI:' pdf='true'} {$fa_ico}<br />
					{l s='VAT:' pdf='true'} {$fa_dic}<br />
					{if ($sk)}
						{l s='IČ DPH:' pdf='true'} {$fa_icdph}<br />
					{/if}
					{l s='Web:' pdf='true'} {$fa_web}<br />
					{l s='Email:' pdf='true'} {$fa_email}<br />
					{l s='Tel.:' pdf='true'} {$fa_tel}<br /><br />
					{$fa_zapis}
					</td>
				</tr>
				<tr style=" border-top:0px dotted #000000">
		   			<td style=" text-align:right; border-top:0px dotted #000000; width:40%;">
						<br />
						{l s='Account number:' pdf='true'}<br />
						{l s='Bank name:' pdf='true'}<br />
						{l s='SWIFT:' pdf='true'}<br />
						{l s='IBAN:' pdf='true'}<br />
						{l s='Variable symbol:' pdf='true'}<br />
						{l s='Constant symbol:' pdf='true'}<br />
						{l s='Order no.:' pdf='true'}<br />
					</td>
					<td style=" text-align:left; border-top:0px dotted #000000; width:60%;">
						<br />
						{$fa_bank_number}<br />
						{$fa_bank_name}<br />
						{$fa_swift}<br />
						{$fa_iban}<br />
						{$fa_prefix_vs}{$fa_ord_inv}<br />
						{$fa_k_symbol}<br />
						#{'%06d'|sprintf:$order->id}
					</td>
				</tr>
			</table>
		
		</td>
<!-- ///////////////  konec dodavatel   ///////////////   -->

	
<!--  číslo faktury -->
		<td style="border:0px solid #000; height:20px; font-size:13pt; text-align:center; background-color:#EEE; font-weight:bold; line-height: 6px;">
		{if !$tax_excluded_display}
			{l s='Tax document no.' pdf='true'} &nbsp; {$invoice_prefix}{'%06d'|sprintf:$order->invoice_number}
		{else}
			{l s='Invoice no.' pdf='true'} &nbsp; {$invoice_prefix}{'%06d'|sprintf:$order->invoice_number}
		{/if}
		</td>
	</tr>
<!-- konec číslo faktury -->


<!-- ///////////////   Odběratel   ///////////////   -->
	<tr>
	   <td style="border-right:0px solid #000;">
			<table cellpadding="4" style="width: 100%;" border="0">
				<tr>
		   			<td colspan="2"></td><!-- mezera -->
				</tr>
				<tr>
		   			<td colspan="2" style="font-size:12pt; font-weight:bold; line-height:1px;">{l s='Subscriber:' pdf='true'}</td>
				</tr>

			{if !empty($inv_adr)}
				<tr>
		   			<td style="width:80px; height:80px;"></td><!-- mezera --><!--{if !$dlv_adr->company}--><!--{/if}-->
					<td>
						{if $dlv_adr->company}{$dlv_adr->company}<br />{/if}
						{$dlv_adr->firstname} {$dlv_adr->lastname}<br />
						{$dlv_adr->address1}<br />
						{if $dlv_adr->address2}{$dlv_adr->address2}<br />{/if}
						{$dlv_adr->postcode} &nbsp; {$dlv_adr->city}<br />
						{$dlv_adr->country}<br />
						{if $telefon}{l s='Tel.: ' pdf='true'}{$telefon}<br />{/if}
						{*if $customer->email}{$customer->email}{/if*}
					</td>
				</tr>
				<tr>
		   			<td colspan="2" style="font-weight:bold; color:#666; line-height:1px;">{l s='Billing address:' pdf='true'}</td>
				</tr>
				<tr>
		   			<td style="width:90px; height:60px;"></td><!-- mezera -->
					<td>
						{if $inv_adr->company}{$inv_adr->company}<br />{/if}
						{$inv_adr->firstname} {$inv_adr->lastname}<br />
						{$inv_adr->address1}<br />
						{if $inv_adr->address2}{$inv_adr->address2}<br />{/if}
						{$inv_adr->postcode} &nbsp; {$inv_adr->city}<br />
						{$inv_adr->country}
					</td>
				</tr>
			{else}
				<tr>
		   			<td style="width:80px; height:150px;"></td><!-- mezera --><!--{if !$dlv_adr->company}--><!--{/if}-->
					<td>
						{if $dlv_adr->company}{$dlv_adr->company}<br />{/if}
						{$dlv_adr->firstname} {$dlv_adr->lastname}<br />
						{$dlv_adr->address1}<br />
						{if $dlv_adr->address2}{$dlv_adr->address2}<br />{/if}
						{$dlv_adr->postcode} &nbsp; {$dlv_adr->city}<br />
						{$dlv_adr->country}<br />
						{if $telefon}{l s='Tel.: ' pdf='true'}{$telefon}<br />{/if}
						{*if $customer->email}{$customer->email}{/if*}
					</td>
				</tr>
			{/if}
			</table>
	   </td>
	</tr>
<!-- ///////////////  konec Odběratel   ///////////////   -->


<!-- ///////////////   datum vystavení  atd.   ///////////////   -->
	<tr>
		<td style="border-right:0px solid #000; border-top:0px dotted #000000">
		<table>
			<tr style=" text-align:left">
				<td colspan="3" style="height:6px;"></td><!-- mezera -->
			</tr>
			<tr style=" text-align:left">
				<td rowspan="3" style="width:2%;"></td><!-- mezera -->
				
				<td style="width:45%;">{if $ic}{l s='DNI:' pdf='true'} {$ic}{/if}</td>
				<td style="width:30%;">{l s='Date of issue:' pdf='true'}</td>
				<td>{dateFormat date=$invoice_date full=0}</td>
			</tr>
			<tr style=" text-align:left">
		   		<td>{if $sk}
						{if $dic !=''}
							{l s='VAT:' pdf='true'} {$dic}
						{elseif $icdph !=''}
							{l s='ič DPH:' pdf='true'} {$icdph}
						{/if}
					{else}
							{if $dic}{l s='VAT:' pdf='true'} {$dic}{/if}
					{/if}
				</td>
				<td>{if !$tax_excluded_display}{l s='Date chargeable event:' pdf='true'}{/if}</td>
				<td>{if !$tax_excluded_display}{dateFormat date=$invoice_date full=0}{/if}</td>
			</tr>
			<tr style=" text-align:left">
		   		<td></td>
				<td>{if $date_due}{l s='Date of maturity:' pdf='true'}{/if}</td>
				<td>{if $date_due}{$date_due|date_format:"%d.%m.%Y"}{/if}</td>
			</tr>
			<tr style=" text-align:left">
				<td colspan="3" style="height:6px;"></td><!-- mezera -->
			</tr>
		</table>
		</td>
	</tr>
<!-- ///////////////  konec datum vystavení  atd.   ///////////////   -->


<!-- ///////////////   doprava  atd.   ///////////////   -->
	<tr>
	   <td colspan="2" style="border:0px solid #000;">
		<table cellpadding="4">
			<tr>
				<td rowspan="2" style="width:2%;"></td><!-- mezera -->
				
				<td style="width:15%;">{l s='Carrier:' pdf='true'}</td>
				<td style="width:35%;">{$doprava}</td>
				<td style="width:17%;">{l s='Payment method:' pdf='true'}</td>
				<td style="width:30%;">{$order->payment}</td>
			</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec doprava  atd.   ///////////////   -->
	
<!--  malá mezera -->
	<tr>
	   <td colspan="2" style="border:0px solid #000; height:2px; font-size:1px;"></td>

	</tr>
<!-- konec malá mezera -->

<!-- ///////////////   záhlaví tabulky produktů   ///////////////   -->
	<tr>
	   <td colspan="2" style="border:0px solid #000;">
			<table cellpadding="2" style="width: 100%; font-size: 8pt;">
				<tr style="line-height:4px; background-color: #999; color: #FFF; text-align: right; font-weight: bold;">
					<td style="text-align: left; width: {$sirka}%; line-height:6px;"> &nbsp; {l s='Product / Reference' pdf='true'}</td>
					{if $reduction_amnt}
						<td style="width: {$Price_incl}%">
							{l s='Base Price' pdf='true'}
							{if !$tax_excluded_display}
							 	{l s='(Tax Excl.)' pdf='true'}
							{/if}
						</td>
						<td style="width: {$Discount}%;">{l s='Discount' pdf='true'}</td>
					{/if}
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style=" width: {$Price_excl}%">{l s='Unit Price' pdf='true'}<br />{l s='(Tax Excl.)' pdf='true'}</td>
					{/if}
					{if !$tax_excluded_display}
						<td style="width: {$Tax_rate}%;">{l s='Tax Rate' pdf='true'}</td>
						<td style="width: {$Tax}%; text-align:center;">{l s='Tax' pdf='true'}</td>
					{/if}
					<td style="width: {$Qty}%; text-align:center;">{l s='Qty' pdf='true'}</td>
					<td style="width: {$Total}%;">
						{l s='Total' pdf='true'}<br />
						{if $tax_excluded_display}
							<!--{l s='(Tax Excl.)' pdf='true'}-->
						{else}
							{l s='(Tax Incl.)' pdf='true'}
						{/if}
					</td>
				</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec záhlaví tabulky produktů   ///////////////   -->


<!-- ///////////////   detaily tabulky produktů   ///////////////   -->
	<tr>
		<td colspan="2" style="border:0px solid #000;">
			<table cellpadding="3" style="width:100%; font-size: 8pt; text-align: right;">
			{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:4px; background-color:{$bgcolor};">
					<td style="text-align:left; width:{$sirka}%;">
						{$order_detail.product_name}{if $order_detail.product_reference}<br />{$order_detail.product_reference}{/if}
					</td>
					{if $reduction_amnt}
						<td style="width:{$Price_incl}%;">
							{if ($order_detail.reduction_amount > 0 || $order_detail.reduction_percent > 0)}
								{number_format($order_detail.original_product_price, 2, ',', ' ')} {$currency->sign}
							{/if}
						</td>
						<td style="width:{$Discount}%;">
						{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
							-{number_format($order_detail.reduction_amount, 2, ',', ' ')} {$currency->sign}
						{else if (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
							-{$order_detail.reduction_percent}%
						{/if}
						</td>
					{/if}
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style=" width:{$Price_excl}%;">
					{number_format($order_detail.unit_price_tax_excl, 2, ',', ' ')} {$currency->sign}
						</td>
					{/if}
					{if !$tax_excluded_display}
					<td style="width:{$Tax_rate}%;">{Tax::getProductTaxRate({$order_detail.id_product})} %</td>
					<td style="width:{$Tax}%;">{number_format($order_detail.unit_price_tax_incl-$order_detail.unit_price_tax_excl, 2, ',', ' ')} {$currency->sign}</td>
					{/if}
					<td style="text-align:center; width:{$Qty}%;">{$order_detail.product_quantity}</td>
					<td style="width:{$Total}%;">
					{if $tax_excluded_display}
						{number_format($order_detail.total_price_tax_excl, 2, ',', ' ')} {$currency->sign}
					{else}
						{number_format($order_detail.total_price_tax_incl, 2, ',', ' ')} {$currency->sign}
					{/if}
					</td>
				</tr>
		{foreach $order_detail.customizedDatas as $customizationPerAddress}
			{foreach $customizationPerAddress as $customizationId => $customization}
				<tr style="line-height:6px;background-color:{$bgcolor}; ">
					<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
						<blockquote>
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
							{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
								{$customization_infos.name}: {$customization_infos.value}
								{if !$smarty.foreach.custo_foreach.last}<br />
								{else}
									<div style="line-height:0.4pt">&nbsp;</div>
								{/if}
							{/foreach}
						{/if}
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
							{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)' pdf='true'}
						{/if}
						</blockquote>
					</td>
					<td style="text-align: right; width: 15%"></td>
					<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
					<td style="width: 15%; text-align: right;"></td>
				</tr>
			{/foreach}
		{/foreach}
			{/foreach}
				<!-- END PRODUCTS -->

			{if $order_invoice->total_discount_tax_incl > 0}
            	{if $bgcolor=='#FFF'}{assign var="bgcolor" value='#DDD'}{else}{assign var="bgcolor" value='#FFF'}{/if}
				<tr style="background-color:{$bgcolor};">
					<td style="text-align: left; width: {$sirka}%;">{l s='Vouchers' pdf='true'}<br />{$order_cart_rule->name}</td>
					{if $reduction_amnt}
						<td style="width: {$Price_incl}%;">
							{*{if $tax_excluded_display}
								- {number_format($order_invoice->total_discount_tax_excl, 2, ',', ' ')} {$currency->sign}
							{else}
								- {number_format($order_invoice->total_discount_tax_incl, 2, ',', ' ')} {$currency->sign}
							{/if}*}
						</td>
						<td style="width: {$Discount}%;"></td>
					{/if}
					{if !$tax_excluded_display}
						<td style="width: {$Price_excl}%;">
							- {number_format($order_invoice->total_discount_tax_excl, 2, ',', ' ')} {$currency->sign}
						</td>
					{/if}
					
					{if !$tax_excluded_display}
						<td style="text-align: center; width: {$Tax_rate}%;"> -- </td>
						<td style="width: {$Tax}%">
							- {number_format($order_invoice->total_discount_tax_incl-$order_invoice->total_discount_tax_excl, 2, ',', ' ')} {$currency->sign}
					</td>
					{/if}
					<td style="text-align: center; width: {$Qty}%;">1</td>
					<td style="width: {$Total}%;">
						{if $tax_excluded_display}
							- {number_format($order_invoice->total_discount_tax_excl, 2, ',', ' ')} {$currency->sign}
						{else}
							- {number_format($order_invoice->total_discount_tax_incl, 2, ',', ' ')} {$currency->sign}
						{/if}
					</td>
				</tr>
			{/if}
			
			{if $order_invoice->total_shipping_tax_incl > 0}
				<tr style="background-color:{if $bgcolor == '#FFF'}#DDD{else}#FFF{/if};">
					<td style="text-align: left; width: {$sirka}%;">{l s='Shipping Cost' pdf='true'}</td>
					{if $reduction_amnt}
						<td style="width: {$Price_incl}%;">
							{*{if $tax_excluded_display}
								{number_format($order_invoice->total_shipping_tax_excl, 2, ',', ' ')} {$currency->sign}
							{else}
								{number_format($order_invoice->total_shipping_tax_incl, 2, ',', ' ')} {$currency->sign}
							{/if}*}
						</td>
						<td style="width: {$Discount}%;"></td>
					{/if}
					{if !$tax_excluded_display}
						<td style="width: {$Price_excl}%;">
							{number_format($order_invoice->total_shipping_tax_excl, 2, ',', ' ')} {$currency->sign}
						</td>
					{/if}
					
					{if !$tax_excluded_display}
						<td style="width: {$Tax_rate}%;">{Tax::getCarrierTaxRate({$order->id_carrier}, {$order->id_address_delivery})} %</td>
						<td style="width: {$Tax}%;">{foreach $shipping_tax_breakdown as $shipping_tax_infos}{if isset($is_order_slip) && $is_order_slip}- {/if}{number_format($shipping_tax_infos.total_amount, 2, ',', ' ')} {$currency->sign}{/foreach}</td>
					{/if}
					<td style="text-align: center; width: {$Qty}%;">1</td>
					<td style="width: {$Total}%;">
						{if $tax_excluded_display}
							{number_format($order_invoice->total_shipping_tax_excl, 2, ',', ' ')} {$currency->sign}
						{else}
							{number_format($order_invoice->total_shipping_tax_incl, 2, ',', ' ')} {$currency->sign}
						{/if}
					</td>
				</tr>
			{/if}
			</table>
		</td>
	</tr>
<!-- ///////////////  konec detaily tabulky produktů   ///////////////   -->


<!-- /////////////// soupis cen   ///////////////  -->
	<tr>
	  <td colspan="2" align="right" style="border-right:0px solid #000; weight:50%;">
	   	<table cellpadding="2" style="line-height:3px; text-align: right; font-weight:bold; font-size:7pt;">
			<tr style="line-height:0px;">
				<td rowspan="20" style="width:60%;"></td>
				<td style="width:22%;"></td>
				<td style="width:18%;"></td>
			</tr>
			{if $tax_excluded_display}
				<tr>
					<td>{l s='Product Total' pdf='true'}</td>
					<td>{number_format($order_invoice->total_products, 2, ',', ' ')} {$currency->sign}</td>
				</tr>
			{else}
				<tr>
					<td>{l s='Product Total (Tax Excl.)' pdf='true'}</td>
					<td>{number_format($order_invoice->total_products, 2, ',', ' ')} {$currency->sign}</td>
				</tr>
				<tr>
					<td>{l s='Product Total (Tax Incl.)' pdf='true'}</td>
					<td>{number_format($order_invoice->total_products_wt, 2, ',', ' ')} {$currency->sign}</td>
				</tr>
			
				{if (($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0)}
					<tr>
						<td>{l s='Total (Tax Excl.)' pdf='true'}</td>
						<td>{number_format($order_invoice->total_paid_tax_incl- ($product_tax_brDown + $shipping_tax_brDown), 2, ',', ' ')} {$currency->sign}</td>
					</tr>
					<tr>
						<td>{l s='Total Tax' pdf='true'}</td>
						<td>{number_format($product_tax_brDown + $shipping_tax_brDown, 2, ',', ' ')} {$currency->sign}</td>
					</tr>
				{/if}
			{/if}

			{if $order_invoice->total_wrapping_tax_incl > 0}
				<tr>
					<td>{l s='Wrapping Cost' pdf='true'}</td>
					<td>
						{if $tax_excluded_display}
							{number_format($order_invoice->total_wrapping_tax_excl, 2, ',', ' ')} {$currency->sign}
						{else}
							{number_format($order_invoice->total_wrapping_tax_incl, 2, ',', ' ')} {$currency->sign}
						{/if}
					</td>
				</tr>
			{/if}

			{if (Tools::ps_round($order_invoice->total_paid_tax_incl)-$order_invoice->total_paid_tax_incl <> 0) && $round_}
				<tr>
					<td>{l s='Rounding' pdf='true'}</td>
					{if $tax_excluded_display}
						<td>{number_format(Tools::ps_round($order_invoice->total_paid_tax_excl)-$order_invoice->total_paid_tax_excl, 2, ',', ' ')} {$currency->sign}</td>
					{else}
						<td>{number_format(Tools::ps_round($order_invoice->total_paid_tax_incl)-$order_invoice->total_paid_tax_incl, 2, ',', ' ')} {$currency->sign}</td>
					{/if}
				</tr>
			{/if}

			<tr style="background-color: #EEE; font-size:10pt; line-height:5px;">
				<td style="width:22%; border-top:1px solid #000; border-left:1px solid #000; border-bottom:1px solid #000;">
					{l s='Total' pdf='true'}
				</td>
				<td style="width:18%; border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000;">
				{if $round_}
					{if $tax_excluded_display}
						{number_format(Tools::ps_round($order_invoice->total_paid_tax_excl), 2, ',', ' ')} {$currency->sign}
					{else}
						{number_format(Tools::ps_round($order_invoice->total_paid_tax_incl), 2, ',', ' ')} {$currency->sign}
					{/if}
				{else}
					{if $tax_excluded_display}
						{number_format($order_invoice->total_paid_tax_excl, 2, ',', ' ')} {$currency->sign}
					{else}
						{number_format($order_invoice->total_paid_tax_incl, 2, ',', ' ')} {$currency->sign}
					{/if}
				{/if}
				</td>
			</tr>
		</table>
			
	  </td>
	</tr>
</table>
<!-- /////////////// konec soupis cen   ///////////////  -->



		<div>
		{if !$tax_excluded_display}
			{$tax_tab}
		{else}
			<table style="width: 55%; font-size:7pt;">
				<tr style="line-height:14px;"><td style="width: 15%">&nbsp;</td></tr>
			</table>        	
		{/if}
		<!-- razítko -->
		{if $razitko_path}
		<table style="width: 100%; line-height:3px; text-align: right; font-weight:bold; font-size:7pt;">
			<tr>
				<td style="width:90%; height:10px;">{l s='Signature and stamp:' pdf='true'}</td>
				<td style="width:10%;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" height="62px;">
					<img src="{$razitko_path}" style="width:120px; height:62px;" />
				</td>
			</tr>
		</table>
		{/if}
		</div>

{if isset($order_invoice->note) && $order_invoice->note}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$order_invoice->note|nl2br}</td>
	</tr>
</table>
{/if}

{if isset($HOOK_DISPLAY_PDF)}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$HOOK_DISPLAY_PDF}</td>
	</tr>
</table>
{/if}

{if $poznamka}
<table style="width:100%;" valign="bottom">
	<tr>
		<td style="font-size:7pt; width:100%;line-height:5px;">
				<b>{l s='Poznámka k objednávce: ' pdf='true'}</b><br>{$poznamka}
		</td>
	</tr>
</table>
{/if}