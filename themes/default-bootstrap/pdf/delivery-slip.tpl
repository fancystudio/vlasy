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
<table cellpadding="0" cellspacing="0" align="left" style="width: 100%; font-size:9pt;">

<!-- ///////////////   dodavatel   ///////////////   -->
	<tr>
		<td rowspan="3" style="border:0px solid #000;"><table cellpadding="5" style="width: 100%;">
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
						<br /><br />
						{l s='Account number:' pdf='true'}<br />
						{l s='Bank name:' pdf='true'}<br />
						{l s='SWIFT:' pdf='true'}<br />
						{l s='IBAN:' pdf='true'}<br />
						{l s='Variable symbol:' pdf='true'}<br />
						{l s='Constant symbol:' pdf='true'}<br />
					</td>
					<td style=" text-align:left; border-top:0px dotted #000000; width:60%;">
						<br /><br />
						{$fa_bank_number}<br />
						{$fa_bank_name}<br />
						{$fa_swift}<br />
						{$fa_iban}<br />
						{$fa_prefix_vs}{'%06d'|sprintf:$fa_ord_inv}<br />
						{$fa_k_symbol}
					</td>
				</tr>
			</table>
		
		</td>
<!-- ///////////////  konec dodavatel   ///////////////   -->

	
<!--  číslo faktury -->
		<td style="border:0px solid #000; height:20px; font-size:13pt; text-align:center; background-color:#EEE; font-weight:bold; line-height: 6px;">
		{l s='Delivery slip no.' pdf='true'} &nbsp; {$delivery_prefix}{'%06d'|sprintf:$order->delivery_number}</td>
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
						{if $customer->email}{$customer->email}{/if}
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
						{if $customer->email}{$customer->email}{/if}
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
				{*<td>{if !$tax_excluded_display}{l s='Date chargeable event:' pdf='true'}{/if}</td>
				<td>{if !$tax_excluded_display}{dateFormat date=$invoice_date full=0}{/if}</td>*}
			</tr>
			<tr style=" text-align:left">
		   		<td></td>
				{*<td>{if $date_due}{l s='Date of maturity:' pdf='true'}{/if}</td>
				<td>{if $date_due}{$date_due|date_format:"%d.%m.%Y"}{/if}</td>*}
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
				<tr style="line-height:4px; background-color: #999; color: #FFF; text-align: left; font-weight: bold;">
					<td style="width: 60%; line-height:6px;"> &nbsp; {l s='Product' pdf='true'}</td>
					<td style="width: 20%;">{l s='Reference' pdf='true'}</td>
					<td style="width: 20%; text-align:center;">{l s='Qty' pdf='true'}</td>
				</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec záhlaví tabulky produktů   ///////////////   -->


<!-- ///////////////   detaily tabulky produktů   ///////////////   -->
	<tr>
		<td colspan="2" style="border:0px solid #000;">
			<table cellpadding="2" cellspacing="1">
			{foreach $order_details as $product}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:4px;background-color:{$bgcolor};">
					<td style="text-align: left; width: 60%">{$product.product_name}</td>
					<td style="text-align: left; width: 20%">
						{if empty($product.product_reference)}
							---
						{else}
							{$product.product_reference}
						{/if}
					</td>
					<td style="text-align: center; width: 20%">{$product.product_quantity}</td>
				</tr>
			{/foreach}
			<!-- END PRODUCTS -->
			</table>
		</td>
	</tr>
<!-- ///////////////  konec detaily tabulky produktů   ///////////////   -->


<!-- ///////////////   razítka  a  podpisy   ///////////////   -->
	<tr>
	   <td colspan="2" style="border:0px solid #000;">
	   	<table cellpadding="2" cellspacing="1">
			<tr>
				<td style="width:40%">
					{l s='He gave:' pdf='true'}
					{if $razitko_path}<br><img src="{$razitko_path}" style="width:120px; height:62px;" />{/if}
				</td>
				<td style="width:30%">
					{l s='He took:' pdf='true'}
				</td>
				<td style="width:30%">
					{l s='Date:' pdf='true'}
				</td>
			</tr>
		</table>
	   </td>
	</tr>
<!-- ///////////////  konec  razítka  a  podpisy   ///////////////   -->
</table>
