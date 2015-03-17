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
      <div id="folders-list" class="box">

      </div>
   </div>
</div>
<div  class="row">
   <div class="col-xs-12" >
      <div id="files-list" class="box">

      </div>
   </div>
</div>
<script  type="text/javascript">
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
         var preParentId = EW.getHashParameter("preParentId");
         EW.setHashParameter("parentId", null);
      }, {float: "right", display: "none"}, "action-bar-items");
      this.bNewAlbum = EW.addActivity({title: "tr{New Album}", activity: "app-admin/ContentManagement/album-form.php", parent: "action-bar-items"}).hide();
      this.bUploadFile = EW.addActivity({title: "tr{Upload Photo}", activity: "app-admin/ContentManagement/upload-form.php", parent: "action-bar-items",
         hash: function ()
         {
            return {albumId: self.parentId}
         }
      });
      this.seeAlbumActivity = EW.getActivity({activity: "app-admin/ContentManagement/album-form.php_see"});
      //this.seeArticleActivity = EW.getActivity({activity: "app-admin/ContentManagement/article-form.php_see"});

      if (this.seeAlbumActivity)
         this.bSee = EW.addAction("tr{See}", $.proxy(this.seeDetails, this), null, "action-bar-items").hide();
      else
         this.bSee = $();
      this.bDel = $();
      $(document).off("album-list");
      $(document).on("album-list.refresh", function (e, eventData) {
         self.listMedia();
      });
      var oldCn = 0;
      $(window).resize(function ()
      {
         var cn = Math.floor(($("#main-content").width() - 30) / 159);
         var mw = Math.floor(($("#main-content").width() - 30 - (cn * 159)) / cn);
         $(".content-item").css("margin-right", mw);
      });
   }

   Media.prototype.seeDetails = function ()
   {
      var albumId = EW.getHashParameter("albumId");
      var imageId = EW.getHashParameter("imageId");
      EW.activeElement = this.currentItem;
      if (albumId)
      {
         this.albumId = albumId;
         this.seeAlbumActivity({albumId: albumId});
      }
      else if (imageId)
      {
         this.imageId = imageId;
         //this.seeArticleActivity({articleId: imageId});
      }
   };

   Media.prototype.selectItem = function (rowElm)
   {
      //$(media.currentItem).removeClass("selected");
      $(rowElm).focus();
      //media.currentItem = rowElm;
   };
   
   Media.prototype.listMedia = function ()
   {
      var self = this;
      var albums = $("<div class='row box-content'></div>");
      var images = $("<div class='row box-content'></div>");
      $("#folders-list").html("<h2>Loading Albums</h2>");
      $("#folders-list").append(albums);
      $("#files-list").html("<h2>Loading Images</h2>");
      $("#files-list").append(images);
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_media_list', {parent_id: self.parentId}, function (data)
      {
         $("#folders-list > h2").html("tr{Albums}");
         $("#files-list > h2").html("tr{Images}");
         var pId = 0;
         var flag = false;
         $.each(data, function (index, element)
         {
            flag = true;
            pId = element.parentId;
            var temp = self.createMedia(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);
            if (element.type == "folder")
            {
               temp.click(function () {
                  //EW.setHashParameter("title", element.title, "Media");
                  EW.setHashParameter("albumId", element.id);
               });
               temp.dblclick(function () {

                  EW.setHashParameter("parentId", element.id);
                  //alert(element.parentId);
               });
               albums.append(temp);
            }
            else
            {
               temp.attr("data-url", element.url);
               temp.click(function () {
                  //console.log(element);
                  EW.setHashParameter("itemId", element.id, "Media");
                  EW.setHashParameter("url", element.url, "Media");
                  EW.setHashParameter("filename", element.filename, "Media");
                  EW.setHashParameter("fileExtension", element.fileExtension, "Media");
                  EW.setHashParameter("absUrl", element.absUrl, "Media");
               });
               temp.dblclick(function () {
                  //media.imageURL = element.url;

                  EW.setHashParameter("cmd", "preview", "Media");
               });
               images.append(temp);
            }

         });
         if (flag)
            EW.setHashParameter("preParentId", pId, "Media");
// Select current item
         self.selectItem($("div[data-item-id='" + self.itemId + "']"));
      }, "json");
   };
   Media.prototype.createMedia = function (title, type, ext, size, ImageURL, id)
   {
      var self = this;
      var div = $(document.createElement("div"));
      div.addClass("content-item");
      div.addClass(type);
      div.addClass(ext);
      div.attr("tabindex","1");
      div.on("focus",function()
      {
         self.currentItem = div;
         EW.setHashParameter("albumId", id);
      });
      //div.append("<span></span>");
      var img = $("<img>");
      if (ImageURL)
      {
         img.attr("src", ImageURL);
         div.append(img);
      }
      else
      {
         div.append("<span></span>");
      }
      div.append("<p>" + title + "</p>");
      if (size)
         div.append("<p class='date'>" + size + " KB</p>");
      div.attr("data-item-id", id);
      div.click(function ()
      {

      });
      div.dblclick(function ()
      {

      });
      return div;
   };
   var media = new Media();
   EW.addURLHandler(function ()
   {
      var parent = EW.getHashParameter("parentId");
      var itemId = EW.getHashParameter("albumId");
      var url = EW.getHashParameter("absUrl", "Media");
      var reg = /(.*)(\/)$/

      if (!parent)
      {
         //EW.setHashParameter("parentId", "0");
         parent = "0";
      }
      if (parent && media.parentId !== parent)
      {
         EW.setHashParameter("preParentId", media.parentId, "Media");
         media.parentId = parent;
         media.listMedia();
      }

      if (parent != 0)
      {
         media.bNewAlbum.comeOut()
         media.bBack.comeIn();
      }
      else
      {
         media.bNewAlbum.comeIn();
         media.bBack.comeOut();
      }

      if (itemId)
      {
         //media.preParentId = itemId;
         media.itemId = itemId;
         $("div[data-item-id='" + itemId + "']").focus();
         //media.bDel.comeIn();
         media.bSee.comeIn();
      }
      else
      {
         media.bSee.comeOut();
         media.bDel.comeOut();
      }

      return "MediaHandler";
   });

</script>