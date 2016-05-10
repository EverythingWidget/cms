<div class="col-xs-12">
  <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
  <input class="text-field" type="hidden" id="{{comp_id}}_value" name="value" value=""/>
  <input class="text-field" data-label="Select a content" id="{{comp_id}}_text" name="text" value="" />
</div>

<div class="col-xs-12">
  <system-list id="{{comp_id}}_attached" class="list" selected-style="active">
    <div class="list-item">
      <a class="link" rel="ajax" href="#" item>{{title}}</a>
    </div>
  </system-list>
  <!--<ul id="{{comp_id}}_attached" class="list"></ul>-->
</div>

<script>
  (function () {
    var text = $("#{{comp_id}}_text");
    var value = $("#{{comp_id}}_value");
    var attached = $("#{{comp_id}}_attached");
    var oldIndex;
    window.testData = [
      {title: 'test'},
      {title: 'test 2'},
      {title: 'test 3'}
    ];
    
    var prop = new System.Property(window.testData);
    attached[0].data = prop;

    attached.on('item-selected', function (event) {
      //var loader = System.ui.lock()
      //if (attached[0].data[oldIndex] && attached[0].data[oldIndex] !== event.originalEvent.detail.data.id) {
      //debugger;
//        $.post("~admin/api/content-management/get-article", {
//          articleId: event.originalEvent.detail.data.id
//        }, function (response) {
//          ContentForm.setData(response.data);
//        }, "json");
      //}
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
//        if (response['data'] && JSON.stringify(response['data']) !== JSON.stringify(attached[0].data)) {
//          attached[0].data = response['data'];
//
//          $.each(attached[0].data, function (i, content) {
//            if (content.id === parseInt("{{value}}")) {
//              attached[0].value = i;
//            }
//          });
//        }

      }, "json");
    });
  }());
</script>