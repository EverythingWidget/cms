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
        y: 50
//        z: -550,
//        rotationX: -22,
//        transformOrigin: 'center center'
      }, {
        opacity: 1,
        y: 0,
//        rotationX: 0,
//        z: 0,
        ease: 'Power3.easeOut',
        clearProps:'y',
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
  System.spiritAnimations = {
    CONFIG: {
      baseDuration: 0.4,
      staggerDuration: 0.05
    }
  };

  // spirit animations should follow the service pattern, so the animation is a singleton object which is
  // responsible for registering elements and managing their animations
  System.spiritAnimations.liveHeight = {
    register: function (element) {
      new LiveHeightAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.liveHeightAnimation) {
        element.xtag.liveHeightAnimation.off();
      }
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
        _this.animation = TweenLite.fromTo(_this.element, System.spiritAnimations.CONFIG.baseDuration, {
          height: _this.height
        }, {
          height: newHeight,
          ease: 'Power2.easeInOut',
          onComplete: function () {
            _this.height = newHeight;
          }
        });
      }
    }, 250);
  };

  // ------ //

  System.spiritAnimations.zoom = {
    register: function (element) {
      new ZoomInAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.PopInAnimation) {
        element.xtag.PopInAnimation.off();
      }
    }
  };

  function ZoomInAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.zoomItem = element.getAttribute('zoom');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodes = [];

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

          node && nodes.push(node);
        });

        _this.animate(nodes);
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

  ZoomInAnimation.prototype.animate = function (nodes, style) {
    if (!nodes.length) {
      return;
    }

    var timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
      }
    });

    nodes.forEach(function (element) {
      TweenLite.set(element, {
        transition: 'none',
        scale: 0.01,
        opacity: 0
      });

      timelineItems.push(TweenLite.to(element, System.spiritAnimations.CONFIG.baseDuration, {
        scale: 1,
        opacity: 1,
        clearProps: 'transition,scale,opacity',
        ease: 'Power3.easeOut',
        onComplete: function () { }
      }));
    });

    timeline.add(timelineItems, null, null, System.spiritAnimations.CONFIG.staggerDuration);

    timeline.play(0);
  };

  // ------ //

  System.spiritAnimations.inOut = {
    register: function (element) {
      new InOutAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.InOutAnimation) {
        element.xtag.InOutAnimation.off();
      }
    }
  };

  function InOutAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.item = element.getAttribute('in-out-item');
    _this.class = element.getAttribute('in-out-class');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodesIn = [];
        var nodesOut = [];

        mutations.forEach(function (item) {
          var node = null;

          if (item.addedNodes[0]) {
            if (item.addedNodes[0].__ui_neutral || item.addedNodes[0].__in_out ||
                    item.addedNodes[0].nodeType !== Node.ELEMENT_NODE ||
                    !item.addedNodes[0].classList.contains(_this.item))
              return null;

            node = item.addedNodes[0];
            nodesIn.push(node);
          }

          if (item.removedNodes[0] && (item.removedNodes[0].__ui_neutral || item.removedNodes[0].__in_out)) {
            //item.removedNodes[0].__in_out = false;
            return null;
          }

          if (item.removedNodes[0] &&
                  item.removedNodes[0].nodeType === Node.ELEMENT_NODE &&
                  item.removedNodes[0].classList.contains(_this.item)) {
            node = item.removedNodes[0];
            node.__in_out = true;
            item.target.insertBefore(node, item.previousSibling.nextSibling);

            nodesOut.push(node);
          }
        });

        if (nodesOut.length) {
          _this.animate(nodesOut, 'out');
        }

        if (nodesIn.length) {
          _this.animate(nodesIn, 'in');
        }
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

  InOutAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  InOutAnimation.prototype.animate = function (nodes, style) {
    var _this = this;

    if (!nodes.length || !_this.class) {
      return;
    }

    _this.timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
        _this.timeline = null;
      }
    });

    if (style === 'in') {
      TweenLite.set(nodes, {
        transition: '',
        className: '+=' + _this.class,
        immediateRender: true
      });

      nodes.forEach(function (element) {
        _this.timelineItems.push(TweenLite.to(element, .4, {
          className: '-=' + _this.class,
          ease: 'Power3.easeOut',
          onComplete: function () { }
        }));
      });

      timeline.add(_this.timelineItems, null, null, 0.2);
    } else {
      TweenLite.set(nodes, {
        className: '-=' + _this.class
      });

      nodes.forEach(function (element) {
        _this.timelineItems.push(TweenLite.to(element, .4, {
          className: '+=' + _this.class,
          ease: 'Power3.easeOut',
          onComplete: function () {
            element.parentNode && element.parentNode.removeChild(element);
          }
        }));
      });

      timeline.add(_this.timelineItems, null, null);
    }

    timeline.play(0);
  };

  // ------ //

  System.spiritAnimations.verticalShift = {
    register: function (element) {
      new verticalShift(element);
    },
    deregister: function (element) {
      if (element.xtag.verticalShift) {
        element.xtag.verticalShift.off();
      }
    }
  };

  function verticalShift(element) {
    var _this = this;

    _this.element = element;
    _this.verticalShiftItem = element.getAttribute('vertical-shift');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodes = [];

        mutations.forEach(function (item) {
          var node = null;
          if (item.addedNodes[0]) {

            if (item.addedNodes[0].__ui_neutral ||
                    item.addedNodes[0].nodeType !== Node.ELEMENT_NODE ||
                    !item.addedNodes[0].classList.contains(_this.verticalShiftItem))
              return null;

            node = item.addedNodes[0];
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          node && nodes.push(node);
        });

        _this.animate(nodes);
      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.verticalShift = this;
  }

  verticalShift.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  verticalShift.prototype.animate = function (nodes) {
    if (!nodes.length) {
      return;
    }

    var timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
      }
    });

    nodes.forEach(function (element) {
      TweenLite.set(element, {
        transition: 'none',
        y: 50,
        opacity: 0
      });

      timelineItems.push(TweenLite.to(element, System.spiritAnimations.CONFIG.baseDuration, {
        opacity: 1,
        y: 0,
        clearProps: 'transition,y,opacity',
        ease: 'Power3.easeOut',
        onComplete: function () { }
      }));
    });

    timeline.add(timelineItems, null, null, System.spiritAnimations.CONFIG.staggerDuration / 2);

    timeline.play(0);
  };

})(System, TweenLite);