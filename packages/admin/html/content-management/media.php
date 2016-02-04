<div data-ui-template="albums-list" class="block-row">  
  <div id="albums-list" class="block-column anim-fade-in">

  </div>
</div>


<div data-ui-template="album-card" id="album-data-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
  <div  class="card-header">
    <div class="card-title-action"></div>
    <div class="card-title-action-right"></div>
    <h1>
      tr{Media}
    </h1>

  </div>
  <div class="card-content top-devider block-row">
    <!--<span class='card-content-title'></span>-->
    <div class="album-images-list"></div>
  </div>
</div>

<script>

  //var d = System.getDomain();
  (function component(System) {

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

    MediaComponent.prototype.init = function (templates) {
      var _this = this;
      //alert(templates);
      this.module.on("album", function (e, id, images) {
        if (id > 0) {
          _this.newAlbumActivity.hide();
          _this.uploadFileActivity.show();
          _this.bBack.comeIn();
        } else {
          _this.newAlbumActivity.show();
          _this.uploadFileActivity.hide();
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
          //_this.seeAction.comeIn();

          _this.currentItem.removeClass("selected");
          $("div[data-item-id='" + _this.selectedItemId + "']").focus();
        } else {
          //_this.seeAction.comeOut();
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
      this.albumCard = this.albumCard || $("#album-data-card").remove();
      this.albumCardTitleAction = this.albumCard.find(".card-title-action").empty();
      this.albumCardTitleActionRight = this.albumCard.find(".card-title-action-right").empty();
      //$("#album-card-action-bar").empty();
      System.UI.components.mainContent.append(this.albumCard);

      this.albumPropertiesBtn = EW.addActionButton({
        text: "tr{Properties}",
        handler: function () {
          _this.seeAlbumActivity({
            albumId: System.getHashNav("album")[0]
          });
        },
        class: "btn-default",
        parent: System.UI.components.mainFloatMenu
      }).hide();

      this.deleteAlbumBtn = EW.addActionButton({
        text: "tr{}",
        class: "btn-text btn-circle btn-danger icon-delete",
        parent: this.albumCardTitleActionRight,
        handler: function () {
          _this.seeAlbumActivity({
            albumId: System.getHashNav("album")[0]
          });
        }
      });

      this.bBack = EW.addActionButton({
        text: "",
        class: "btn-text btn-default btn-circle icon-back",
        handler: function () {
          System.setHashParameters({
            album: "0/images"
          });
        },
        parent: this.albumCardTitleAction
      });

      this.newAlbumActivity = EW.addActivity({
        title: "tr{New Album}",
        activity: "admin/html/content-management/album-form.php",
        parent: System.UI.components.mainFloatMenu
      });

      this.uploadFileActivity = EW.addActivity({
        title: "tr{Upload Photo}",
        activity: "admin/html/content-management/upload-form.php",
        parent: System.UI.components.mainFloatMenu,
        hash: function () {
          return {
            parentId: System.getHashNav("album")[0]
          };
        },
        onDone: function () {
          EW.setHashParameter("parentId", null);
        }
      });

      this.seeAlbumActivity = EW.getActivity({
        activity: "admin/html/content-management/album-form.php",
        parent: "action-bar-items",
        modal: {
          class: "center properties"
        }
      });

      _this.module.setParamIfNone("album", "0/images");
      alert(_this.module.html);
      _this.module.html = "";
    };

    MediaComponent.prototype.seeItemDetails = function () {
      var albumId = this.selectedItemId;
      EW.activeElement = this.currentItem;
      if (albumId) {
        this.albumId = albumId;
        this.seeAlbumActivity({
          albumId: albumId
        });
      }
    };

    MediaComponent.prototype.seeImageActivity = function (id) {

    };

    MediaComponent.prototype.listMedia = function () {
      var component = this;
      //var albums = $("<div class='row box-content'></div>");
      this.itemsList = $("<div class='box-content anim-fade-in'></div>");
      var elementsList = $("#files-list");
      elementsList.html("<h2>Loading...</h2><div class='loader center'></div>");
      this.listInited = false;
      var listContainer = component.albumCard.find(".card-content");
      component.itemsList = component.albumCard.find(".card-content .album-images-list").empty();
      var albumsList = $("#albums-list");
      if (component.parentId === 0) {
        this.albumPropertiesBtn.comeOut();
        //this.deleteAlbumBtn.comeOut();
      } else {
        this.albumPropertiesBtn.comeIn();
        //this.deleteAlbumBtn.comeIn();
      }
      System.addActiveRequest($.get('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {
        parent_id: component.parentId
      },
        function (response) {
          //var listContainer = null;
          if (component.parentId === 0) {
            component.albumCard.hide();
            albumsList.show();
            //component.albumDataCard.find("h1").html("tr{Albums}");
            component.itemsList = albumsList;
            albumsList.empty();
            //component.albumCard.removeClass("action-bar-active");
          } else {
            component.albumCard.show();
            albumsList.hide();
            component.albumCard.find("h1").text(response.included.album.title);
            //component.albumCard.addClass("action-bar-active");
            //component.albumDataCard.find(".card-content .card-content-title").text("tr{Images}");

          }

          $.each(response.data, function (index, element) {
            var temp;
            if (component.parentId === 0) {
              temp = component.createAlbumElement(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);
            } else {
              temp = component.createImageElement(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);
            }
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
                component.module.setParam("select", element.id);
              });
              component.itemsList.append(temp);
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
                EW.setHashParameters({
                  albumId: null,
                  "imageId": element.id
                },
                  "media");

              });

              component.itemsList.append(temp);
            }

          });

          //listContainer.append(component.itemsList);
          component.itemsList.addClass("in");
          component.listInited = true;
          // Select current item            
          if (component.selectedItemId) {
            $("div[data-item-id='" + component.selectedItemId + "']").focus();
          }

        }, "json"));
    };

    MediaComponent.prototype.createImageElement = function (title, type, ext, size, ImageURL, id) {
      var _this = this,
        column = $(document.createElement("div")),
        div = $(document.createElement("div")),
        img = $(document.createElement("img"));

      column.addClass("col-lg-3 col-md-4 col-xs-6");
      div.addClass("content-item z-index-0")
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
      column.append(div);
      return column;
    };

    MediaComponent.prototype.createAlbumElement = function (title, type, ext, size, ImageURL, id) {
      var _this = this,
        //column = $(document.createElement("div")),
        div = $(document.createElement("div")),
        img = $(document.createElement("img"));

      //column.addClass("col-lg-3 col-md-4 col-xs-6");
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
      //column.append(div);
      return div;
    };

    System.module("content-management/media", function () {
      new MediaComponent(this);
    });
  }(System));
  //d.init();
  //d.start();

</script>
