<?php
session_start();
if (!$_SESSION['login'])
{
   header('Location: Login.php');
   return;
}
?>

<div id="media-items-container" class="row">
   <div id="files-list" class="elements-list">

   </div>

   <div id="album-data-card" class="card z-index-1 width-lg-8">

   </div>
</div>
<script>
   (function (System) {

      function MediaComponent(module) {
         var _this = this;
         _this.module = module;
         _this.module.type = "app-section";

         _this.module.onInit = function () {
            _this.init();
         };

         _this.module.onStart = function () {
            _this.start();
         };
      }

      MediaComponent.prototype.init = function () {
         var _this = this;
         this.module.on("album", function (e, id, images) {
            alert(id + " " + images)
            if (id > 0) {
               _this.newAlbumActivity.comeOut();
               _this.uploadFileActivity.comeIn();
               _this.bBack.comeIn();
            } else {
               _this.newAlbumActivity.comeIn();
               _this.uploadFileActivity.comeOut();
               _this.bBack.comeOut();
            }

            if (!id) {
               id = 0;
            }

            if (images) {
               if (id !== null && _this.parentId !== id) {
                  _this.parentId = parseInt(id);
                  if (_this.listInited) {
                     _this.module.setParam("select", null, true);
                  }

                  _this.listMedia();
               }
            }
         });

         this.module.on("select", function (itemId) {
            if (itemId > 0) {
               _this.selectedItemId = itemId;
               _this.seeAction.comeIn();

               _this.currentItem.removeClass("selected");
               $("div[data-item-id='" + _this.selectedItemId + "']").focus();
            } else {
               _this.seeAction.comeOut();
            }
         });

         System.UI.components.document.off("media-list");
         System.UI.components.document.on("media-list.refresh", function (e, eventData) {
            _this.listMedia();
         });
      };

      MediaComponent.prototype.start = function () {
         var _this = this;
         this.parentId = null;
         this.itemsList = $();
         this.currentItem = $();
         this.bDel = $();
         this.listInited = false;

         this.bBack = EW.addAction("tr{Back to Media}", function () {
            System.setHashParameters({
               album: "0/images"
            });
         }, {
            float: "right",
            display: "none"
         }, "action-bar-items").removeClass("btn-primary").addClass("btn-default");

         this.newAlbumActivity = EW.addActivity({
            title: "tr{New Album}",
            activity: "admin/html/content-management/album-form.php",
            parent: "action-bar-items"
         }).hide();

         this.uploadFileActivity = EW.addActivity({
            title: "tr{Upload Photo}",
            activity: "admin/html/content-management/upload-form.php",
            parent: "action-bar-items",
            hash: function () {
               return {
                  parentId: System.getHashNav("album")[0]
               };
            },
            onDone: function () {
               EW.setHashParameter("parentId", null);
            }
         }).hide();

         this.seeAlbumActivity = EW.getActivity({
            activity: "admin/html/content-management/album-form.php",
            parent: "action-bar-items"
         });
         //this.seeArticleActivity = EW.getActivity({activity: "admin/api/ContentManagement/article-form.php_see"});

         if (this.seeAlbumActivity) {
            this.seeAction = EW.addAction("tr{See}", $.proxy(this.seeItemDetails, this)).hide();
         } else {
            this.seeAction = $();
         }

         _this.module.setParamIfNone("album", "0/images");
      };

      MediaComponent.prototype.seeItemDetails = function () {
         var albumId = this.selectedItemId;
         EW.activeElement = this.currentItem;
         if (albumId) {
            this.albumId = albumId;
            this.seeAlbumActivity({albumId: albumId});
         }
      };

      MediaComponent.prototype.seeImageActivity = function (id) {

      };

      MediaComponent.prototype.listMedia = function () {
         var _this = this;
         //var albums = $("<div class='row box-content'></div>");
         this.itemsList = $("<div class='box-content anim-fade-in'></div>");
         var elementsList = $("#files-list");
         elementsList.html("<h2>Loading...</h2><div class='loader center'></div>");
         var albumDataCard = $("#album-data-card");
         this.listInited = false;

         System.addActiveRequest($.get('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {parent_id: _this.parentId}, function (response) {
            var curp = null;
            if (_this.parentId === 0) {
               albumDataCard.hide();
               elementsList.html("<h2>tr{Albums}</h2>");
               curp = elementsList;
               elementsList.show();
            } else {
               elementsList.hide();
               albumDataCard.html("<div  class='card-header'><h1>" +
                       response.included.album.title +
                       "</h1>" +
                       "<div id='album-card-action-bar' class='card-action-bar'></div>" +
                       "</div><div class='card-content top-devider'>" +
                       "<span class='card-content-title'>tr{Images}</span></div>");
               curp = albumDataCard.find(".card-content");
               var prop = EW.addAction("tr{Properties}", function () {
                  _this.seeAlbumActivity({
                     albumId: response.included.album.id
                  });
               }, null, "album-card-action-bar").removeClass("btn-primary").addClass("btn-text btn-default");

               var deleteAlbum = EW.addAction("tr{Delete}", function () {
                  _this.seeAlbumActivity({
                     albumId: response.included.album.id
                  });
               }, null, "album-card-action-bar").removeClass("btn-primary").addClass("btn-text btn-danger pull-right");
               albumDataCard.show();
            }

            $.each(response.data, function (index, element) {
               var temp = _this.createMediaElement(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);

               if (element.type === "album") {
                  temp.on('keydown', function (e) {
                     if (e.which === 13) {
                        System.setHashParameters({
                           album: element.id + "/images"
                        });
                     }
                  });

                  temp.dblclick(function () {
                     System.setHashParameters({
                        album: element.id + "/images"
                     });
                  });

                  temp.on("focus", function (e) {
                     _this.module.setParam("select", element.id);
                  });
                  _this.itemsList.append(temp);
               } else {
                  temp.attr("data-url", element.url);
                  temp.dblclick(function () {
                     EW.setHashParameter("cmd", "preview", "media");
                  });

                  temp.on("focus", function () {
                     EW.setHashParameter("itemId", element.id, "media");
                     EW.setHashParameter("url", element.url, "media");
                     EW.setHashParameter("filename", element.filename, "media");
                     EW.setHashParameter("fileExtension", element.fileExtension, "media");
                     EW.setHashParameter("absUrl", element.absUrl, "media");
                     EW.setHashParameters({albumId: null, "imageId": element.id}, "media");

                  });

                  _this.itemsList.append(temp);
               }

            });

            curp.append(_this.itemsList);
            _this.itemsList.addClass("in");
            _this.listInited = true;
            // Select current item            
            if (_this.selectedItemId) {
               $("div[data-item-id='" + _this.selectedItemId + "']").focus();
            }

         }, "json"));
      };

      MediaComponent.prototype.createMediaElement = function (title, type, ext, size, ImageURL, id) {
         var _this = this,
                 div = $(document.createElement("div")),
                 img = $(document.createElement("img"));

         div.addClass("content-item")
                 .addClass(type)
                 .addClass(ext);
         div.attr("tabindex", "1");
         div.on("focus click", function () {
            _this.currentItem.removeClass("selected");
            div.addClass("selected");
            _this.currentItem = div;
         });

         if (ImageURL) {
            img.attr("src", ImageURL);
            div.append(img);
         } else {
            div.append("<span></span>");
         }

         div.append("<p>" + title + "</p>");

         if (size) {
            div.append("<p class='date'>" + size + " KB</p>");
         }

         div.attr("data-item-id", id);
         return div;
      };

      System.module("content-management").module("media", function () {
         new MediaComponent(this);
      });
   }(System));

</script>
