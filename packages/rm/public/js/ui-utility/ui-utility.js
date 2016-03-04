(function () {
  UIUtility = {
    viewRegex: /\{\{([^\{\}]*)\}\}/g,
    populate: function (template, data) {
      template = template.replace(this.viewRegex, function (match, key) {
        //eval make it possible to reach nested objects
        var val = eval("data." + key);
        return "undefined" === typeof val ? "" : val;
      });
      return template;
    },
    hasClass: function (element, className) {
      if (element.classList)
        return  element.classList.contains(className);
      else
        return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
    },
    addClass: function (el, className) {
      if ('string' === typeof className)
        className = className.split(' ');

      for (var i = 0, c = ''; i < className.length; i++) {
        c = className[i];

        if (!c)
          continue;

        if (el.classList)
          el.classList.add(c);
        else
          el.className += ' ' + c;
      }

      return el;
    },
    removeClass: function (el, className) {
      if (el.classList)
        el.classList.remove(className);
      else
        el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');

      return el;
    },
    toTreeObject: function (element) {
      var jsTree = {
        _: element,
        _children: [
        ]
      };
      var indexIndicator = {};
      for (var index in element.childNodes) {
        var node = element.childNodes[index];

        if (node.nodeType === Node.ELEMENT_NODE) {
          var key = node.nodeName.toLowerCase();
          if (indexIndicator[key]) {
            indexIndicator[key]++;
            jsTree[key + '_' + indexIndicator[key]] = UIUtility.toTreeObject(node);
          } else {
            indexIndicator[key] = 1;
            jsTree[node.nodeName.toLowerCase()] = UIUtility.toTreeObject(node);
          }

          jsTree._children.push(node);
        }
      }

      return jsTree;
    }
  };
})();