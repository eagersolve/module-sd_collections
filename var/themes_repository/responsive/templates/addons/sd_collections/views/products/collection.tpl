<div id="product_features_{$block.block_id}">
<div class="ty-feature">
    {if $collection_data.main_pair}
    <div class="ty-feature__image">
        {include file="common/image.tpl" images=$collection_data.main_pair}
    </div>
    {/if}
    <div class="ty-feature__description ty-wysiwyg-content">

        {$collection_data.description nofilter}
    </div>
</div>

{if $products}
{assign var="layouts" value=""|fn_get_products_views:false:0}
{if $layouts.$selected_layout.template}
    {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_products_list}
{/if}
{else}
    <p class="ty-no-items">{__("text_no_products")}</p>
{/if}
<!--product_features_{$block.block_id}--></div>
{capture name="mainbox_title"}{$variant_data.variant nofilter}{/capture}