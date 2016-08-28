/* global Scope, System */

Scope.export = CommentsComponent;

function CommentsComponent(scope, state) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.state.type = "app";
  component.data = {
    tab: null,
    card_title: 'Comments',
    comments: [
    ]
  };

  component.state.onInit = component.init.bind(component);

  component.state.onStart = component.start.bind(component);
}

CommentsComponent.prototype.init = function () {
  var component = this;
  component.vue = new Vue({
    el: Scope.views.comments_card,
    data: component.data
  });

};

CommentsComponent.prototype.start = function () {
  var component = this;
  component.data.tab = null;

  component.readComments();
};

CommentsComponent.prototype.readComments = function () {
  var component = this;
  $.get('api/ew-blog/comments/', {
    page_size: 30
  }, function (response) {
    console.log(response);
    component.data.comments = response.data;
  });
};


// ------ Registring the state handler ------ //
var stateId = 'ew-blog/comments';

if (Scope._stateId === stateId) {
  Scope.primaryMenu = System.entity('ui/primary-menu');

  System.state(stateId, function (state) {
    new CommentsComponent(Scope, state);
  });
}