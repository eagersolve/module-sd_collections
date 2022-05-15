{if $collection_data}
    {assign var="id" value=$collection_data.collection_id}
{else}
    {assign var="id" value=0}
{/if}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="banners_form" enctype="multipart/form-data">
  <input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
  <input type="hidden" class="cm-no-hide-input" name="collection_id" value="{$id}" />
       
        <div id="content_general">
        <div class="control-group">
            <label for="elm_banner_name" class="control-label cm-required">{__("name")}</label>
            <div class="controls">
            <input type="text" name="collection_data[collection]" id="elm_banner_name" value="{$collection_data.collection}" size="25" class="input-large" /></div>
        </div>

        <div class="control-group">
            <label for="elm_banner_position" class="control-label">{__("position_short")}</label>
            <div class="controls">
                <input type="text" name="collection_data[position]" id="elm_banner_position" value="{$collection_data.position|default:"0"}" size="3"/>
            </div>
        </div>

        <div class="control-group" id="banner_graphic">
            <label class="control-label">{__("image")}</label>
            <div class="controls">
                {include file="common/attach_images.tpl"
                    image_name="collection"
                    image_object_type="collection"
                    image_pair=$collection_data.main_pair
                    image_object_id=$id
                    no_detailed=true
                    hide_titles=true
                }
            </div>
        </div>

        <div class="control-group" id="banner_text">
            <label class="control-label" for="elm_banner_description">{__("description")}:</label>
            <div class="controls">
                <textarea id="elm_banner_description" name="collection_data[description]" cols="35" rows="8" class="cm-wysiwyg input-large">{$collection_data.description}</textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_banner_timestamp_{$id}">{__("creation_date")}</label>
            <div class="controls">
            {include file="common/calendar.tpl" date_id="elm_banner_timestamp_`$id`" date_name="collection_data[timestamp]" date_val=$collection_data.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
            </div>
        </div>
        {include file="common/select_status.tpl" input_name="collection_data[status]" id="elm_banner_status" obj_id=$id obj=$collection_data hidden=false}


        <div class="control-group">
            <label class="control-label">{__("users")}</label>
            <div class="controls">
                {include file="pickers/users/picker.tpl" 
                but_text=__("add_recipients_from_users") 
                data_id="return_users" 
                but_meta="btn" 
                input_name="collection_data[user_id]" 
                item_ids=$collection_data.user_id
                placement="right"
                display = "radio"
                view_mode = "single_button"
                user_info = $u_info}
                <p class="muted description">{__("tt_addons_newsletters_views_newsletters_update_users")}</p>
            </div>
        </div>
        
        {include file="views/products/components/picker/picker.tpl"
        input_name="collection_data[product_ids][]"
        item_ids=$collection_data.product_ids
        multiple=true
        view_mode="external"
        select_group_class="btn-toolbar"
    }
    </div>
   
   
    <!--content_general--></div>

{capture name="buttons"}
    {if !$id}
        {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="banners_form" but_name="dispatch[products.update_collection]"}
    {else}
        {include file="buttons/save_cancel.tpl" but_name="dispatch[products.update_collection]" but_role="submit-link" but_target_form="banners_form" hide_first_button=$hide_first_button hide_second_button=$hide_second_button save=$id}
        {capture name="tools_list"}
            <li>{btn type="list" text=__("delete") class="cm-confirm" href="products.delete_collection?collection_id=`$id`" method="POST"}</li>
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/if}
{/capture}

</form>

{/capture}


{if !$id}
    {$title = 'Добавить новую коллекцию'}
{else}
    {$title_start = "Изменить"}
    {$title_end = $collection_data.collection}
{/if}

{include file="common/mainbox.tpl"
    title_start=$title_start
    title_end=$title_end
    title=$title
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
    select_languages=true}

{** collection section **}

