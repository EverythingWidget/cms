<input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
<input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>

<system-field class="field col-xs-12">
  <label>tr{Add a language}</label>
  <select id="{{comp_id}}_select">
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
</system-field>

<div class="col-xs-12">
  <ul id="{{comp_id}}_languages" class="list links">
    <li v-for="item in items" v-bind:class="{ 'active': item.id === contentId }" transition="slide-vertical">
      <a href='#' class='link' v-on:click="select($event,item)">
        {{ languages[item.value] }}
        <p>
          {{ item.title }}
        </p>
      </a>
    </li>
  </ul>
</div>
<script>
  (function () {
    var languages = {en: "English", es: "Spanish", de: "German", ru: "Russian", cmn: "Mandarin", ar: "Arabic", fa: "فارسی", nl: 'Dutch'};
    var languagesLabelVue = new Vue({
      el: '#{{comp_id}}_languages',
      data: {
        items: [],
        languages: languages,
        contentId: null
      },
      methods: {
        select: function ($event, item) {
          $event.preventDefault();

          if (item.id === languagesLabelVue.contentId) {
            return;
          }

          $.get('api/admin/content-management/get-article', {articleId: item.id}, function (response) {
            ContentForm.setData(response.data);
          });
        }
      }
    });

    $("#{{comp_id}}_value").val("<?php echo $value ?>");
    $("#{{comp_id}}_select").on("change", function () {
      $("#{{comp_id}}_value").val($("#{{comp_id}}_select").val());
    });

    $("#{{form_id}}").on("refresh", function (e, response) {
      var documentId = response.id;
      languagesLabelVue.contentId = documentId;

      if (ContentForm.getLabel("admin_ContentManagement_document") != documentId) {
        documentId = ContentForm.getLabel("admin_ContentManagement_document");
      }

      // Init
      if (!ContentForm.getLabel("{{comp_id}}")) {
        // Active language label as default for the new article
        ContentForm.activeLabel("{{comp_id}}", true);
        $("#{{comp_id}}_select").change();
      }

      $.get("api/admin/content-management/contents-labels", {
        content_id: documentId,
        key: "{{comp_id}}"
      }, success);

      function success(data) {
        languagesLabelVue.items = data['data'];
      }
    });
  })();
</script>