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
   function Media()
   {
      var self = this;
      this.oldPreParentId = "";
      this.parentId = null;
      this.categoryId = 0;
      this.articleId = 0;
      this.preCategoryId = -1;
      this.currentItem;
      this.bBack = EW.addAction("tr{Back to Media}", function ()
      {
         Module.setNav("app", "");
      }, {
         float: "right",
         display: "none"
      }, "action-bar-items");

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
      $(document).off("media-list");
      $(document).on("media-list.refresh", function (e, eventData) {
         self.listMedia();
      });

      /*$(window).resize(function () {
       var cn = Math.floor(($("#main-content").width() - 30) / 159);
       var mw = Math.floor(($("#main-content").width() - 30 - (cn * 159)) / cn);
       $(".content-item").css("margin-right", mw);
       });*/
   }

   Media.prototype.seeDetails = function () {
      var albumId = EW.getHashParameter("albumId", "media");
      var imageId = EW.getHashParameter("imageId", "media");
      EW.activeElement = this.currentItem;
      if (albumId) {
         this.albumId = albumId;
         this.seeAlbumActivity({albumId: albumId});
      } else if (imageId) {
         this.imageId = imageId;
         this.seeImageActivity({articleId: imageId});
      }
   };

   Media.prototype.seeImageActivity = function (id)
   {

   };

   Media.prototype.listMedia = function ()
   {
      var self = this;
      //var albums = $("<div class='row box-content'></div>");
      var itemsList = $("<div class='row box-content'></div>");

      $("#files-list").html("<h2>Loading...</h2>");
      $("#files-list").append(itemsList);

      $.post('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {parent_id: self.parentId}, function (response) {
         if (self.parentId == 0) {
            $("#files-list > h2").html("tr{Albums}");
         } else {
            $("#files-list > h2").html("tr{Images}");
         }

         //var pId = 0;
         //var flag = false;
         $.each(response.data, function (index, element) {
            //flag = true;
            //pId = element.parentId;
            var temp = self.createMedia(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);
            //temp.click(temp.focus);
            if (element.type === "album") {
               temp.on('keydown', function (e) {
                  if (e.which == 13)
                     EW.setHashParameter("parent", element.id);
               });

               temp.dblclick(function () {
                  //EW.setHashParameter("parent", element.id);
                  Module.setNav("app", element.id);
               });

               temp.on("focus", function (e) {
                  //console.log('call');
                  EW.setHashParameters({imageId: null, "albumId": element.id}, "media");
                  //console.log(System.getHashParam("app"));
                  //console.log(Module)
                  //Module.setNav("app","testy");
               });
               itemsList.append(temp);
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

               itemsList.append(temp);
            }

         });
         /*if (flag)
          EW.setHashParameter("preParentId", pId, "media");*/
// Select current item
         $("div[data-item-id='" + self.itemId + "']").focus();
      }, "json");
   };

   Media.prototype.createMedia = function (title, type, ext, size, ImageURL, id)
   {
      var self = this;
      var div = $(document.createElement("div"));
      div.addClass("content-item");
      div.addClass(type);
      div.addClass(ext);
      div.attr("tabindex", "1");
      div.on("focus click", function ()
      {
         $(self.currentItem).removeClass("selected");
         div.addClass("selected");
         self.currentItem = div;
      });

      //div.append("<span></span>");
      var img = $("<img>");
      if (ImageURL)
      {
         img.attr("src", ImageURL);
         div.append(img);
      } else
      {
         div.append("<span></span>");
      }
      div.append("<p>" + title + "</p>");
      if (size)
         div.append("<p class='date'>" + size + " KB</p>");
      div.attr("data-item-id", id);
      return div;
   };
   var media = new Media();
   /*var mediaRouter = EW.Router.get("/nav-media");
    mediaRouter.on(/\/albumId-(\d*)/, function (value, val) {
    console.log(arguments);
    
    });
    mediaRouter.on(/\/imageId-(\d*)/, function (r, v) {
    //alert("image: " + v);
    });
    mediaRouter.on("nav-media", function (value) {
    alert("root");
    });*/

   media.handler = EW.addURLHandler(function ()
   {

      /*var itemId = EW.getHashParameter("albumId", "media") || EW.getHashParameter("imageId", "media");
       var url = EW.getHashParameter("absUrl", "media");
       var reg = /(.*)(\/)$/
       
       if (itemId)
       {
       if (media.itemId !== itemId)
       {
       media.itemId = itemId;
       media.seeAction.comeIn();
       }
       } else
       {
       media.seeAction.comeOut();
       media.bDel.comeOut();
       }*/

   }, "media");
   EW.addURLHandler(function ()
   {
      var parent = EW.getHashParameter("parent");


      /*if (parent > 0)
       {
       media.newAlbumActivity.comeOut();
       media.uploadFileActivity.comeIn();
       media.bBack.comeIn();
       } else
       {
       media.newAlbumActivity.comeIn();
       media.uploadFileActivity.comeOut();
       media.bBack.comeOut();
       }*/
   });

   media.dispose = function ()
   {
      EW.setHashParameters({imageId: null, albumId: null, parent: null});
      EW.removeURLHandler(media.handler);
   };
   var Module;
   (function (System) {

      var M = function () {
         this.type = "appSection";
         this.onInit = function () {

            this.on("app", function (e, parent, id)
            {
               if (parent > 0) {
                  media.newAlbumActivity.comeOut();
                  media.uploadFileActivity.comeIn();
                  media.bBack.comeIn();
               } else {
                  media.newAlbumActivity.comeIn();
                  media.uploadFileActivity.comeOut();
                  media.bBack.comeOut();
               }

               if (!parent) {
                  parent = "0";
               }

               if (parent && media.parentId !== parent) {
                  media.parentId = parent;
                  media.listMedia();
               }

            });
         };

         this.onStart = function () {

         };
      };

      Module = System.module("content-management")
              .module("media", M);
   }(System));

</script>
