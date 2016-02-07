(function () {
  UIUtil = {
    viewRegex: /\{\{([^\{\}]*)\}\}/g,
    populate: function (template, data) {
      template = template.replace(this.viewRegex, function (match, key) {
        //eval make it possible to reach nested objects
        var val = eval("data." + key);
        return "undefined" === typeof val ? "" : val;
      });
      return template;
    },
    stringifyAttribute: function (element, attr, value) {
      if ("object" === typeof value) {
        value = JSON.stringify(value);
        //element.setAttribute(attr, value);
        //return false;
      }
      return value;
      //return true;
    },
    hasCSSClass: function (element, className) {
      if (element.classList)
        return  element.classList.contains(className);
      else
        return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
    },
    addCSSClass: function (element, className) {
      if (this.hasCSSClass(element, className))
        return;
      if (element.classList)
        element.classList.add(className);
      else
        element.className += ' ' + className;
    },
    removeCSSClass: function (element, className) {
      if (element.classList)
        element.classList.remove(className);
      else
        element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }
  };
})();

(function (xtag, System) {
  var ewList = {
  };

  ewList.lifecycle = {
    created: function () {
      this.template = this.innerHTML;
      this.innerHTML = "";
      this.links = {};
      this.data = [
      ];
      this.value = null;
      //console.log(this);
    },
    inserted: function () {
    },
    attributeChanged: function (attrName, oldValue, newValue) {
    }
  };

  ewList.methods = {
    render: function (data) {
      //var data = this.data;
      this.innerHTML = "";
      var a = null;
      for (var i = 0, len = data.length; i < len; i++) {
        data[i]._itemIndex = i;
        var item = xtag.createFragment(UIUtil.populate(this.template, data[i]));
        a = xtag.query(item, "a")[0];

        if (data[i].id)
          this.links[data[i].id] = a;

        this.links[i] = a;
        a.dataset.index = i;

        this.appendChild(item);
      }
    },
    selectItem: function (i, element) {
      if (this.onItemSelected) {
        this.onItemSelected(this.xtag.data[i], i, element);
      }

      /*if (EW_List.selectedElement) {
       UIUtil.removeCSSClass(EW_List.selectedElement, "selected");
       }
       
       EW_List.selectedElement = element;
       UIUtil.addCSSClass(EW_List.selectedElement, "selected");*/
    }
  };

  ewList.accessors = {
    data: {
      attribute: {},
      set: function (value) {

        this.xtag.value = null;
        if ("object" !== typeof value) {
          this.xtag.data = [
          ];
          value = [
          ];
          //return;
        }

        this.xtag.data = value;

        if (this.onSetData) {
          this.onSetData(value);
        }

        this.render(value);
      },
      get: function () {
        return this.xtag.data;
      }
    },
    onItemSelected: {
      attribute: {},
      set: function (value) {
        //console.log(this.onSetData, typeof this.onSetData);
        this.xtag.onItemSelected = value;
      },
      get: function (value) {
        return this.xtag.onItemSelected;
      }
    },
    onSetData: {
      attribute: {},
      set: function (value) {
        this.xtag.onSetData = value;
      },
      get: function (value) {
        return this.xtag.onSetData;
      }
    },
    value: {
      attribute: {},
      set: function (value) {
        value = parseInt(value);

        if (value > -1 /*&& value !== this.value*/ && this.xtag.data.length) {
          this.selectItem(value, this.links[value]);
        }

        this.xtag.value = value;
      },
      get: function () {
        return this.xtag.value;
      }
    }
  };

  ewList.events = {
    "click:delegate(a)": function (e) {
      e.preventDefault();
    },
    "tap:delegate(a)": function (e) {
      e.preventDefault();
      e.currentTarget.value = e.target.dataset.index;

    }
  };

  xtag.register("system-list", ewList);

  // EW Actions Container

  var ewFloatMenu = {
    lifecycle: {
      created: function () {
        var _this = this;
        this.xtag.indicator = document.createElement("div");
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.xtag.indicator.style.position = "absolute";

        this.xtag.indicator.addEventListener("click", function () {
          if (_this.expanded) {
            _this.contract();

          } else {
            _this.expand();
          }
        });

        this.style.position = "absolute";
        this.xtag.originClassName = this.className;

        this.render();
      },
      inserted: function () {
        this.className = this.xtag.originClassName;
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.parentNode.appendChild(this.xtag.indicator);
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      },
      removed: function () {
        this.off(true);
      }
    },
    accessors: {
      position: {
        attribute: {}
      },
      parent: {
        attribute: {}
      },
      onAttached: {
        attribute: {},
        set: function (value) {
          this.xtag.onAttached = value;
        },
        get: function (value) {
          return this.xtag.onAttached;
        }
      }
    },
    methods: {
      render: function () {
        switch (this.position || "css") {
          case "css":
            this.xtag.indicator.style.right = this.style.right = "";
            this.xtag.indicator.style.top = this.style.bottom = "";
            this.xtag.indicator.style.position = "";
            this.style.position = "";
            break;
            /*case "ne":
             this.xtag.indicator.style.right = this.style.right = "5%";
             this.xtag.indicator.style.top = this.style.bottom = "5%";*/
            break;
          case "se":
          default:
            //this.xtag.indicator.style.right = this.style.right = "5%";
            //this.xtag.indicator.style.bottom = this.style.bottom = "5%";
            this.xtag.indicator.setAttribute("position", "se");
            break;
        }
      },
      expand: function () {
        if (this.expanded)
          return;
        this.expanded = true;
        var originDim = this.getBoundingClientRect();
        //this.className += " expand";
        //this.style.width = "auto";
        //this.style.height = "auto";

        var distDim = this.getBoundingClientRect();
        //this.className = this.xtag.originClassNaame;
        /*TweenLite.fromTo(this, 1, {
         width: originDim.width,
         height: originDim.height
         }, {
         width: distDim.width,
         height: distDim.height
         });*/

        TweenLite.to(this, .3, {
          className: this.xtag.originClassName + " expand",
          ease: "Power2.easeInOut"
        });

        TweenLite.to(this.xtag.indicator, .3, {
          className: "+=active",
          ease: "Power2.easeInOut"
        });
      },
      contract: function () {
        /*if (!this.expanded)
         return;*/
        this.expanded = false;
        TweenLite.to(this, .4, {
          className: this.xtag.originClassName,
          ease: "Power2.easeInOut"
        });

        TweenLite.to(this.xtag.indicator, .4, {
          className: this.xtag.originClassName + "-indicator",
          ease: "Power2.easeInOut"
        });
      },
      on: function (flag) {
        if (this.xtag.indicator.parentNode) {
          //this.xtag.indicator.className = this.xtag.originClassName + "-indicator";
          TweenLite.to(this.xtag.indicator, .4, {
            className: "-=destroy",
            onComplete: function () {
            }
          });

          TweenLite.to(this.xtag.indicator, .4, {
            className: this.xtag.originClassName + "-indicator",
            ease: "Power2.easeInOut"
          });
        }
      },
      off: function (flag) {
        var _this = this;
        if (_this.xtag.indicator.parentNode) {
          this.xtag.indicator.className = "ew-float-menu-indicator";

          TweenLite.to(this.xtag.indicator, .3, {
            className: "+=destroy",
            onComplete: function () {
              if (flag)
                _this.xtag.indicator.parentNode.removeChild(_this.xtag.indicator);
            }
          });
        }
      },
      clean: function () {
        this.innerHTML = "";
        //this.appendChild(this.xtag.indicator);
      }
    },
    events: {
      "mouseleave": function () {
        //this.contract();
      }
    }
  };

  xtag.register("system-float-menu", ewFloatMenu);

  var ewUITemplate = {
    lifecycle: {
      created: function () {
        this.xtag.validate = false;
        if (!this.name) {
          throw "system-ui-view missing the `name` attribute";
        }
      },
      inserted: function () {
        if (this.validate)
          return;

        if (!System.uiTemplates["system/" + this.module]) {
          System.uiTemplates["system/" + this.module] = {};
        }

        System.uiTemplates["system/" + this.module][this.name] = this.parentNode.removeChild(this);
        this.xtag.validate = true;
      },
      removed: function () {

      }
    },
    accessors: {
      name: {
        attribute: {}
      },
      module: {
        attribute: {}
      },
      validate: {
        attribute: {},
        set: function (value) {
          this.xtag.validate = value;
        },
        get: function (value) {
          return this.xtag.validate;
        }
      }
    }
  };

  xtag.register("system-ui-view", ewUITemplate);

  var sortableList = {
    lifecycle: {
      created: function () {
        this.xtag.placeHolder = document.createElement("li");
        this.xtag.placeHolder.className += "placeholder";
        this.style.overflow = "hidden";
        this.isValidParent = function () {
          return true;
        };
        this.onDrop = function () {
        };
      },
      inserted: function () {

      },
      removed: function () {

      }
    },
    events: {
      mousedown: function (event) {
        //console.log("down");
      },
      "mousedown:delegate(.handle)": function (e) {
        var dim = this.getBoundingClientRect();
        e.currentTarget.xtag.initDragPosition = {
          x: e.pageX - dim.left,
          y: e.pageY - dim.top
        };

        var draggedItem = this;
        while (draggedItem.tagName.toLowerCase() !== "li") {
          draggedItem = draggedItem.parentNode;
        }

        var diDimension = draggedItem.getBoundingClientRect();
        e.currentTarget.xtag.draggedItem = draggedItem;
        draggedItem.style.position = "fixed";
        draggedItem.style.width = diDimension.width + "px";
        draggedItem.style.height = diDimension.height + "px";

        //console.log(e, draggedItem);
        e.stopPropagation();
        e.preventDefault();
      },
      "mouseup:delegate(.handle)": function (e) {
        e.stopPropagation();
        e.preventDefault();
      },
      mousemove: function (event) {
        if (!this.xtag.draggedItem)
          return;

        var groups = this.querySelectorAll("ul");
        var groupDim = [];
        for (var i = 0, len = groups.length; i < len; i++) {
          groupDim.push(groups[i].getBoundingClientRect());
        }

        var parent = null;
        var index = 0;
        var indexElement = null;

        for (var i = groupDim.length - 1; i >= 0; i--) {
          var parentDim = groupDim[i];
          if (event.pageX > parentDim.left && event.pageX < parentDim.right && event.pageY > parentDim.top && event.pageY < parentDim.bottom) {
            parent = groups[i];
            //indexElement = parent.lastChild;
            var children = parent.childNodes || [];
            var childElements = [];
            //index = childElements.length;
            for (var n = 0; n < children.length; n++) {
              if (children[n].tagName.toLowerCase() !== "li" || children[n] === this.xtag.draggedItem /*|| children[n].className === "placeholder"*/)
                continue;
              childElements.push(children[n]);
            }
            //console.log(childElements)
            var extra = {
              height: 0,
              left: 0
            };
            for (n = childElements.length - 1; n >= 0; n--) {
              if (childElements[n].className === "placeholder") {
                //extra = childElements[n].getBoundingClientRect();
                //console.log(extra.height)
                continue;
              }

              var childDim = childElements[n].getBoundingClientRect();

              if (event.pageY > childDim.top && event.pageY < childDim.top + (childDim.height / 2) /*&& event.pageY + extra.height < childDim.bottom - (childDim.height / 2)*/) {
                index = n;
                indexElement = childElements[index] /*|| parent.firstChild*/;
                //console.log("above", index);
                //console.log(childDim, event.pageY, n)
                break;
              } else if (event.pageY >= childDim.top + (childDim.height / 2) /*&& event.pageY < childDim.bottom*/) {
                index = n;
                indexElement = childElements[index].nextSibling;
                //console.log("lower", index);
                //console.log(childDim, event.pageY, n)
                break;
              } else {
                indexElement = this.xtag.tempIndexElement;
                //console.log(extra, event.pageY)
              }
              //console.log(extra)
              //extra.height = 0;
              //extra.top = 0;
            }
            break;
          }
        }

        this.xtag.draggedItem.style.left = event.pageX + 1 + "px";
        this.xtag.draggedItem.style.top = event.pageY + 1 + "px";

        if (parent && (this.xtag.tempParent !== parent || this.xtag.tempIndexElement !== indexElement)) {
          this.xtag.tempParent = parent;
          this.xtag.tempIndex = index;
          this.xtag.tempIndexElement = indexElement;
          if (this.isValidParent(this.xtag.draggedItem, parent, this.xtag.tempIndex)) {
            //console.log(indexElement)
            if (indexElement && indexElement.parentNode === parent)
              parent.insertBefore(this.xtag.placeHolder, indexElement);
            else if (!indexElement)
              parent.insertBefore(this.xtag.placeHolder, indexElement);
          }
        }
      },
      mouseup: function (event) {
        //console.log("up");
        if (this.xtag.draggedItem) {
          this.xtag.draggedItem.style.position = "";
          this.xtag.draggedItem.style.width = "";
          this.xtag.draggedItem.style.height = "";
          this.xtag.draggedItem.style.left = "";
          this.xtag.draggedItem.style.top = "";

          if (this.xtag.placeHolder.parentNode) {
            this.onDrop(this.xtag.draggedItem, this.xtag.tempParent, this.xtag.tempIndex);
            this.xtag.placeHolder.parentNode.replaceChild(this.xtag.draggedItem, this.xtag.placeHolder);
          }

          this.xtag.draggedItem = null;
          this.xtag.tempParent = null;
          this.xtag.tempIndex = null;
        }
      }
    }
  };

  xtag.register("system-sortable-list", sortableList);


  var inputNumber = {
    lifecycle: {
      created: function () {
        this.xtag.input = document.createElement("input");
        this.xtag.input.value = this.getAttribute("value");
        this.tabIndex = 1;
      },
      inserted: function () {
        this.appendChild(this.xtag.input);
      },
      removed: function () {
        
      }
    },
    accessors: {
      value: {
        attribute: {},
        set: function (value) {
          this.xtag.value = value;
        },
        get: function () {
          return this.xtag.value;
        }
      }
    },
    methods: {
      increase: function () {
        
      }
    },
    events: {
      "focus:delegate(input)": function (event) {
        event.currentTarget.focus();
      }
    }
  };

  xtag.register("system-input-number", inputNumber);

})(xtag, System);