<?php
session_start();
if (!$_SESSION['login'])
{
   header('Location: Login.php');
   return;
}
?>
<!DOCTYPE html>

<div  class="row">
   <div class="col-xs-12" >
      <div id="categories-list"  class="box box-white">

      </div>
   </div>
</div>
<div class="row">
   <div class="col-xs-12" >
      <div class="box box-white"  id="articles-list">

      </div>
   </div>
</div>

<script  type="text/javascript">

   function Documents()
   {
      var self = this;
      this.parentId = 0;
      this.categoryId = 0;
      this.articleId = 0;
      this.preParentId = -1;
      this.oldItem;
      this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {display: "none"});
      this.bUp.css("float", "right");
      this.bNewFolder = EW.addActivity({title: "tr{New Folder}", activity: "app-admin/ContentManagement/category-form.php", parent: "action-bar-items", hash: {categoryId: null}}).hide();
      this.bNewFile = EW.addActivity({title: "tr{New Article}", activity: "app-admin/ContentManagement/article-form.php", parent: "action-bar-items", hash: {articleId: null}}).hide().comeIn(300);

      this.seeFolderActivity = EW.getActivity({activity: "app-admin/ContentManagement/category-form.php_see"});
      this.seeArticleActivity = EW.getActivity({activity: "app-admin/ContentManagement/article-form.php_see"});
      if (this.seeArticleActivity || this.seeFolderActivity)
         this.bSee = EW.addAction("tr{See}", $.proxy(this.seeDetails, this), null, "action-bar-items").hide();
      else
         this.bSee = $();
      var oldCn = 0;
      $(document).off("article-list");
      $(document).on("article-list.refresh", function () {
         self.listCategories();
      });
      /*$(document).off("category-list");
      $(document).on("category-list.refresh", function () {
         self.listCategories();
      });*/
   }

   Documents.prototype.preCategory = function ()
   {
      EW.setHashParameter("parent", this.preParentId);
   };

   Documents.prototype.seeDetails = function ()
   {
      var categoryId = EW.getHashParameter("categoryId");
      var articleId = EW.getHashParameter("articleId");
      if (categoryId)
      {
         this.categoryId = categoryId;
         this.seeFolderActivity({categoryId: categoryId});
      }
      else if (articleId)
      {
         this.articleId = articleId;
         this.seeArticleActivity({articleId: articleId});
      }
   };

   /*Documents.prototype.seeArticle = function ()
    {
    tp = EW.createModal({class: "full", onClose: function ()
    {
    article.dispose();
    EW.setHashParameter("cmd", null);
    }});
    documents.currentTopPane = tp;
    EW.lock(tp);
    $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/article-form.php', {act: "see", articleId: documents.articleId, categoryId: documents.parentId}, function (data) {
    tp.html(data);
    article.editArticleForm();
    });
    };*/


   Documents.prototype.newArticle = function ()
   {
      tp = EW.createModal({class: "full", onClose: function ()
         {
            article.dispose();
            EW.setHashParameter("cmd", null);
         }});
      documents.currentTopPane = tp;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/article-form.php', {categoryId: documents.parentId}, function (data) {
         tp.html(data);
         article.newArticleForm();
      });
   };


   Documents.prototype.newCategory = function ()
   {
      var temp = documents.categoryId;
      tp = EW.newTopPane(function () {
         EW.setHashParameter("cmd", null);
         //contentManagement.showActions();
      });
      documents.currentTopPane = tp;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/category-form.php', {parentId: documents.categoryId}, function (data) {
         tp.html(data);
      });
   };

   Documents.prototype.addCategory = function ()
   {
      //var c = tinyMCE.activeEditor.getContent();
      var params = $.parseJSON($("#category-form").serializeJSON());
      params.parentId = documents.parentId;
      //alert(params);
      if ($("#title").val())
      {
         EW.lock(documents.currentTopPane, "Saving...");
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/add_category', params, function (data) {
            //if (data.status === "success")
            //{
            EW.$("body").EW().notify(data);
            documents.listCategories();
            documents.currentTopPane.dispose();
            //}
         }, "json");
      }
      return false;
   };

   Documents.prototype.editCategory = function ()
   {
      if ($("#title").val())
      {
         EW.lock(documents.currentTopPane, "Saving...");
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/update_category', $.parseJSON($("#category-form").serializeJSON()), function (data) {
            // if (data.status === "success")
            //{
            documents.listCategories();
            $("body").EW().notify(data);
            EW.unlock(documents.currentTopPane);
            //}

         }, "json");
      }
      return false;
   };

   Documents.prototype.deleteCategory = function ()
   {
      $('<div></div>').appendTo('body')
              .html('<div><p>Are you sure of deleting this folder?</p></div>')
              .dialog({
                 modal: true, title: 'Delete Folder', zIndex: 1000, autoOpen: true,
                 width: '300px', resizable: false,
                 buttons: {
                    Yes: function () {
                       EW.lock(documents.currentTopPane, "Saving...");
                       $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/delete_category', {
                          categoryId: documents.categoryId}, function (data) {
                          if (data.status === "unable")
                          {
                             //listCategories();
                             //EW.unlock(contentManagement.currentTopPane);
                             alert("To delete this folder you have to delete all it's sub categories and articles first.");
                             EW.unlock(documents.currentTopPane);
                          }
                          else if (data.status === "success")
                          {
                             EW.setHashParameter("categoryId", null);
                             $("body").EW().notify(data);
                             documents.listCategories();
                             documents.currentTopPane.dispose();
                          }
                          else
                          {
                             EW.unlock(documents.currentTopPane);
                             $("body").EW().notify(data);
                          }
                       }, "json");
                       $(this).dialog("close");
                    },
                    No: function () {
                       //doFunctionForNo();
                       $(this).dialog("close");
                    }
                 },
                 close: function (event, ui) {
                    $(this).remove();
                 }
              });
      return false;
   };

   Documents.prototype.deleteArticle = function ()
   {
      if (confirm("Do you really want to delete this article?"))
      {
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/delete_article', {
            articleId: documents.articleId}, function (data) {
            if (data.status === "success")
            {
               EW.setHashParameter("article", null);
               $("body").EW().notify(data);
               documents.listCategories();
               documents.currentTopPane.dispose();
            }
            else
            {
               EW.unlock(documents.currentTopPane);
               $("body").EW().notify(data);
            }
         }, "json");
      }
   };

   Documents.prototype.selectCategory = function (rowElm, cId)
   {
      $(documents.oldItem).removeClass("selected");
      $(rowElm).addClass("selected");
      documents.oldItem = rowElm;
   };

   Documents.prototype.seeSubCategories = function () {
      EW.setHashParameters({"parent": documents.categoryId});
   };

   Documents.prototype.listCategories = function ()
   {
      //contentManagement.bSee.fadeOut(0);
      //$("#main-content").html("<span class='LoadingAnimation'></span>");
      var pId = 0;
      var hasNode = false;
      $("#categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_categories_list', {parent_id: documents.parentId}, function (data)
      {
         $("#categories-list").html("<div class='col-xs-12'><h2 id='cate-title'>tr{Folders}</h2></div>");
         //$("#cate-title").loadingText();
         var cId = EW.getHashParameter("categoryId");
         $.each(data.result, function (index, element)
         {
            pId = element.pre_parent_id;
            hasNode = true;
            var temp = documents.createFolder(element.title, element.round_date_created, element.id);
            if (element.id == cId)
            {
               temp.addClass("selected");
               documents.oldItem = temp;
            }
            $("#categories-list").append(temp);
         });
         if (hasNode)
         {
            documents.preParentId = pId;
         }
      }, "json");
      $("#articles-list").html("<div class='col-xs-12'><h2>Loading Article</h2></div>");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_articles_list', {parent_id: documents.parentId}, function (data)
      {
         $("#articles-list").html("<div class='col-xs-12'><h2>tr{Articles}</h2></div>");
         var aId = EW.getHashParameter("articleId");
         $.each(data.result, function (index, element)
         {
            pId = element.pre_parent_id;
            hasNode = true;
            var temp = documents.createFile(element.title, element.round_date_created, element.id);
            if (element.id == aId)
            {
               temp.addClass("selected");
               documents.oldItem = temp;
            }
            $("#articles-list").append(temp);
         });
         if (hasNode)
         {
            documents.preParentId = pId;
         }
      }, "json");

   };

   Documents.prototype.createFolder = function (title, dateCreated, id)
   {
      var div = $(document.createElement("div"));
      div.addClass("content-item folder");
      div.append("<span></span>");
      div.append("<p>" + title + "</p>");
      div.append("<p class='date'>" + dateCreated + "</p>");
      div.attr("data-category-id", id);
      div.click(function () {
         EW.setHashParameters({"articleId": null, "categoryId": id});
         //contentManagement.selectCategory(div, id);
      });
      div.dblclick(function () {
         //EW.setHashParameter("preCategoryId", documents.parentId);
         //alert(id + " " + documents.preParentId);
         EW.setHashParameter("parent", id);
         //contentManagement.selectCategory(div, id);
      });
      return div;
   };

   Documents.prototype.createFile = function (title, dateCreated, id)
   {
      var self = this;
      var div = $(document.createElement("div"));
      div.addClass("content-item article");
      div.append("<span></span>");
      div.append("<p>" + title + "</p>");
      div.append("<p class='date'>" + dateCreated + "</p>");
      div.attr("data-article-id", id);
      div.click(function () {
         EW.setHashParameters({categoryId: null, articleId: id});
      });
      div.dblclick(function () {
         //EW.setHashParameter("preCategoryId", contentManagement.parentId);
         self.seeArticleActivity({articleId: id});
         //contentManagement.selectCategory(div, id);
      });
      return div;
   };

   if (!EW.getHashParameter("parent"))
      EW.setHashParameter("parent", "0");
   //listCategories();

   var documents = new Documents();
   documents.handler = EW.addURLHandler(function ()
   {

      var cId = EW.getHashParameter("categoryId");
      var aId = EW.getHashParameter("articleId");
      var pcId = EW.getHashParameter("preCategoryId");
      var cmd = EW.getHashParameter("cmd");
      var parent = EW.getHashParameter("parent");

      if (!cId && !aId) {
         documents.bSee.comeOut(200);
         $(documents.oldItem).removeClass("selected");
      }
      if (cId)
      {
         documents.bSee.comeIn(300);
         documents.selectCategory($("div[data-category-id=" + cId + "]"), cId);
      }
      if (aId)
      {
         documents.bSee.comeIn(300);
         documents.selectCategory($("div[data-article-id=" + aId + "]"), aId);
      }

      if (!parent)
      {
         EW.setHashParameter("parent", "0");
         parent = "0";
      }
      else if (!cmd)
      {
         documents.bNewFolder.comeIn(300);
      }
      if (parent && documents.parentId !== parent)
      {
         documents.preParentId = documents.parentId;
         documents.parentId = parent;
         documents.listCategories();

      }

      if (parent >= 0)
      {
         //documents.bNewFile.comeIn(300);
      }
      else
      {
         //documents.bNewFile.comeOut(200);
      }
      if (parent == 0)
      {
         //pcId = null;
         //EW.setHashParameter("preCategoryId", null);
         documents.bUp.comeOut(300);
      }
      if (parent > 0)
         documents.bUp.comeIn(300);


      if (cmd)
      {
         if (cmd === "see")
         {

            if (cId)
            {
               documents.categoryId = cId;
               documents.seeCategory();
            }
            else if (aId)
            {
               documents.articleId = aId;
               documents.seeArticle();
            }
         }
         if (cmd == "new-category")
         {
            documents.newCategory();
         }
         else if (cmd == "new-article")
         {
            documents.newArticle();
         }
      }

      if (!cmd)
      {
         if (documents.currentTopPane)
            documents.currentTopPane.dispose();
         //contentManagement.setPreCategoryId(EW.getHashParameter("preCategoryId"));
      }



      return "DocumentsHandler";
   });

   documents.dispose = function ()
   {
      EW.removeURLHandler(documents.handler);
      documents.bNewFile.remove();
      documents.bNewFolder.remove();
      documents.bSee.remove();
      documents.bUp.remove();
      if (documents.currentTopPane)
         documents.currentTopPane.dispose();
   };
</script>