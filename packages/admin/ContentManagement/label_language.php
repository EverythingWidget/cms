<div class="col-xs-12">
   <input class="text-field" type="hidden" id="<?php echo $key ?>_key" name="key" value="<?php echo $key ?>"/>
   <input class="text-field" type="hidden" id="<?php echo $key ?>_value" name="value" value=""/>
   <select id="<?php echo $key ?>_select" data-label="tr{Add a language}">
      <option value="en">Default</option>
      <option value="en">English</option>
      <option value="es">Spanish</option>
      <option value="de">German</option>
      <option value="ru">Russian</option>
      <option value="cmn">Mandarin</option>
      <option value="ar">Arabic</option>
      <option value="fa">فارسی</option>
      <option value="nl">Dutch</option>
   </select>
</div>
<div class="col-xs-12">
   <ul id="<?php echo $key ?>_languages" class="list links">

   </ul>
</div>
<script>
   var languages = {en: "English", es: "Spanish", de: "German", ru: "Russian", cmn: "Mandarin", ar: "Arabic", fa: "فارسی", nl: 'Dutch'};
   $("#<?php echo $key ?>_value").val("<?php echo $value ?>");
   $("#<?php echo $key ?>_select").on("change", function ()
   {
      $("#<?php echo $key ?>_value").val($("#<?php echo $key ?>_select").val());
   });
   $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
   {
      var documentId = formData.id;
      if (ContentForm.getLabel("admin_ContentManagement_document") != documentId)
      {
         documentId = ContentForm.getLabel("admin_ContentManagement_document");
      }
      //init
      if (!ContentForm.getLabel("admin_ContentManagement_language"))
      {
         // Active language label as default for the new article
         ContentForm.activeLabel("admin_ContentManagement_language", true);
         $("#<?php echo $key ?>_select").change();
      }
      $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_content_with_label", {content_id: documentId, key: "<?php echo $key ?>"}, function (data) {
         $("#<?php echo $key ?>_languages").empty();
         if (data['result'])
            $.each(data['result'], function (i, content)
            {
               //$("#<?php echo $key ?>_select option[value='" + content.value + "']").remove();
               var langItem = $("<li><a rel='ajax' href='#' class='link'>" + languages[content.value] + "<p>" + content["title"] + "</p></a></li>");
               if (content.id == formData.id)
               {
                  langItem.addClass("active");
                  $("#<?php echo $key ?>_value").val(content.value);
               }
               else
                  langItem.find("a").on("click", function ()
                  {
                     $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_article", {articleId: content.id}, function (data)
                     {
                        ContentForm.setData(data);
                        //EW.setHashParameter("articleId", lang.id)
                     }, "json");
                  });
               $("#<?php echo $key ?>_languages").append(langItem);
            });
      }, "json");
   });

</script>