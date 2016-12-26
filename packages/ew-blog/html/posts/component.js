/* global Scope, System, EW */

var BlogService = Scope.import('html/ew-blog/core/service.html');

Scope.export = PostsComponent;

function PostsComponent(state, scope) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.data = {
    tab: null,
    card_title: 'Posts',
    compact_view: false,
    loading: false,
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

  state.onInit = function () {
    component.vue = new Vue({
      el: Scope.views.comments_card,
      data: component.data,
      methods: {
        getCommentStatus: function (post) {
          return this.comment_status.filter(function (item) {
            return item.value === post.comments;
          })[0];
        },
        showPost: BlogService.showArticle,
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

  state.onStart = function () {
    component.data.tab = null;

    $(document).off('article-list.refresh').on('article-list.refresh', function (e, eventData) {
      component.vue.$broadcast('refresh');
    });
  };
}

System.newStateHandler(Scope, function (state) {
  new PostsComponent(state, Scope);
});