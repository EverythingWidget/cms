Scope.export = CommentsComponent;

function CommentsComponent(state) {
  var component = this;
  this.state = state;
  this.state.type = "app";
  this.data = {};

  this.state.onInit = function () {
    component.init();
  };

  this.state.onStart = function () {
    component.start();
  };
}

CommentsComponent.prototype.init = function () {
  var component = this;

};

CommentsComponent.prototype.start = function () {
  this.data.tab = null;
};


// ------ Registring the state handler ------ //
var stateId = 'ew-blog/comments';

if (Scope._stateId === stateId) {
  Scope.primaryMenu = System.entity('ui/primary-menu');

  System.state(stateId, function (state) {
    new CommentsComponent(Scope, state);
  });
}