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
   // System.app.ContentManagement.module('Document',
   function Documents()
   {
      var self = this;
      this.parentId = 0;
      this.folderId = 0;
      this.articleId = 0;
      this.preParentId = -1;
      this.currentItem;
      this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {
         display: "none"
      });
      this.bUp.css("float", "right");
      this.bNewFolder = EW.addActivity({
         title: "tr{New Folder}",
         activity: "admin-html/content-management/category-form.php",
         parent: "action-bar-items",
         hash:
                 {
                    folderId: null
                 }
      }).hide().comeIn();
      this.bNewFile = EW.addActivity({
         title: "tr{New Article}",
         activity: "admin-html/content-management/article-form.php",
         parent: "action-bar-items",
         hash:
                 {
                    articleId: null
                 }
      }).hide().comeIn();
      this.seeFolderActivity = EW.getActivity({
         activity: "admin-html/content-management/category-form.php",
         onDone: function ()
         {
            EW.setHashParameters({
               folderId: null,
               articleId: null
            });
         }
      });
      this.seeArticleActivity = EW.getActivity({
         activity: "admin-html/content-management/article-form.php",
         onDone: function ()
         {
            EW.setHashParameters({
               folderId: null,
               articleId: null
            });
         }
      });
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
               EW.setHashParameters({
                  folderId: null,
                  articleId: eventData.data.id
               },
                       "document");
            if (eventData.data.type == "folder")
               EW.setHashParameters({
                  folderId: eventData.data.id,
                  articleId: null
               },
                       "document");
         }
      });
      /*$(document).off("category-list");
       $(document).on("category-list.refresh", function () {
       self.listCategories();
       });*/
   }

   Documents.prototype.preCategory = function ()
   {
      System.setHashParameters({
         //app:System.navigation[0],
         "parent": this.preParentId
      });
   };
   Documents.prototype.seeDetails = function ()
   {
      var tFolderId = EW.getHashParameter("folderId", "document");
      var tArticleId = EW.getHashParameter("articleId", "document");
      EW.activeElement = documents.currentItem;
      if (tFolderId)
      {
         this.folderId = tFolderId;
         this.seeFolderActivity({
            folderId: tFolderId
         });
      } else if (tArticleId)
      {
         this.articleId = tArticleId;
         this.seeArticleActivity({
            articleId: tArticleId
         });
      }
   };
   Documents.prototype.listCategories = function ()
   {
      var pId = 0;
      var hasNode = false;
      var aId = EW.getHashParameter("articleId", "document");
      $("#categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");
      $.post('~admin-api/content-management/get-categories-list', {
         parent_id: documents.parentId
      },
              function (data)
              {
                 $("#categories-list").html("<h2 id='cate-title'>tr{Folders}</h2><div class='row box-content'></div>");
                 //$("#cate-title").loadingText();
                 var cId = EW.getHashParameter("folderId", "document");
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
      $.post('~admin-api/content-management/get-articles-list', {
         parent_id: documents.parentId
      },
              function (data)
              {
                 $("#articles-list").html("<h2>tr{Articles}</h2><div class='row box-content'></div>");

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

   Documents.prototype.focusOn = function (item)
   {
      if (this.currentItem) {
         this.currentItem.removeClass("selected");
      }
      item.addClass("selected");
      this.currentItem = item;
   };

   Documents.prototype.createFolder = function (title, dateCreated, id, model)
   {
      var self = this;
      var div = $("<div tabindex='1' class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.dblclick(function () {
         System.setHashParameters({"parent": id});
      });
      div.on('focus', function ()
      {
         EW.setHashParameters({
            "articleId": null,
            "folderId": id
         },
                 "document");
         self.focusOn(div);
      });
      div.data("label", title);
      return div;
   };

   Documents.prototype.createFile = function (title, dateCreated, id, model)
   {
      var self = this;
      var div = $("<div tabindex='1' class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
      div.dblclick(function () {
         self.seeArticleActivity({
            articleId: id
         });
      });
      div.on('click focus', function ()
      {
         EW.setHashParameters({
            folderId: null,
            articleId: id
         },
                 "document");
         self.focusOn(div);
      });
      div.data("label", title);
      return div;
   };
   var documents = new Documents();

   documents.handler = EW.addURLHandler(function ()
   {
      var itemId = EW.getHashParameter("articleId", "document") || EW.getHashParameter("folderId", "document") || null;

      if (!itemId)
      {

         documents.bSee.comeOut();
         $(documents.currentItem).removeClass("selected");
      }
      if (itemId)
      {
         documents.bSee.comeIn();
      }
   }, "document");
   EW.addURLHandler(function ()
   {
      var parent = EW.getHashParameter("parent");
      if (!parent)
      {
         parent = "0";
      } else
      {
         documents.bNewFolder.comeIn();
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
   });

   documents.dispose = function ()
   {
      EW.removeURLHandler(documents.handler);
   };
</script>
