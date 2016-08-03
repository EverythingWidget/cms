<div class='header-pane'>
  <h1 class='form-title'>Media</h1>
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
  </system-ui-view > 
</div>

<system-float-menu id='media-chooser-main-actions' class="system-float-menu" position="se">  
  <div class="float-menu-indicator"></div>
  <div class="float-menu-actions" actions>    
  </div>
</system-float-menu>

<system-ui-view name="main-actions"></system-ui-view>

<script>
  var LinkChooserDomain = new System.Domain();
  LinkChooserDomain.init([]);

  LinkChooserDomain.domainHashString = "#app=media-chooser";
  LinkChooserDomain.ui.components = {
    document: $(document)
  };
  LinkChooserDomain.ui.components.mainFloatMenu = Scope.ui.filter("#media-chooser-main-actions").find('.float-menu-actions');
  LinkChooserDomain.ui.components.mainActions = Scope.uiViews['main-actions'];
  LinkChooserDomain.start();

  var Photos = Scope.import('html/admin/content-management/media/photos.component.php');

  LinkChooserDomain.state('media-chooser', function (state) {
    new Photos(Scope, state);
  });

  LinkChooserDomain.state("media-chooser").start();
</script>
