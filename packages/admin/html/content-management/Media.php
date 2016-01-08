<?php
session_start();
if (!$_SESSION['login'])
{
   header('Location: Login.php');
   return;
}
?>

<div id="media-items-container" class="row">
   <div class="col-xs-12" >
      <div id="files-list" class="box">

      </div>
   </div>
</div>
<script>
   (function (System) {

      function Media(module) {
         var _this = this;
         this.module = module;
         this.currentItem = $();
         this.itemsList = $();

         $(document).off("media-list");
         $(document).on("media-list.refresh", function (e, eventData) {
            _this.listMedia();
         });

         module.on("album", function (e, id, images) {

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
                  _this.listMedia();
               }
            }
         });

         module.on("select", function (itemId) {
            if (itemId > 0) {
               _this.selectedItemId = itemId;
               _this.seeAction.comeIn();

            } else {
               _this.seeAction.comeOut();
            }
         });
      }

      Media.prototype.start = function () {
         var _this = this;
         this.parentId = null;
         this.currentItem = $();

         this.bBack = EW.addAction("tr{Back to Media}", function () {
            _this.module.setNav("app", null);
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
               return {parentId: EW.getHashParameter("parent")};
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
            this.seeAction = EW.addAction("tr{See}", $.proxy(this.seeDetails, this)).hide();
         } else {
            this.seeAction = $();
         }

         this.bDel = $();
         //System.setHashParameters({album: "0/images"})

      };

      Media.prototype.seeDetails = function () {
         var albumId = this.selectedItemId;
         EW.activeElement = this.currentItem;
         if (albumId) {
            this.albumId = albumId;
            this.seeAlbumActivity({albumId: albumId});
         } /*else if (imageId) {
          this.imageId = imageId;
          this.seeImageActivity({articleId: imageId});
          }*/
      };

      Media.prototype.seeImageActivity = function (id) {

      };

      Media.prototype.listMedia = function () {
         var _this = this;
         //var albums = $("<div class='row box-content'></div>");
         this.itemsList = $("<div class='row box-content'></div>");

         $("#files-list").html("<h2>Loading...</h2>");
         $("#files-list").append(_this.itemsList);

         System.addActiveRequest($.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {parent_id: _this.parentId}, function (response) {
            if (_this.parentId === 0) {
               $("#files-list > h2").html("tr{Albums}");
            } else {
               $("#files-list > h2").html("tr{Images}");
            }

            $.each(response.data, function (index, element) {
               //flag = true;
               //pId = element.parentId;
               var temp = _this.createMedia(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);
               //temp.click(temp.focus);
               if (element.type === "album") {
                  temp.on('keydown', function (e) {
                     if (e.which === 13) {
                        _this.module.setNav("app", element.id);
                     }
                  });

                  temp.dblclick(function () {
                     _this.module.setNav("app", element.id);
                  });

                  temp.on("focus", function (e) {
                     System.setHashParameters({
                        select: element.id
                     });
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

            // Select current item
            if (_this.selectedItemId) {
               $("div[data-item-id='" + _this.selectedItemId + "']").focus();
            }

         }, "json"));
      };

      Media.prototype.createMedia = function (title, type, ext, size, ImageURL, id) {
         var self = this,
                 div = $(document.createElement("div"));

         div.addClass("content-item");
         div.addClass(type);
         div.addClass(ext);
         div.attr("tabindex", "1");
         div.on("focus click", function () {
            self.currentItem.removeClass("selected");
            div.addClass("selected");
            self.currentItem = div;
         });

         var img = $(document.createElement("img"));
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

      var module = function () {
         this.type = "appSection";
         this.onInit = function () {
            this.class = new Media(this);
         };

         this.onStart = function () {
            this.class.start();

         };
      };

      System.module("content-management").module("media", module);
   }(System));

</script>
