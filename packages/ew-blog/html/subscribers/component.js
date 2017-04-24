/* global System, Scope */

function SubscribersComponent(state, scope) {
  var component = this;
  component.scope = scope;

  state.onInit = function () {
    component.vue = new Vue({
      el: scope.views.subscribers_card,
      data: {
        card_title: 'Subscribers',
        subscribers: {
          url: 'api/ew-blog/subscribers/',
          page_size: 15
        }
      },
      methods: {
        deleteSubscriber: component.deleteSubscriber.bind(component),
        reload: function () {
          component.vue.$broadcast('refresh');
        }
      }
    });
  };

  state.onStart = function () {

  };
}

SubscribersComponent.prototype.deleteSubscriber = function (id) {
  var component = this;
  var lock = System.ui.lock({
    element: component.scope.views.subscribers_card,
    akcent: 'loader center'
  });

  $.ajax({
    type: 'DELETE',
    url: 'api/ew-blog/subscribers/' + id,
    success: function () {
      component.vue.$broadcast('refresh');
    },
    complete: function () {
      lock.dispose();
    }
  });
};

// ------ Registering the state handler ------ //

System.newStateHandler(Scope, function (state) {
  new SubscribersComponent(state, Scope);
});
