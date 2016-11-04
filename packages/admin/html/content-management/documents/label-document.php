<input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
<input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" v-model="parentContentId"/>

<div class="col-xs-12">
  <system-field class="field">
    <label>tr{Select a content}</label>
    <input class="text-field" id="{{comp_id}}_text" name="text" v-model="parentContentTitle" />  
    <div class="field-actions">
      <button type="button" class="btn btn-info" v-on:click="selectContent()"><i class="icon-link"></i></button>
    </div>
  </system-field>


  <system-spirit animations="liveHeight">
    <ul id="{{comp_id}}_attached" class="list">
      <li v-for="item in items" track-by="id" class="list-item">
        <a v-bind:class="{'link': true,'active' : item.id === contentId}" href="#" v-on:click="select($event, item)">
          {{ item.title }}
        </a>
      </li>
    </ul>
  </system-spirit>
</div>

<script>
  (function () {
    var text = $("#{{comp_id}}_text");
    var value = $("#{{comp_id}}_value");

    var relatedDocumentsVue = new Vue({
      el: "#{{comp_id}}_label_block",
      data: {
        parentContentId: null,
        parentContentTitle: '',
        contentId: parseInt("{{value}}"),
        items: []
      },
      methods: {
        select: function ($event, item) {
          $event.preventDefault();

          if (item.id === relatedDocumentsVue.contentId) {
            return;
          }

          $.get('api/admin/content-management/contents/' + item.id, success);

          function success(response) {
            relatedDocumentsVue.contentId = item.id;
            ContentForm.setData(response.data);
          }
        },
        selectContent: function () {
          System.entity('ui/dialogs/link-chooser').open(function (content) {
            relatedDocumentsVue.parentContentId = content.id;
            relatedDocumentsVue.parentContentTitle = content.title;
          });
        }
      }
    });

    $("#{{form_id}}").on('refresh.documents', function (e, formData) {
      relatedDocumentsVue.contentId = formData.id;

      if (!ContentForm.getLabel("{{comp_id}}")) {
        ContentForm.activeLabel("{{comp_id}}", true);
        value.val('$content.id');
        text.val(formData["title"]).change();
      }

      $.get('api/admin/content-management/contents-labels/', {
        content_id: ContentForm.getLabel("{{comp_id}}"),
        key: "{{comp_id}}"
      }, function (response) {
        relatedDocumentsVue.items = response['data'];
      });
    });

  }());
</script>