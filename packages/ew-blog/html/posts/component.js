/* global Scope, System, EW */

Scope.export = PostsComponent;

var blogService = Scope.import('html/ew-blog/core/service.html');

function PostsComponent(scope, state) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.state.type = 'app';
  component.data = {
    tab: null,
    card_title: 'Posts',
    compact_view: false,
    filter: {},
    posts: {
      url: 'api/ew-blog/posts/included-contents',
      page_size: 9
    },
    comment_status: [
      {
        value: 0,
        title: 'Inherit'
      },
      {
        value: 1,
        title: 'Only confirmed comments'
      },
      {
        value: 2,
        title: 'All'
      },
      {
        value: -1,
        title: 'None, disable comments'
      }
    ]
  };

  component.state.onInit = component.init.bind(component);

  component.state.onStart = component.start.bind(component);
}

PostsComponent.prototype.init = function () {
  var component = this;

  component.vue = new Vue({
    el: Scope.views.comments_card,
    data: component.data,
    methods: {
      getCommentStatus: function (post) {
        return this.comment_status.filter(function (item) {
          return item.value === post.comments;
        })[0];
      },
      showPost: blogService.showArticle,
      reload: function () {
        component.vue.$broadcast('refresh');
      }
    }
  });

  component.vue.$watch('compact_view', function (value) {
    if (value) {
      component.data.posts.page_size = 15;
    } else {
      component.data.posts.page_size = 9;
    }
  });

};

PostsComponent.prototype.start = function () {
  var component = this;
  component.data.tab = null;

  $(document).off('article-list.refresh').on('article-list.refresh', function (e, eventData) {
    component.vue.$broadcast('refresh');
  });
};

PostsComponent.prototype.readPosts = function () {
  var component = this;
  $.get('api/ew-blog/posts/included-contents', {
    page_size: 10
  }, function (response) {
    component.data.posts = response;
  });
};


// ------ Registring the state handler ------ //
var stateId = 'ew-blog/posts';

if (Scope._stateId === stateId) {
  Scope.primaryMenu = System.entity('ui/primary-menu');

  System.state(stateId, function (state) {
    new PostsComponent(Scope, state);
  });
}