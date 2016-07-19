/* global TweenLite, System */

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

(function (System, TweenLite) {
  System.spiritAnimations = {};

  // spirit animations should follow the service pattern, so the animation is a singleton object which is
  // responsible for registering elements and managing their animations
  System.spiritAnimations.liveHeight = {};

  System.spiritAnimations.liveHeight.register = function (element) {
    new LiveHeightAnimation(element);
  };

  System.spiritAnimations.liveHeight.deregister = function (element) {
    if (element.xtag.liveHeightAnimation) {
      element.xtag.liveHeightAnimation.off();
    }
  };

  function LiveHeightAnimation(element) {
    var _this = this;
    _this.element = element;

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (item) {
          if (item.addedNodes[0] && item.addedNodes[0].__ui_neutral) {
            return null;
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          _this.animate();
        });
      });

      window.requestAnimationFrame(function () {
        _this.height = element.offsetHeight;

        TweenLite.set(_this.element, {
          height: _this.height
        });

        if (_this.observer) {
          _this.observer.observe(_this.element, {
            attributes: false,
            childList: true,
            characterData: false,
            subtree: true
          });
        }
      });

      _this.resizeHandler = function () {
        _this.animate();
      };

      window.addEventListener('resize', _this.resizeHandler);
    }

    _this.element.xtag.liveHeightAnimation = this;
  }

  LiveHeightAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;

      window.removeEventListener('resize', this.resizeHandler);
      TweenLite.set(this.element, {
        height: ''
      });
    }
  };

  LiveHeightAnimation.prototype.animate = function () {
    var _this = this;
    clearTimeout(_this.animationThrottle);

    _this.animationThrottle = setTimeout(function () {
      if (_this.animation) {
        _this.animation.pause();
        _this.animation = null;
      }

      var newHeight = System.ui.utility.getContentHeight(_this.element, true);

      if (_this.height !== newHeight) {
        _this.animation = TweenLite.fromTo(_this.element, .3, {
          height: _this.height
        }, {
          height: newHeight,
          ease: 'Power2.easeInOut',
          onComplete: function () {
            _this.height = newHeight;
          }
        });
      }
    }, 100);
  };

  // ------ //

  System.spiritAnimations.zoom = {};

  System.spiritAnimations.zoom.register = function (element) {
    new ZoomInAnimation(element);
  };

  System.spiritAnimations.zoom.deregister = function (element) {
    if (element.xtag.PopInAnimation) {
      element.xtag.PopInAnimation.off();
    }
  };

  function ZoomInAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.zoomItem = element.getAttribute('zoom');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {

        _this.stagger = 0;

        mutations.forEach(function (item) {
          var node = null;
          if (item.addedNodes[0]) {

            if (item.addedNodes[0].__ui_neutral ||
                    item.addedNodes[0].nodeType !== Node.ELEMENT_NODE ||
                    !item.addedNodes[0].classList.contains(_this.zoomItem))
              return null;

            node = item.addedNodes[0];
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          _this.animate(node);
        });


      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.PopInAnimation = this;
  }

  ZoomInAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  ZoomInAnimation.prototype.animate = function (node) {
    var _this = this;

    if (!node) {
      return;
    }

    if (!_this.timeline) {
      _this.timeline = new TimelineLite({
        paused: true,
        smoothChildTiming: true,
        onComplete: function () {
          _this.timeline = null;
        }
      });
    }

    TweenLite.set(node, {
      transition: 'none',
      scale: .1,
      opacity: 0
    });

    _this.timeline.fromTo(node, .3, {
      scale: .1
    }, {
      scale: 1,
      opacity: 1,
      ease: 'Power3.easeOut',
      clearProps: 'transition,opacity,scale',
      onComplete: function () { }
    }, '-=.28');

    clearTimeout(_this.animationThrottle);
    _this.animationThrottle = setTimeout(function () {
      _this.timeline.play(0);
    }, 100);
  };
})(System, TweenLite);