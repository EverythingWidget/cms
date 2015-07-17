<div class="col-xs-12">
   <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
   <input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>
   <input class="text-field" data-label="Select a content" id="{{comp_id}}_text" name="text" value="" />
</div>
<div class="col-xs-12">
   <ul id="{{comp_id}}_attached" class="list indent">

   </ul>
</div>
<script>
   (function ()
   {
      var text = $("#{{comp_id}}_text");
      var value = $("#{{comp_id}}_value");
      var attached = $("#{{comp_id}}_attached");
      text.autocomplete({
         source: function (input) {
            $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_contents",
                    {
                       title_filter: text.val(),
                       type: "article",
                       size: 30
                    },
            function (data)
            {
               input.trigger("updateList", [data.result]);
            }, "json");
         },
         templateText: "<li class='text-item'><a href='#'><%= title %><span><%= date_created %></span></a><li>",
         insertText: function (item)
         {
            value.val(item.id);
            return item.title;
         }
      });

      $("#{{form_id}}").on("refresh", function (e, formData)
      {

         // Init
         if (!ContentForm.getLabel("{{comp_id}}"))
         {
            ContentForm.activeLabel("{{comp_id}}", true);
            value.val('$content.id');
            text.val(formData["title"]).change();
         }
         $.post("app-admin/ContentManagement/get_content_with_label",
                 {
                    content_id: ContentForm.getLabel("{{comp_id}}"),
                    key: "{{comp_id}}"
                 },
         function (data)
         {
            attached.empty();
            if (data['result'])
               $.each(data['result'], function (i, content)
               {
                  var langItem = $("<li class=''><a rel='ajax' href='#' class='link'>" + content.title + "</a></li>");
                  if (content.id == "{{value}}")
                  {
                     value.val(content.id);
                     text.val(content.title).change();
                  }
                  if (content.id == formData.id)
                  {
                     langItem.addClass("active");
                  }
                  else
                     langItem.find("a").on("click", function ()
                     {
                        $.post("app-admin/ContentManagement/get_article",
                                {
                                   articleId: content.id
                                },
                        function (data)
                        {
                           ContentForm.setData(data);
                        }, "json");
                     });
                  attached.append(langItem);
               });
         }, "json");
      });
   }());
</script>