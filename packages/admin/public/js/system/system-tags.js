/* global System, xtag */

//  polyfill the CustomEvent() constructor functionality in Internet Explorer 9 and higher 
(function () {

  if (typeof window.CustomEvent === "function")
    return false;

  function CustomEvent(event, params) {
    params = params || {
      bubbles: false,
      cancelable: false,
      detail: undefined
    };
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;

  window.CustomEvent = CustomEvent;
})();

(function (System, xtag) {

  var SystemList = {
    lifecycle: {
      created: function () {
        this.template = this.innerHTML;
        this.xtag.selectedStyle = 'selected';
        this.xtag.action = '[item]';
        this.innerHTML = "";
        this.links = {};
        this.data = [];
        this.value = -1;
      },
      inserted: function () {
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      }
    },
    methods: {
      render: function (data, action) {
        //var data = this.data;
        this.innerHTML = "";
        var selectableItem = null;
        for (var i = 0, len = data.length; i < len; i++) {
          //data[i]._itemIndex = i;
          var item = xtag.createFragment(System.ui.utility.populate(this.template, data[i]));
          if (action) {
            selectableItem = xtag.query(item, action)[0];

            if (selectableItem) {
              selectableItem.dataset.index = i;
              selectableItem.setAttribute('item', '');

              if (data[i].id) {
                this.links[data[i].id] = selectableItem;
              }

              this.links[i] = selectableItem;
            }
          }

          this.appendChild(item);
        }
      },
      selectItem: function (i, element) {
        var oldItem = this.links[this.xtag.value];
        if (oldItem) {
          System.ui.utility.removeClass(oldItem, this.xtag.selectedStyle);
        }

        var newItem = this.links[i];
        if (this.data[i].id) {
          newItem = this.links[this.data[i].id];
        }

        System.ui.utility.addClass(newItem, this.xtag.selectedStyle);

        var event = new CustomEvent('item-selected', {
          detail: {
            index: i,
            data: this.xtag.data[i],
            element: element
          }
        });

        this.dispatchEvent(event);
      }
    },
    accessors: {
      data: {
        set: function (value) {
          var element = this;

          this.value = -1;
          if ("object" !== typeof value) {
            this.xtag.data = [];
            value = [];
          }

          var toRender = value;

          this.xtag.data = value;

          if (this.onSetData) {
            this.onSetData(toRender);
          }

          this.render(toRender, this.xtag.action);
        },
        get: function () {
          return this.xtag.data;
        }
      },
      onSetData: {
        attribute: {
          validate: function (value) {
            this.xtag.onSetData = value;
            return '[ function ]';
          }
        },
        set: function (value) {
        },
        get: function (value) {
          return this.xtag.onSetData;
        }
      },
      selectedStyle: {
        attribute: {},
        set: function (value) {
          this.xtag.selectedStyle = value;
        },
        get: function () {
          return this.xtag.selectedStyle;
        }
      },
      value: {
        attribute: {},
        set: function (value, oldValue) {
          if (value === oldValue) {
            return;
          }

          value = parseInt(value);

          if (value > -1 && /*value !== this.xtag.value && */this.xtag.data.length) {
            this.selectItem(value, this.links[value]);
          }

          this.xtag.value = value;


        },
        get: function () {
          return this.xtag.value;
        }
      },
      action: {
        attribute: {},
        set: function (value) {
          this.xtag.action = value;
        },
        get: function () {
          return this.xtag.action;
        }
      }
    },
    events: {
      "click:delegate([item])": function (e) {
        e.preventDefault();
        e.currentTarget.value = this.dataset.index;
      },
      "tap:delegate([item])": function (e) {
        e.preventDefault();
        e.currentTarget.value = this.dataset.index;
      }
    }
  };

  xtag.register("system-list", SystemList);

  // ------ //

  var FloatMenu = {
    lifecycle: {
      created: function () {
        var _this = this;
        this.xtag.indicator = document.createElement("div");
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.xtag.indicator.style.position = "absolute";
        _this.xtag.actionsContainer = _this.querySelector('[actions]');

        var expand = function (e) {
          e.stopPropagation();
          e.preventDefault();

          if (!_this.expanded) {
            _this.expand();
            window.addEventListener('touchstart', contract);
          }
        };

        var contract = function (e) {
          e.stopPropagation();
          e.preventDefault();

          if (_this.expanded) {
            _this.contract();
          }

          window.removeEventListener('touchstart', contract);
        };

        //_this.xtag.indicator.addEventListener('mouseenter', expand);
        //_this.xtag.indicator.addEventListener('touchstart', expand);

        _this.addEventListener('mouseenter', expand);
        _this.addEventListener('touchstart', expand);

        _this.addEventListener('mouseleave', contract);

        this.style.position = 'absolute';
        this.xtag.originClassName = this.className;

        this.xtag.observer = new MutationObserver(function (mutations) {
          if (_this.xtag.actionsContainer.children.length) {
            _this.on();
          } else {
            _this.off();
          }
        });
      },
      inserted: function () {
        var _this = this;

        _this.xtag.observer.observe(_this.xtag.actionsContainer, {
          attributes: false,
          childList: true,
          characterData: false
        });

        if (_this.children.length) {
          _this.on();
        } else {
          _this.off();
        }
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
      expand: function () {
        if (this.expanded)
          return;

        this.expanded = true;
        System.ui.utility.addClass(this, 'expand');
      },
      contract: function () {
        this.expanded = false;
        System.ui.utility.removeClass(this, 'expand');
      },
      on: function (flag) {
        System.ui.utility.removeClass(this, 'off');
      },
      off: function (flag) {
        System.ui.utility.addClass(this, 'off');
      },
      clean: function () {
        this.innerHTML = "";
      }
    },
    events: {}
  };

  xtag.register("system-float-menu", FloatMenu);

  // ------ //

  var SystemUITemplate = {
    lifecycle: {
      created: function () {
        this.xtag.validate = false;
        this.xtag.show = true;

        if (!this.name) {
          throw "system-ui-view missing the `name` attribute";
        }

        this.xtag.placeholder = document.createComment(' ' + this.module + '/' + this.name + ' ');
      },
      inserted: function () {
        if (this.xtag.validate) {
          this.xtag.originalParent = this.parentNode;
          return;
        }

        this.xtag.originalParent = this.parentNode;
//        System.ui.templates["system/" + this.module][this.name] = this;
        if (this.xtag.showWhenAdded) {
          this.xtag.showWhenAdded = null;
          this.show();
          return;
        }
      },
      removed: function () {
        this.xtag.validate = false;
      }
    },
    methods: {
      show: function () {
        this.xtag.validate = true;
        this.xtag.shouldBeShown = true;
        if (!this.xtag.originalParent) {
          this.xtag.showAsSoonAsAdded = true;
          return;
        }
        if (this.xtag.placeholder.parentNode)
          this.xtag.originalParent.replaceChild(this, this.xtag.placeholder);
      },
      hide: function () {
        this.xtag.originalParent = this.parentNode;
        this.xtag.originalParent.replaceChild(this.xtag.placeholder, this);
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

  xtag.register("system-ui-view", SystemUITemplate);

  // ------ //

  var SortableList = {
    lifecycle: {
      created: function () {
        this.xtag.placeHolder = document.createElement("li");
        this.xtag.placeHolder.className += "placeholder";

        this.xtag.glass = document.createElement("div");
        this.xtag.glass.style.position = "absolute";
        this.xtag.glass.style.width = "100%";
        this.xtag.glass.style.height = "100%";

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
        e.currentTarget.xtag.glass.width = diDimension.width + "px";
        e.currentTarget.xtag.glass.height = diDimension.height + "px";
        draggedItem.appendChild(e.currentTarget.xtag.glass);
        System.ui.utility.addClass(draggedItem, "dragged");

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
        var groupDim = [
        ];
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
            var children = parent.childNodes || [
            ];
            var childElements = [
            ];
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
                index = n + 1;
                indexElement = childElements[index];
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

        this.xtag.draggedItem.style.left = event.pageX - this.xtag.initDragPosition.x + "px";
        this.xtag.draggedItem.style.top = event.pageY - this.xtag.initDragPosition.y + "px";

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
          this.xtag.draggedItem.removeChild(this.xtag.glass);
          System.ui.utility.removeClass(this.xtag.draggedItem, "dragged");

          if (this.xtag.placeHolder.parentNode) {
            this.onDrop(this.xtag.draggedItem, this.xtag.tempParent, this.xtag.tempIndex);
            this.xtag.placeHolder.parentNode.replaceChild(this.xtag.draggedItem, this.xtag.placeHolder);
          }

          this.xtag.draggedItem = null;
          this.xtag.tempParent = null;
          this.xtag.tempIndex = null;
        }
        event.preventDefault();
        event.stopPropagation();
      }

    }
  };

  xtag.register("system-sortable-list", SortableList);

  // ------ //

  var SwitchButton = {
    lifecycle: {
      created: function () {
        this.xtag.active = false;
      },
      inserted: function () {
      },
      removed: function () {
      },
      attributeChanged: function (attrName, oldValue, newValue) {

      }
    },
    accessors: {
      name: {
        attribute: {}
      },
      module: {
        attribute: {}
      },
      active: {
        attribute: {
          //boolean: true
        },
        set: function (value) {
          xtag.fireEvent(this, 'switched', {
            detail: {
              active: Boolean(value)
            },
            bubbles: true,
            cancelable: true
          });
          this.xtag.active = Boolean(value);
        },
        get: function () {
          return this.xtag.active;
        }
      }
    },
    events: {
      click: function (event) {
        if (this.xtag.active) {
          event.currentTarget.removeAttribute('active');
        } else {
          event.currentTarget.setAttribute('active', 'true');
        }
      }
    }
  };

  xtag.register("system-button-switch", SwitchButton);

  // ------ //

  var SystemInputJson = {
    lifecycle: {
      created: function () {
        this.xtag.elementType = 'input';
        this.xtag.allFields = [];
        this.xtag.fields = [];
        this.xtag.lastField = this.createField('', '');
        this.xtag.active = this.xtag.lastField;

        this.elementType = this.xtag.elementType;

        this.updateFieldsCount();
      }
    },
    methods: {
      createField: function (nameValue, valueValue) {
        var jsonInput = this;
        var name = document.createElement('input');
        name.value = nameValue;
        name.className = 'name';
        name.placeholder = 'name';

        var value = document.createElement('input');
        if ('object' === typeof valueValue) {
          value = document.createElement('system-input-json');
        }
        value.value = valueValue;
        value.className = 'value';
        value.placeholder = 'value';

        var field = document.createElement('p');

        name.addEventListener('keyup', function (e) {
          jsonInput.updateFieldsCount();
        });

        name.addEventListener('focus', function (e) {
          jsonInput.xtag.active = field;
        });

        value.addEventListener('keyup', function (e) {
          jsonInput.updateFieldsCount();
        });

        value.addEventListener('focus', function (e) {
          jsonInput.xtag.active = field;
        });

        field._name = name;
        field.appendChild(name);
        field.appendChild(value);

        this.xtag.allFields.push({
          name: name,
          value: value,
          field: field
        });

        this.appendChild(field);

        return {
          name: name,
          value: value,
          field: field
        };
      },
      updateFieldsCount: function () {
        var jsonInput = this;
        var newFields = [];
        this.xtag.fields = [];
        this.xtag.allFields.forEach(function (item) {
          if (!item.name.value && !item.value.value && item.field.parentNode && item.field !== jsonInput.xtag.lastField.field) {
            item.field.parentNode.removeChild(item.field);
            return;
          }

          if (item.value.nodeName === 'INPUT' && item.value.value === '{}') {
            var json = document.createElement('system-input-json');
            json.className = 'value';
            item.field.replaceChild(json, item.value);
            item.value = json;
            json.focus();
          }

          if (item.field !== jsonInput.xtag.lastField.field) {
            jsonInput.xtag.fields.push(item);
          }

          newFields.push(item);
        });

        this.xtag.allFields = newFields;

        if (!jsonInput.xtag.lastField.name || jsonInput.xtag.lastField.name.value) {
          jsonInput.xtag.lastField = this.createField('', '');
        }

        if (jsonInput.xtag.active && jsonInput.xtag.active.parentNode) {
          jsonInput.xtag.active.focus();
        } else {
          jsonInput.xtag.lastField.name.focus();
        }
      },
      focus: function () {
        this.xtag.allFields[this.xtag.allFields.length - 1].name.focus();
      }
    },
    accessors: {
      value: {
        set: function (data) {
          var jsonInput = this;

          if (jsonInput.xtag.allFields)
            jsonInput.xtag.allFields.forEach(function (item) {
              if (item.field.parentNode)
                item.field.parentNode.removeChild(item.field);
            });

          jsonInput.xtag.allFields = [];
          jsonInput.xtag.fields = [];

          if ('string' === typeof data)
            data = JSON.parse(data);

          if ('object' !== typeof data) {
            return;
          }

          for (var key in data) {
            if (data.hasOwnProperty(key)) {
              jsonInput.xtag.lastField = jsonInput.createField(key, data[key]);
              jsonInput.xtag.allFields.push(jsonInput.xtag.lastField);
            }
          }

          jsonInput.xtag.lastField = {};
          jsonInput.updateFieldsCount();
        },
        get: function () {
          var value = {};
          this.xtag.fields.forEach(function (item) {
            value[item.name.value] = item.value.value;
          });

          return value;
        }
      },
      elementType: {
        attribute: {},
        set: function (value) {
        },
        get: function () {
          return this.xtag.elementType;
        }
      }
    }
  };

  xtag.register('system-input-json', SystemInputJson);

  // ------ //

  var SystemField = {
    lifecycle: {
      created: function () {
        var element = this;
        var input = this.querySelectorAll('input, textarea, select');
        if (input.length > 1) {
          console.warn('Only one input field is allowed inside system-field', this);
        }

        this.xtag._input = this.querySelectorAll('input, textarea, select')[0];

        var setEmptiness = function () {
          if (element.xtag._input.value || element.xtag._input.type === 'file') {
            element.removeAttribute('empty');
          } else {
            element.setAttribute('empty', '');
          }
        };

        if (this.xtag._input) {
          setEmptiness();

          this.xtag._input.addEventListener('focus', function () {
            element.setAttribute('focus', '');
            setEmptiness();
          });

          this.xtag._input.addEventListener('blur', function () {
            element.removeAttribute('focus');
          });

          this.xtag._input.onchange = function (e) {
            if (this.value) {
              element.removeAttribute('empty');
            } else {
              element.setAttribute('empty', '');
            }
          };

          this.xtag._input.addEventListener('input', function (e) {
            setEmptiness();
          });
        }
      },
      inserted: function () {
      },
      removed: function () {
      }
    },
    accessors: {
    },
    events: {
    }
  };

  xtag.register('system-field', SystemField);

  // ------ //

  var SystemSpirit = {
    lifecycle: {
      created: function () {
        var element = this;
        element.xtag.animations = [];
        element.xtag.registeredAnimations = [];
        this.xtag.cachedAnimations = this.getAttribute('animations');
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      },
      inserted: function () {
        if (this.xtag.cachedAnimations && !this.xtag.animations.length) {
          this.setAttribute('animations', this.xtag.cachedAnimations)
          this.xtag.cachedAnimations = null;
          this.prepare();
        }
      },
      removed: function () {
        this.xtag.cachedAnimations = xtag.clone(this.xtag.animations).join(',');
        this.xtag.animations = [];
        this.prepare();
      }
    },
    accessors: {
      animations: {
        attribute: {
        },
        set: function (value) {
          var element = this;
          if (typeof value === 'string') {
            this.xtag.animations = value.split(/[\s,]+/).filter(Boolean);
          } else {
            this.xtag.animations = [];
          }

          element.prepare();
        },
        get: function () {
          return this.xtag.animations;
        }
      }
    },
    events: {
    },
    methods: {
      prepare: function () {
        var element = this;
        this.xtag.animations.forEach(function (item) {
          if (element.xtag.registeredAnimations.indexOf(item) !== -1) {
            return null;
          }

          if (!System.spiritAnimations[item]) {
            return console.warn('spirit animation not found:', item);
          }

          System.spiritAnimations[item].register(element);
          element.xtag.registeredAnimations.push(item);
        });

        this.xtag.registeredAnimations = this.xtag.registeredAnimations.filter(function (item) {
          if (element.xtag.animations.indexOf(item) === -1) {
            System.spiritAnimations[item].deregister(element);
            return false;
          }

          return true;
        });
      }
    }
  };

  xtag.register('system-spirit', SystemSpirit);
})(System, xtag);