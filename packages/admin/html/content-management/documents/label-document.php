<input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
<input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>

<div class="col-xs-12">
  <system-field class="field">
    <label>tr{Select a content}</label>
    <input class="text-field" id="{{comp_id}}_text" name="text" value="" />  
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
          contentChooserDialog();
        }
      }
    });

//    text.autocomplete({
//      source: function (input) {
//        $.get('api/admin/content-management/contents/', {
//          filter: {
//            where: {
//              type: 'article',
//              title: {
//                like: text.val() + '%'
//              }
//            }
//          },
//          page_size: 10
//        }, function (response) {
//          input.trigger("updateList", [
//            response.data
//          ]);
//        });
//      },
//      templateText: "<li class='text-item'><a href='#'><%= title %><span><%= date_created %></span></a><li>",
//      insertText: function (item) {
//        value.val(item.id);
//        return item.title;
//      }
//    });

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

    function contentChooserDialog() {
      var linkChooserDialog = EW.createModal({
        class: "center slim"
      });

      System.loadModule({
        url: 'html/admin/content-management/link-chooser/component.php',
        params: {
          contentType: 'content'
        }
      }, function (module) {
        module.scope.onSelect = function (parameters) {
          alert('asdasd');
          linkChooserDialog.dispose();
        };

        linkChooserDialog.html(module.html);
      });
    }
  }());
</script>