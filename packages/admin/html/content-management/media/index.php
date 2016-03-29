<div class="tabs-bar block-row">
  <ul id="content-media-tabs" class="nav nav-pills nav-black-text">
    <li class="active">
      <a href="#media-photos" data-toggle="tab">Photos</a>
    </li>

    <li>
      <a href="#media-audios" data-toggle="tab">Audios</a>
    </li>
  </ul>
</div>

<div class="no-footer tab-content">
  <div id="media-photos" class="tab-pane active">
    <system-ui-view module="content-management/media" name="albums-list" class="block-row">  
      <div class="block-column anim-fade-in"></div>
    </system-ui-view>

    <system-ui-view module="content-management/media" name="album-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
      <div  class="card-header">
        <div class="card-title-action"></div>
        <div class="card-title-action-right"></div>
        <h1>
          tr{Media}
        </h1>
      </div>

      <div class="card-content top-devider block-row">
        <div class="album-images-list grid-list"></div>
      </div>
    </system-ui-view>
  </div> 

  <div id="media-audios" class="tab-pane" >
    <system-ui-view module="content-management/media" name="audios-list" class="block-row">  
    </system-ui-view >
  </div>
</div>

<script>
  (function () {
    System.entity('services/media_chooser', {
      selectItem: function (item) {
        if (item.type === 'image')
          return item;

        if (item.type === 'audio')
          return {
            type: 'text',
            text: item.path
          };

        return null;
      }
    });

    System.entity('components/media/photos', {
      create: function (module) {
        var mediaComponent = System.entity('components/media').$service;

        module.bind('start', function () {
          mediaComponent.uploadAudioActivity.hide();
        });

        module.on('album', function (e, id, images) {
          if (id > 0) {
            mediaComponent.newAlbumActivity.hide();
            mediaComponent.uploadImageActivity.comeIn();
            mediaComponent.bBack.comeIn();
          } else {
            mediaComponent.newAlbumActivity.comeIn();
            mediaComponent.uploadImageActivity.hide();
            mediaComponent.bBack.hide();
          }

          if (!id) {
            id = 0;
          }

          if (images) {
            if (id !== null && mediaComponent.albumId !== id) {
              mediaComponent.albumId = parseInt(id);
              if (mediaComponent.listInited) {
                mediaComponent.module.setParam("select", null, true);
              }

              mediaComponent.listMedia();
            }
          }
        });

        module.on('select', mediaComponent.states.select);

      }
    });

    System.entity('components/media/audios', {
      create: function (module) {
        var mediaComponent = System.entity('components/media').$service;

        module.bind('start', function () {
          mediaComponent.uploadAudioActivity.comeIn();
          mediaComponent.newAlbumActivity.hide();
          mediaComponent.uploadImageActivity.hide();
        });
      }
    });

    function MediaComponent(module) {
      var component = this;
      component.states = {};
      component.tabs = {};
      component.ui = {
        components: {},
        behaviors: {}
      };
      component.ui.components.tabs_pills = $();
      component.module = module;
      component.module.type = "app-section";

      component.module.onInit = function (templates) {
        component.init(templates);
      };

      component.module.onStart = function () {
        component.start();
      };

      component.defineTabs(component.tabs);
      component.defineStateHandlers(component.states);
      System.Util.installModuleStateHandlers(component.module, component.states);
    }

    MediaComponent.prototype.defineTabs = function (tabs) {
      var component = this;
      tabs.photos = function () {
        component.module.setParamIfNot('app', 'content-management/media/photos');
        System.ui.behaviors.selectTab('#media-photos', component.ui.components.tabs_pills);
        component.module.setParamIfNull("album", "0/images");
        System.state('content-management/media/photos').start();
      };

      tabs.audios = function () {
        System.ui.behaviors.selectTab('#media-audios', component.ui.components.tabs_pills);
        System.state('content-management/media/audios').start();

      };
    };

    MediaComponent.prototype.defineStateHandlers = function (states) {
      var component = this;

      states.select = function (nav, itemId) {
        if (itemId > 0) {
          component.selectedItemId = itemId;
          $("div[data-item-id='" + component.selectedItemId + "']:not(:focus)").focus();
        } else {
        }
      };

      states.app = function (full, tab) {
        component.module.data.tab = tab || component.module.data.tab || 'photos';

//        if (component.module.data.oldTab === component.module.data.tab) {
//          component.module.setParamIfNot('app', 'content-management/media/' + component.module.data.tab);
//          return;
//        }

        if (component.module.getParam('app') !== 'content-management/media/' + component.module.data.tab) {
          component.module.setParamIfNot('app', 'content-management/media/' + component.module.data.tab);
          return;
        }

        if ('function' === typeof component.tabs[component.module.data.tab]) {
          component.tabs[component.module.data.tab].call(component);
          component.module.data.oldTab = component.module.data.tab;
        }
      };
    };

    MediaComponent.prototype.initAudiosTab = function () {
      this.audiosListTable = EW.createTable({
        name: "audio-list",
        rowLabel: "{name}",
        columns: [
          "title",
          "content"
        ],
        headers: {
          Title: {},
          Path: {}
        },
        rowCount: true,
        url: "~admin/api/content-management/media-audios",
        pageSize: 30
      });


      this.audiosList.html(this.audiosListTable.container);
      this.audiosListTable.read();
    };

    MediaComponent.prototype.init = function () {
      var templates = System.ui.templates['system/content-management/media'];
      var component = this;
      this.albumId = null;
      this.albumCard = $(templates["album-card"]).hide();
      this.albumCardTitleAction = this.albumCard.find(".card-title-action");
      this.albumCardTitleActionRight = this.albumCard.find(".card-title-action-right");
      this.albumsList = $(templates['albums-list']);
      this.audiosList = $(templates['audios-list']);
      this.currentItem = null;

      this.albumPropertiesBtn = EW.addActionButton({
        text: '',
        handler: function () {
          component.seeAlbumActivity({
            albumId: System.getHashNav("album")[0]
          });
        },
        class: "btn btn-text btn-default btn-circle icon-edit",
        parent: this.albumCardTitleActionRight
      }).hide();

      this.deleteAlbumActivity = EW.addActivity({
        activity: "admin/api/content-management/delete-album",
        text: "tr{}",
        class: "btn btn-text btn-circle btn-danger icon-delete",
        parent: this.albumCardTitleActionRight,
        parameters: function () {
          if (!confirm("tr{Are you sure of deleting this album?}")) {
            return false;
          }

          return {
            id: component.albumId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          System.setHashParameters({
            album: "0/images"
          });
        }
      });

      this.deleteImageActivity = EW.getActivity({
        activity: "admin/api/content-management/delete-image",
        parameters: function () {
          if (!confirm("tr{Are you sure of deleting this image?} ")) {
            return false;
          }

          return {
            'id': component.selectedItemId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          component.listMedia();
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

      this.seeAlbumActivity = EW.getActivity({
        activity: "admin/html/content-management/media/album-form.php",
        parent: "action-bar-items",
        modal: {
          class: "center properties"
        }
      });

      this.initAudiosTab();

      System.UI.components.document.off("media-list");
      System.UI.components.document.on("media-list.refresh", function (e, eventData) {
        component.listMedia();
      });

      System.UI.components.document.off('media.audios.list.refresh').on('media.audios.list.refresh', function (e, eventData) {
        component.audiosListTable.refresh();
      });
    };

    MediaComponent.prototype.start = function () {
      var component = this;
      this.itemsList = $();
      this.bDel = $();
      this.listInited = false;

      this.newAlbumActivity = EW.addActivity({
        title: "tr{New Album}",
        activity: "admin/html/content-management/media/album-form.php",
        parent: System.UI.components.mainFloatMenu,
        hash: function (params) {
          params.albumId = null;
          return params;
        }
      });

      this.uploadImageActivity = EW.addActivity({
        title: "tr{Upload Photo}",
        activity: "admin/html/content-management/media/upload-form.php",
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

      this.uploadAudioActivity = EW.addActivity({
        title: "tr{Upload Audio}",
        activity: "admin/html/content-management/media/upload-audio-form.php",
        parent: System.UI.components.mainFloatMenu,
        hash: function () {
          return {
            parentId: System.getHashNav("album")[0]
          };
        },
        onDone: function () {
          System.setHashParameters({
            parentId: null
          });
        }
      });

      this.albumCard[0].show();
      this.albumsList[0].show();
      this.audiosList[0].show();
      this.ui.components.tabs_pills = $('#content-media-tabs');

      // Select photos tab if no tab is selected
//      component.module.data.tab = component.module.data.tab || component.module.getNav('app')[2];
//      if (!component.module.data.tab) {
//        component.module.setParam('app', 'content-management/media/photos');
//      }


      $('a[href="#media-audios"]').off('click').on('click', function () {
        component.module.setParam('app', 'content-management/media/audios');
      });

      $('a[href="#media-photos"]').off('click').on('click', function () {
        component.module.setParam('app', 'content-management/media/photos');
      });
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
      var albumsList = this.albumsList.children().eq(0);
      if (component.albumId === 0) {
        this.albumPropertiesBtn.comeOut();
        //this.deleteAlbumBtn.comeOut();
      } else {
        this.albumPropertiesBtn.comeIn();
        //this.deleteAlbumBtn.comeIn();
      }
      System.addActiveRequest($.get('<?php echo EW_ROOT_URL; ?>~admin/api/content-management/get-media-list', {
        parent_id: component.albumId
      }, function (response) {
        if (component.albumId === 0) {
          component.albumCard.hide();
          albumsList.show();
          component.itemsList = albumsList;
          albumsList.empty();
        } else {
          component.albumCard.show();
          albumsList.hide();
          component.albumCard.find("h1").text(response.included.album.title);
        }

        $.each(response.data, function (index, element) {
          var temp;
          if (component.albumId === 0) {
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

            temp[0].addEventListener('dblclick', function () {
              System.setHashParameters({
                album: element.id + "/images"
              });
            });

            temp[0].addEventListener("focus", function (e) {
              component.module.setParam("select", element.id);
            });

            component.itemsList.append(temp);
          } else {
            temp.attr("data-url", element.url);
            temp[0].addEventListener('dblclick', function () {
              EW.setHashParameter("cmd", "preview", "media");
            });

            temp[0].addEventListener("focus", function () {
              component.module.setParam("select", element.id);
            });

            component.itemsList.append(temp);
          }
        });

        component.itemsList.addClass("in");
        component.listInited = true;
        // Select current item            
        if (component.selectedItemId) {
          $("div[data-item-id='" + component.selectedItemId + "']").focus();
        }

      }, "json"));
    };

    MediaComponent.prototype.createImageElement = function (title, type, ext, size, ImageURL, id) {
      var component = this,
              column = $(document.createElement("div")),
              div = $(document.createElement("div")),
              img = $(document.createElement("img"));

      //column.addClass("col-lg-3 col-md-4 col-xs-6 block-row");
      div.addClass("content-item z-index-0")
              .addClass(type)
              .addClass(ext);
      div.attr("tabindex", "1");
      div.on("focus click", function () {
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0], component.currentItem);
      });

      if (ImageURL) {
        img.attr("src", ImageURL);
        div.append(img);
      } else {
        div.append("<span></span>");
      }

      div.append("<button class='pull-right btn-text btn-circle btn-danger icon-delete'></button>");
      if (size) {
        div.append("<p class='date'>" + size + " KB</p>");
      }

      div.attr("data-item-id", id);

      var divTree = UIUtility.toTreeObject(div[0]);
      divTree.button._.addEventListener('click', function () {
        component.selectedItemId = id;
        component.deleteImageActivity();
      });

      return div;
    };

    MediaComponent.prototype.createAlbumElement = function (title, type, ext, size, ImageURL, id) {
      var component = this,
              div = $(document.createElement("div")),
              img = $(document.createElement("img"));

      div.addClass("content-item")
              .addClass(type)
              .addClass(ext);
      div.attr("tabindex", "1");
      div[0].addEventListener("focus", function (e) {
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0], component.currentItem);
      });

      div[0].addEventListener("click", function () {
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0], component.currentItem);
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

    System.entity('components/media', {
      $service: null,
      create: function (module) {
        return new MediaComponent(module);
      },
      service: function (module) {
        return this.$service !== null ? this.$service : this.$service = this.create(module);
      }
    });

    System.state("content-management/media", function () {
      System.entity('components/media').service(this);
    });

    System.state("content-management/media/photos", function () {
      System.entity('components/media/photos').create(this);
    });

    System.state("content-management/media/audios", function () {
      System.entity('components/media/audios').create(this);
    });



  })();
</script>
