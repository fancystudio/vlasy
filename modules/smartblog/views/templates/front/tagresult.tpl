{capture name=path}<a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Všetky novinky blogu' mod='smartblog'}</a>
     {if $title_category != ''}
    <span class="navigation-pipe">{$navigationPipe}</span>{$title_category}{/if}{/capture}
 
    {if $postcategory == ''}
             <p class="error">{l s='Žiadne príspevky v tomto tagu' mod='smartblog'}</p>
    {else}
    <div id="smartblogcat" class="block">
{foreach from=$postcategory item=post}
    {include file="./category_loop.tpl" postcategory=$postcategory}
{/foreach}
    </div>
{/if}
{if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}