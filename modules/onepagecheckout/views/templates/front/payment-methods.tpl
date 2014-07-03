{if isset($payment_methods)}
<table id="paymentMethodsTable" class="std">
    <tbody>
        {foreach from=$payment_methods item=payment_method name=myLoop}
        <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
            <td class="payment_action radio">
                <input type="radio" name="id_payment_method" value="{$payment_method.link}"
                       id="payment_{$payment_method.link}" {if ($payment_methods|@count == 1)}checked="checked"{/if} />
            </td>
            <td class="payment_name">
                <label for="payment_{$payment_method.link}">
                    {if $payment_method.img}<img{if isset($payment_method.class)} class="cssback {$payment_method.class}"{/if} src="{$payment_method.img|escape:'htmlall':'UTF-8'}"/>{/if}
                </label>
            </td>
            <td class="payment_description">
                <label for="payment_{$payment_method.link}">
	    	    {$payment_method.desc}
		</label>
	    </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}
