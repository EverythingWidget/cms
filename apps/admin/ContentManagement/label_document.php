<div class="col-xs-12">
   <input class="text-field" type="hidden" id="<?php echo $key ?>_key" name="key" value="<?php echo $key ?>"/>
   <input class="text-field" type="hidden" id="<?php echo $key ?>_value" name="value" value=""/>
   <input class="text-field" data-label="Select a content" id="<?php echo $key ?>_text" name="text" value="" />
</div>
<div class="col-xs-12">
   <ul id="<?php echo $key ?>_attached" class="list indent">

   </ul>
</div>
<script>

   $("#<?php echo $key ?>_text").autocomplete({
      source: function (input) {
         $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_contents", {title_filter: $("#<?php echo $key ?>_text").val(), type: "article", size: 30}, function (data) {
            input.trigger("updateList", [data.result]);
         }, "json");
      },
      templateText: "<li class='text-item'><a href='#'><%= title %><span><%= date_created %></span></a><li>",
      insertText: function (item) {
         $("#<?php echo $key ?>_value").val(item.id);
         return item.title;
      }
   });

   $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
   {
      // Init
      if (!ContentForm.getLabel("admin_ContentManagement_document"))
      {
         ContentForm.activeLabel("admin_ContentManagement_document", true);
         $("#<?php echo $key ?>_value").val(formData["id"]);
         $("#<?php echo $key ?>_text").val(formData["title"]).change();         
      }
      $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_content_with_label", {content_id: ContentForm.getLabel("admin_ContentManagement_document"), key: "<?php echo $key ?>"}, function (data) {
         $("#<?php echo $key ?>_attached").empty();
         $.each(data, function (i, content)
         {
            var langItem = $("<li class=''><a rel='ajax' href='#' class='link'>" + content.title + "</a></li>");
            if (content.id == "<?php echo $value ?>")
            {
               $("#<?php echo $key ?>_value").val(content.id);
               $("#<?php echo $key ?>_text").val(content.title).change();
            }
            if (content.id == formData.id)
            {
               langItem.addClass("active");
               //$("#<?php echo $key ?>_value").val(formData["id"]);
               //$("#<?php echo $key ?>_text").val(formData["title"]).change();
            }
            else
               langItem.find("a").on("click", function ()
               {
                  $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_article", {articleId: content.id}, function (data)
                  {
                     ContentForm.setData(data);
                  }, "json");
               });
            $("#<?php echo $key ?>_attached").append(langItem);
         });
      }, "json");
   });
</script>