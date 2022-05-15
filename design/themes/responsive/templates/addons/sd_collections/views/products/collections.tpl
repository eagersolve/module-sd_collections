{if $collections}

    {script src="js/tygh/exceptions.js"}
    

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}


    {if !$show_empty}
        {split data=$collections size=$columns|default:"2" assign="splitted_collections"}
    {else}
        {split data=$collections size=$columns|default:"2" assign="splitted_collections" skip_complete=true}
    {/if}
    {math equation ="100 /x" x = $columns|default:"2" assign="cell_width"}


    {* FIXME: Don't move this file *}
    {script src="js/tygh/product_image_gallery.js"}


    <div class="grid-list">
        {strip}
            {foreach from=$splitted_collections item="scollections" name="sprod"}
                {foreach from=$scollections item="collection"}
                    <div class="ty-column{$columns}">
                        {if $collection}
                            {assign var="obj_id" value=$collection.collection_id}
                            {assign var="obj_id_prefix" value="`$obj_prefix``$collection.collection_id`"}
                            
                            <div class="ty-grid-list__item ty-quick-view-button__wrapper ">

                                <div class="ty-grid-list__image">
                                    <a href="{"products.collection?collection_id={$collection.collection_id}"|fn_url}">
                                    {include 
                                        file="common/image.tpl"
                                        no_ids=true 
                                        images=$collection.main_pair 
                                        image_width=$settings.Thumbnails.product_lists_thumbnail_width 
                                        image_height=$settings.Thumbnails.product_lists_thumbnail_height 
                                        lazy_load=true
                                    }
                                    </a>
                                </div>

                                <div class="ty-grid-list__item-name">
                                    {assign var="name" value="name_$obj_id"}
                                    <bdi>{$smarty.capture.$name nofilter}</bdi>
                                </div>

                                <div class="ty-grid-list__item-name">
                                    <bdi>
                                        <a href="{"products.collection?collection_id={$collection.collection_id}"|fn_url}" class="product-title" title="$collection.collection">{$collection.collection}</a>    
                                    </bdi>
                                </div>

                            </div>
                        {/if}
                    </div>
                {/foreach}
            {/foreach}
        {/strip}
    </div>

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

{/if}

{capture name="mainbox_title"}{$title}{/capture}