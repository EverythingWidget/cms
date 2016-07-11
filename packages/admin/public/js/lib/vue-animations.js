(function () {
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
  });

  Vue.transition('slide-vertical', {
    enter: function (element, done) {
      element.className += ' trans-slide-vertical';
      TweenLite.to(element, .3, {
        className: '-=trans-slide-vertical',
        onComplete: function () {
          done();
        }
      });
    },
    leave: function (element, done) {
      TweenLite.to(element, .3, {
        className: '+=trans-slide-vertical',
        ease: 'Power2.easeInOut',
        onComplete: function () {
          done();
        }
      });
    }
  });

  Vue.transition('in', {
    css: false,
    enter: function (element, done) {
      var timeline = new TimelineLite({});
      timeline.fromTo(element, .6, {
        opacity: 0,
        y: 0,
        z: -550,
        rotationX: -22,
        transformOrigin: 'center center'
      }, {
        opacity: 1,
        y: 0,
        rotationX: 0,
        z: 0,
        ease: 'Power1.easeInOut',
        onComplete: function () {
          done();
        }
      });
    },
    leave: function (element, done) {
      done();
    }
  });


})();
