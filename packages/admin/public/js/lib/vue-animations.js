Vue.transition('slide', {
  beforeEnter: function (el) {
  },
  enter: function (el, done) {
    el.className += ' slide-out';
    TweenLite.to(el, .3, {
      className: '-=slide-out',
      onComplete: function () {
        done();
      }
    });
  },
  afterEnter: function (el) {
  },
  enterCancelled: function (el) {
    // handle cancellation
  },
  beforeLeave: function (el) {
  },
  leave: function (el, done) {
    TweenLite.to(el, .3, {
      className: '+=slide-out',
      ease: 'Power2.easeInOut',
      onComplete: function () {
        done();
      }
    });
  },
  afterLeave: function (el) {
  },
  leaveCancelled: function (el) {
    // handle cancellation
  }
})