/* global Scope, System, EW */

Scope.export = LayoutBlocksComponent;

function LayoutBlocksComponent(state, scope) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.data = {
    tab: null,
    card_title: 'Blocks',
    compact_view: false,
    loading: false,
    filter: {},
    blocks: {
      url: 'api/webroot/blocks',
      page_size: 9,
      data: []
    }
  };

  state.onInit = function () {
    component.vue = new Vue({
      el: Scope.views.blocks_card,
      data: component.data,
      methods: {
        show: function () {

        },
        reload: function () {
          component.vue.$broadcast('refresh');
        }
      }
    });
  };

  state.onStart = function () {
    component.data.tab = null;

    System.entity('ui/primary-menu').actions = [
      {
        title: "tr{New Block}",
        activity: 'webroot/html/blocks/block-form/component.php',
        parameters: {
          uisId: null
        },
        modal: {
          class: 'full'
        },
        onDone: function (hash) {
          hash.id = null;
        }
      }
    ];
  };
}

System.newStateHandler(Scope, function (state) {
  new LayoutBlocksComponent(state, Scope);
});