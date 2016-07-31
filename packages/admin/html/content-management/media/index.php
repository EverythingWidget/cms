<div class="tabs-bar block-row">
  <ul id="content-media-tabs" class="nav nav-pills nav-black-text">
    <li class="active">
      <a href="#media-photos" data-toggle="tab">Photos</a>
    </li>

    <!--<li>
      <a href="#media-audios" data-toggle="tab">Audios</a>
    </li>-->
  </ul>
</div>

<div class="no-footer tab-content">
  <div id="media-photos" class="tab-pane active">
    <system-spirit animations="zoom" zoom="album">
      <system-ui-view module="content-management/media" name="albums-list" class="block-row">  
        <div tabindex="1" v-for="album in albums" track-by="id" class="content-item album" 
             v-if="albumId === 0"
             v-on:focus="selectItem(album.id)" 
             v-on:dblclick="openAlbum(album.id)">
          <span></span>
          <p>{{ album.title }}</p>
        </div>      
      </system-ui-view>
    </system-spirit>

    <system-ui-view module="content-management/media" name="album-card" class="grid">
      <div class="grid-header">
        <div class="card-title-action"></div>
        <div class="card-title-action-right"></div>
        <h1>
          tr{Media}
        </h1>
      </div>
      <system-spirit animations="zoom" zoom="grid-cell">
        <div class="album-images-list grid-content block-row">
          <div tabindex="1" class="grid-cell image"
               v-for="image in images" 
               v-on:focus="selectItem(image.id)" >
            <img alt="{{image.title}}" v-bind:src="image.thumbURL" />
            <div class="grid-cell-caption">
              <p class="title date">{{ image.size }} KB</p>
              <button class='pull-right btn-text btn-circle btn-danger icon-delete' v-on:click="deleteImage(image.id)"></button>
            </div>
          </div>
        </div>
      </system-spirit>
    </system-ui-view>
  </div> 

  <div id="media-audios" class="tab-pane" >
    <system-ui-view module="content-management/media" name="audios-list" class="block-row">  
    </system-ui-view >
  </div>
</div>

