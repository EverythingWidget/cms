/* global Scope, System, system */

Scope.export = CommentsComponent;

function CommentsComponent(scope, state) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.state.type = "app";
  component.data = {
    tab: null,
    card_title: 'Comments',
    url: 'api/ew-blog/comments/',
    filter: {
      include: ['ewContent'],
      where: {
        visibility: {
          not: 'confirmed'
        }
      }
    },
    comments: {
      url: 'api/ew-blog/comments/',
      page_size: 15
    }
  };

  component.state.onInit = component.init.bind(component);

  component.state.onStart = component.start.bind(component);
}

CommentsComponent.prototype.init = function () {
  var component = this;
  component.vue = new Vue({
    el: Scope.views.comments_card,
    data: component.data,
    methods: {
      confirmComment: component.confirmComment.bind(component),
      deleteComment: component.deleteComment.bind(component),
      reloadComments: function () {
        component.vue.$broadcast('refresh');
      }
    }
  });
};

CommentsComponent.prototype.start = function () {
  var component = this;
  component.data.tab = null;
};

CommentsComponent.prototype.confirmComment = function (id) {
  var component = this;
  var lock = System.ui.lock({
    element: Scope.views.comments_card,
    akcent: 'loader center'
  });

  $.ajax({
    type: 'PUT',
    url: 'api/ew-blog/comments/confirm/' + id,
    success: function () {
      component.vue.$broadcast('refresh');
    },
    complete: function () {
      lock.dispose();
    }
  });
};

CommentsComponent.prototype.deleteComment = function (id) {
  var component = this;
  var lock = System.ui.lock({
    element: Scope.views.comments_card,
    akcent: 'loader center'
  });

  $.ajax({
    type: 'DELETE',
    url: 'api/ew-blog/comments/' + id,
    success: function () {
      component.vue.$broadcast('refresh');
    },
    complete: function () {
      lock.dispose();
    }
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