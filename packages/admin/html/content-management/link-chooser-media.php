<div class='header-pane tabs-bar'>
  <h1 class='form-title'>Media</h1>
  <ul id="content-media-tabs" class="nav nav-pills">
    <li class="active">
      <a href="#media-photos" data-toggle="tab">Photos</a>
    </li>

    <li>
      <a href="#media-audios" data-toggle="tab">Audios</a>
    </li>
  </ul>
</div>

<div class='form-content grid tabs-bar no-footer'>
  <div class="no-footer tab-content">
    <div id="media-photos" class="tab-pane active">
      <system-ui-view module="media-chooser" name="albums-list" class="block-row">  
        <div class="block-column anim-fade-in"></div>
      </system-ui-view >

      <system-ui-view module="media-chooser" name="album-card" class="card z-index-1 center-block col-lg-9 col-md-10 col-xs-12">
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
      <system-ui-view module="media-chooser" name="audios-list" class="block-row">  
      </system-ui-view >
    </div>
  </div>
</div>

<system-float-menu id='media-chooser-main-actions' class="ew-float-menu" position="se">  
</system-float-menu>

<script>
  var LinkChooserDomain = new System.Domain();
  LinkChooserDomain.init([]);

  LinkChooserDomain.domainHashString = "#app=media-chooser";
  LinkChooserDomain.ui.components = {
    document: $(document)
  };
  LinkChooserDomain.ui.components.mainFloatMenu = $("#media-chooser-main-actions");
  LinkChooserDomain.ui.components.mainContent = $(EW.getParentDialog($("#media-chooser-main-actions")).find(".form-content"));
  LinkChooserDomain.start();

  (function (Domain) {

    function MediaComponent(module) {
      var component = this;
      this.states = {};
      this.tabs = {};
      this.ui = {
        components: {},
        behaviors: {}
      };
      this.ui.components.tabs_pills = $();
      component.module = module;
      component.module.type = "app-section";

      component.module.onInit = function () {
        component.init();
      };

      component.module.onStart = function () {
        component.start();
      };

      this.defineTabs(this.tabs);
      this.defineStateHandlers(this.states);
      System.Util.installModuleStateHandlers(this.module, this.states);
    }

    MediaComponent.prototype.defineTabs = function (tabs) {
      var component = this;
      tabs.photos = function () {
        component.module.setParamIfNot('app', 'media-chooser/photos');
        System.ui.behaviors.selectTab('#media-photos', component.ui.components.tabs_pills);
        component.module.setParamIfNull("album", "0/images");
        component.uploadAudioActivity.hide();
        //component.newAlbumActivity.show();
      };

      tabs.audios = function () {
        System.ui.behaviors.selectTab('#media-audios', component.ui.components.tabs_pills);
        component.uploadAudioActivity.show();
        component.newAlbumActivity.hide();
        component.uploadImageActivity.hide();
      };
    };

    MediaComponent.prototype.defineStateHandlers = function (states) {
      var component = this;

      states.select = function (nav, itemId) {
        if (itemId > 0) {
          component.selectedItemId = itemId;
          $("div[data-item-id='" + component.selectedItemId + "']:not(:focus)").focus();
        } else {
          component.selectMediaAction.comeOut();
        }
      };

      states.app = function (full, tab) {
        component.module.data.tab = tab || component.module.data.tab || 'photos';

        if (component.module.data.oldTab === component.module.data.tab) {
          component.module.setParamIfNot('app', 'media-chooser/' + component.module.data.tab);
          return;
        }

        if ('function' === typeof component.tabs[component.module.data.tab]) {
          component.tabs[component.module.data.tab].call(component);
          component.module.data.oldTab = component.module.data.tab;
        }
      };
    };

    MediaComponent.prototype.initAudiosTab = function () {
      var component = this;

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
        pageSize: 30,
        buttons: {
          select: function (e) {
            var audio = {
              type: 'audio',
              path: this.data.result[e[0].index].content
            };

            component.mediaChooserDialog[0].selectMedia(audio);
          }
        }
      });


      this.audiosList.html(this.audiosListTable.container);
      this.audiosListTable.read();
    };

    MediaComponent.prototype.init = function () {
      var component = this;
      var templates = System.ui.templates['system/media-chooser'];
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
            albumId: component.module.getNav("album")[0]
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
          component.module.setParam('album', '0/images');
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
          component.module.setParam('album', '0/images');
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

      Domain.ui.components.document.off("media-list");
      Domain.ui.components.document.on("media-list.refresh", function (e, eventData) {
        component.listMedia();
      });
    };

    MediaComponent.prototype.start = function () {
      var component = this;
      this.parentId = null;
      this.itemsList = $();
      this.bDel = $();
      this.listInited = false;
      this.mediaChooserDialog = EW.getParentDialog($("#media-chooser-main-actions"));

      this.selectMediaAction = EW.addActionButton({
        text: "",
        handler: function () {
          component.selectMedia(component.selectedImage);
        },
        class: "btn-float btn-success icon-ok pos-se",
        parent: this.mediaChooserDialog
      }).hide();

      /*this.newAlbumActivity = EW.addActivity({
        title: "tr{New Album}",
        activity: "admin/html/content-management/media/album-form.php",
        parent: Domain.ui.components.mainFloatMenu,
        hash: function (params) {
          params.albumId = null;
          return params;
        }
      });*/

      this.uploadImageActivity = EW.addActivity({
        title: "tr{Upload Photo}",
        activity: "admin/html/content-management/media/upload-form.php",
        parent: Domain.ui.components.mainFloatMenu,
        parameters: function () {
          return {
            parentId: component.module.getNav("album")[0]
          };
        },
        onDone: function () {
          component.module.setParam('parentId', null);
        }
      });

      this.uploadAudioActivity = EW.addActivity({
        title: "tr{Upload Audio}",
        activity: "admin/html/content-management/media/upload-audio-form.php",
        parent: Domain.ui.components.mainFloatMenu,
        parameters: function () {
          return {
            parentId: component.module.getNav("album")[0]
          };
        },
        onDone: function () {
          component.module.setParam('parentId', null);
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
        component.module.setParam('app', 'media-chooser/audios');
      });

      $('a[href="#media-photos"]').off('click').on('click', function () {
        component.module.setParam('app', 'media-chooser/photos');
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

      component.itemsList = component.albumCard.find(".card-content .album-images-list").empty();
      var albumsList = this.albumsList.children().eq(0);
      if (component.albumId === 0) {
        //this.albumPropertiesBtn.comeOut();
      } else {
        //this.albumPropertiesBtn.comeIn();
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
                component.module.setParam('album', element.id + "/images");
              }
            });

            temp[0].addEventListener('dblclick', function () {
              component.module.setParam('album', element.id + "/images");
            });

            temp[0].addEventListener("focus", function (e) {
              component.module.setParam("select", element.id);
            });

            component.itemsList.append(temp);
          } else {
            temp[0].addEventListener("click", function (e) {
              component.selectedImage = {
                type: 'image',
                src: element.absURL
              };

              Domain.state('media-chooser/photos').setParam("image", element.id);
            });

            temp[0].addEventListener("dblclick", function (e) {
              component.selectMedia(component.selectedImage);
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
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0],component.currentItem);
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
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0],component.currentItem);
      });

      div[0].addEventListener("click", function () {
        component.currentItem = System.ui.behaviors.selectElementOnly(div[0],component.currentItem);
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

    MediaComponent.prototype.selectMedia = function (image) {
      var _this = this;
      var img = new Image();
      img.onerror = function (e) {
        alert('Image is invalid');
      };
      img.onload = function () {
        _this.selectedImage.width = img.width;
        _this.selectedImage.height = img.height;
        _this.mediaChooserDialog[0].selectMedia(_this.selectedImage);
      };
      img.src = image.src;
    };

    /*MediaComponent.prototype.selectAudio = function (image) {
     var component = this;
     component.mediaChooserDialog[0].selectMedia(component.selectedImage);
     };*/

    var mediaComponent;
    Domain.state("media-chooser", function () {
      mediaComponent = new MediaComponent(this);
    });

    Domain.state("media-chooser/photos", function () {
      var _this = this;
      _this.started = true;

      this.on('album', function (e, id, images) {
        if (id > 0) {
          //mediaComponent.newAlbumActivity.hide();
          mediaComponent.uploadImageActivity.show();
          mediaComponent.uploadAudioActivity.show();
          mediaComponent.bBack.comeIn();
        } else {
          //mediaComponent.newAlbumActivity.show();
          mediaComponent.uploadImageActivity.hide();
          mediaComponent.uploadAudioActivity.hide();
          mediaComponent.bBack.comeOut();
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

      this.on('select', mediaComponent.states.select);

      this.on("image", function (imageId) {
        if (mediaComponent.albumId && parseInt(imageId) !== mediaComponent.albumId)
          mediaComponent.selectMediaAction.comeIn();
      });
    });

    Domain.state("media-chooser/audios", function () {
      var _this = this;
      _this.started = true;
    });

  }(LinkChooserDomain));

  LinkChooserDomain.state("media-chooser").start();

</script>
