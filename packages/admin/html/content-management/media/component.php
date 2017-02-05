<script>
  function MediaStateHandler(scope, state) {
    var handler = this;
    handler.scope = scope;
    handler.states = {};
    handler.statePath = state.id.replace('system/', '');
    handler.tabs = {};

    handler.state = state;

    handler.state.onInit = function (templates) {
      handler.init(templates);
    };

    handler.state.onStart = function () {
      handler.start();
    };
  }

  MediaStateHandler.prototype.defineTabs = function (tabs) {
    var handler = this;
    tabs.photos = function () {
      handler.state.setParamIfNot('app', 'content-management/media/photos');
      handler.state.setParamIfNull('album', '0/images');
      System.state(handler.statePath + '/photos').start();
    };

    tabs.audios = function () {
      System.state(handler.statePath + '/audios').start();
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
    var component = this;

    component.state.on('app', System.utility.withHost(this.state).behave(System.services.app_service.select_sub_section));
  };

  MediaStateHandler.prototype.start = function () {
    var component = this;

    System.entity('ui/app-bar').subSections = [
      {
        title: 'Photos',
        state: 'photos',
        url: 'html/admin/content-management/media/photos.component.php',
        id: 'content-management/media/photos'
      }
    ];

    if (!System.entity('ui/apps').currentSubSection) {
      System.entity('ui/apps').goToState('content-management/media/photos');
    }
  };

  Scope.export = MediaStateHandler;

  // ------ Registring the state handler ------ //

  System.state('content-management/media', function (state) {
     new MediaStateHandler(Scope, state);
  });
</script>
