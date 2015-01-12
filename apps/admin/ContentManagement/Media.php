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
      this.bUp = EW.addAction("Up", function ()
      {
         var preParentId = EW.getHashParameter("preParentId", "Media");
         EW.setHashParameter("parentId", preParentId, "Media");

      }, {float: "right", display: "none"});
      this.bNewFolder = EW.addAction("tr{New Album}", function ()
      {
         EW.setHashParameter("cmd", "media-new-dir", "Media");
      }, {display: "none"});
      this.bUploadFile = EW.addAction("tr{Upload File}", function ()
      {
         EW.setHashParameter("cmd", "media-upload-file", "Media");
      }, {display: "none"});


      this.bSee = EW.addAction("tr{See/Edit}", function () {
         var id = EW.getHashParameter("itemId", "Media");

         if ($("div[data-item-id='" + id + "']").hasClass("image"))
            $("div[data-item-id='" + id + "']").dblclick();
         else
            EW.setHashParameter("cmd", "see", "Media");
      }, {display: "none"});
      this.bDel = EW.addAction("Delete", function () {
         if (confirm("tr{Are you sure you want to delete this item?}"))
         {
            var action = "delete_content_by_id";
            if (self.currentItem.hasClass("image"))
            {
               action = "delete_image";
            }

            $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/' + action, {id: media.itemId}, function (data) {
               if (data.status_code === 1)
               {
                  $("body").EW().notify(data);
                  EW.setHashParameter("title", null, "Media");
                  self.listMedia();
                  if (self.currentTopPane)
                     self.currentTopPane.dispose();
                  //t.dialog("close");
               }
               else
               {
                  $("body").EW().notify(data);
               }
            }, "json");
         }
      }, {display: "none"});
      this.bDel.addClass("btn-danger");
      this.bDel.css("margin-left", "20px");
      var oldCn = 0;

      $(window).resize(function ()
      {
         var cn = Math.floor(($("#main-content").width() - 30) / 159);
         var mw = Math.floor(($("#main-content").width() - 30 - (cn * 159)) / cn);

         $(".content-item").css("margin-right", mw);

      });
   }

   Media.prototype.selectArticle = function (rowElm, aId)
   {
      var self = this;
      $(self.currentItem).removeClass("Selected");
      $(rowElm).addClass("Selected");
      self.currentItem = rowElm;
      self.categoryId = null;
      self.articleId = aId;
      EW.setHashParameter("categoryId", null, "Media");
      EW.setHashParameter("articleId", aId, "Media");
      //contentManagement.bSee.fadeIn(300);
   };

   Media.prototype.preCategory = function ()
   {
      EW.setHashParameter("parent", this.preCategoryId, "Media");
   };

   Media.prototype.seeDetails = function ()
   {
      //contentManagement.categoryId = EW.getHashParameter("categoryId");
      //contentManagement.articleId = EW.getHashParameter("articleId");
      EW.setHashParameter("cmd", "see", "Media");
   };

   Media.prototype.seeImage = function (url, fn)
   {
      tp = EW.createModal({onClose: function ()
         {
            EW.setHashParameter("cmd", null, "Media");
            //contentManagement.showActions();
         }, class: "full"});

      this.currentTopPane = tp;
      var img = $("<img>");
      img.attr("src", url);

      tp.append("<h1>Preview</h1>");
      //tp.append("<div class='row'><label>File Name</label><label>"++"</label></div>");
      tp.append($("<div class='form-content no-footer'>").append(img));

   };

   Media.prototype.seeAlbum = function (id)
   {
      tp = EW.newTopPane(function () {
         EW.setHashParameter("cmd", null, "Media");
      });
      this.currentTopPane = tp;

      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/album-form.php', {albumId: id}, function (data) {
         /*var form = EW.createForm({folder_name: {label: "Folder Name"}});
          form.prepend("<h1>Create New Folder</h1>");
          form.attr("id", "ne-dir-form");*/
         tp.html(data);
      });
   };


   Media.prototype.uploadFile = function ()
   {
      tp = EW.createModal({onClose: function () {
            EW.setHashParameter("cmd", null, "Media");
         }});
      this.currentTopPane = tp;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/UploadForm.php', {parentId: media.parentId}, function (data)
      {
         tp.html(data);
      });
   };

   Media.prototype.newDir = function ()
   {
      tp = EW.createModal({onClose: function () {
            EW.setHashParameter("cmd", null, "Media");
         }});
      this.currentTopPane = tp;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/album-form.php', {parentId: media.parentId}, function (data) {
         /*var form = EW.createForm({folder_name: {label: "Folder Name"}});
          form.prepend("<h1>Create New Folder</h1>");
          form.attr("id", "ne-dir-form");*/
         tp.html(data);
      });
   };



   Media.prototype.deleteCategory = function ()
   {
      $('<div></div>').appendTo('body')
              .html('<div><p>Are you sure of deleting this folder?</p></div>')
              .dialog({
                 modal: true, title: 'Delete Folder', zIndex: 1000, autoOpen: true,
                 width: '300px', resizable: false,
                 buttons: {
                    Yes: function () {
                       EW.lock(media.currentTopPane, "Saving...");
                       $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/delete_album', {
                          albumId: media.itemId}, function (data) {
                          if (data.status === "unable")
                          {
                             //listCategories();
                             //EW.unlock(contentManagement.currentTopPane);
                             alert("To delete this folder you have to delete all it's sub categories and articles first.");
                             EW.unlock(media.currentTopPane);
                          }
                          else if (data.status === "success")
                          {
                             EW.setHashParameter("categoryId", null, "Media");
                             $("body").EW().notify(data);
                             media.listCategories();
                             media.currentTopPane.dispose();
                          }
                          else
                          {
                             EW.unlock(media.currentTopPane);
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

   Media.prototype.selectItem = function (rowElm)
   {
      $(media.currentItem).removeClass("selected");
      $(rowElm).addClass("selected");
      media.currentItem = rowElm;
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
         /*if (EW.getHashParameter("itemId", "Media"))
          {
          EW.setHashParameter("itemId", null, "Media");
          //media.selectItem($("div[data-item-id='" + EW.getHashParameter("title") + "']"));
          }*/
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
                  EW.setHashParameter("itemId", element.id, "Media");
               });
               temp.dblclick(function () {

                  EW.setHashParameter("parentId", element.id, "Media");
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
      var div = $(document.createElement("div"));
      div.addClass("content-item");
      div.addClass(type);
      div.addClass(ext);
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
      div.click(function () {
         //EW.setHashParameter("categoryId", null);
         //EW.setHashParameter("articleId", id);
         //contentManagement.selectCategory(div, id);
      });
      div.dblclick(function () {
         //EW.setHashParameter("preCategoryId", contentManagement.parentId);
         //EW.setHashParameter("cmd", "see");
         //contentManagement.selectCategory(div, id);
      });
      return div;
   };



   var media = new Media();

   EW.addURLHandler(function ()
   {
      var parent = EW.getHashParameter("parentId", "Media");
      var preParentId = EW.getHashParameter("preParentId", "Media");
      //var path = EW.getHashParameter("path", "Media");
      var cmd = EW.getHashParameter("cmd", "Media");
      var itemId = EW.getHashParameter("itemId", "Media");
      var url = EW.getHashParameter("absUrl", "Media");
      var reg = /(.*)(\/)$/

      if (!parent)
      {
         //EW.setHashParameter("parentId", "0");
         parent = "0";
      }
      if (!preParentId)
      {
         preParentId = "0";
         //EW.setHashParameter("preParentId", preParentId, "Media");
      }
      if (parent && media.parentId !== parent)
      {
         EW.setHashParameter("preParentId", media.parentId, "Media");
         media.parentId = parent;
         media.listMedia();
      }
      if (preParentId)
      {
         if (parent != "0")
            media.bUp.comeIn(300);
         else
            media.bUp.comeOut(200);
      }
      if (itemId)
      {
         //alert(itemId);
         //if (media.preParentId != itemId)
         media.preParentId = itemId;
         media.itemId = itemId;
         media.selectItem($("div[data-item-id='" + itemId + "']"));
         media.bDel.comeIn(300);
         // if ($("div[data-item-id='" + title + "']").hasClass("image"))
         media.bSee.comeIn(300);
         /*else
          media.bSee.comeOut(200);*/
      }
      else
      {
         media.bSee.comeOut(200);
         media.bDel.comeOut(200);
      }
      if (cmd)
      {
         if (cmd == "media-new-dir")
         {
            media.newDir();
            media.bDel.comeOut(200);
         }
         if (cmd == "media-upload-file")
         {
            media.uploadFile();
            media.bDel.comeOut(200);
         }
         if (cmd == "preview")
         {
            media.seeImage(url, itemId);
         }
         if (cmd == "see")
         {
            media.seeAlbum(itemId);
         }
         //media.bUploadFile.comeOut(200);
         //media.bNewFolder.comeOut(200);
         //media.bUp.comeOut(200);
         //media.bSee.comeOut(200);
         //media.bDel.comeOut(200);
      }
      else
      {
         media.bNewFolder.comeIn(300);
         media.bUploadFile.comeIn(300);
      }
      return "MediaHandler";
   }, "Media");
   /*media.dispose = function()
    {
    EW.removeURLHandler(media.handler, "Media");
    media.bUploadFile.remove();
    media.bNewFolder.remove();
    media.bSee.remove();
    media.bUp.remove();
    media.bDel.remove();
    if (media.currentTopPane)
    media.currentTopPane.dispose();
    };*/
</script>