/* global Scope, System */

var BlogService = Scope.import('html/ew-blog/core/service.html');

Scope.export = CommentsComponent;

function CommentsComponent(state, scope) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.data = {
    tab: null,
    loading: false,
    card_title: 'Comments',
    url: 'api/ew-blog/comments/',
    show: 'new',
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

  state.onInit = function () {
    component.vue = new Vue({
      el: Scope.views.comments_card,
      data: component.data,
      methods: {
        confirmComment: component.confirmComment.bind(component),
        deleteComment: component.deleteComment.bind(component),
        showPost: BlogService.showArticle,
        reloadComments: function () {
          component.vue.$broadcast('refresh');
        }
      },
      watch: {
        show: function (value, oldValue) {
          switch (value) {
            case 'confirmed':
              component.data.filter.where.visibility = 'confirmed';
              break;
            case 'new':
              component.data.filter.where.visibility = {not: 'confirmed'};
              break;
          }

          this.reloadComments();
        }
      }
    });
  };

  state.onStart = function () {
    component.data.tab = null;
  };
}


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

System.newStateHandler(Scope, function (state) {
  new CommentsComponent(state, Scope);
});