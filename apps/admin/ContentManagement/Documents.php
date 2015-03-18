<div  class="row">
   <div class="col-xs-12" >
      <div id="categories-list"  class="box">

      </div>
   </div>
</div>
<div class="row">
   <div class="col-xs-12" >
      <div class="box"  id="articles-list">

      </div>
   </div>
</div>

<script>
   function Documents()
   {
      var self = this;
      this.parentId = 0;
      this.categoryId = 0;
      this.articleId = 0;
      this.preParentId = -1;
      this.currentItem;
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
      $(document).on("article-list.refresh", function (e, eventData) {
         self.listCategories();
         if (eventData)
         {
            if (eventData.data.type == "article")
               EW.setHashParameters({categoryId: null, articleId: eventData.data.id});
            if (eventData.data.type == "folder")
               EW.setHashParameters({categoryId: eventData.data.id, articleId: null});
         }
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
      EW.activeElement = documents.currentItem;
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

   Documents.prototype.listCategories = function ()
   {
      //contentManagement.bSee.fadeOut(0);
      //$("#main-content").html("<span class='LoadingAnimation'></span>");
      var pId = 0;
      var hasNode = false;
      $("#categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_categories_list', {parent_id: documents.parentId}, function (data)
      {
         $("#categories-list").html("<h2 id='cate-title'>tr{Folders}</h2><div class='row box-content'></div>");
         //$("#cate-title").loadingText();
         var cId = EW.getHashParameter("categoryId");
         var foldersPane = $("#categories-list .box-content");
         $.each(data.result, function (index, element)
         {
            pId = element.pre_parent_id;
            hasNode = true;
            var temp = documents.createFolder(element.title, element.round_date_created, element.id, element);
            if (element.id == cId)
            {
               temp.addClass("selected");
               documents.currentItem = temp;
            }
            foldersPane.append(temp);
         });
         if (hasNode)
         {
            documents.preParentId = pId;
         }
      }, "json");
      $("#articles-list").html("<div class='col-xs-12'><h2>Loading Article</h2></div>");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_articles_list', {parent_id: documents.parentId}, function (data)
      {
         $("#articles-list").html("<h2>tr{Articles}</h2><div class='row box-content'></div>");
         var aId = EW.getHashParameter("articleId");
         var articlesPane = $("#articles-list .box-content");
         $.each(data.result, function (index, element)
         {
            pId = element.pre_parent_id;
            hasNode = true;
            var temp = documents.createFile(element.title, element.round_date_created, element.id, element);
            if (element.id == aId)
            {
               temp.addClass("selected");
               documents.currentItem = temp;
            }
            articlesPane.append(temp);
         });
         if (hasNode)
         {
            documents.preParentId = pId;
         }
      }, "json");

   };

   Documents.prototype.createFolder = function (title, dateCreated, id, model)
   {
      var self = this;
      var div = $("<div tabindex='1' class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.click(function () {
         EW.setHashParameters({"articleId": null, "categoryId": id});
      });
      div.dblclick(function () {
         EW.setHashParameter("parent", id);
      });
      div.on('focus', function ()
      {
         EW.setHashParameters({"articleId": null, "categoryId": id});
         $(self.currentItem).removeClass("selected");
         $(div).addClass("selected");
         self.currentItem = div;
      });
      return div;
   };

   Documents.prototype.createFile = function (title, dateCreated, id, model)
   {
      var self = this;
      var div = $("<div tabindex='1' class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.click(function () {
         EW.setHashParameters({categoryId: null, articleId: id});
      });
      div.dblclick(function () {
         self.seeArticleActivity({articleId: id});
      });
      div.on('focus', function ()
      {
         EW.setHashParameters({categoryId: null, articleId: id});
         $(self.currentItem).removeClass("selected");
         $(div).addClass("selected");
         self.currentItem = div;
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
         $(documents.currentItem).removeClass("selected");
      }
      if (cId)
      {
         documents.bSee.comeIn(300);
      }
      if (aId)
      {
         documents.bSee.comeIn(300);
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

      if (parent == 0)
      {
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
            }
            else if (aId)
            {
               documents.articleId = aId;
            }
         }
      }

      if (!cmd)
      {
         if (documents.currentTopPane)
            documents.currentTopPane.dispose();
         //contentManagement.setPreCategoryId(EW.getHashParameter("preCategoryId"));
      }
      //alert("d");
   });

   documents.dispose = function ()
   {
      EW.removeURLHandler(documents.handler);
   };
</script>
