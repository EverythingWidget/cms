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
      <div id="album-list" class="box">
         <h2 id='cate-title' >
            <span>tr{Albums}</span>
            <button class='button' id='documents-up-btn' type='button' style='display:none;float:right;'>UP</button>
         </h2>
         <div class='row box-content'></div>
      </div>
   </div>
</div>
<div  class="row">
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
         EW.setHashParameter("parent", null, "media");
      }, {float: "right", display: "none"});
      this.uploadFileActivity = EW.addActivity({title: "tr{Upload Photo}", activity: "admin/api/ContentManagement/upload-form.php"}).hide();
      this.seeAlbumActivity = EW.getActivity({activity: "admin/api/ContentManagement/album-form.php_see"});
      //this.seeArticleActivity = EW.getActivity({activity: "admin/api/ContentManagement/article-form.php_see"});

      if (this.seeAlbumActivity)
         this.seeAction = EW.addAction("tr{See}", $.proxy(this.seeDetails, this)).hide();
      else
         this.seeAction = $();
      this.bDel = $();
      $(document).off("media-list");
      $(document).on("media-list.refresh", function (e, eventData) {
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
      var albumId = EW.getHashParameter("albumId", "media");
      var imageId = EW.getHashParameter("imageId", "media");
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


   Media.prototype.listMedia = function ()
   {
      var self = this;
      //var albums = $("<div class='row box-content'></div>");
      var images = $("<div class='row box-content'></div>");
      //$("#album-list").html("<h2>Loading Albums</h2>");
      var albums = $("#album-list .box-content");
      albums.empty();
      $("#files-list").html("<h2>Loading Images</h2>");
      $("#files-list").append(images);
      $.post('<?php echo EW_ROOT_URL; ?>admin/api/ContentManagement/get_media_list', {parent_id: self.parentId}, function (data)
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
            //temp.click(temp.focus);
            if (element.type == "album")
            {
               temp.on('keydown', function (e) {
                  if (e.which == 13)
                     EW.setHashParameter("parent", element.id, "media");
               });

               temp.dblclick(function () {
                  EW.setHashParameter("parent", element.id, "media");
               });
               temp.on("focus", function (e)
               {
                  //console.log('call');
                  EW.setHashParameters({imageId: null, "albumId": element.id}, "media");
               });
               albums.append(temp);
            }
            else
            {
               temp.attr("data-url", element.url);
               temp.dblclick(function () {
                  EW.setHashParameter("cmd", "preview", "media");
               });
               temp.on("click focus", function ()
               {
                  EW.setHashParameters({
                     itemId: element.id,
                     absUrl: element.absUrl,
                     url: element.url,
                     filename: element.filename,
                     fileExtension: element.fileExtension
                  }, "media");
                  EW.setHashParameters({albumId: null, "imageId": element.id});
                  //alert(element.absUrl);
               });
               images.append(temp);
            }

         });

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
      }
      else
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

   media.handler = EW.addURLHandler(function ()
   {
      var parent = EW.getHashParameter("parent", "media");
      var itemId = EW.getHashParameter("albumId", "media") || EW.getHashParameter("imageId", "media");
      //var url = EW.getHashParameter("absUrl", "media");
      //var reg = /(.*)(\/)$/

      if (!parent)
      {
         //EW.setHashParameter("parentId", "0");
         parent = "0";
      }
      if (parent && media.parentId !== parent)
      {
         EW.setHashParameter("preParentId", media.parentId, "media");
         media.parentId = parent;
         media.listMedia();
      }

      if (parent != 0)
      {
         //media.newAlbumActivity.comeOut();
         media.uploadFileActivity.comeIn();
         media.bBack.comeIn();
      }
      else
      {
         //media.newAlbumActivity.comeIn();
         media.uploadFileActivity.comeOut();
         media.bBack.comeOut();
      }

      if (itemId)
      {
         if (media.itemId !== itemId)
         {
            media.itemId = itemId;
            media.seeAction.comeIn();
         }
      }
      else
      {
         media.seeAction.comeOut();
         media.bDel.comeOut();
      }

   }, "media");


</script>
