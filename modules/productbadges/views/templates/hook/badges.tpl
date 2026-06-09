<div class="product-badges-container {if $is_product_page}on-product-page{else}on-list-page{/if}">
    {foreach from=$badges item=badge}
        <span class="badge-item badge-position-{$badge.position|escape:'html':'UTF-8'}" 
              style="background-color: {$badge.color_bg|escape:'html':'UTF-8'}; color: {$badge.color_text|escape:'html':'UTF-8'};">
            {$badge.text|escape:'html':'UTF-8'}
        </span>
    {foreach}
</div>