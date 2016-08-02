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
    var MediaStateHandler = Scope.import('html/admin/content-management/media/media.component.php');
    var Photos = Scope.import('html/admin/content-management/media/photos.component.html');
    var Audios = Scope.import('html/admin/content-management/media/audios.component.html');

    System.state('content-management/media', function (state) {
      System.entity('objects/media-state-handler', new MediaStateHandler(Scope, state));
    });

    System.state('content-management/media/photos', function (state) {
      new Photos(Scope).create(state);
    });

    System.state('content-management/media/audios', function (state) {
      new Audios(Scope).create(state);
    });


  })();
</script>
