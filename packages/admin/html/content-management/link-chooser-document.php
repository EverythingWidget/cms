<div id="content-chooser-actions-bar" class="actions-bar">

</div>

<div class="elements-list" id="content-chooser-folders-list"  >
  <h2></h2>
  <div class="box-content"></div>
</div>

<div class="elements-list"  id="articles-list">
  <h2></h2>
  <div class="box-content"></div>
</div>


<script  type="text/javascript">
  var LinkChooserDocuments = (function () {
    var parentDialog = EW.getParentDialog($("#content-chooser-folders-list"));
    function LinkChooserDocuments() {
      var _this = this;
      this.contentType = "<?php echo $_REQUEST["contentType"] ?>" || null;
      this.parentId = 0;
      this.preParentId = -1;
      this.document = {};
      //$("#documents-up-btn").click($.proxy(this.preCategory, this));
      this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {
        float: "right"
      },
        "content-chooser-actions-bar").hide();

      this.bSelect = EW.addActionButton({
        text: "",
        class: "btn-float btn-success icon-ok pos-se",
        parent: parentDialog,
        handler: function () {
          _this.selectContent(null);
        }
      }).hide();

      this.folderList = $("#content-chooser #content-chooser-folders-list");
      this.folderListHeader = this.folderList.children().eq(0);
      this.folderListContent = this.folderList.children().eq(1);

      this.articlesList = $("#content-chooser #articles-list");
      this.articlesListHeader = this.articlesList.children().eq(0);
      this.articlesListContent = this.articlesList.children().eq(1);

      this.listFilesAndFolders(this.parentId);
    }

    LinkChooserDocuments.prototype.preCategory = function () {
      this.listFilesAndFolders(this.preParentId);
    };

    LinkChooserDocuments.prototype.listFilesAndFolders = function (parentId) {
      var _this = this;
      _this.preParentId = _this.parentId;
      _this.parentId = parentId;

      if (parentId == 0) {
        this.bUp.comeOut(200);
      } else {
        this.bUp.comeIn(300);
      }

      _this.folderListContent.empty();
      _this.folderList.show();
      _this.articlesListContent.empty();

      this.bSelect.hide();
      if (_this.contentType === "all" || _this.contentType === "list" || _this.contentType === "contentField") {
        _this.folderListHeader.html("tr{Loading folders}");

        $.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/contents-folders', {
          parent_id: parentId
        },
          function (data) {
            _this.folderListHeader.html("tr{Folders}");
            var cId = EW.getHashParameter("categoryId");
            $.each(data.data, function (index, element) {
              var temp = _this.createFolder(element);

              if (element.id == cId) {
                temp.addClass("selected");
                _this.oldItem = temp;
              }

              _this.folderListContent.append(temp);
            });
          }, "json");
      }

      if (_this.contentType === "all" || _this.contentType === "content" || _this.contentType === "contentField") {
        _this.articlesListHeader.html("tr{Loading articles}");

        $.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/contents-articles', {
          parent_id: parentId
        },
          function (data) {
            _this.articlesListHeader.html("tr{Articles}");

            var aId = EW.getHashParameter("articleId");
            $.each(data.data, function (index, element) {
              var temp = _this.createFile(element);

              if (element.id == aId) {
                temp.addClass("selected");
                _this.oldItem = temp;
              }

              _this.articlesListContent.append(temp);
            });
          }, "json");
      }
    };

    LinkChooserDocuments.prototype.createFolder = function (model) {
      var _this = this;
      var div = $("<div class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.click(function () {

        if (_this.contentType === "all" || _this.contentType === "list") {
          _this.bSelect.comeIn(300);
        }

        _this.document = {
          feederId: "admin/api/content-management/ew-list-feeder-folder",
          id: model.id
        };

        _this.highlightContent(div);
      });

      div.dblclick(function () {
        _this.listFilesAndFolders(model.id);
      });

      return div;
    };

    LinkChooserDocuments.prototype.createFile = function (model) {
      var _this = this;
      var div = $("<div class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.click(function () {

        if (_this.contentType === "all" || _this.contentType === "content") {
          _this.bSelect.comeIn(300);
        }

        _this.document = {
          feederId: "admin/api/content-management/ew-page-feeder-article",
          id: model.id
        };

        _this.highlightContent(div);
      });

      div.dblclick(function () {
        _this.selectContent("article", model);
      });

      return div;
    };

    LinkChooserDocuments.prototype.createField = function (model) {
      var _this = this;
      var div = $("<div class='content-item article'><span></span><p>{fieldId}</p></div>").EW().createView(model);
      div.click(function () {
        _this.bSelect.comeIn(300);
        _this.document = {
          feederId: "admin/api/content-management/content-fields",
          id: model.contentId,
          fieldId: model.fieldId
        };
        _this.highlightContent(div);
      });

      div.dblclick(function () {
        _this.selectContent("field");
      });

      return div;
    };

    LinkChooserDocuments.prototype.highlightContent = function (element)
    {
      if (this.oldElement) {
        this.oldElement.removeClass("selected");
      }

      element.addClass("selected");
      this.oldElement = element;
    };

    LinkChooserDocuments.prototype.showContentFields = function (content) {
      var _this = this;
      _this.preParentId = content.up_parent_id;
      _this.folderList.hide();
      //_this.folderListContent.empty();
      _this.articlesListContent.empty();
      _this.articlesListHeader.html("<span class='header-label'>"+content.title + "</span> Fields");

      if (content) {
        $.each(content.content_fields || {}, function (key, element) {
          var temp = _this.createField({
            contentId: content.id,
            fieldId: key
          });

          _this.articlesListContent.append(temp);
        });
      }
    };

    LinkChooserDocuments.prototype.selectContent = function (type, content) {
      if (this.contentType === "contentField" && type && type !== "field") {
        this.showContentFields(content);
        this.bUp.comeIn(300);

        return;
      }

<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference")
{
  echo 'var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(this.document));';
}
else
  echo $_REQUEST["callback"] . '(this.document);';
?>
    };

    return new LinkChooserDocuments();
  })();

</script>