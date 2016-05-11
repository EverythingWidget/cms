<div class="col-xs-12">
  <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
  <input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>
  <input class="text-field" data-label="Select a content" id="{{comp_id}}_text" name="text" value="" />
</div>

<div class="col-xs-12">
  <ul id="{{comp_id}}_attached" class="list">
    <div v-for="item in items" class="list-item">
      <a class="link {{item.id === currentDocId ? 'active': ''}}" rel="ajax" href="#" v-on:click="select(item)">{{item.title}}</a>
    </div>
  </ul>
</div>

<script>
  (function () {
    var text = $("#{{comp_id}}_text");
    var value = $("#{{comp_id}}_value");

    var relatedDocuments = new Vue({
      el: "#{{comp_id}}_attached",
      data: {
        currentDocId: parseInt("{{value}}"),
        items: []
      },
      methods: {
        select: function (item) {
          var _this = this;
          $.post("~admin/api/content-management/get-article", {
            articleId: item.id
          }, function (response) {
            _this.currentDocId = item.id;
            ContentForm.setData(response.data);
          }, "json");
        }
      }
    });

    text.autocomplete({
      source: function (input) {
        $.post("~admin/api/content-management/contents", {
          title_filter: text.val(),
          type: "article",
          size: 30
        }, function (response) {
          input.trigger("updateList", [
            response.data
          ]);
        }, "json");
      },
      templateText: "<li class='text-item'><a href='#'><%= title %><span><%= date_created %></span></a><li>",
      insertText: function (item) {
        value.val(item.id);
        return item.title;
      }
    });

    $("#{{form_id}}").on("refresh", function (e, formData) {
      if (!ContentForm.getLabel("{{comp_id}}")) {
        ContentForm.activeLabel("{{comp_id}}", true);
        value.val('$content.id');
        text.val(formData["title"]).change();
      }

      $.post("~admin/api/content-management/contents-labels", {
        content_id: ContentForm.getLabel("{{comp_id}}"),
        key: "{{comp_id}}"
      }, function (response) {
        relatedDocuments.items = response['data'];
      }, "json");
    });
  }());
</script>