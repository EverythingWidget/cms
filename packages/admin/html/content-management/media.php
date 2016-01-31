<div class="block-row">  
  <div id="albums-list" class="block-column anim-fade-in">

  </div>
</div>

<div  class="col-lg-12">
  <div id="album-data-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
    <div  class='card-header'>
      <h1>
        tr{Media}
      </h1>
      <div id='album-card-action-bar' class='card-action-bar'></div>
    </div>
    <div class='card-content top-devider'>
      <!--<span class='card-content-title'></span>-->
      <div class="album-images-list"></div>
    </div>
  </div>
</div>

<ew-float-menu id='main-actions' class="ew-float-menu">
  
</ew-float-menu>

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

    MediaComponent.prototype.init = function () {
      var _this = this;
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
      this.albumDataCard = $("#album-data-card");
      $("#album-card-action-bar").empty();

      this.albumPropertiesBtn = EW.addActionButton({
        text: "tr{Properties}",
        handler: function () {
          _this.seeAlbumActivity({
            albumId: System.getHashNav("album")[0]
          });
        },
        class: "btn-default",
        parent: "album-card-action-bar"
      }).hide();

      this.deleteAlbumBtn = EW.addActionButton({
        text: "tr{Delete}",
        class: "btn-text btn-danger pull-right",
        handler: function () {
          _this.seeAlbumActivity({
            albumId: System.getHashNav("album")[0]
          });
        },
        parent: "album-card-action-bar"}).hide();

      this.bBack = EW.addActionButton({
        text: "tr{Back to Media}",
        class: "btn-text btn-default pull-right",
        handler: function () {
          System.setHashParameters({
            album: "0/images"
          });
        }, parent: "album-card-action-bar"});

      this.newAlbumActivity = EW.addActivity({
        title: "tr{New Album}",
        activity: "admin/html/content-management/album-form.php",
        parent: "main-actions"
      });           

      this.uploadFileActivity = EW.addActivity({
        title: "tr{Upload Photo}",
        activity: "admin/html/content-management/upload-form.php",
        parent: "main-actions",
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
      var component = this;
      //var albums = $("<div class='row box-content'></div>");
      this.itemsList = $("<div class='box-content anim-fade-in'></div>");
      var elementsList = $("#files-list");
      elementsList.html("<h2>Loading...</h2><div class='loader center'></div>");
      this.listInited = false;
      var listContainer = component.albumDataCard.find(".card-content");
      component.itemsList = component.albumDataCard.find(".card-content .album-images-list").empty();
      var albumsList = $("#albums-list");
      if (component.parentId === 0) {
        this.albumPropertiesBtn.comeOut();
        this.deleteAlbumBtn.comeOut();
      } else {
        this.albumPropertiesBtn.comeIn();
        this.deleteAlbumBtn.comeIn();
      }
      System.addActiveRequest($.get('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {parent_id: component.parentId}, function (response) {
        //var listContainer = null;
        if (component.parentId === 0) {
          component.albumDataCard.find("h1").html("tr{Albums}");
          component.itemsList = albumsList;
        } else {
          component.albumDataCard.find("h1").text(response.included.album.title);
          //component.albumDataCard.find(".card-content .card-content-title").text("tr{Images}");

        }

        $.each(response.data, function (index, element) {
          var temp = component.createAlbumElement(element.title, element.type, element.ext, element.size, element.thumbURL, element.id);

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
              EW.setHashParameters({albumId: null, "imageId": element.id}, "media");

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

    MediaComponent.prototype.createMediaElement = function (title, type, ext, size, ImageURL, id) {
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
              column = $(document.createElement("div")),
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
