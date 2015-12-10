<?php ?>
<div  class="row">
   <div class="col-xs-12" >            
      <div id="folders-list"  class="box">
         <h2 id='cate-title' class="box-title actions-bar action-bar-items">
            <span>tr{Folders}</span>
            <button class='button' id='documents-up-btn' type='button' style='display:none;float:right;'>UP</button>
         </h2>
         <div class='row box-content'></div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-xs-12" >
      <div class="box"  id="articles-list">
         <h2>tr{Articles}</h2>
         <div class='row box-content'></div>
      </div>
   </div>
</div>


<script  type="text/javascript">
   var LinkChooserDocuments = (function () {
      function LinkChooserDocuments() {
         var self = this;
         this.parentId = 0;
         this.categoryId = 0;
         this.articleId = 0;
         this.preParentId = -1;
         this.document = {};
         $("#documents-up-btn").click($.proxy(this.preCategory, this));
         this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {float: "right"}).hide();
         this.bSelect = EW.addAction("tr{Select}", $.proxy(this.selectContent, this)).addClass("btn-success").hide();

         this.folderList = $("#link-chooser #folders-list");
         //this.folderListHeader = this.articlesList.children().eq(0);
         this.folderListContent = this.folderList.children().eq(1);

         this.articlesList = $("#link-chooser #articles-list");
         this.articlesListHeader = this.articlesList.children().eq(0);
         this.articlesListContent = this.articlesList.children().eq(1);

         this.listFilesAndFolders(this.parentId);
      }

      LinkChooserDocuments.prototype.preCategory = function ()
      {
         this.listFilesAndFolders(this.preParentId);
         //EW.setHashParameter("parent", this.preParentId);
      };

      LinkChooserDocuments.prototype.listFilesAndFolders = function (parentId)
      {
         var self = this;
         var pId = 0;
         self.preParentId = self.parentId;
         self.parentId = parentId
         var hasNode = false;
         if (parentId == 0)
         {
            this.bUp.comeOut(200);
         } else
         {
            this.bUp.comeIn(300);
         }
         this.bSelect.hide();
         //$("#link-chooser #categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");
         self.folderListContent.empty();
         $.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-categories-list', {parent_id: parentId}, function (data)
         {
            //$("#cate-title").loadingText();
            var cId = EW.getHashParameter("categoryId");
            var foldersPane = $("#link-chooser #categories-list .box-content").empty();
            $.each(data.items, function (index, element)
            {
               //pId = element.pre_parent_id;
               //hasNode = true;
               var temp = self.createFolder(element);
               if (element.id == cId)
               {
                  temp.addClass("selected");
                  self.oldItem = temp;
               }
               self.folderListContent.append(temp);
            });

         }, "json");
         console.log(self.articlesListHeader)
         self.articlesListHeader.html("Loading articles");
         self.articlesListContent.empty();
         $.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-articles-list', {parent_id: parentId}, function (data)
         {
            //$("#link-chooser #articles-list").html("<h2>tr{Articles}</h2><div class='row box-content'></div>");
            var aId = EW.getHashParameter("articleId");

            $.each(data.items, function (index, element) {
               var temp = self.createFile(element);
               if (element.id == aId) {
                  temp.addClass("selected");
                  self.oldItem = temp;
               }
               self.articlesListContent.append(temp);
            });

         }, "json");

      };

      LinkChooserDocuments.prototype.createFolder = function (model)
      {
         var self = this;
         var div = $("<div class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.click(function () {
            self.document = {type: "folder", id: model.id};
            self.highlightContent(div);
            //EW.setHashParameters({"articleId": null, "categoryId": model.id});
         });
         div.dblclick(function () {
            self.listFilesAndFolders(model.id);
         });
         return div;
      };

      LinkChooserDocuments.prototype.createFile = function (model)
      {
         var self = this;
         var div = $("<div class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.click(function () {
            self.document = {type: "article", id: model.id};
            self.highlightContent(div, model);
            //EW.setHashParameters({categoryId: null, articleId: id});
         });
         div.dblclick(function () {
            self.selectContent("article", model.id);
         });
         return div;
      };

      LinkChooserDocuments.prototype.createField = function (model)
      {
         var self = this;
         var div = $("<div class='content-item article' data-field-id='{id}'><span></span><p>{title}</p></div>").EW().createView(model);
         div.click(function () {
            //self.document = {type: "article", id: model.id};
            //self.highlightContent(div, model);
            //EW.setHashParameters({categoryId: null, articleId: id});
         });
         div.dblclick(function () {
            //self.selectContent("article", model.id);
         });
         return div;
      };

      LinkChooserDocuments.prototype.highlightContent = function (element, data)
      {
         if (this.oldElement)
            this.oldElement.removeClass("selected");
         element.addClass("selected");
         this.oldElement = element;
         this.bSelect.comeIn(300);
         if (data)
            this.showContentFields(data.contentFields);
      };

      LinkChooserDocuments.prototype.showContentFields = function (fields) {
         var _this = this;
         $.each(fields, function (index, element) {
            var temp = _this.createField(element);

            _this.articlesListContent.append(temp);
         });
      };

      LinkChooserDocuments.prototype.selectContent = function ()
      {

<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference")
{
   echo 'var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(this.document));';
}
else
   echo $_REQUEST["callback"] . '(this.document);';
?>
      }
      return new LinkChooserDocuments();
   })();

</script>