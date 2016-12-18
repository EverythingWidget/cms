<input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
<input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" v-model="activeLanguage"/>

<system-field class="field col-xs-12">
  <label>tr{Add a language}</label>
  <select id="{{comp_id}}_select" v-model="activeLanguage">
    <option v-for="lang in languages" v-bind:value="lang.name">{{ lang.title }}</option>
  </select>
</system-field>

<div class="col-xs-12">
  <system-spirit animations="liveHeight">
    <ul id="{{comp_id}}_languages" class="list links">
      <li v-for="item in items" class="zoom-item" v-bind:class="{ 'active': item.id === contentId }" >
        <a href='#' class='link' v-on:click="select($event,item)">
          {{ languagesMap[item.value] }}
          <p>
            {{ item.title }}
          </p>
        </a>
      </li>
    </ul>
  </system-spirit>
</div>
<script>
  (function () {
    var languages = [
      {
        name: 'en',
        title: 'Default'
      },
      {
        name: 'en',
        title: 'English'
      },
      {
        name: 'es',
        title: 'Spanish'
      },
      {
        name: 'de',
        title: 'German'
      },
      {
        name: 'ru',
        title: 'Russian'
      },
      {
        name: 'cmn',
        title: 'Mandarin'
      },
      {
        name: 'ar',
        title: 'Arabic'
      },
      {
        name: 'fa',
        title: 'Persian'
      },
      {
        name: 'nl',
        title: 'Dutch'
      }
    ];

    var languagesMap = {
      en: "English",
      es: "Spanish",
      de: "German",
      ru: "Russian",
      cmn: "Mandarin",
      ar: "Arabic",
      fa: "Persian",
      nl: 'Dutch'
    };

    var languagesLabelVue = new Vue({
      el: '#{{comp_id}}_label_block',
      data: {
        items: [],
        activeLanguage: '$php.value' === '' ? 'en' : '$php.value',
        languagesMap: languagesMap,
        languages: languages,
        contentId: null
      },
      methods: {
        select: function ($event, item) {
          $event.preventDefault();

          if (item.id === languagesLabelVue.contentId) {
            return;
          }

          $.get('api/admin/content-management/get-article/', {articleId: item.id}, function (response) {
            ContentForm.setData(response.data);
            languagesLabelVue.activeLanguage = ContentForm.getLabel("{{comp_id}}");
          });
        }
      }
    });


//    $("#{{comp_id}}_select").on("change", function () {
//      $("#{{comp_id}}_value").val($("#{{comp_id}}_select").val());
//    });

    $("#$php.form_id").on('refresh', function (e, response) {
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

      $.get("api/admin/content-management/contents-labels/", {
        content_id: documentId,
        key: "{{comp_id}}"
      }, success);

      function success(data) {
        languagesLabelVue.items = data['data'];
      }
    });
  })();
</script>