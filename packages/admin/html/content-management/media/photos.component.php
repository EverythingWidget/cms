<system-ui-view name="photos-component" >
  <system-ui-view  name="albums-list" class="block-row" v-show="showAlbumList">  
    <system-spirit animations="zoom" zoom="album">

      <div tabindex="1" v-for="album in albums" track-by="id" class="content-item album" 
           v-if="albumId === 0"
           v-on:focus="selectItem(album.id)" 
           v-on:dblclick="openAlbum(album.id)">
        <span></span>
        <p>{{ album.title }}</p>
      </div>      

    </system-spirit>
  </system-ui-view>

  <system-ui-view name="album-card" class="grid" v-show="!showAlbumList">
    <div class="grid-header">
      <div class="card-title-action"></div>
      <div class="card-title-action-right"></div>
      <h1>
        {{ albumTitle }}
      </h1>
    </div>
    <system-spirit animations="zoom" zoom="grid-cell">
      <div class="album-images-list grid-content block-row" >
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
</system-ui-view> 

<script>
  /* global System */

  Scope.export = MediaPhotos;

  function MediaPhotos(scope, state) {
    var component = this;
    component.scope = scope;
    component.state = state;
    component.data = {
      albumTitle: 'Media',
      albumId: null,
      albums: [],
      images: []
    };

    component.state.onInit = function () {
      component.init();
    };

    component.state.onStart = function () {
      component.start();
    };
  }

  MediaPhotos.prototype.init = function () {
    var component = this;

    component.albumCard = $(component.scope.uiViews.album_card);
    component.albumCardTitleAction = this.albumCard.find(".card-title-action");
    component.albumCardTitleActionRight = this.albumCard.find(".card-title-action-right");

    component.photosVue = new Vue({
      el: component.scope.uiViews.photos_component,
      data: component.data,
      computed: {
        showAlbumList: function () {
          return component.data.albumId === 0 ? true : false;
        }
      },
      methods: {
        openAlbum: function (albumId) {
          component.state.setParam('album', albumId + '/images');
        },
        selectItem: function (id) {
          component.selectedItemId = id;
          component.state.setParam('select', id);
        },
        deleteImage: function (id) {
          component.selectedItemId = id;
          component.deleteImageActivity();
        }
      }
    });

    component.state.on('album', function (e, id, images) {
      if (id > 0) {
        component.data.uploadImage = false;
      } else {
        component.data.uploadImage = true;
      }

      if (!id) {
        id = 0;
      }

      if (images) {
        if (id !== null && component.data.albumId + '' !== id) {
          component.data.albumId = parseInt(id);
          if (component.listInited) {
            component.state.setParam("select", null, true);
          }

          component.listMedia();
        }
      }
    });

//    component.state.on('select', component.states.select);

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
          id: component.data.albumId
        };
      },
      onDone: function (response) {
        $("body").EW().notify(response).show();
        component.state.setParam('album', '0/images');
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
      text: "", class: "btn-text btn-default btn-circle icon-back",
      handler: function () {
        component.state.setParam('album', '0/images');
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

    $(document).off("media-list.refresh").on("media-list.refresh", function () {
      component.listMedia();
    });

    $(document).off('media.audios.list.refresh').on('media.audios.list.refresh', function (e, eventData) {
      component.audiosListTable.refresh();
    });
  };

  MediaPhotos.prototype.start = function () {
    var component = this;
    this.itemsList = $();
    this.bDel = $();
    this.listInited = false;

    System.entity('ui/primary-actions').actions = [
      {
        title: "tr{New Album}",
        activity: "admin/html/content-management/media/album-form.php",
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

//    this.albumCard[0].show();
//    this.albumsList[0].show();

    component.state.setParamIfNull("album", "0/images");
  };

  MediaPhotos.prototype.seeImageActivity = function (id) {

  };

  MediaPhotos.prototype.listMedia = function () {
    var component = this;

    this.listInited = false;
    component.data.albums = [];
    component.data.images = [];

    $.get('api/admin/content-management/get-media-list/', {
      parent_id: component.data.albumId
    }, done);

    function done(response) {
      //component.data.albumId = component.albumId;

      if (component.data.albumId === 0) {
        component.data.albums = response.data;
      } else {
        component.data.images = response.data;
      }

      component.listInited = true;
      component.photosVue.$nextTick(function () {
        if (component.selectedItemId) {
          $("div[data-item-id='" + component.selectedItemId + "']").focus();
        }
      });
    }
  };

  // ------ Registring the state handler ------ //

  if (Scope._stateId === 'content-management/media/photos') {
    console.log(Scope);
    System.state('content-management/media/photos', function (state) {
      new MediaPhotos(Scope, state);
    });
  }

</script>
