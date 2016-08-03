<script>

//  var Photos = Scope.import('html/admin/content-management/media/photos.component.html');
//  var Audios = Scope.import('html/admin/content-management/media/audios.component.html');

  function MediaStateHandler(scope, state) {
    var handler = this;
    handler.scope = scope;
    handler.states = {};
    handler.statePath = state.id.replace('system/', '');
    handler.tabs = {};
    handler.ui = {
      components: {},
      behaviors: {}
    };
    handler.ui.components.tabs_pills = $();
    handler.state = state;
    handler.state.type = "app-section";

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
      System.ui.behaviors.selectTab('#media-photos', handler.ui.components.tabs_pills);
      handler.state.setParamIfNull("album", "0/images");
      System.state(handler.statePath + '/photos').start();
    };

    tabs.audios = function () {
      System.ui.behaviors.selectTab('#media-audios', handler.ui.components.tabs_pills);
      System.state(handler.statePath + '/audios').start();
    };
  };

  /*MediaStateHandler.prototype.defineStateHandlers = function (states) {
   var handler = this;
   
   states.select = function (nav, itemId) {
   if (itemId > 0) {
   handler.selectedItemId = itemId;
   $("div[data-item-id='" + handler.selectedItemId + "']:not(:focus)").focus();
   } else {
   }
   };
   
   states.app = function (full, tab) {
   handler.state.data.tab = tab || handler.state.data.tab || 'photos';
   
   //        if (component.module.data.oldTab === component.module.data.tab) {
   //          component.module.setParamIfNot('app', 'content-management/media/' + component.module.data.tab);
   //          return;
   //        }
   
   if (handler.state.getParam('app') !== handler.statePath + '/' + handler.state.data.tab) {
   handler.state.setParamIfNot('app', handler.statePath + '/' + handler.state.data.tab);
   return;
   }
   
   if ('function' === typeof handler.tabs[handler.state.data.tab]) {
   handler.tabs[handler.state.data.tab].call(handler);
   handler.state.data.oldTab = handler.state.data.tab;
   }
   };
   };*/

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
    System.entity('objects/media-state-handler', new MediaStateHandler(Scope, state));
  });
</script>
