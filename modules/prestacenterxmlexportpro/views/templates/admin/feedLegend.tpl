
{**
* 2012-2014 PrestaCS, PrestaCenter - Anatoret plus s.r.o.
*
* PrestaCenter XML Export Pro
*
* Module PrestaCenter XML Export Pro – version for PrestaShop 1.5 and 1.6
* Modul PrestaCenter XML Export Pro – verze pro PrestaShop 1.5 a 1.6
*
* PrestaCenter - modules and customization for PrestaShop
* PrestaCS - moduly, česká lokalizace a úpravy pro PrestaShop
* http://www.prestacs.cz
*
* @author    PrestaCenter <info@prestacenter.com>
* @category  others
* @package   prestacenterxmlexportpro
* @copyright 2012-2014 PrestaCenter - Anatoret plus s.r.o.
* @license   see file licence-prestacenter.html
*}

{**
 * @since 1.5.0
 * @version 1.2.4.2 (2014-07-07)
*}

{*
Popis proměnných, které jsou použitelné v šabloně XML feedu. Musí korespondovat s polem PrestaCenterXmlExportPro->allowedProperties.
Description of variables/placeholders you can use in XML feed template. All variables must be defined in PrestaCenterXmlExportPro->allowedProperties array.
*}
<b>{l s='XML template' mod='prestacenterxmlexportpro'}</b><br />
<span id="prestacenterxmlexportproblock1"><br />
<b>{l s='General Information' mod='prestacenterxmlexportpro'}</b><br />
<br />
{l s='For the element that marks the product in the feed (e.g. SHOPITEM), mark it with the "ps_block" attribute with the "product" value (e.g. %s).' sprintf='&lt;SHOPITEM ps_block="product"&gt;' mod='prestacenterxmlexportpro'}<br />
{l s='For values of the individual elements, you can use the following variables (wildcards), including the braces:' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{shop_name}{/literal}</b> - {l s='Shop name' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{shop_url}{/literal}</b> - {l s='Shop URL' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{id}{/literal}</b> - {l s='Product ID (according to the database)' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{name}{/literal}</b> - {l s='Product name' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{manufacturer}{/literal}</b> - {l s='Manufacturer name' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{ean}{/literal}</b> - {l s='EAN13 code' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{upc}{/literal}</b> - {l s='UPS code' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{reference}{/literal}</b> - {l s='Reference' mod='prestacenterxmlexportpro'}<br />
{*<b>{literal}{supplier_reference}{/literal}</b> - {l s='Supplier reference' mod='prestacenterxmlexportpro'}<br />*}
<b>{literal}{description}{/literal}</b> - {l s='Product description' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{description_short}{/literal}</b> - {l s='Short product description.' mod='prestacenterxmlexportpro'}
{l s='You can trim the description to the specified number of characters, for example:' mod='prestacenterxmlexportpro'}
<b>{literal}{description_short: "60"}{/literal}</b><br />
<b>{literal}{quantity}{/literal}</b> - {l s='Product quantity' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{categories}{/literal}</b> - {l s='List of the product categories (e.g. "Clothing | Women | Summer | Swimwear"). The separator can specify a different character, such as:' mod='prestacenterxmlexportpro'}
<b>{literal}{categories: "&gt;"}{/literal}</b><br />
<b>{literal}{url}{/literal}</b> - {l s='Product URL (in shop)' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{img_url}{/literal}</b> - {l s='Product image URL' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{condition}{/literal}</b> - {l s='Status of the product (new, used, refurbished). You can use your own values in the feed: enter your own values after the colon, separate values with a decimal point, also, enter your own values in this order "new", "sale", "refurbished product", such as:' mod='prestacenterxmlexportpro'}
<b>{literal}{condition: "new,bazaar,bazaar"}{/literal}</b>.<br />
<b>{literal}{price_vat}{/literal}</b> - {l s='Price tax included as a decimal number. For example: "25.50"' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{price_vat_local}{/literal}</b> - {l s='Price tax included as a decimal number, including the currency sign. For example as "$25.50".' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{price_vat_iso}{/literal}</b> - {l s='Price tax included as a decimal number, including the relevant ISO code for the currency (as per ISO 4217). For example as "25.50 USD".' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{price}, {price_local}, {price_iso}{/literal}</b> - {l s='Price without tax as a decimal number.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{wholesale_price}{/literal}</b> - {l s='Wholesale price without tax as a decimal number.' mod='prestacenterxmlexportpro'}<br />
{l s='Variables %1$s are displayed with decimal point. If you want to use different character, such as comma, write it as a parameter: %2$s' sprintf=['{price}, {price_iso}, {price_vat}, {price_vat_iso}, {wholesale_price}, {vat}', '<b>{price: ","}</b>']  mod='prestacenterxmlexportpro'}<br />
<b>{literal}{vat}{/literal}</b> - {l s='Tax rate as a decimal number.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{online_only}{/literal}</b> - {l s='Products is available only in the e-shop (= 1) or in the brick and mortar store (= 0).' mod='prestacenterxmlexportpro'}<br />
{*<b>{literal}{update_item}{/literal}</b> - {l s='Date and time when the product was updated (in GMT). The default format is Atom 1.0 (e.g. "2012-12-08T14:29:57+00:00"), but you can enter your own format (see PHP date() function). Example of the custom format:' mod='prestacenterxmlexportpro'} <b>{literal}{update_item: "Y/m/d H:i:s"}{/literal}</b><br />
<b>{literal}{update_feed}{/literal}</b> - {l s='Date and time when the file was created (GMT). You can use custom format.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{update_item_local}, {update_feed_local}{/literal}</b> - {l s='Local date and time of creating the feed or last update of the product. You can use custom format.' mod='prestacenterxmlexportpro'}<br />*}
<b>{literal}{date_feed}{/literal}</b> - {l s='Date and time when the feed was created.' mod='prestacenterxmlexportpro'} <br />
<b>{literal}{date_add}{/literal}</b> - {l s='Date and time when the product was added in shop.' mod='prestacenterxmlexportpro'} <br />
<b>{literal}{date_upd}{/literal}</b> - {l s='Date and time when the product was updated in shop.' mod='prestacenterxmlexportpro'}<br />
{l s='All date and time variables use as default Atom 1.0 format (e.g. "2012-12-08T14:29:57+00:00"). It is possible to use custom format - see PHP date() function. Two modifiers are available - LOCAL (default) for local time and GMT for Greenwich Mean Time. Samples:' mod='prestacenterxmlexportpro'} <b>{literal}{date_feed: GMT}, {date_add: "Y-m-d H:i:s": LOCAL}, {date_upd: "d.m.Y H:i:s"}{/literal}</b><br />
<b>{literal}{lang_code}{/literal}</b> - {l s='The language code of the feed (e.g. "en-us" for American English).' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{lang_code_iso}{/literal}</b> - {l s='Two-letter language code according to ISO 639-1 (e.g. "en" for English).' mod='prestacenterxmlexportpro'}<br />
<br />
<b>{l s='Note to special characters' mod='prestacenterxmlexportpro'}</b><br />
{l s='HTML tags are automatically deleted from HTML tags or special characters (angle brackets or ampersands are converted to HTML entities).' mod='prestacenterxmlexportpro'}
{l s='This does not apply when using modifiers - see below.' mod='prestacenterxmlexportpro'}
<br />
<b>{l s='Modifiers' mod='prestacenterxmlexportpro'}</b><br />
<b>{literal}HTML{/literal}</b> - {l s='It specifies that HTML tags will not be removed from the variable. Rule about special characters converting into entities is still valid. We do not recommend using the description_short variable and to trim the specified number of characters at the same time.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}CDATA{/literal}</b> - {l s='The variable will wrap itself in a CDATA block. There is no need to convert special characters to entities. You may use it inside an XML element. The attribute value will be ignored inside an XML element.' mod='prestacenterxmlexportpro'}<br />
{l s='Modifiers affect the variable for which they are used. Enter the modifier next to the name of the variable, separate it by a colon and do not close it in quotation marks. If the variable has some parameters, enter them after it. It is also possible to use more modifiers, however each must be separated by a comma.' mod='prestacenterxmlexportpro'}<br />
<b>{l s='Examples:' mod='prestacenterxmlexportpro'}</b><br />
<b>{literal}{description_short: HTML}{/literal}</b> - {l s='Short description of the product, including HTML tags.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{description_short: "60": CDATA}{/literal}</b> - {l s='Short description of the product cropped to 60 characters without HTML tags and wrapped in a CDATA block.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{description: HTML, CDATA}{/literal}</b> - {l s='Long description of the product, including HTML tags, wrapped in a CDATA block.' mod='prestacenterxmlexportpro'}
<br />
</span>
<br />
<u><a href="#" class="prestacenterxmlexportprotoggle" rel="1"></a></u><br />
<span id="prestacenterxmlexportproblock2"><br />
<b>{l s='Product availability' mod='prestacenterxmlexportpro'}</b><br />
<br />
{l s='There are two different variables to indicate the availability of the product: "days" and "availability". Both are governed by the stock of the availability of the product (i.e. if the product is available for order), see the "Available for order" in the Information tab (Catalog > Products). If there is a text entered to the product that is displayed in your e-shop (front office), depending on whether the product is / is not available / is available to order (see card Quantities), the values from this text may be used. The "days" variable is simpler, the first number found will be displayed. Variable "availability" is able to distinguish between numbers (the number of days needed for delivery) and dates (e.g. when the product will be available). You can set various values for the availability (e.g. in stock / to order / sold out).' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{days}{/literal}</b> - {l s='If the product is in stock, the first number from the text displayed in your shop as products in stock or the zero will be inserted into the feed. If the product is not in stock and the product availability is empty, then no value will be returned. Then it depends on the feed setting if the XML tag may be empty or not.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{availability}{/literal}</b> - {l s='This variable enables you to specify various custom values - like {condition}. Default functionality is this: for products that are in stock, the identical number as {days} is entered. The same applies for products that are not in stock, but can be ordered. If a product is unavailable, it will not be inserted in the feed at all.' mod='prestacenterxmlexportpro'}<br />
{l s='Customization options: enter your own values in this order - "in stock", "to order" and "sold out". They should not start with a number sign, which is reserved for special values. You can enter spaces after commas for improve readability.' mod='prestacenterxmlexportpro'}
<br />
<b>{literal}{availability_text}{/literal}</b> - {l s='This variable contains the description of the product availability exactly as it was entered in the back office for this product.' mod='prestacenterxmlexportpro'}<br />
<br />
<b>{l s='Examples:' mod='prestacenterxmlexportpro'}</b><br />
<b>{literal}{availability: "in stock, on order, sold out"}{/literal}</b> - {l s='For products that are in stock, in "stock" is inserted into the feed. The other two values are for products that are not in stock. If it is allowed to order products which are not in stock, then the second value "on order" is used. If it is not permitted to order the product and the third value is entered, the "sold out" value is used. If it is not permitted to order the product and the third value is missing, the product will not be exported at all.' mod='prestacenterxmlexportpro'}
{l s='Pro version differentiates between availability of the entire product and each combination of the attributes.' mod='prestacenterxmlexportpro'}
{*{l s='Inserts the entered texts, unavailable products are added into the feed as well.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{availability: "#, #:d.m.Y"}{/literal}</b> - {l s='For products in stock, a number of products is entered. For products in order, a number or date is entered. Unavailable products are not added to the feed at all (this is the default value).' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{availability: "3:immediately:expected product, #"}{/literal}</b> - {l s='If you want to further differentiate products in stock according to the delivery time, enter the number of days. If the value is less than or equal to the availability represented by the number of days and by the later availability value, separate it by a colon. According to this example, for the products in stock that are to be sent within three days, the "immediately" value is inserted. For the other products in stock, the "on the way\ value is inserted. For other products, number / date is entered.' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{availability: "#, #, #skipProduct"}{/literal}</b> - {l s='For products with "in stock" status, a number of products is entered. For products with "on the way" status, a number or date is entered. Unavailable products are not added to the feed at all (this is the default value).' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{availability: "3:in stock:available for order, preorder, out of stock"}{/literal}</b> - {l s='Settings for Google Merchant: for the products that are to be shipped within three days,"in stock" is inserted. For other products in stock, "available for order" is inserted. For products that are not in stock, but orders of such products are accepted, "preorder" is inserted. For the unavailable products "out of stock" is inserted.' mod='prestacenterxmlexportpro'}
*}
<br />
</span>
<br />
<u><a href="#" class="prestacenterxmlexportprotoggle" rel="2"></a></u><br />
<span id="prestacenterxmlexportproblock3"><br />
<b>{l s='Attributes and combinations' mod='prestacenterxmlexportpro'}</b><br />
<br />
<b>{l s='Product variants:' mod='prestacenterxmlexportpro'}</b> {l s='The XML element that is repeated for each product variant (i.e. for a combination of attributes), should be marked with the "ps_block" XML attribute and with the "variant" value (e.g. %1$s). If each variant is exported as a new product (e.g. for the Google Merchant), use two values separated by a space (e.g. %2$s). The following variables are available inside the product variants:' sprintf=['&lt;VARIANT ps_block="variant"&gt;', '&lt;entry ps_block="product variant"&gt;'] mod='prestacenterxmlexportpro'}<br />
<b>{literal}{id_combination}{/literal}</b> - {l s='The unique identifier for this version of the product' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{variant_description}{/literal}</b> - {l s='Summary of the attributes of this option (e.g. clothing, there will be "women red XL")' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{attrib: "{/literal}{l s='Attribute name' mod='prestacenterxmlexportpro'}{literal}"}{/literal}</b> - {l s='By using this variable, you may export individual attributes such as size, color, etc. The attribute name needs to be the same as the name given in the BO for the attribute and combination. It also needs to be in the same language as the language in which the feed is set. For example, in the English feed you will use \{attrib: "color"\} and \{attrib: "size"\}, but in Czech language, you will use \{attrib: "barva"\} and \{attrib: "velikost"\}. Always enter the name in lowercase letters.' mod='prestacenterxmlexportpro'}<br />
<br />
{l s='In the BO, you can set individual values for each product variant. For example, the default product variant has the EAN13 code set. The second product variant has different EAN13 set. The third variant has no EAN13 code set. The variable %1$s is used in two places, in the context of the products and in the context of the variants:' sprintf='{ean}' mod='prestacenterxmlexportpro'}<br />
&lt;SHOPITEM ps_block="product"&gt;<br />
&nbsp;&nbsp;&lt;GROUP_ITEM_ID&gt;{literal}{id}{/literal}&lt;/GROUP_ITEM_ID&gt;<br />
&nbsp;&nbsp;&lt;TITLE&gt;{literal}{name}{/literal}&lt;/TITLE&gt;<br />
&nbsp;&nbsp;&lt;EAN&gt;{literal}{ean}{/literal}&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;VARIANT ps_block="variant"&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;ID&gt;{literal}{id_combination}{/literal}&lt;/ID&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;EAN&gt;{literal}{ean}{/literal}&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;/VARIANT&gt;<br />
&lt;/SHOPITEM&gt;<br /><br />
{l s='Result:' mod='prestacenterxmlexportpro'}<br />
&lt;SHOPITEM&gt;<br />
&nbsp;&nbsp;&lt;GROUP_ITEM_ID&gt;{l s='Product ID' mod='prestacenterxmlexportpro'}&lt;/GROUP_ITEM_ID&gt;<br />
&nbsp;&nbsp;&lt;TITLE&gt;{l s='Product name' mod='prestacenterxmlexportpro'}&lt;/TITLE&gt;<br />
&nbsp;&nbsp;&lt;EAN&gt;{l s='EAN13 code of the product' mod='prestacenterxmlexportpro'}&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;VARIANT&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;ID&gt;{l s='Default product variant ID' mod='prestacenterxmlexportpro'}&lt;/ID&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;EAN&gt;{l s='EAN13 code for the default product variant' mod='prestacenterxmlexportpro'}&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;/VARIANT&gt;<br />
&nbsp;&nbsp;&lt;VARIANT&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;ID&gt;{l s='Second product variant ID' mod='prestacenterxmlexportpro'}&lt;/ID&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;EAN&gt;{l s='Different EAN13 of the second product variant' mod='prestacenterxmlexportpro'}&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;/VARIANT&gt;<br />
&nbsp;&nbsp;&lt;VARIANT&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;ID&gt;{l s='ID for the third product variant' mod='prestacenterxmlexportpro'}&lt;/ID&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;EAN&gt;<b>{l s='EAN13 product code' mod='prestacenterxmlexportpro'}</b>&lt;EAN&gt;<br />
&nbsp;&nbsp;&lt;/VARIANT&gt;<br />
&lt;/SHOPITEM&gt;<br />
<br />
{l s='EAN13 of the third variant of the product has not been entered, so EAN13, which is set in the basic version of the product has been used instead. Data for all other variables are exported in a same way. Variables may vary for each variant:' mod='prestacenterxmlexportpro'}
{literal}{ean}, {upc}, {price_vat}, {price_vat_local}, {price_vat_iso}, {url}, {img_url}, {quantity}, {reference}, {supplier_reference}{/literal}<br />
<br />
<b>{l s='Various attributes:' mod='prestacenterxmlexportpro'}</b> {l s='Some services require you to enter each attribute separately as name-value pairs. Use the "ps_block" XML attribute and the "attribute\ value. Two other variables are available:' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{attrib_name}{/literal}</b> - {l s='Attribute name' mod='prestacenterxmlexportpro'}<br />
<b>{literal}{attrib_value}{/literal}</b> - {l s='Attribute value' mod='prestacenterxmlexportpro'}<br />
<br />
{l s='Example: Clothing has two attributes: color and size. Enter the following in the feed:' mod='prestacenterxmlexportpro'}<br />
{literal}&lt;PARAM ps_block="attribute"&gt;<br />
&nbsp;&nbsp;&lt;PARAM_NAME&gt;{attrib_name}&lt;/PARAM_NAME&gt;<br />
&nbsp;&nbsp;&lt;VAL&gt;{attrib_value}&lt;/VAL&gt;<br />
&lt;/PARAM&gt;{/literal}<br />
<br />
{l s='To each variant of the product the following will be exported:' mod='prestacenterxmlexportpro'}<br />
{literal}&lt;PARAM&gt;<br />
&nbsp;&nbsp;&lt;PARAM_NAME&gt;{/literal}{l s='size' mod='prestacenterxmlexportpro'}{literal}&lt;/PARAM_NAME&gt;<br />
&nbsp;&nbsp;&lt;VAL&gt;38&lt;/VAL&gt;<br />
&lt;/PARAM&gt;<br />
&lt;PARAM&gt;<br />
&nbsp;&nbsp;&lt;PARAM_NAME&gt;{/literal}{l s='color' mod='prestacenterxmlexportpro'}{literal}&lt;/PARAM_NAME&gt;<br />
&nbsp;&nbsp;&lt;VAL&gt;{/literal}{l s='green' mod='prestacenterxmlexportpro'}{literal}&lt;/VAL&gt;<br />
&lt;/PARAM&gt;{/literal}
<br />
</span>
<br />
<u><a href="#" class="prestacenterxmlexportprotoggle" rel="3"></a></u><br />
<span id="prestacenterxmlexportproblock4"><br />
<b>{l s='Product features' mod='prestacenterxmlexportpro'}</b><br />
<br />
<b>{literal}{feature_block}{/literal}</b> - {l s='By using this variable, you may export individual product features such as weight, material, country of origin, etc. This variable accepts three parameters: text to be displayed before feature name, text between feature name and value, and text after feature value.' mod='prestacenterxmlexportpro'}
<br />
{l s='Example: Shoes have two features: upper and sole materials. Enter the following in the feed template:' mod='prestacenterxmlexportpro'}<br />
{literal}&lt;DESCRIPTION&gt;{feature_block: "- ", ": ", ";"}&lt;/DESCRIPTION&gt;{/literal}<br />
<br />
{l s='The following will be exported:' mod='prestacenterxmlexportpro'}<br />
&lt;DESCRIPTION&gt;<br />
- {l s='upper material' mod='prestacenterxmlexportpro'}: {l s='leather' mod='prestacenterxmlexportpro'};<br />
- {l s='sole material' mod='prestacenterxmlexportpro'}: {l s='natural rubber' mod='prestacenterxmlexportpro'};<br />
&lt;/DESCRIPTION&gt;<br />
<br />
{l s='In case if you want to add an HTML list of features to HTML product description, enter this code in the feed template:' mod='prestacenterxmlexportpro'}<br />
{literal}&lt;DESCRIPTION&gt;&lt;![CDATA[<br />
{description_short: HTML}<br />
&lt;ul&gt;<br />
{feature_block: "&lt;li&gt;", ": ", "&lt;/li&gt;": HTML}<br />
&lt;/ul&gt;<br />
]]&gt;&lt;/DESCRIPTION&gt;{/literal}<br />
<br />
{l s='The following will be exported:' mod='prestacenterxmlexportpro'}<br />
&lt;DESCRIPTION&gt;<br />
{l s='Product description.' mod='prestacenterxmlexportpro'}<br />
&lt;ul&gt;<br />
&lt;li&gt;{l s='upper material' mod='prestacenterxmlexportpro'}: {l s='leather' mod='prestacenterxmlexportpro'}&lt;/li&gt;<br />
&lt;li&gt;{l s='sole material' mod='prestacenterxmlexportpro'}: {l s='natural rubber' mod='prestacenterxmlexportpro'}&lt;/li&gt;<br />
&lt;/ul&gt;<br />
&lt;/DESCRIPTION&gt;<br />
<br />
{*
{l s='Please note the use of CDATA section in the second example. If you need to use HTML tags in variable parameters, you must either replace angle brackets with HTML entities, or enclose the whole variable into a CDATA section. The following example will produce the same result:' mod='prestacenterxmlexportpro'}<br />
&lt;DESCRIPTION&gt;{literal}{description_short: HTML, CDATA}{/literal}<br />
&amp;lt;ul&amp;gt;<br />
{literal}{feature_block:{/literal}"&amp;lt;li&amp;gt;", ": ", "&amp;lt;/li&amp;gt;"{literal}}{/literal}<br />
&amp;lt;/ul&amp;gt;<br />
&lt;/DESCRIPTION&gt;
*}
</span>
<br />
<u><a href="#" class="prestacenterxmlexportprotoggle" rel="4"></a></u><br />
{* skrývání nápovědy *}
<script>
$('a.prestacenterxmlexportprotoggle').each(function() {
	$(this).click(function(button) {
		var texty = {
			1: "{l s='Show general information' mod='prestacenterxmlexportpro'}",
			2: "{l s='Show help to product availability' mod='prestacenterxmlexportpro'}",
			3: "{l s='Show help to attributes' mod='prestacenterxmlexportpro'}",
			4: "{l s='Show help to product features' mod='prestacenterxmlexportpro'}",
		};
		var i = button.target.rel;
		$('#prestacenterxmlexportproblock'+i).fadeToggle( { 'complete' : function() {
			if ($('#prestacenterxmlexportproblock'+i).css('display') === 'none') {
				$(button.target).text(texty[i]);
			} else {
				$(button.target).text("{l s='Hide help' mod='prestacenterxmlexportpro'}");
			}
		}});
	});
	$(this).click();
});
</script>
