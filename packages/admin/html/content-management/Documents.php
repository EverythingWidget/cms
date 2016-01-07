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
   (function (System) {
      function Documents(module) {
         this.module = module;
      }

      Documents.prototype.start = function () {
         this.parentId = null;
         this.folderId = 0;
         this.articleId = 0;
         this.upParentId = -1;
         this.currentItem = $();
         this.bUp = EW.addAction("tr{Up}", $.proxy(this.preCategory, this), {
            display: "none"
         });

         this.bUp.css("float", "right");
         this.bNewFolder = EW.addActivity({
            title: "tr{New Folder}",
            activity: "admin/html/content-management/folder-form.php",
            parent: "action-bar-items",
            hash: {
               folderId: null
            }
         }).hide();

         this.bNewFile = EW.addActivity({
            title: "tr{New Article}",
            activity: "admin/html/content-management/article-form.php_new",
            parent: "action-bar-items",
            hash: {
               articleId: null
            },
            onDone: function (hash) {
               hash.articleId = null;
            }
         }).hide();

         this.seeFolderActivity = EW.getActivity({
            activity: "admin/html/content-management/folder-form.php_see",
            onDone: function () {
               EW.setHashParameters({
                  folderId: null,
                  articleId: null
               });
            }
         });

         this.seeArticleActivity = EW.getActivity({
            activity: "admin/html/content-management/article-form.php_see",
            onDone: function () {
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

         $(document).off("article-list");
         $(document).on("article-list.refresh", function (e, eventData) {
            self.listCategories();
            if (eventData) {
               if (eventData.data.type == "article") {
                  EW.setHashParameters({
                     folderId: null,
                     articleId: eventData.data.id
                  }, "document");
               }

               if (eventData.data.type == "folder") {
                  EW.setHashParameters({
                     folderId: eventData.data.id,
                     articleId: null
                  }, "document");
               }
            }
         });
      };

      Documents.prototype.preCategory = function () {
         System.setHashParameters({
            article: null
         });
         this.module.setNav("app", this.upParentId);
      };

      Documents.prototype.seeDetails = function () {
         var tFolderId = System.getHashParam("folder");
         var tArticleId = System.getHashParam("article");
         EW.activeElement = this.currentItem;
         if (tFolderId) {
            this.folderId = tFolderId;
            this.seeFolderActivity({
               folderId: tFolderId
            });
         } else if (tArticleId) {
            this.articleId = tArticleId;
            this.seeArticleActivity({
               articleId: tArticleId
            });
         }
      };

      Documents.prototype.listCategories = function () {
         var _this = this;
         var pId = 0;
         var hasNode = false;
         var aId = System.getHashParam("article");
         var cId = System.getHashParam("folder");
         $("#categories-list").html("<div class='col-xs-12'><h2 >Loading Folders</h2></div>");

         System.addActiveRequest($.post('~admin/api/content-management/contents-folders', {
            parent_id: _this.parentId
         }, function (data) {
            $("#categories-list").html("<h2 id='cate-title'>tr{Folders}</h2><div class='row box-content'></div>");
            //$("#cate-title").loadingText();

            var foldersPane = $("#categories-list .box-content");
            $.each(data.data, function (index, element) {
               pId = element.pre_parent_id;
               hasNode = true;
               var temp = _this.createFolder(element.title, element.round_date_created, element.id, element);
               temp.addClass("anim-scale-in");
               if (element.id == cId) {
                  temp.addClass("selected");
                  _this.currentItem = temp;
               }
               foldersPane.append(temp);
               temp.addClass("in");
            });

            if (hasNode) {
               _this.upParentId = pId;
            }
         }, "json"));

         $("#articles-list").html("<div class='col-xs-12'><h2>Loading Article</h2></div>");
         System.addActiveRequest($.post('~admin/api/content-management/contents-articles', {
            parent_id: _this.parentId
         }, function (data) {
            $("#articles-list").html("<h2>tr{Articles}</h2><div class='row box-content'></div>");

            var articlesPane = $("#articles-list .box-content");
            $.each(data.data, function (index, element) {
               pId = element.pre_parent_id;
               hasNode = true;
               var temp = _this.createFile(element.title, element.round_date_created, element.id, element);
               temp.addClass("anim-scale-in");
               if (element.id == aId) {
                  temp.addClass("selected");
                  _this.currentItem = temp;
               }
               articlesPane.append(temp);
               // setTimeout(function ()            {
               temp.addClass("in");
               //}, 1);

            });

            if (hasNode) {
               _this.upParentId = pId;
            }
         }, "json"));
      };

      Documents.prototype.focusOn = function (item) {
         if (this.currentItem) {
            this.currentItem.removeClass("selected");
         }
         item.addClass("selected");
         this.currentItem = item;
      };

      Documents.prototype.createFolder = function (title, dateCreated, id, model) {
         var self = this;
         var div = $("<div tabindex='1' class='content-item folder' data-category-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.dblclick(function () {
            self.module.setNav("app", id);
         });

         div.on('focus', function () {
            System.setHashParameters({
               article: null,
               folder: id
            });
            self.focusOn(div);
         });

         div.data("label", title);
         return div;
      };

      Documents.prototype.createFile = function (title, dateCreated, id, model) {
         var self = this;
         var div = $("<div tabindex='1' class='content-item article' data-article-id='{id}'><span></span><p>{title}</p><p class='date'>{round_date_created}</p></div>").EW().createView(model);
         div.dblclick(function () {
            self.seeArticleActivity({
               articleId: id
            });
         });

         div.on('focus', function () {
            System.setHashParameters({
               folder: null,
               article: id
            });
            self.focusOn(div);
         });
         div.data("label", title);
         return div;
      };

      var module = function () {
         this.type = "appSection";
         this.onInit = function () {
            this.class = new Documents(this);

            this.on("app", function (e, parent) {
               if (!parent) {
                  parent = 0;
               } else {
                  this.class.bNewFolder.comeIn();
               }

               if (this.class.parentId !== parent) {
                  this.class.preParentId = this.class.parentId;
                  this.class.parentId = parseInt(parent);
                  this.class.listCategories();
               }

               if (parent === 0) {
                  this.class.bUp.comeOut(300);
               }

               if (parent > 0) {
                  this.class.bUp.comeIn(300);
               }
            });

            this.on("article", function (full, id) {
               console.log(id)

               if (!id) {
                  this.class.bSee.comeOut();
                  this.class.currentItem.removeClass("selected");
               }

               if (id) {
                  this.class.bSee.comeIn();
               }
            });

            this.on("folder", function (full, id) {
               if (!id) {
                  this.class.bSee.comeOut();
                  this.class.currentItem.removeClass("selected");
               }

               if (id) {
                  this.class.bSee.comeIn();
               }
            });
         };

         this.onStart = function () {
            this.class.start();
            this.class.bNewFile.comeIn();
            this.class.bNewFolder.comeIn();
         };
      };

      System.module("content-management").module("documents", module);
   }(System));
</script>
