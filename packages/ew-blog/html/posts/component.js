/* global Scope, System, EW */

Scope.export = PostsComponent;

function PostsComponent(scope, state) {
  var component = this;
  component.scope = scope;
  component.state = state;
  component.state.type = 'app';
  component.data = {
    tab: null,
    card_title: 'Posts',
    posts: [
    ],
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
  component.seeArticleActivity = EW.getActivity({
    activity: "admin/html/content-management/documents/article-form/component.php_see",
    onDone: function () {
      System.setHashParameters({
        folderId: null
      });
    }
  });

  component.vue = new Vue({
    el: Scope.views.comments_card,
    data: component.data,
    methods: {
      getCommentStatus: function (post) {
        return this.comment_status.filter(function (item) {
          return item.value === post.comments;
        })[0];
      },
      showPost: component.showPost.bind(component)
    }
  });

};

PostsComponent.prototype.start = function () {
  var component = this;
  component.data.tab = null;

  $(document).off('article-list.refresh').on('article-list.refresh', function (e, eventData) {
    component.readComments();
  });
  
  component.readComments();
};

PostsComponent.prototype.readComments = function () {
  var component = this;
  $.get('api/ew-blog/posts/', {
    page_size: 12
  }, function (response) {
    component.data.posts = response.data;
  });
};

PostsComponent.prototype.showPost = function (post) {
  this.seeArticleActivity({article: post.content.id});
};


// ------ Registring the state handler ------ //
var stateId = 'ew-blog/posts';

if (Scope._stateId === stateId) {
  Scope.primaryMenu = System.entity('ui/primary-menu');

  System.state(stateId, function (state) {
    new PostsComponent(Scope, state);
  });
}