<div class="col-xs-12">
   <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
   <input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>
   <select id="{{comp_id}}_select" data-label="tr{Add a language}">
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
   <ul id="{{comp_id}}_languages" class="list links">

   </ul>
</div>
<script>
   var languages = {en: "English", es: "Spanish", de: "German", ru: "Russian", cmn: "Mandarin", ar: "Arabic", fa: "فارسی", nl: 'Dutch'};
   $("#{{comp_id}}_value").val("<?php echo $value ?>");
   $("#{{comp_id}}_select").on("change", function ()
   {
      $("#{{comp_id}}_value").val($("#{{comp_id}}_select").val());
   });
   $("#{{form_id}}").on("refresh", function (e, formData)
   {
      var documentId = formData.id;
      if (ContentForm.getLabel("admin_ContentManagement_document") != documentId)
      {
         documentId = ContentForm.getLabel("admin_ContentManagement_document");
      }
      //init
      if (!ContentForm.getLabel("{{comp_id}}"))
      {
         // Active language label as default for the new article
         ContentForm.activeLabel("{{comp_id}}", true);
         $("#{{comp_id}}_select").change();
      }
      $.post("~admin/api/content-management/get-content-with-label", {content_id: documentId, key: "{{comp_id}}"}, function (data) {
         $("#{{comp_id}}_languages").empty();
         if (data['result'])
            $.each(data['result'], function (i, content)
            {
               //$("#{{comp_id}}_select option[value='" + content.value + "']").remove();
               var langItem = $("<li><a rel='ajax' href='#' class='link'>" + languages[content.value] + "<p>" + content["title"] + "</p></a></li>");
               if (content.id == formData.id)
               {
                  langItem.addClass("active");
                  $("#{{comp_id}}_value").val(content.value);
               }
               else
                  langItem.find("a").on("click", function ()
                  {
                     $.post("~admin/api/content-management/get-article", {articleId: content.id}, function (data)
                     {
                        ContentForm.setData(data);
                        //EW.setHashParameter("articleId", lang.id)
                     }, "json");
                  });
               $("#{{comp_id}}_languages").append(langItem);
            });
      }, "json");
   });

</script>