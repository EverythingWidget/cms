/* global EW, System, Scope */


System.newStateHandler(Scope, Handler);

Scope.export = Handler;

function Handler(state, scope) {
  scope = scope || Scope;

  var tabs = [
    {
      title: 'Layouts',
      state: 'all',
      url: 'html/webroot/widgets-management/layouts-tab/component.php',
      id: 'widgets-management/layouts/all'
    },
    {
      title: 'Blocks',
      state: 'blocks',
      url: 'html/webroot/blocks/blocks-tab/component.php',
      id: 'widgets-management/layouts/blocks'
    }
  ];

  state.onInit = function () {
    state.on('app', System.utility.withHost(state).behave(System.services.app_service.select_sub_section));
  };

  state.onStart = function () {
    System.entity('ui/app-bar').subSections = tabs;

    if (!System.entity('ui/apps').currentSubSection) {
      System.entity('ui/apps').goToState('widgets-management/layouts/all');
    }
  };
}