<script>
  (function () {
    function MediaPhotos() {}

    MediaPhotos.prototype.create = function (state) {
      var mediaStateHandler = System.entity('objects/media-state-handler');

      state.bind('start', function () {
        //mediaStateHandler.uploadAudioActivity.hide();
      });

      state.on('album', function (e, id, images) {
        if (id > 0) {
          mediaStateHandler.uploadImageActivity.hide = false;
        } else {
          mediaStateHandler.uploadImageActivity.hide = true;
        }

        if (!id) {
          id = 0;
        }

        if (images) {
          if (id !== null && mediaStateHandler.albumId !== id) {
            mediaStateHandler.albumId = parseInt(id);
            if (mediaStateHandler.listInited) {
              mediaStateHandler.state.setParam("select", null, true);
            }

            mediaStateHandler.listMedia();
          }
        }
      });

      state.on('select', mediaStateHandler.states.select);
    };

    System.entity('components/media/photos', new MediaPhotos());

    function MediaAudios() {}

    MediaAudios.prototype.create = function (state) {
      var mediaStateHandler = System.entity('objects/media-state-handler');

      state.bind('start', function () {
        //mediaStateHandler.uploadAudioActivity.comeIn();
        mediaStateHandler.uploadImageActivity.hide();
      });
    };

    System.entity('components/media/audios', new MediaAudios());

    function MediaStateHandler(state) {
      var component = this;
      component.states = {};
      component.tabs = {};
      component.ui = {
        components: {},
        behaviors: {}
      };
      component.ui.components.tabs_pills = $();
      component.state = state;
      component.state.type = "app-section";

      component.state.bind('init', function (templates) {
        component.init(templates);
      });

      component.state.bind('start', function () {
        component.start();
      });

      component.defineTabs(component.tabs);
      component.defineStateHandlers(component.states);
      System.utility.installModuleStateHandlers(component.state, component.states);
    }

    MediaStateHandler.prototype.defineTabs = function (tabs) {
      var component = this;
      tabs.photos = function () {
        component.state.setParamIfNot('app', 'content-management/media/photos');
        System.ui.behaviors.selectTab('#media-photos', component.ui.components.tabs_pills);
        component.state.setParamIfNull("album", "0/images");
        System.state('content-management/media/photos').start();
      };

      tabs.audios = function () {
        System.ui.behaviors.selectTab('#media-audios', component.ui.components.tabs_pills);
        System.state('content-management/media/audios').start();

      };
    };

    MediaStateHandler.prototype.defineStateHandlers = function (states) {
      var component = this;

      states.select = function (nav, itemId) {
        if (itemId > 0) {
          component.selectedItemId = itemId;
          $("div[data-item-id='" + component.selectedItemId + "']:not(:focus)").focus();
        } else {
        }
      };

      states.app = function (full, tab) {
        component.state.data.tab = tab || component.state.data.tab || 'photos';

//        if (component.module.data.oldTab === component.module.data.tab) {
//          component.module.setParamIfNot('app', 'content-management/media/' + component.module.data.tab);
//          return;
//        }

        if (component.state.getParam('app') !== 'content-management/media/' + component.state.data.tab) {
          component.state.setParamIfNot('app', 'content-management/media/' + component.state.data.tab);
          return;
        }

        if ('function' === typeof component.tabs[component.state.data.tab]) {
          component.tabs[component.state.data.tab].call(component);
          component.state.data.oldTab = component.state.data.tab;
        }
      };
    };

    MediaStateHandler.prototype.initAudiosTab = function () {
      this.audiosListTable = EW.createTable({
        name: "audio-list",
        rowLabel: "{name}",
        columns: [
          "title",
          "content"],
        headers: {
          Title: {},
          Path: {}
        },
        rowCount: true,
        url: "api/admin/content-management/media-audios/",
        pageSize: 30
      });


      this.audiosList.html(this.audiosListTable.container);
      this.audiosListTable.read();
    };

    MediaStateHandler.prototype.init = function () {
      var handler = this;
      this.albumId = null;
      this.albumCard = $(Scope.uiViews.album_card).hide();
      this.albumCardTitleAction = this.albumCard.find(".card-title-action");
      this.albumCardTitleActionRight = this.albumCard.find(".card-title-action-right");
      this.albumsList = $(Scope.uiViews.albums_list);
      this.audiosList = $(Scope.uiViews.audios_list);
      this.currentItem = null;

      this.albumPropertiesBtn = EW.addActionButton({
        text: '', handler: function () {
          handler.seeAlbumActivity({albumId: System.getHashNav("album")[0]
          });
        },
        class: "btn btn-text btn-default btn-circle icon-edit",
        parent: this.albumCardTitleActionRight
      });
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
            id: handler.albumId
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
            'id': handler.selectedItemId
          };
        },
        onDone: function (response) {
          $("body").EW().notify(response).show();
          handler.listMedia();
        }
      });

      this.bBack = EW.addActionButton({
        text: "", class: "btn-text btn-default btn-circle icon-back",
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
      handler.ui.media_photos_vue = new Vue({
        el: Scope.ui.find('#media-photos')[0],
        data: {
          albumId: 0,
          albums: [],
          images: []
        },
        methods: {
          openAlbum: function (albumId) {
            handler.state.setParam('album', albumId + '/images');
          },
          selectItem: function (id) {
            handler.selectedItemId = id;
            handler.state.setParam("select", id);
          },
          deleteImage: function (id) {
            handler.selectedItemId = id;
            handler.deleteImageActivity();
          }
        }
      });

      this.initAudiosTab();

      $(document).off("media-list.refresh").on("media-list.refresh", function () {
        handler.listMedia();
      });

      $(document).off('media.audios.list.refresh').on('media.audios.list.refresh', function (e, eventData) {
        handler.audiosListTable.refresh();
      });
    };

    MediaStateHandler.prototype.start = function () {
      var component = this;
      this.itemsList = $();
      this.bDel = $();
      this.listInited = false;

      System.entity('ui/primary-actions').actions = [
        {
          title: "tr{New Album}",
          activity: "admin/html/content-management/media/album-form.php",
          //parent: System.UI.components.mainFloatMenu,
          parameters: function (params) {
            params.albumId = null;
            return params;
          }
        }

      ];

//      this.newAlbumActivity = EW.addActivity();

      this.uploadImageActivity = {
        title: "tr{Upload Photo}",
        activity: "admin/html/content-management/media/upload-form.php",
        hide: true,
        //parent: System.UI.components.mainFloatMenu,
        parameters: function () {
          return {
            parentId: System.getHashNav("album")[0]
          };
        },
        onDone: function () {
          EW.setHashParameter("parentId", null);
        }
      };

      System.entity('ui/primary-actions').actions = [
        {
          title: "tr{New Album}",
          activity: "admin/html/content-management/media/album-form.php",
          //parent: System.UI.components.mainFloatMenu,
          parameters: function (params) {
            params.albumId = null;
            return params;
          }
        },
        this.uploadImageActivity
      ];

//      this.uploadAudioActivity = EW.addActivity({
//        title: "tr{Upload Audio}",
//        activity: "admin/html/content-management/media/upload-audio-form.php",
//        parent: System.UI.components.mainFloatMenu,
//        parameters: function () {
//          return {
//            parentId: System.getHashNav("album")[0]
//          };
//        },
//        onDone: function () {
//          System.setHashParameters({
//            parentId: null
//          });
//        }
//      });

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
        component.state.setParam('app', 'content-management/media/audios');
      });

      $('a[href="#media-photos"]').off('click').on('click', function () {
        component.state.setParam('app', 'content-management/media/photos');
      });
    };

    MediaStateHandler.prototype.seeItemDetails = function () {
      var albumId = this.selectedItemId;
      EW.activeElement = this.currentItem;
      if (albumId) {
        this.albumId = albumId;
        this.seeAlbumActivity({albumId: albumId
        });
      }
    };

    MediaStateHandler.prototype.seeImageActivity = function (id) {

    };

    MediaStateHandler.prototype.listMedia = function () {
      var stateHandler = this;
      //var albums = $("<div class='row box-content'></div>");
      //this.itemsList = $("<div class='box-content anim-fade-in'></div>");
      var elementsList = $("#files-list");
      elementsList.html("<h2>Loading...</h2><div class='loader center'></div>");
      this.listInited = false;
      //var listContainer = component.albumCard.find(".card-content");
      //component.itemsList = component.albumCard.find(".album-images-list").empty();
      var albumsList = this.albumsList.children().eq(0);
      if (stateHandler.albumId === 0) {
        //this.albumPropertiesBtn.comeOut();
        //this.deleteAlbumBtn.comeOut();
      } else {
        //this.albumPropertiesBtn.comeIn();
        //this.deleteAlbumBtn.comeIn();
      }

      $.get('api/admin/content-management/get-media-list/', {
        parent_id: stateHandler.albumId
      }, done);

      function done(response) {
        if (stateHandler.albumId === 0) {
          stateHandler.albumCard.hide();
          albumsList.show();
          stateHandler.itemsList = albumsList;
          albumsList.empty();
        } else {
          stateHandler.albumCard.show();
          albumsList.hide();
          stateHandler.albumCard.find("h1").text(response.included.album.title);
        }

        stateHandler.ui.media_photos_vue.albumId = stateHandler.albumId;

        if (stateHandler.albumId === 0) {
          stateHandler.ui.media_photos_vue.albums = response.data;
        } else {
          stateHandler.ui.media_photos_vue.images = response.data;
        }

        stateHandler.listInited = true;
        stateHandler.ui.media_photos_vue.$nextTick(function () {
          if (stateHandler.selectedItemId) {
            $("div[data-item-id='" + stateHandler.selectedItemId + "']").focus();
          }
        });
      }
    };

    System.state("content-management/media", function (state) {
      System.entity('objects/media-state-handler', new MediaStateHandler(state));
    });

    System.state("content-management/media/photos", function (state) {
      System.entity('components/media/photos').create(state);
    });

    System.state("content-management/media/audios", function (state) {
      System.entity('components/media/audios').create(state);
    });
  })();
</script>
