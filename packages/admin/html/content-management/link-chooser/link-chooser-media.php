<div class='header-pane thin'>
  <h1 class='form-title'>Media Chooser</h1>
</div>

<div class='form-content grid tabs-bar no-footer'>
  <system-ui-view name="photos-component" >
    <system-spirit animations="zoom" zoom="album">
      <system-ui-view name="albums-list" class="block-row" v-show="showAlbumList">  
        <div tabindex="1" v-for="album in albums" track-by="id" class="content-item album" 
             v-if="albumId === 0"
             v-on:focus="selectItem(album.id)" 
             v-on:dblclick="openAlbum(album.id)">
          <span></span>
          <p>{{ album.title }}</p>
        </div>      
      </system-ui-view>
    </system-spirit>

    <system-ui-view name="album-card" class="grid" v-show="!showAlbumList">
      <div  class="grid-header">
        <div class="card-title-action"></div>
        <div class="card-title-action-right"></div>
        <h1>
          {{ albumTitle }}
        </h1>
      </div>

      <system-spirit animations="zoom" zoom="grid-cell">
        <div class="album-images-list grid-content block-row">
          <div tabindex="1" class="grid-cell image"
               v-for="image in images" 
               v-on:focus="selectImage(image)" >
            <img alt="{{image.title}}" v-bind:src="image.thumbURL" />
            <div class="grid-cell-caption">
              <p class="title date">{{ image.size }} KB</p>
              <button class='pull-right btn-text btn-circle btn-danger icon-delete' v-on:click="deleteImage(image.id)"></button>
            </div>
          </div>
        </div>
      </system-spirit>
    </system-ui-view>
  </system-ui-view > 
</div>

<system-float-menu id='media-chooser-main-actions' class="system-float-menu" position="se">  
  <div class="float-menu-indicator"></div>
  <div class="float-menu-actions" actions>
    <button type="button"
            class="btn btn-primary"
            v-if="!action.hide"
            v-for="action in actions" v-on:click="callActivity(action)">
      {{ action.title }}
    </button>
  </div>
</system-float-menu>

<system-ui-view name="main-actions"></system-ui-view>

<script>
  var mediaChooserDomain = new System.Domain('#app=media-chooser');

  mediaChooserDomain.init();
  mediaChooserDomain.start();

  var Photos = Scope.import('html/admin/content-management/media/photos.component.php');

  Scope.primaryMenu = new Vue({
    el: Scope.ui.filter("#media-chooser-main-actions")[0],
    data: {
      actions: []
    },
    methods: {
      callActivity: System.entity('ui/primary-menu').callActivity
    }
  });

  mediaChooserDomain.state('media-chooser', function (state) {
    var photos = new Photos(Scope, state);

    var selectMediaAction = EW.addActionButton({
      text: "",
      handler: function () {
        photos.selectMedia(photos.data.activeImage);
      },
      class: "btn-float btn-success icon-ok pos-se",
      parent: $(Scope.uiViews.main_actions)
    }).hide();

    state.on('select', function (full, imageId) {
      if (photos.data.albumId && imageId) {
        selectMediaAction.comeIn();
      } else {
        selectMediaAction.comeOut();
      }
    });

    photos.selectMedia = function (image) {
      var _this = this;

      var loader = System.UI.lock({
        element: Scope.uiViews.photos_component,
        akcent: "loader center"
      }, .5);

      var img = new Image();
      img.onerror = function (e) {
        alert('Image is invalid');
        loader.dispose();

        Scope.selectMedia(false);
      };

      img.onload = function () {
        _this.data.activeImage.width = img.width;
        _this.data.activeImage.height = img.height;
        loader.dispose();

        Scope.selectMedia(_this.data.activeImage);
      };

      img.src = image.src;
    };
  });

  mediaChooserDomain.state('media-chooser').start();
</script>
