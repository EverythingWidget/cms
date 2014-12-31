<?php
session_start();
if (!$_SESSION['login'])
{
   header('Location: Login.php');
   return;
}
?>
<div  class="row">
   <div class="col-xs-12" >            
      <div id="categories-list"  class="box">
         <h2 id='cate-title' class="actions-bar action-bar-items">
            <span>tr{Folders}<span>
            <button class='button' id='documents-up-btn' type='button' style='display:none;float:right;'>UP</button>
         </h2>
         <div class='row box-content'></div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-xs-12" >
      <div class="box"  id="articles-list">

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
         //this.bUp.css("float", "right");
         /*this.bNewFolder = EW.addActivity({title: "tr{New Folder}", activity: "app-admin/ContentManagement/category-form.php", parent: "action-bar-items", hash: {categoryId: null}}).hide();
          this.bNewFile = EW.addActivity({title: "tr{New Article}", activity: "app-admin/ContentManagement/article-form.php", parent: "action-bar-items", hash: {articleId: null}}).hide().comeIn(300);
          
          this.seeFolderActivity = EW.getActivity({activity: "app-admin/ContentManagement/category-form.php_see"});
          this.seeArticleActivity = EW.getActivity({activity: "app-admin/ContentManagement/article-form.php_see"});
          if (this.seeArticleActivity || this.seeFolderActivity)
          this.bSee = EW.addAction("tr{See}", $.proxy(this.seeDetails, this), null, "action-bar-items").hide();
          else
          this.bSee = $();*/
         var oldCn = 0;
         /*$(document).off("article-list");
          $(document).on("article-list.refresh", function () {
          self.listCategories();
          });*/
         /*$(document).off("category-list");
          $(document).on("category-list.refresh", function () {
          self.listCategories();
          });*/
         this.listCategories(this.parentId);

      }

      LinkChooserDocuments.prototype.preCategory = function ()
      {
         this.listCategories(this.preParentId);
         //EW.setHashParameter("parent", this.preParentId);
      };

      LinkChooserDocuments.prototype.listCategories = function (parentId)
      {
         var self = this;
         var pId = 0;
         self.preParentId = self.parentId;
         self.parentId = parentId
         var hasNode = false;
         if (parentId == 0)
         {
            this.bUp.comeOut(200);            
         }
         else
         {
            this.bUp.comeIn(300);
         }
         this.bSelect.hide();
         //$("#link-chooser #categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_categories_list', {parent_id: parentId}, function (data)
         {
            //$("#cate-title").loadingText();
            var cId = EW.getHashParameter("categoryId");
            var foldersPane = $("#link-chooser #categories-list .box-content").empty();
            $.each(data.result, function (index, element)
            {
               //pId = element.pre_parent_id;
               //hasNode = true;
               var temp = self.createFolder(element);
               if (element.id == cId)
               {
                  temp.addClass("selected");
                  self.oldItem = temp;
               }
               foldersPane.append(temp);
            });

         }, "json");
         $("#link-chooser #articles-list").html("<div class='col-xs-12'><h2>Loading Article</h2></div>");
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_articles_list', {parent_id: parentId}, function (data)
         {
            $("#link-chooser #articles-list").html("<h2>tr{Articles}</h2><div class='row box-content'></div>");
            var aId = EW.getHashParameter("articleId");
            var articlesPane = $("#link-chooser #articles-list .box-content");
            $.each(data.result, function (index, element)
            {
               //pId = element.pre_parent_id;
               //hasNode = true;
               var temp = self.createFile(element);
               if (element.id == aId)
               {
                  temp.addClass("selected");
                  self.oldItem = temp;
               }
               articlesPane.append(temp);
            });

         }, "json");

      };

      LinkChooserDocuments.prototype.createFolder = function (model)
      {
         var self = this;
         var div = $("<div class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.click(function () {
            self.document = {type: "category", id: model.id};
            self.highlightContent(div);
            //EW.setHashParameters({"articleId": null, "categoryId": model.id});
         });
         div.dblclick(function () {
            self.listCategories(model.id)
         });
         return div;
      };

      LinkChooserDocuments.prototype.createFile = function (model)
      {
         var self = this;
         var div = $("<div class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.click(function () {
            self.document = {type: "article", id: model.id};
            self.highlightContent(div);
            //EW.setHashParameters({categoryId: null, articleId: id});
         });
         div.dblclick(function () {
            self.selectContent("article", model.id);
         });
         return div;
      };
      LinkChooserDocuments.prototype.highlightContent = function (element)
      {
         if (this.oldElement)
            this.oldElement.removeClass("selected");
         element.addClass("selected");
         this.oldElement = element;
         this.bSelect.comeIn(300);
      }
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