(function () {
  window.FSM = {};

  FSM.Machine = (function () {
    function Machine(context) {
      this.context = context;
      this._stateTransitions = {};
      this._stateTransitionsAny = {};
      this._defaultTransition = null;
      this._initialState = null;
      this._currentState = null;
    }

    Machine.prototype.addTransition = function (action, state, nextState, callback) {
      if (!nextState) {
        nextState = state;
      }
      return this._stateTransitions[[action, state]] = [nextState, callback];
    };

    Machine.prototype.addTransitions = function (actions, state, nextState, callback) {
      var action, _i, _len, _results;
      if (!nextState) {
        nextState = state;
      }
      _results = [];
      for (_i = 0, _len = actions.length; _i < _len; _i++) {
        action = actions[_i];
        _results.push(this.addTransition(action, state, nextState, callback));
      }
      return _results;
    };

    Machine.prototype.addTransitionAny = function (state, nextState, callback) {
      if (!nextState) {
        nextState = state;
      }
      return this._stateTransitionsAny[state] = [nextState, callback];
    };

    Machine.prototype.setDefaultTransition = function (state, callback) {
      return this._defaultTransition = [state, callback];
    };

    Machine.prototype.getTransition = function (action, state) {
      if (this._stateTransitions[[action, state]]) {
        return this._stateTransitions[[action, state]];
      } else if (this._stateTransitionsAny[state]) {
        return this._stateTransitionsAny[state];
      } else if (this._defaultTransition) {
        return this._defaultTransition;
      }
      throw new Error("Transition is undefined: (" + action + ", " + state + ")");
    };

    Machine.prototype.getCurrentState = function () {
      return this._currentState;
    };

    Machine.prototype.setInitialState = function (state) {
      this._initialState = state;
      if (!this._currentState) {
        return this.reset();
      }
    };

    Machine.prototype.reset = function () {
      return this._currentState = this._initialState;
    };

    Machine.prototype.process = function (action) {
      var result;
      result = this.getTransition(action, this._currentState);
      if (result[1]) {
        result[1].call(this.context || (this.context = this), action);
      }
      return this._currentState = result[0];
    };

    return Machine;

  })();

}).call(this);

(function () {
  var ALPHA_CHARS, ALPHA_NUMERIC_CHARS, ATTR_DELIM, ATTR_ENTITY_DOUBLE_DELIM, ATTR_ENTITY_NO_DELIM, ATTR_ENTITY_SINGLE_DELIM, ATTR_NAME, ATTR_NAME_CHARS, ATTR_NAME_FIND_VALUE, ATTR_OR_TAG_END, ATTR_VALUE_DOUBLE_DELIM, ATTR_VALUE_NO_DELIM, ATTR_VALUE_SINGLE_DELIM, CHAR_OR_ENTITY_OR_TAG, CLOSING_TAG, ENTITY, ENTITY_CHARS, OPENING_TAG, OPENNING_OR_CLOSING_TAG, TAG_NAME_CHARS, TAG_NAME_CLOSING, TAG_NAME_MUST_CLOSE, TAG_NAME_OPENING, TAG_OPENING_SELF_CLOSING, _Parser,
          __slice = [].slice,
          __indexOf = [].indexOf || function (item) {
    for (var i = 0, l = this.length; i < l; i++) {
      if (i in this && this[i] === item)
        return i;
    }
    return -1;
  };

  window.HTMLString = {};

  HTMLString.String = (function () {
    String._parser = null;

    function String(html, preserveWhitespace) {
      if (preserveWhitespace == null) {
        preserveWhitespace = false;
      }
      this._preserveWhitespace = preserveWhitespace;
      if (html) {
        if (HTMLString.String._parser === null) {
          HTMLString.String._parser = new _Parser();
        }
        this.characters = HTMLString.String._parser.parse(html, this._preserveWhitespace).characters;
      } else {
        this.characters = [];
      }
    }

    String.prototype.isWhitespace = function () {
      var c, _i, _len, _ref;
      _ref = this.characters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        if (!c.isWhitespace()) {
          return false;
        }
      }
      return true;
    };

    String.prototype.length = function () {
      return this.characters.length;
    };

    String.prototype.preserveWhitespace = function () {
      return this._preserveWhitespace;
    };

    String.prototype.capitalize = function () {
      var c, newString;
      newString = this.copy();
      if (newString.length()) {
        c = newString.characters[0]._c.toUpperCase();
        newString.characters[0]._c = c;
      }
      return newString;
    };

    String.prototype.charAt = function (index) {
      return this.characters[index].copy();
    };

    String.prototype.concat = function () {
      var c, indexChar, inheritFormat, inheritedTags, newString, string, strings, tail, _i, _j, _k, _l, _len, _len1, _len2, _ref, _ref1;
      strings = 2 <= arguments.length ? __slice.call(arguments, 0, _i = arguments.length - 1) : (_i = 0, []), inheritFormat = arguments[_i++];
      if (!(typeof inheritFormat === 'undefined' || typeof inheritFormat === 'boolean')) {
        strings.push(inheritFormat);
        inheritFormat = true;
      }
      newString = this.copy();
      for (_j = 0, _len = strings.length; _j < _len; _j++) {
        string = strings[_j];
        if (string.length === 0) {
          continue;
        }
        tail = string;
        if (typeof string === 'string') {
          tail = new HTMLString.String(string, this._preserveWhitespace);
        }
        if (inheritFormat && newString.length()) {
          indexChar = newString.charAt(newString.length() - 1);
          inheritedTags = indexChar.tags();
          if (indexChar.isTag()) {
            inheritedTags.shift();
          }
          if (typeof string !== 'string') {
            tail = tail.copy();
          }
          _ref = tail.characters;
          for (_k = 0, _len1 = _ref.length; _k < _len1; _k++) {
            c = _ref[_k];
            c.addTags.apply(c, inheritedTags);
          }
        }
        _ref1 = tail.characters;
        for (_l = 0, _len2 = _ref1.length; _l < _len2; _l++) {
          c = _ref1[_l];
          newString.characters.push(c);
        }
      }
      return newString;
    };

    String.prototype.contains = function (substring) {
      var c, found, from, i, _i, _len, _ref;
      if (typeof substring === 'string') {
        return this.text().indexOf(substring) > -1;
      }
      from = 0;
      while (from <= (this.length() - substring.length())) {
        found = true;
        _ref = substring.characters;
        for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
          c = _ref[i];
          if (!c.eq(this.characters[i + from])) {
            found = false;
            break;
          }
        }
        if (found) {
          return true;
        }
        from++;
      }
      return false;
    };

    String.prototype.endsWith = function (substring) {
      var c, characters, i, _i, _len, _ref;
      if (typeof substring === 'string') {
        return substring === '' || this.text().slice(-substring.length) === substring;
      }
      characters = this.characters.slice().reverse();
      _ref = substring.characters.slice().reverse();
      for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
        c = _ref[i];
        if (!c.eq(characters[i])) {
          return false;
        }
      }
      return true;
    };

    String.prototype.format = function () {
      var c, from, i, newString, tags, to, _i;
      from = arguments[0], to = arguments[1], tags = 3 <= arguments.length ? __slice.call(arguments, 2) : [];
      if (to < 0) {
        to = this.length() + to + 1;
      }
      if (from < 0) {
        from = this.length() + from;
      }
      newString = this.copy();
      for (i = _i = from; from <= to ? _i < to : _i > to; i = from <= to ? ++_i : --_i) {
        c = newString.characters[i];
        c.addTags.apply(c, tags);
      }
      return newString;
    };

    String.prototype.hasTags = function () {
      var c, found, strict, tags, _i, _j, _len, _ref;
      tags = 2 <= arguments.length ? __slice.call(arguments, 0, _i = arguments.length - 1) : (_i = 0, []), strict = arguments[_i++];
      if (!(typeof strict === 'undefined' || typeof strict === 'boolean')) {
        tags.push(strict);
        strict = false;
      }
      found = false;
      _ref = this.characters;
      for (_j = 0, _len = _ref.length; _j < _len; _j++) {
        c = _ref[_j];
        if (c.hasTags.apply(c, tags)) {
          found = true;
        } else {
          if (strict) {
            return false;
          }
        }
      }
      return found;
    };

    String.prototype.html = function () {
      var c, closingTag, closingTags, head, html, openHeads, openTag, openTags, tag, _i, _j, _k, _l, _len, _len1, _len2, _len3, _len4, _m, _ref, _ref1, _ref2, _ref3;
      html = '';
      openTags = [];
      openHeads = [];
      closingTags = [];
      _ref = this.characters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        closingTags = [];
        _ref1 = openTags.slice().reverse();
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          openTag = _ref1[_j];
          closingTags.push(openTag);
          if (!c.hasTags(openTag)) {
            for (_k = 0, _len2 = closingTags.length; _k < _len2; _k++) {
              closingTag = closingTags[_k];
              html += closingTag.tail();
              openTags.pop();
              openHeads.pop();
            }
            closingTags = [];
          }
        }
        _ref2 = c._tags;
        for (_l = 0, _len3 = _ref2.length; _l < _len3; _l++) {
          tag = _ref2[_l];
          if (openHeads.indexOf(tag.head()) === -1) {
            if (!tag.selfClosing()) {
              head = tag.head();
              html += head;
              openTags.push(tag);
              openHeads.push(head);
            }
          }
        }
        if (c._tags.length > 0 && c._tags[0].selfClosing()) {
          html += c._tags[0].head();
        }
        html += c.c();
      }
      _ref3 = openTags.reverse();
      for (_m = 0, _len4 = _ref3.length; _m < _len4; _m++) {
        tag = _ref3[_m];
        html += tag.tail();
      }
      return html;
    };

    String.prototype.indexOf = function (substring, from) {
      var c, found, i, _i, _len, _ref;
      if (from == null) {
        from = 0;
      }
      if (from < 0) {
        from = 0;
      }
      if (typeof substring === 'string') {
        return this.text().indexOf(substring, from);
      }
      while (from <= (this.length() - substring.length())) {
        found = true;
        _ref = substring.characters;
        for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
          c = _ref[i];
          if (!c.eq(this.characters[i + from])) {
            found = false;
            break;
          }
        }
        if (found) {
          return from;
        }
        from++;
      }
      return -1;
    };

    String.prototype.insert = function (index, substring, inheritFormat) {
      var c, head, indexChar, inheritedTags, middle, newString, tail, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2;
      if (inheritFormat == null) {
        inheritFormat = true;
      }
      head = this.slice(0, index);
      tail = this.slice(index);
      if (index < 0) {
        index = this.length() + index;
      }
      middle = substring;
      if (typeof substring === 'string') {
        middle = new HTMLString.String(substring, this._preserveWhitespace);
      }
      if (inheritFormat && index > 0) {
        indexChar = this.charAt(index - 1);
        inheritedTags = indexChar.tags();
        if (indexChar.isTag()) {
          inheritedTags.shift();
        }
        if (typeof substring !== 'string') {
          middle = middle.copy();
        }
        _ref = middle.characters;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          c.addTags.apply(c, inheritedTags);
        }
      }
      newString = head;
      _ref1 = middle.characters;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        c = _ref1[_j];
        newString.characters.push(c);
      }
      _ref2 = tail.characters;
      for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
        c = _ref2[_k];
        newString.characters.push(c);
      }
      return newString;
    };

    String.prototype.lastIndexOf = function (substring, from) {
      var c, characters, found, i, skip, _i, _j, _len, _len1;
      if (from == null) {
        from = 0;
      }
      if (from < 0) {
        from = 0;
      }
      characters = this.characters.slice(from).reverse();
      from = 0;
      if (typeof substring === 'string') {
        if (!this.contains(substring)) {
          return -1;
        }
        substring = substring.split('').reverse();
        while (from <= (characters.length - substring.length)) {
          found = true;
          skip = 0;
          for (i = _i = 0, _len = substring.length; _i < _len; i = ++_i) {
            c = substring[i];
            if (characters[i + from].isTag()) {
              skip += 1;
            }
            if (c !== characters[skip + i + from].c()) {
              found = false;
              break;
            }
          }
          if (found) {
            return from;
          }
          from++;
        }
        return -1;
      }
      substring = substring.characters.slice().reverse();
      while (from <= (characters.length - substring.length)) {
        found = true;
        for (i = _j = 0, _len1 = substring.length; _j < _len1; i = ++_j) {
          c = substring[i];
          if (!c.eq(characters[i + from])) {
            found = false;
            break;
          }
        }
        if (found) {
          return from;
        }
        from++;
      }
      return -1;
    };

    String.prototype.optimize = function () {
      var c, closingTag, closingTags, head, lastC, len, openHeads, openTag, openTags, runLength, runLengthSort, runLengths, run_length, t, tag, _i, _j, _k, _l, _len, _len1, _len2, _len3, _len4, _len5, _len6, _m, _n, _o, _ref, _ref1, _ref2, _ref3, _ref4, _ref5, _results;
      openTags = [];
      openHeads = [];
      lastC = null;
      _ref = this.characters.slice().reverse();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        c._runLengthMap = {};
        c._runLengthMapSize = 0;
        closingTags = [];
        _ref1 = openTags.slice().reverse();
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          openTag = _ref1[_j];
          closingTags.push(openTag);
          if (!c.hasTags(openTag)) {
            for (_k = 0, _len2 = closingTags.length; _k < _len2; _k++) {
              closingTag = closingTags[_k];
              openTags.pop();
              openHeads.pop();
            }
            closingTags = [];
          }
        }
        _ref2 = c._tags;
        for (_l = 0, _len3 = _ref2.length; _l < _len3; _l++) {
          tag = _ref2[_l];
          if (openHeads.indexOf(tag.head()) === -1) {
            if (!tag.selfClosing()) {
              openTags.push(tag);
              openHeads.push(tag.head());
            }
          }
        }
        for (_m = 0, _len4 = openTags.length; _m < _len4; _m++) {
          tag = openTags[_m];
          head = tag.head();
          if (!lastC) {
            c._runLengthMap[head] = [tag, 1];
            continue;
          }
          if (!c._runLengthMap[head]) {
            c._runLengthMap[head] = [tag, 0];
          }
          run_length = 0;
          if (lastC._runLengthMap[head]) {
            run_length = lastC._runLengthMap[head][1];
          }
          c._runLengthMap[head][1] = run_length + 1;
        }
        lastC = c;
      }
      runLengthSort = function (a, b) {
        return b[1] - a[1];
      };
      _ref3 = this.characters;
      _results = [];
      for (_n = 0, _len5 = _ref3.length; _n < _len5; _n++) {
        c = _ref3[_n];
        len = c._tags.length;
        if ((len > 0 && c._tags[0].selfClosing() && len < 3) || len < 2) {
          continue;
        }
        runLengths = [];
        _ref4 = c._runLengthMap;
        for (tag in _ref4) {
          runLength = _ref4[tag];
          runLengths.push(runLength);
        }
        runLengths.sort(runLengthSort);
        _ref5 = c._tags.slice();
        for (_o = 0, _len6 = _ref5.length; _o < _len6; _o++) {
          tag = _ref5[_o];
          if (!tag.selfClosing()) {
            c.removeTags(tag);
          }
        }
        _results.push(c.addTags.apply(c, (function () {
          var _len7, _p, _results1;
          _results1 = [];
          for (_p = 0, _len7 = runLengths.length; _p < _len7; _p++) {
            t = runLengths[_p];
            _results1.push(t[0]);
          }
          return _results1;
        })()));
      }
      return _results;
    };

    String.prototype.slice = function (from, to) {
      var c, newString;
      newString = new HTMLString.String('', this._preserveWhitespace);
      newString.characters = (function () {
        var _i, _len, _ref, _results;
        _ref = this.characters.slice(from, to);
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c.copy());
        }
        return _results;
      }).call(this);
      return newString;
    };

    String.prototype.split = function (separator, limit) {
      var count, i, index, indexes, lastIndex, substrings, _i, _ref;
      if (separator == null) {
        separator = '';
      }
      if (limit == null) {
        limit = 0;
      }
      lastIndex = 0;
      count = 0;
      indexes = [0];
      while (true) {
        if (limit > 0 && count > limit) {
          break;
        }
        index = this.indexOf(separator, lastIndex);
        if (index === -1 || index === (this.length() - 1)) {
          break;
        }
        indexes.push(index);
        lastIndex = index + 1;
      }
      indexes.push(this.length());
      substrings = [];
      for (i = _i = 0, _ref = indexes.length - 2; 0 <= _ref ? _i <= _ref : _i >= _ref; i = 0 <= _ref ? ++_i : --_i) {
        substrings.push(this.slice(indexes[i], indexes[i + 1]));
      }
      return substrings;
    };

    String.prototype.startsWith = function (substring) {
      var c, i, _i, _len, _ref;
      if (typeof substring === 'string') {
        return this.text().slice(0, substring.length) === substring;
      }
      _ref = substring.characters;
      for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
        c = _ref[i];
        if (!c.eq(this.characters[i])) {
          return false;
        }
      }
      return true;
    };

    String.prototype.substr = function (from, length) {
      if (length <= 0) {
        return new HTMLString.String('', this._preserveWhitespace);
      }
      if (from < 0) {
        from = this.length() + from;
      }
      if (length === void 0) {
        length = this.length() - from;
      }
      return this.slice(from, from + length);
    };

    String.prototype.substring = function (from, to) {
      if (to === void 0) {
        to = this.length();
      }
      return this.slice(from, to);
    };

    String.prototype.text = function () {
      var c, text, _i, _len, _ref;
      text = '';
      _ref = this.characters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        if (c.isTag()) {
          if (c.isTag('br')) {
            text += '\n';
          }
          continue;
        }
        if (c.c() === '&nbsp;') {
          text += c.c();
          continue;
        }
        text += c.c();
      }
      return this.constructor.decode(text);
    };

    String.prototype.toLowerCase = function () {
      var c, newString, _i, _len, _ref;
      newString = this.copy();
      _ref = newString.characters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        if (c._c.length === 1) {
          c._c = c._c.toLowerCase();
        }
      }
      return newString;
    };

    String.prototype.toUpperCase = function () {
      var c, newString, _i, _len, _ref;
      newString = this.copy();
      _ref = newString.characters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        c = _ref[_i];
        if (c._c.length === 1) {
          c._c = c._c.toUpperCase();
        }
      }
      return newString;
    };

    String.prototype.trim = function () {
      var c, from, newString, to, _i, _j, _len, _len1, _ref, _ref1;
      _ref = this.characters;
      for (from = _i = 0, _len = _ref.length; _i < _len; from = ++_i) {
        c = _ref[from];
        if (!c.isWhitespace()) {
          break;
        }
      }
      _ref1 = this.characters.slice().reverse();
      for (to = _j = 0, _len1 = _ref1.length; _j < _len1; to = ++_j) {
        c = _ref1[to];
        if (!c.isWhitespace()) {
          break;
        }
      }
      to = this.length() - to - 1;
      newString = new HTMLString.String('', this._preserveWhitespace);
      newString.characters = (function () {
        var _k, _len2, _ref2, _results;
        _ref2 = this.characters.slice(from, +to + 1 || 9e9);
        _results = [];
        for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
          c = _ref2[_k];
          _results.push(c.copy());
        }
        return _results;
      }).call(this);
      return newString;
    };

    String.prototype.trimLeft = function () {
      var c, from, newString, to, _i, _len, _ref;
      to = this.length() - 1;
      _ref = this.characters;
      for (from = _i = 0, _len = _ref.length; _i < _len; from = ++_i) {
        c = _ref[from];
        if (!c.isWhitespace()) {
          break;
        }
      }
      newString = new HTMLString.String('', this._preserveWhitespace);
      newString.characters = (function () {
        var _j, _len1, _ref1, _results;
        _ref1 = this.characters.slice(from, +to + 1 || 9e9);
        _results = [];
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          c = _ref1[_j];
          _results.push(c.copy());
        }
        return _results;
      }).call(this);
      return newString;
    };

    String.prototype.trimRight = function () {
      var c, from, newString, to, _i, _len, _ref;
      from = 0;
      _ref = this.characters.slice().reverse();
      for (to = _i = 0, _len = _ref.length; _i < _len; to = ++_i) {
        c = _ref[to];
        if (!c.isWhitespace()) {
          break;
        }
      }
      to = this.length() - to - 1;
      newString = new HTMLString.String('', this._preserveWhitespace);
      newString.characters = (function () {
        var _j, _len1, _ref1, _results;
        _ref1 = this.characters.slice(from, +to + 1 || 9e9);
        _results = [];
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          c = _ref1[_j];
          _results.push(c.copy());
        }
        return _results;
      }).call(this);
      return newString;
    };

    String.prototype.unformat = function () {
      var c, from, i, newString, tags, to, _i;
      from = arguments[0], to = arguments[1], tags = 3 <= arguments.length ? __slice.call(arguments, 2) : [];
      if (to < 0) {
        to = this.length() + to + 1;
      }
      if (from < 0) {
        from = this.length() + from;
      }
      newString = this.copy();
      for (i = _i = from; from <= to ? _i < to : _i > to; i = from <= to ? ++_i : --_i) {
        c = newString.characters[i];
        c.removeTags.apply(c, tags);
      }
      return newString;
    };

    String.prototype.copy = function () {
      var c, stringCopy;
      stringCopy = new HTMLString.String('', this._preserveWhitespace);
      stringCopy.characters = (function () {
        var _i, _len, _ref, _results;
        _ref = this.characters;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c.copy());
        }
        return _results;
      }).call(this);
      return stringCopy;
    };

    String.encode = function (string) {
      var textarea;
      textarea = document.createElement('textarea');
      textarea.textContent = string;
      return textarea.innerHTML;
    };

    String.decode = function (string) {
      var textarea;
      textarea = document.createElement('textarea');
      textarea.innerHTML = string;
      return textarea.textContent;
    };

    return String;

  })();

  ALPHA_CHARS = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz-_$'.split('');

  ALPHA_NUMERIC_CHARS = ALPHA_CHARS.concat('1234567890'.split(''));

  ATTR_NAME_CHARS = ALPHA_NUMERIC_CHARS.concat([':']);

  ENTITY_CHARS = ALPHA_NUMERIC_CHARS.concat(['#']);

  TAG_NAME_CHARS = ALPHA_NUMERIC_CHARS.concat([':']);

  CHAR_OR_ENTITY_OR_TAG = 1;

  ENTITY = 2;

  OPENNING_OR_CLOSING_TAG = 3;

  OPENING_TAG = 4;

  CLOSING_TAG = 5;

  TAG_NAME_OPENING = 6;

  TAG_NAME_CLOSING = 7;

  TAG_OPENING_SELF_CLOSING = 8;

  TAG_NAME_MUST_CLOSE = 9;

  ATTR_OR_TAG_END = 10;

  ATTR_NAME = 11;

  ATTR_NAME_FIND_VALUE = 12;

  ATTR_DELIM = 13;

  ATTR_VALUE_SINGLE_DELIM = 14;

  ATTR_VALUE_DOUBLE_DELIM = 15;

  ATTR_VALUE_NO_DELIM = 16;

  ATTR_ENTITY_NO_DELIM = 17;

  ATTR_ENTITY_SINGLE_DELIM = 18;

  ATTR_ENTITY_DOUBLE_DELIM = 19;

  _Parser = (function () {
    function _Parser() {
      this.fsm = new FSM.Machine(this);
      this.fsm.setInitialState(CHAR_OR_ENTITY_OR_TAG);
      this.fsm.addTransitionAny(CHAR_OR_ENTITY_OR_TAG, null, function (c) {
        return this._pushChar(c);
      });
      this.fsm.addTransition('<', CHAR_OR_ENTITY_OR_TAG, OPENNING_OR_CLOSING_TAG);
      this.fsm.addTransition('&', CHAR_OR_ENTITY_OR_TAG, ENTITY);
      this.fsm.addTransitions(ENTITY_CHARS, ENTITY, null, function (c) {
        return this.entity += c;
      });
      this.fsm.addTransition(';', ENTITY, CHAR_OR_ENTITY_OR_TAG, function () {
        this._pushChar("&" + this.entity + ";");
        return this.entity = '';
      });
      this.fsm.addTransitions([' ', '\n'], OPENNING_OR_CLOSING_TAG);
      this.fsm.addTransitions(ALPHA_CHARS, OPENNING_OR_CLOSING_TAG, OPENING_TAG, function () {
        return this._back();
      });
      this.fsm.addTransition('/', OPENNING_OR_CLOSING_TAG, CLOSING_TAG);
      this.fsm.addTransitions([' ', '\n'], OPENING_TAG);
      this.fsm.addTransitions(ALPHA_CHARS, OPENING_TAG, TAG_NAME_OPENING, function () {
        return this._back();
      });
      this.fsm.addTransitions([' ', '\n'], CLOSING_TAG);
      this.fsm.addTransitions(ALPHA_CHARS, CLOSING_TAG, TAG_NAME_CLOSING, function () {
        return this._back();
      });
      this.fsm.addTransitions(TAG_NAME_CHARS, TAG_NAME_OPENING, null, function (c) {
        return this.tagName += c;
      });
      this.fsm.addTransitions([' ', '\n'], TAG_NAME_OPENING, ATTR_OR_TAG_END);
      this.fsm.addTransition('/', TAG_NAME_OPENING, TAG_OPENING_SELF_CLOSING, function () {
        return this.selfClosing = true;
      });
      this.fsm.addTransition('>', TAG_NAME_OPENING, CHAR_OR_ENTITY_OR_TAG, function () {
        return this._pushTag();
      });
      this.fsm.addTransitions([' ', '\n'], TAG_OPENING_SELF_CLOSING);
      this.fsm.addTransition('>', TAG_OPENING_SELF_CLOSING, CHAR_OR_ENTITY_OR_TAG, function () {
        return this._pushTag();
      });
      this.fsm.addTransitions([' ', '\n'], ATTR_OR_TAG_END);
      this.fsm.addTransition('/', ATTR_OR_TAG_END, TAG_OPENING_SELF_CLOSING, function () {
        return this.selfClosing = true;
      });
      this.fsm.addTransition('>', ATTR_OR_TAG_END, CHAR_OR_ENTITY_OR_TAG, function () {
        return this._pushTag();
      });
      this.fsm.addTransitions(ALPHA_CHARS, ATTR_OR_TAG_END, ATTR_NAME, function () {
        return this._back();
      });
      this.fsm.addTransitions(TAG_NAME_CHARS, TAG_NAME_CLOSING, null, function (c) {
        return this.tagName += c;
      });
      this.fsm.addTransitions([' ', '\n'], TAG_NAME_CLOSING, TAG_NAME_MUST_CLOSE);
      this.fsm.addTransition('>', TAG_NAME_CLOSING, CHAR_OR_ENTITY_OR_TAG, function () {
        return this._popTag();
      });
      this.fsm.addTransitions([' ', '\n'], TAG_NAME_MUST_CLOSE);
      this.fsm.addTransition('>', TAG_NAME_MUST_CLOSE, CHAR_OR_ENTITY_OR_TAG, function () {
        return this._popTag();
      });
      this.fsm.addTransitions(ATTR_NAME_CHARS, ATTR_NAME, null, function (c) {
        return this.attributeName += c;
      });
      this.fsm.addTransitions([' ', '\n'], ATTR_NAME, ATTR_NAME_FIND_VALUE);
      this.fsm.addTransition('=', ATTR_NAME, ATTR_DELIM);
      this.fsm.addTransitions([' ', '\n'], ATTR_NAME_FIND_VALUE);
      this.fsm.addTransition('=', ATTR_NAME_FIND_VALUE, ATTR_DELIM);
      this.fsm.addTransitions('>', ATTR_NAME, ATTR_OR_TAG_END, function () {
        this._pushAttribute();
        return this._back();
      });
      this.fsm.addTransitionAny(ATTR_NAME_FIND_VALUE, ATTR_OR_TAG_END, function () {
        this._pushAttribute();
        return this._back();
      });
      this.fsm.addTransitions([' ', '\n'], ATTR_DELIM);
      this.fsm.addTransition('\'', ATTR_DELIM, ATTR_VALUE_SINGLE_DELIM);
      this.fsm.addTransition('"', ATTR_DELIM, ATTR_VALUE_DOUBLE_DELIM);
      this.fsm.addTransitions(ALPHA_NUMERIC_CHARS.concat(['&'], ATTR_DELIM, ATTR_VALUE_NO_DELIM, function () {
        return this._back();
      }));
      this.fsm.addTransition(' ', ATTR_VALUE_NO_DELIM, ATTR_OR_TAG_END, function () {
        return this._pushAttribute();
      });
      this.fsm.addTransitions(['/', '>'], ATTR_VALUE_NO_DELIM, ATTR_OR_TAG_END, function () {
        this._back();
        return this._pushAttribute();
      });
      this.fsm.addTransition('&', ATTR_VALUE_NO_DELIM, ATTR_ENTITY_NO_DELIM);
      this.fsm.addTransitionAny(ATTR_VALUE_NO_DELIM, null, function (c) {
        return this.attributeValue += c;
      });
      this.fsm.addTransition('\'', ATTR_VALUE_SINGLE_DELIM, ATTR_OR_TAG_END, function () {
        return this._pushAttribute();
      });
      this.fsm.addTransition('&', ATTR_VALUE_SINGLE_DELIM, ATTR_ENTITY_SINGLE_DELIM);
      this.fsm.addTransitionAny(ATTR_VALUE_SINGLE_DELIM, null, function (c) {
        return this.attributeValue += c;
      });
      this.fsm.addTransition('"', ATTR_VALUE_DOUBLE_DELIM, ATTR_OR_TAG_END, function () {
        return this._pushAttribute();
      });
      this.fsm.addTransition('&', ATTR_VALUE_DOUBLE_DELIM, ATTR_ENTITY_DOUBLE_DELIM);
      this.fsm.addTransitionAny(ATTR_VALUE_DOUBLE_DELIM, null, function (c) {
        return this.attributeValue += c;
      });
      this.fsm.addTransitions(ENTITY_CHARS, ATTR_ENTITY_NO_DELIM, null, function (c) {
        return this.entity += c;
      });
      this.fsm.addTransitions(ENTITY_CHARS, ATTR_ENTITY_SINGLE_DELIM, function (c) {
        return this.entity += c;
      });
      this.fsm.addTransitions(ENTITY_CHARS, ATTR_ENTITY_DOUBLE_DELIM, null, function (c) {
        return this.entity += c;
      });
      this.fsm.addTransition(';', ATTR_ENTITY_NO_DELIM, ATTR_VALUE_NO_DELIM, function () {
        this.attributeValue += "&" + this.entity + ";";
        return this.entity = '';
      });
      this.fsm.addTransition(';', ATTR_ENTITY_SINGLE_DELIM, ATTR_VALUE_SINGLE_DELIM, function () {
        this.attributeValue += "&" + this.entity + ";";
        return this.entity = '';
      });
      this.fsm.addTransition(';', ATTR_ENTITY_DOUBLE_DELIM, ATTR_VALUE_DOUBLE_DELIM, function () {
        this.attributeValue += "&" + this.entity + ";";
        return this.entity = '';
      });
    }

    _Parser.prototype._back = function () {
      return this.head--;
    };

    _Parser.prototype._pushAttribute = function () {
      this.attributes[this.attributeName] = this.attributeValue;
      this.attributeName = '';
      return this.attributeValue = '';
    };

    _Parser.prototype._pushChar = function (c) {
      var character, lastCharacter;
      character = new HTMLString.Character(c, this.tags);
      if (this._preserveWhitespace) {
        this.string.characters.push(character);
        return;
      }
      if (this.string.length() && !character.isTag() && !character.isEntity() && character.isWhitespace()) {
        lastCharacter = this.string.characters[this.string.length() - 1];
        if (lastCharacter.isWhitespace() && !lastCharacter.isTag() && !lastCharacter.isEntity()) {
          return;
        }
      }
      return this.string.characters.push(character);
    };

    _Parser.prototype._pushTag = function () {
      var tag, _ref;
      tag = new HTMLString.Tag(this.tagName, this.attributes);
      this.tags.push(tag);
      if (tag.selfClosing()) {
        this._pushChar('');
        this.tags.pop();
        if (!this.selfClosed && (_ref = this.tagName, __indexOf.call(HTMLString.Tag.SELF_CLOSING, _ref) >= 0)) {
          this.fsm.reset();
        }
      }
      this.tagName = '';
      this.selfClosed = false;
      return this.attributes = {};
    };

    _Parser.prototype._popTag = function () {
      var character, tag;
      while (true) {
        tag = this.tags.pop();
        if (this.string.length()) {
          character = this.string.characters[this.string.length() - 1];
          if (!character.isTag() && !character.isEntity() && character.isWhitespace()) {
            character.removeTags(tag);
          }
        }
        if (tag.name() === this.tagName.toLowerCase()) {
          break;
        }
      }
      return this.tagName = '';
    };

    _Parser.prototype.parse = function (html, preserveWhitespace) {
      var character, error;
      this._preserveWhitespace = preserveWhitespace;
      this.reset();
      html = this.preprocess(html);
      this.fsm.parser = this;
      while (this.head < html.length) {
        character = html[this.head];
        try {
          this.fsm.process(character);
        } catch (_error) {
          error = _error;
          throw new Error("Error at char " + this.head + " >> " + error);
        }
        this.head++;
      }
      return this.string;
    };

    _Parser.prototype.preprocess = function (html) {
      html = html.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
      html = html.replace(/<!--[\s\S]*?-->/g, '');
      if (!this._preserveWhitespace) {
        html = html.replace(/\s+/g, ' ');
      }
      return html;
    };

    _Parser.prototype.reset = function () {
      this.fsm.reset();
      this.head = 0;
      this.string = new HTMLString.String();
      this.entity = '';
      this.tags = [];
      this.tagName = '';
      this.selfClosing = false;
      this.attributes = {};
      this.attributeName = '';
      return this.attributeValue = '';
    };

    return _Parser;

  })();

  HTMLString.Tag = (function () {
    function Tag(name, attributes) {
      var k, v;
      this._name = name.toLowerCase();
      this._selfClosing = HTMLString.Tag.SELF_CLOSING[this._name] === true;
      this._head = null;
      this._attributes = {};
      for (k in attributes) {
        v = attributes[k];
        this._attributes[k] = v;
      }
    }

    Tag.SELF_CLOSING = {
      'area': true,
      'base': true,
      'br': true,
      'hr': true,
      'img': true,
      'input': true,
      'link meta': true,
      'wbr': true
    };

    Tag.prototype.head = function () {
      var components, k, v, _ref;
      if (!this._head) {
        components = [];
        _ref = this._attributes;
        for (k in _ref) {
          v = _ref[k];
          if (v) {
            components.push("" + k + "=\"" + v + "\"");
          } else {
            components.push("" + k);
          }
        }
        components.sort();
        components.unshift(this._name);
        this._head = "<" + (components.join(' ')) + ">";
      }
      return this._head;
    };

    Tag.prototype.name = function () {
      return this._name;
    };

    Tag.prototype.selfClosing = function () {
      return this._selfClosing;
    };

    Tag.prototype.tail = function () {
      if (this._selfClosing) {
        return '';
      }
      return "</" + this._name + ">";
    };

    Tag.prototype.attr = function (name, value) {
      if (value === void 0) {
        return this._attributes[name];
      }
      this._attributes[name] = value;
      return this._head = null;
    };

    Tag.prototype.removeAttr = function (name) {
      if (this._attributes[name] === void 0) {
        return;
      }
      return delete this._attributes[name];
    };

    Tag.prototype.copy = function () {
      return new HTMLString.Tag(this._name, this._attributes);
    };

    return Tag;

  })();

  HTMLString.Character = (function () {
    function Character(c, tags) {
      this._c = c;
      if (c.length > 1) {
        this._c = c.toLowerCase();
      }
      this._tags = [];
      this.addTags.apply(this, tags);
    }

    Character.prototype.c = function () {
      return this._c;
    };

    Character.prototype.isEntity = function () {
      return this._c.length > 1;
    };

    Character.prototype.isTag = function (tagName) {
      if (this._tags.length === 0 || !this._tags[0].selfClosing()) {
        return false;
      }
      if (tagName && this._tags[0].name() !== tagName) {
        return false;
      }
      return true;
    };

    Character.prototype.isWhitespace = function () {
      var _ref;
      return ((_ref = this._c) === ' ' || _ref === '\n' || _ref === '&nbsp;') || this.isTag('br');
    };

    Character.prototype.tags = function () {
      var t;
      return (function () {
        var _i, _len, _ref, _results;
        _ref = this._tags;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          t = _ref[_i];
          _results.push(t.copy());
        }
        return _results;
      }).call(this);
    };

    Character.prototype.addTags = function () {
      var tag, tags, _i, _len, _results;
      tags = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      _results = [];
      for (_i = 0, _len = tags.length; _i < _len; _i++) {
        tag = tags[_i];
        if (tag.selfClosing()) {
          if (!this.isTag()) {
            this._tags.unshift(tag.copy());
          }
          continue;
        }
        _results.push(this._tags.push(tag.copy()));
      }
      return _results;
    };

    Character.prototype.eq = function (c) {
      var tag, tags, _i, _j, _len, _len1, _ref, _ref1;
      if (this.c() !== c.c()) {
        return false;
      }
      if (this._tags.length !== c._tags.length) {
        return false;
      }
      tags = {};
      _ref = this._tags;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        tag = _ref[_i];
        tags[tag.head()] = true;
      }
      _ref1 = c._tags;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        tag = _ref1[_j];
        if (!tags[tag.head()]) {
          return false;
        }
      }
      return true;
    };

    Character.prototype.hasTags = function () {
      var tag, tagHeads, tagNames, tags, _i, _j, _len, _len1, _ref;
      tags = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      tagNames = {};
      tagHeads = {};
      _ref = this._tags;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        tag = _ref[_i];
        tagNames[tag.name()] = true;
        tagHeads[tag.head()] = true;
      }
      for (_j = 0, _len1 = tags.length; _j < _len1; _j++) {
        tag = tags[_j];
        if (typeof tag === 'string') {
          if (tagNames[tag] === void 0) {
            return false;
          }
        } else {
          if (tagHeads[tag.head()] === void 0) {
            return false;
          }
        }
      }
      return true;
    };

    Character.prototype.removeTags = function () {
      var heads, names, newTags, tag, tags, _i, _len;
      tags = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      if (tags.length === 0) {
        this._tags = [];
        return;
      }
      names = {};
      heads = {};
      for (_i = 0, _len = tags.length; _i < _len; _i++) {
        tag = tags[_i];
        if (typeof tag === 'string') {
          names[tag] = tag;
        } else {
          heads[tag.head()] = tag;
        }
      }
      newTags = [];
      return this._tags = this._tags.filter(function (tag) {
        if (!heads[tag.head()] && !names[tag.name()]) {
          return tag;
        }
      });
    };

    Character.prototype.copy = function () {
      var t;
      return new HTMLString.Character(this._c, (function () {
        var _i, _len, _ref, _results;
        _ref = this._tags;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          t = _ref[_i];
          _results.push(t.copy());
        }
        return _results;
      }).call(this));
    };

    return Character;

  })();

}).call(this);
(function () {
  var SELF_CLOSING_NODE_NAMES, _containedBy, _getChildNodeAndOffset, _getNodeRange, _getOffsetOfChildNode,
          __indexOf = [].indexOf || function (item) {
    for (var i = 0, l = this.length; i < l; i++) {
      if (i in this && this[i] === item)
        return i;
    }
    return -1;
  };

  window.ContentSelect = {};

  ContentSelect.Range = (function () {
    function Range(from, to) {
      this.set(from, to);
    }

    Range.prototype.isCollapsed = function () {
      return this._from === this._to;
    };

    Range.prototype.span = function () {
      return this._to - this._from;
    };

    Range.prototype.collapse = function () {
      return this._to = this._from;
    };

    Range.prototype.eq = function (range) {
      return this.get()[0] === range.get()[0] && this.get()[1] === range.get()[1];
    };

    Range.prototype.get = function () {
      return [this._from, this._to];
    };

    Range.prototype.select = function (element) {
      var docRange, endNode, endNodeLen, endOffset, startNode, startNodeLen, startOffset, _ref, _ref1;
      ContentSelect.Range.unselectAll();
      docRange = document.createRange();
      _ref = _getChildNodeAndOffset(element, this._from), startNode = _ref[0], startOffset = _ref[1];
      _ref1 = _getChildNodeAndOffset(element, this._to), endNode = _ref1[0], endOffset = _ref1[1];
      startNodeLen = startNode.length || 0;
      endNodeLen = endNode.length || 0;
      docRange.setStart(startNode, Math.min(startOffset, startNodeLen));
      docRange.setEnd(endNode, Math.min(endOffset, endNodeLen));
      return window.getSelection().addRange(docRange);
    };

    Range.prototype.set = function (from, to) {
      from = Math.max(0, from);
      to = Math.max(0, to);
      this._from = Math.min(from, to);
      return this._to = Math.max(from, to);
    };

    Range.prepareElement = function (element) {
      var i, node, selfClosingNodes, _i, _len, _results;
      selfClosingNodes = element.querySelectorAll(SELF_CLOSING_NODE_NAMES.join(', '));
      _results = [];
      for (i = _i = 0, _len = selfClosingNodes.length; _i < _len; i = ++_i) {
        node = selfClosingNodes[i];
        node.parentNode.insertBefore(document.createTextNode(''), node);
        if (i < selfClosingNodes.length - 1) {
          _results.push(node.parentNode.insertBefore(document.createTextNode(''), node.nextSibling));
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    Range.query = function (element) {
      var docRange, endNode, endOffset, range, startNode, startOffset, _ref;
      range = new ContentSelect.Range(0, 0);
      try {
        docRange = window.getSelection().getRangeAt(0);
      } catch (_error) {
        return range;
      }
      if (element.firstChild === null && element.lastChild === null) {
        return range;
      }
      if (!_containedBy(docRange.startContainer, element)) {
        return range;
      }
      if (!_containedBy(docRange.endContainer, element)) {
        return range;
      }
      _ref = _getNodeRange(element, docRange), startNode = _ref[0], startOffset = _ref[1], endNode = _ref[2], endOffset = _ref[3];
      range.set(_getOffsetOfChildNode(element, startNode) + startOffset, _getOffsetOfChildNode(element, endNode) + endOffset);
      return range;
    };

    Range.rect = function () {
      var docRange, marker, rect;
      try {
        docRange = window.getSelection().getRangeAt(0);
      } catch (_error) {
        return null;
      }
      if (docRange.collapsed) {
        marker = document.createElement('span');
        docRange.insertNode(marker);
        rect = marker.getBoundingClientRect();
        marker.parentNode.removeChild(marker);
        return rect;
      } else {
        return docRange.getBoundingClientRect();
      }
    };

    Range.unselectAll = function () {
      if (window.getSelection()) {
        return window.getSelection().removeAllRanges();
      }
    };

    return Range;

  })();

  SELF_CLOSING_NODE_NAMES = ['br', 'img', 'input'];

  _containedBy = function (nodeA, nodeB) {
    while (nodeA) {
      if (nodeA === nodeB) {
        return true;
      }
      nodeA = nodeA.parentNode;
    }
    return false;
  };

  _getChildNodeAndOffset = function (parentNode, parentOffset) {
    var childNode, childOffset, childStack, n, _ref;
    if (parentNode.childNodes.length === 0) {
      return [parentNode, parentOffset];
    }
    childNode = null;
    childOffset = parentOffset;
    childStack = (function () {
      var _i, _len, _ref, _results;
      _ref = parentNode.childNodes;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        n = _ref[_i];
        _results.push(n);
      }
      return _results;
    })();
    while (childStack.length > 0) {
      childNode = childStack.shift();
      switch (childNode.nodeType) {
        case Node.TEXT_NODE:
          if (childNode.textContent.length >= childOffset) {
            return [childNode, childOffset];
          }
          childOffset -= childNode.textContent.length;
          break;
        case Node.ELEMENT_NODE:
          if (_ref = childNode.nodeName.toLowerCase(), __indexOf.call(SELF_CLOSING_NODE_NAMES, _ref) >= 0) {
            if (childOffset === 0) {
              return [childNode, 0];
            } else {
              childOffset = Math.max(0, childOffset - 1);
            }
          } else {
            if (childNode.childNodes) {
              Array.prototype.unshift.apply(childStack, (function () {
                var _i, _len, _ref1, _results;
                _ref1 = childNode.childNodes;
                _results = [];
                for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
                  n = _ref1[_i];
                  _results.push(n);
                }
                return _results;
              })());
            }
          }
      }
    }
    return [childNode, childOffset];
  };

  _getOffsetOfChildNode = function (parentNode, childNode) {
    var childStack, n, offset, otherChildNode, _ref, _ref1;
    if (parentNode.childNodes.length === 0) {
      return 0;
    }
    offset = 0;
    childStack = (function () {
      var _i, _len, _ref, _results;
      _ref = parentNode.childNodes;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        n = _ref[_i];
        _results.push(n);
      }
      return _results;
    })();
    while (childStack.length > 0) {
      otherChildNode = childStack.shift();
      if (otherChildNode === childNode) {
        if (_ref = otherChildNode.nodeName.toLowerCase(), __indexOf.call(SELF_CLOSING_NODE_NAMES, _ref) >= 0) {
          return offset + 1;
        }
        return offset;
      }
      switch (otherChildNode.nodeType) {
        case Node.TEXT_NODE:
          offset += otherChildNode.textContent.length;
          break;
        case Node.ELEMENT_NODE:
          if (_ref1 = otherChildNode.nodeName.toLowerCase(), __indexOf.call(SELF_CLOSING_NODE_NAMES, _ref1) >= 0) {
            offset += 1;
          } else {
            if (otherChildNode.childNodes) {
              Array.prototype.unshift.apply(childStack, (function () {
                var _i, _len, _ref2, _results;
                _ref2 = otherChildNode.childNodes;
                _results = [];
                for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
                  n = _ref2[_i];
                  _results.push(n);
                }
                return _results;
              })());
            }
          }
      }
    }
    return offset;
  };

  _getNodeRange = function (element, docRange) {
    var childNode, childNodes, endNode, endOffset, endRange, i, startNode, startOffset, startRange, _i, _j, _len, _len1, _ref;
    childNodes = element.childNodes;
    startRange = docRange.cloneRange();
    startRange.collapse(true);
    endRange = docRange.cloneRange();
    endRange.collapse(false);
    startNode = startRange.startContainer;
    startOffset = startRange.startOffset;
    endNode = endRange.endContainer;
    endOffset = endRange.endOffset;
    if (!startRange.comparePoint) {
      return [startNode, startOffset, endNode, endOffset];
    }
    if (startNode === element) {
      startNode = childNodes[childNodes.length - 1];
      startOffset = startNode.textContent.length;
      for (i = _i = 0, _len = childNodes.length; _i < _len; i = ++_i) {
        childNode = childNodes[i];
        if (startRange.comparePoint(childNode, 0) !== 1) {
          continue;
        }
        if (i === 0) {
          startNode = childNode;
          startOffset = 0;
        } else {
          startNode = childNodes[i - 1];
          startOffset = childNode.textContent.length;
        }
        if (_ref = startNode.nodeName.toLowerCase, __indexOf.call(SELF_CLOSING_NODE_NAMES, _ref) >= 0) {
          startOffset = 1;
        }
        break;
      }
    }
    if (docRange.collapsed) {
      return [startNode, startOffset, startNode, startOffset];
    }
    if (endNode === element) {
      endNode = childNodes[childNodes.length - 1];
      endOffset = endNode.textContent.length;
      for (i = _j = 0, _len1 = childNodes.length; _j < _len1; i = ++_j) {
        childNode = childNodes[i];
        if (endRange.comparePoint(childNode, 0) !== 1) {
          continue;
        }
        if (i === 0) {
          endNode = childNode;
        } else {
          endNode = childNodes[i - 1];
        }
        endOffset = childNode.textContent.length + 1;
      }
    }
    return [startNode, startOffset, endNode, endOffset];
  };

}).call(this);

(function () {
  var _Root, _TagNames, _mergers,
          __slice = [].slice,
          __indexOf = [].indexOf || function (item) {
    for (var i = 0, l = this.length; i < l; i++) {
      if (i in this && this[i] === item)
        return i;
    }
    return -1;
  },
          __hasProp = {}.hasOwnProperty,
          __extends = function (child, parent) {
            for (var key in parent) {
              if (__hasProp.call(parent, key))
                child[key] = parent[key];
            }
            function ctor() {
              this.constructor = child;
            }
            ctor.prototype = parent.prototype;
            child.prototype = new ctor();
            child.__super__ = parent.prototype;
            return child;
          },
          __bind = function (fn, me) {
            return function () {
              return fn.apply(me, arguments);
            };
          };

  window.ContentEdit = {
    DEFAULT_MAX_ELEMENT_WIDTH: 800,
    DEFAULT_MIN_ELEMENT_WIDTH: 80,
    DRAG_HOLD_DURATION: 500,
    DROP_EDGE_SIZE: 50,
    HELPER_CHAR_LIMIT: 250,
    INDENT: '    ',
    LANGUAGE: 'en',
    RESIZE_CORNER_SIZE: 15,
    _translations: {},
    _: function (s) {
      var lang;
      lang = ContentEdit.LANGUAGE;
      if (ContentEdit._translations[lang] && ContentEdit._translations[lang][s]) {
        return ContentEdit._translations[lang][s];
      }
      return s;
    },
    addTranslations: function (language, translations) {
      return ContentEdit._translations[language] = translations;
    },
    addCSSClass: function (domElement, className) {
      var c, classAttr, classNames;
      if (domElement.classList) {
        domElement.classList.add(className);
        return;
      }
      classAttr = domElement.getAttribute('class');
      if (classAttr) {
        classNames = (function () {
          var _i, _len, _ref, _results;
          _ref = classAttr.split(' ');
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            _results.push(c);
          }
          return _results;
        })();
        if (classNames.indexOf(className) === -1) {
          return domElement.setAttribute('class', "" + classAttr + " " + className);
        }
      } else {
        return domElement.setAttribute('class', className);
      }
    },
    attributesToString: function (attributes) {
      var attributeStrings, name, names, value, _i, _len;
      if (!attributes) {
        return '';
      }
      names = (function () {
        var _results;
        _results = [];
        for (name in attributes) {
          _results.push(name);
        }
        return _results;
      })();
      names.sort();
      attributeStrings = [];
      for (_i = 0, _len = names.length; _i < _len; _i++) {
        name = names[_i];
        value = attributes[name];
        if (value === '') {
          attributeStrings.push(name);
        } else {
          value = HTMLString.String.encode(value);
          value = value.replace(/"/g, '&quot;');
          attributeStrings.push("" + name + "=\"" + value + "\"");
        }
      }
      return attributeStrings.join(' ');
    },
    removeCSSClass: function (domElement, className) {
      var c, classAttr, classNameIndex, classNames;
      if (domElement.classList) {
        domElement.classList.remove(className);
        if (domElement.classList.length === 0) {
          domElement.removeAttribute('class');
        }
        return;
      }
      classAttr = domElement.getAttribute('class');
      if (classAttr) {
        classNames = (function () {
          var _i, _len, _ref, _results;
          _ref = classAttr.split(' ');
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            _results.push(c);
          }
          return _results;
        })();
        classNameIndex = classNames.indexOf(className);
        if (classNameIndex > -1) {
          classNames.splice(classNameIndex, 1);
          if (classNames.length) {
            return domElement.setAttribute('class', classNames.join(' '));
          } else {
            return domElement.removeAttribute('class');
          }
        }
      }
    }
  };

  _TagNames = (function () {
    function _TagNames() {
      this._tagNames = {};
    }

    _TagNames.prototype.register = function () {
      var cls, tagName, tagNames, _i, _len, _results;
      cls = arguments[0], tagNames = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
      _results = [];
      for (_i = 0, _len = tagNames.length; _i < _len; _i++) {
        tagName = tagNames[_i];
        _results.push(this._tagNames[tagName.toLowerCase()] = cls);
      }
      return _results;
    };

    _TagNames.prototype.match = function (tagName) {
      if (this._tagNames[tagName.toLowerCase()]) {
        return this._tagNames[tagName.toLowerCase()];
      }
      return ContentEdit.Static;
    };

    return _TagNames;

  })();

  ContentEdit.TagNames = (function () {
    var instance;

    function TagNames() {}

    instance = null;

    TagNames.get = function () {
      return instance != null ? instance : instance = new _TagNames();
    };

    return TagNames;

  })();

  ContentEdit.Node = (function () {
    function Node() {
      this._bindings = {};
      this._parent = null;
      this._modified = null;
    }

    Node.prototype.lastModified = function () {
      return this._modified;
    };

    Node.prototype.parent = function () {
      return this._parent;
    };

    Node.prototype.parents = function () {
      var parent, parents;
      parents = [];
      parent = this._parent;
      while (parent) {
        parents.push(parent);
        parent = parent._parent;
      }
      return parents;
    };

    Node.prototype.type = function () {
      return 'Node';
    };

    Node.prototype.html = function (indent) {
      if (indent == null) {
        indent = '';
      }
      throw new Error('`html` not implemented');
    };

    Node.prototype.bind = function (eventName, callback) {
      if (this._bindings[eventName] === void 0) {
        this._bindings[eventName] = [];
      }
      this._bindings[eventName].push(callback);
      return callback;
    };

    Node.prototype.trigger = function () {
      var args, callback, eventName, _i, _len, _ref, _results;
      eventName = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
      if (!this._bindings[eventName]) {
        return;
      }
      _ref = this._bindings[eventName];
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        callback = _ref[_i];
        if (!callback) {
          continue;
        }
        _results.push(callback.call.apply(callback, [this].concat(__slice.call(args))));
      }
      return _results;
    };

    Node.prototype.unbind = function (eventName, callback) {
      var i, suspect, _i, _len, _ref, _results;
      if (!eventName) {
        this._bindings = {};
        return;
      }
      if (!callback) {
        this._bindings[eventName] = void 0;
        return;
      }
      if (!this._bindings[eventName]) {
        return;
      }
      _ref = this._bindings[eventName];
      _results = [];
      for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
        suspect = _ref[i];
        if (suspect === callback) {
          _results.push(this._bindings[eventName].splice(i, 1));
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    Node.prototype.commit = function () {
      this._modified = null;
      return ContentEdit.Root.get().trigger('commit', this);
    };

    Node.prototype.taint = function () {
      var now, parent, root, _i, _len, _ref;
      now = Date.now();
      this._modified = now;
      _ref = this.parents();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        parent = _ref[_i];
        parent._modified = now;
      }
      root = ContentEdit.Root.get();
      root._modified = now;
      return root.trigger('taint', this);
    };

    Node.prototype.closest = function (testFunc) {
      var parent;
      parent = this.parent();
      while (parent && !testFunc(parent)) {
        if (parent.parent) {
          parent = parent.parent();
        } else {
          parent = null;
        }
      }
      return parent;
    };

    Node.prototype.next = function () {
      var children, index, node, _i, _len, _ref;
      if (this.children && this.children.length > 0) {
        return this.children[0];
      }
      _ref = [this].concat(this.parents());
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        node = _ref[_i];
        if (!node.parent()) {
          return null;
        }
        children = node.parent().children;
        index = children.indexOf(node);
        if (index < children.length - 1) {
          return children[index + 1];
        }
      }
    };

    Node.prototype.nextContent = function () {
      return this.nextWithTest(function (node) {
        return node.content !== void 0;
      });
    };

    Node.prototype.nextSibling = function () {
      var index;
      index = this.parent().children.indexOf(this);
      if (index === this.parent().children.length - 1) {
        return null;
      }
      return this.parent().children[index + 1];
    };

    Node.prototype.nextWithTest = function (testFunc) {
      var node;
      node = this;
      while (node) {
        node = node.next();
        if (node && testFunc(node)) {
          return node;
        }
      }
    };

    Node.prototype.previous = function () {
      var children, node;
      if (!this.parent()) {
        return null;
      }
      children = this.parent().children;
      if (children[0] === this) {
        return this.parent();
      }
      node = children[children.indexOf(this) - 1];
      while (node.children && node.children.length) {
        node = node.children[node.children.length - 1];
      }
      return node;
    };

    Node.prototype.previousContent = function () {
      var node;
      return node = this.previousWithTest(function (node) {
        return node.content !== void 0;
      });
    };

    Node.prototype.previousSibling = function () {
      var index;
      index = this.parent().children.indexOf(this);
      if (index === 0) {
        return null;
      }
      return this.parent().children[index - 1];
    };

    Node.prototype.previousWithTest = function (testFunc) {
      var node;
      node = this;
      while (node) {
        node = node.previous();
        if (node && testFunc(node)) {
          return node;
        }
      }
    };

    Node.extend = function (cls) {
      var key, value, _ref;
      _ref = cls.prototype;
      for (key in _ref) {
        value = _ref[key];
        if (key === 'constructor') {
          continue;
        }
        this.prototype[key] = value;
      }
      for (key in cls) {
        value = cls[key];
        if (__indexOf.call('__super__', key) >= 0) {
          continue;
        }
        this.prototype[key] = value;
      }
      return this;
    };

    Node.fromDOMElement = function (domElement) {
      throw new Error('`fromDOMElement` not implemented');
    };

    return Node;

  })();

  ContentEdit.NodeCollection = (function (_super) {
    __extends(NodeCollection, _super);

    function NodeCollection() {
      NodeCollection.__super__.constructor.call(this);
      this.children = [];
    }

    NodeCollection.prototype.descendants = function () {
      var descendants, node, nodeStack;
      descendants = [];
      nodeStack = this.children.slice();
      while (nodeStack.length > 0) {
        node = nodeStack.shift();
        descendants.push(node);
        if (node.children && node.children.length > 0) {
          nodeStack = node.children.slice().concat(nodeStack);
        }
      }
      return descendants;
    };

    NodeCollection.prototype.isMounted = function () {
      return false;
    };

    NodeCollection.prototype.type = function () {
      return 'NodeCollection';
    };

    NodeCollection.prototype.attach = function (node, index) {
      if (node.parent()) {
        node.parent().detach(node);
      }
      node._parent = this;
      if (index !== void 0) {
        this.children.splice(index, 0, node);
      } else {
        this.children.push(node);
      }
      if (node.mount && this.isMounted()) {
        node.mount();
      }
      this.taint();
      return ContentEdit.Root.get().trigger('attach', this, node);
    };

    NodeCollection.prototype.commit = function () {
      var descendant, _i, _len, _ref;
      _ref = this.descendants();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        descendant = _ref[_i];
        descendant._modified = null;
      }
      this._modified = null;
      return ContentEdit.Root.get().trigger('commit', this);
    };

    NodeCollection.prototype.detach = function (node) {
      var nodeIndex;
      nodeIndex = this.children.indexOf(node);
      if (nodeIndex === -1) {
        return;
      }
      if (node.unmount && this.isMounted() && node.isMounted()) {
        node.unmount();
      }
      this.children.splice(nodeIndex, 1);
      node._parent = null;
      this.taint();
      return ContentEdit.Root.get().trigger('detach', this, node);
    };

    return NodeCollection;

  })(ContentEdit.Node);

  ContentEdit.Element = (function (_super) {
    __extends(Element, _super);

    function Element(tagName, attributes) {
      Element.__super__.constructor.call(this);
      this._tagName = tagName.toLowerCase();
      this._attributes = attributes ? attributes : {};
      this._domElement = null;
    }

    Element.prototype.attributes = function () {
      var attributes, name, value, _ref;
      attributes = {};
      _ref = this._attributes;
      for (name in _ref) {
        value = _ref[name];
        attributes[name] = value;
      }
      return attributes;
    };

    Element.prototype.cssTypeName = function () {
      return 'element';
    };

    Element.prototype.domElement = function () {
      return this._domElement;
    };

    Element.prototype.isFocused = function () {
      return ContentEdit.Root.get().focused() === this;
    };

    Element.prototype.isMounted = function () {
      return this._domElement !== null;
    };

    Element.prototype.type = function () {
      return 'Element';
    };

    Element.prototype.typeName = function () {
      return 'Element';
    };

    Element.prototype.addCSSClass = function (className) {
      var modified;
      modified = false;
      if (!this.hasCSSClass(className)) {
        modified = true;
        if (this.attr('class')) {
          this.attr('class', "" + (this.attr('class')) + " " + className);
        } else {
          this.attr('class', className);
        }
      }
      this._addCSSClass(className);
      if (modified) {
        return this.taint();
      }
    };

    Element.prototype.attr = function (name, value) {
      name = name.toLowerCase();
      if (value === void 0) {
        return this._attributes[name];
      }
      this._attributes[name] = value;
      if (this.isMounted() && name.toLowerCase() !== 'class') {
        this._domElement.setAttribute(name, value);
      }
      return this.taint();
    };

    Element.prototype.blur = function () {
      var root;
      root = ContentEdit.Root.get();
      if (this.isFocused()) {
        this._removeCSSClass('ce-element--focused');
        root._focused = null;
        return root.trigger('blur', this);
      }
    };

    Element.prototype.createDraggingDOMElement = function () {
      var helper;
      if (!this.isMounted()) {
        return;
      }
      helper = document.createElement('div');
      helper.setAttribute('class', "ce-drag-helper ce-drag-helper--type-" + (this.cssTypeName()));
      helper.setAttribute('data-ce-type', ContentEdit._(this.typeName()));
      return helper;
    };

    Element.prototype.drag = function (x, y) {
      var root;
      if (!this.isMounted()) {
        return;
      }
      root = ContentEdit.Root.get();
      root.startDragging(this, x, y);
      return root.trigger('drag', this);
    };

    Element.prototype.drop = function (element, placement) {
      var root;
      root = ContentEdit.Root.get();
      if (element) {
        element._removeCSSClass('ce-element--drop');
        element._removeCSSClass("ce-element--drop-" + placement[0]);
        element._removeCSSClass("ce-element--drop-" + placement[1]);
        if (this.constructor.droppers[element.type()]) {
          this.constructor.droppers[element.type()](this, element, placement);
          root.trigger('drop', this, element, placement);
          return;
        } else if (element.constructor.droppers[this.type()]) {
          element.constructor.droppers[this.type()](this, element, placement);
          root.trigger('drop', this, element, placement);
          return;
        }
      }
      return root.trigger('drop', this, null, null);
    };

    Element.prototype.focus = function (supressDOMFocus) {
      var root;
      root = ContentEdit.Root.get();
      if (this.isFocused()) {
        return;
      }
      if (root.focused()) {
        root.focused().blur();
      }
      this._addCSSClass('ce-element--focused');
      root._focused = this;
      if (this.isMounted() && !supressDOMFocus) {
        this.domElement().focus();
      }
      return root.trigger('focus', this);
    };

    Element.prototype.hasCSSClass = function (className) {
      var c, classNames;
      if (this.attr('class')) {
        classNames = (function () {
          var _i, _len, _ref, _results;
          _ref = this.attr('class').split(' ');
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            _results.push(c);
          }
          return _results;
        }).call(this);
        if (classNames.indexOf(className) > -1) {
          return true;
        }
      }
      return false;
    };

    Element.prototype.merge = function (element) {
      if (this.constructor.mergers[element.type()]) {
        return this.constructor.mergers[element.type()](element, this);
      } else if (element.constructor.mergers[this.type()]) {
        return element.constructor.mergers[this.type()](element, this);
      }
    };

    Element.prototype.mount = function () {
      var sibling;
      if (!this._domElement) {
        this._domElement = document.createElement(this.tagName());
      }
      sibling = this.nextSibling();
      if (sibling) {
        this.parent().domElement().insertBefore(this._domElement, sibling.domElement());
      } else {
        this.parent().domElement().appendChild(this._domElement);
      }
      this._addDOMEventListeners();
      this._addCSSClass('ce-element');
      this._addCSSClass("ce-element--type-" + (this.cssTypeName()));
      if (this.isFocused()) {
        this._addCSSClass('ce-element--focused');
      }
      return ContentEdit.Root.get().trigger('mount', this);
    };

    Element.prototype.removeAttr = function (name) {
      name = name.toLowerCase();
      if (!this._attributes[name]) {
        return;
      }
      delete this._attributes[name];
      if (this.isMounted() && name.toLowerCase() !== 'class') {
        this._domElement.removeAttribute(name);
      }
      return this.taint();
    };

    Element.prototype.removeCSSClass = function (className) {
      var c, classNameIndex, classNames;
      if (!this.hasCSSClass(className)) {
        return;
      }
      classNames = (function () {
        var _i, _len, _ref, _results;
        _ref = this.attr('class').split(' ');
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      }).call(this);
      classNameIndex = classNames.indexOf(className);
      if (classNameIndex > -1) {
        classNames.splice(classNameIndex, 1);
      }
      if (classNames.length) {
        this.attr('class', classNames.join(' '));
      } else {
        this.removeAttr('class');
      }
      this._removeCSSClass(className);
      return this.taint();
    };

    Element.prototype.tagName = function (name) {
      if (name === void 0) {
        return this._tagName;
      }
      this._tagName = name.toLowerCase();
      if (this.isMounted()) {
        this.unmount();
        this.mount();
      }
      return this.taint();
    };

    Element.prototype.unmount = function () {
      this._removeDOMEventListeners();
      if (this._domElement.parentNode) {
        this._domElement.parentNode.removeChild(this._domElement);
      }
      this._domElement = null;
      return ContentEdit.Root.get().trigger('unmount', this);
    };

    Element.prototype._addDOMEventListeners = function () {
      this._domElement.addEventListener('focus', (function (_this) {
        return function (ev) {
          return ev.preventDefault();
        };
      })(this));
      this._domElement.addEventListener('dragstart', (function (_this) {
        return function (ev) {
          return ev.preventDefault();
        };
      })(this));
      this._domElement.addEventListener('keydown', (function (_this) {
        return function (ev) {
          return _this._onKeyDown(ev);
        };
      })(this));
      this._domElement.addEventListener('keyup', (function (_this) {
        return function (ev) {
          return _this._onKeyUp(ev);
        };
      })(this));
      this._domElement.addEventListener('mousedown', (function (_this) {
        return function (ev) {
          if (ev.button === 0) {
            return _this._onMouseDown(ev);
          }
        };
      })(this));
      this._domElement.addEventListener('mousemove', (function (_this) {
        return function (ev) {
          return _this._onMouseMove(ev);
        };
      })(this));
      this._domElement.addEventListener('mouseover', (function (_this) {
        return function (ev) {
          return _this._onMouseOver(ev);
        };
      })(this));
      this._domElement.addEventListener('mouseout', (function (_this) {
        return function (ev) {
          return _this._onMouseOut(ev);
        };
      })(this));
      this._domElement.addEventListener('mouseup', (function (_this) {
        return function (ev) {
          if (ev.button === 0) {
            return _this._onMouseUp(ev);
          }
        };
      })(this));
      this._domElement.addEventListener('paste', (function (_this) {
        return function (ev) {
          return _this._onPaste(ev);
        };
      })(this));
      this._domElement.addEventListener('dragover', (function (_this) {
        return function (ev) {
          return ev.preventDefault();
        };
      })(this));
      return this._domElement.addEventListener('drop', (function (_this) {
        return function (ev) {
          return _this._onNativeDrop(ev);
        };
      })(this));
    };

    Element.prototype._onKeyDown = function (ev) {};

    Element.prototype._onKeyUp = function (ev) {};

    Element.prototype._onMouseDown = function (ev) {
      if (this.focus) {
        return this.focus(true);
      }
    };

    Element.prototype._onMouseMove = function (ev) {
      return this._onOver(ev);
    };

    Element.prototype._onMouseOver = function (ev) {
      return this._onOver(ev);
    };

    Element.prototype._onMouseOut = function (ev) {
      var dragging, root;
      this._removeCSSClass('ce-element--over');
      root = ContentEdit.Root.get();
      dragging = root.dragging();
      if (dragging) {
        this._removeCSSClass('ce-element--drop');
        this._removeCSSClass('ce-element--drop-above');
        this._removeCSSClass('ce-element--drop-below');
        this._removeCSSClass('ce-element--drop-center');
        this._removeCSSClass('ce-element--drop-left');
        this._removeCSSClass('ce-element--drop-right');
        return root._dropTarget = null;
      }
    };

    Element.prototype._onMouseUp = function (ev) {};

    Element.prototype._onNativeDrop = function (ev) {
      ev.preventDefault();
      ev.stopPropagation();
      return ContentEdit.Root.get().trigger('native-drop', this, ev);
    };

    Element.prototype._onPaste = function (ev) {
      ev.preventDefault();
      ev.stopPropagation();
      return ContentEdit.Root.get().trigger('paste', this, ev);
    };

    Element.prototype._onOver = function (ev) {
      var dragging, root;
      this._addCSSClass('ce-element--over');
      root = ContentEdit.Root.get();
      dragging = root.dragging();
      if (!dragging) {
        return;
      }
      if (dragging === this) {
        return;
      }
      if (root._dropTarget) {
        return;
      }
      if (this.constructor.droppers[dragging.type()] || dragging.constructor.droppers[this.type()]) {
        this._addCSSClass('ce-element--drop');
        return root._dropTarget = this;
      }
    };

    Element.prototype._removeDOMEventListeners = function () {};

    Element.prototype._addCSSClass = function (className) {
      if (!this.isMounted()) {
        return;
      }
      return ContentEdit.addCSSClass(this._domElement, className);
    };

    Element.prototype._attributesToString = function () {
      if (!(Object.getOwnPropertyNames(this._attributes).length > 0)) {
        return '';
      }
      return ' ' + ContentEdit.attributesToString(this._attributes);
    };

    Element.prototype._removeCSSClass = function (className) {
      if (!this.isMounted()) {
        return;
      }
      return ContentEdit.removeCSSClass(this._domElement, className);
    };

    Element.droppers = {};

    Element.mergers = {};

    Element.placements = ['above', 'below'];

    Element.getDOMElementAttributes = function (domElement) {
      var attribute, attributes, _i, _len, _ref;
      if (!domElement.hasAttributes()) {
        return {};
      }
      attributes = {};
      _ref = domElement.attributes;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        attribute = _ref[_i];
        attributes[attribute.name.toLowerCase()] = attribute.value;
      }
      return attributes;
    };

    Element._dropVert = function (element, target, placement) {
      var insertIndex;
      element.parent().detach(element);
      insertIndex = target.parent().children.indexOf(target);
      if (placement[0] === 'below') {
        insertIndex += 1;
      }
      return target.parent().attach(element, insertIndex);
    };

    Element._dropBoth = function (element, target, placement) {
      var aClassNames, className, insertIndex, _i, _len, _ref;
      element.parent().detach(element);
      insertIndex = target.parent().children.indexOf(target);
      if (placement[0] === 'below' && placement[1] === 'center') {
        insertIndex += 1;
      }
      if (element.a) {
        element._removeCSSClass('align-left');
        element._removeCSSClass('align-right');
        if (element.a['class']) {
          aClassNames = [];
          _ref = element.a['class'].split(' ');
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            className = _ref[_i];
            if (className === 'align-left' || className === 'align-right') {
              continue;
            }
            aClassNames.push(className);
          }
          if (aClassNames.length) {
            element.a['class'] = aClassNames.join(' ');
          } else {
            delete element.a['class'];
          }
        }
      } else {
        element.removeCSSClass('align-left');
        element.removeCSSClass('align-right');
      }
      if (placement[1] === 'left') {
        if (element.a) {
          if (element.a['class']) {
            element.a['class'] += ' align-left';
          } else {
            element.a['class'] = 'align-left';
          }
          element._addCSSClass('align-left');
        } else {
          element.addCSSClass('align-left');
        }
      }
      if (placement[1] === 'right') {
        if (element.a) {
          if (element.a['class']) {
            element.a['class'] += ' align-right';
          } else {
            element.a['class'] = 'align-right';
          }
          element._addCSSClass('align-right');
        } else {
          element.addCSSClass('align-right');
        }
      }
      return target.parent().attach(element, insertIndex);
    };

    return Element;

  })(ContentEdit.Node);

  ContentEdit.ElementCollection = (function (_super) {
    __extends(ElementCollection, _super);

    ElementCollection.extend(ContentEdit.NodeCollection);

    function ElementCollection(tagName, attributes) {
      ElementCollection.__super__.constructor.call(this, tagName, attributes);
      ContentEdit.NodeCollection.prototype.constructor.call(this);
    }

    ElementCollection.prototype.cssTypeName = function () {
      return 'element-collection';
    };

    ElementCollection.prototype.isMounted = function () {
      return this._domElement !== null;
    };

    ElementCollection.prototype.type = function () {
      return 'ElementCollection';
    };

    ElementCollection.prototype.createDraggingDOMElement = function () {
      var helper, text;
      if (!this.isMounted()) {
        return;
      }
      helper = ElementCollection.__super__.createDraggingDOMElement.call(this);
      text = this._domElement.textContent;
      if (text.length > ContentEdit.HELPER_CHAR_LIMIT) {
        text = text.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      helper.innerHTML = text;
      return helper;
    };

    ElementCollection.prototype.detach = function (element) {
      ContentEdit.NodeCollection.prototype.detach.call(this, element);
      if (this.children.length === 0 && this.parent()) {
        return this.parent().detach(this);
      }
    };

    ElementCollection.prototype.html = function (indent) {
      var c, children;
      if (indent == null) {
        indent = '';
      }
      children = (function () {
        var _i, _len, _ref, _results;
        _ref = this.children;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c.html(indent + ContentEdit.INDENT));
        }
        return _results;
      }).call(this);
      return ("" + indent + "<" + (this.tagName()) + (this._attributesToString()) + ">\n") + ("" + (children.join('\n')) + "\n") + ("" + indent + "</" + (this.tagName()) + ">");
    };

    ElementCollection.prototype.mount = function () {
      var child, name, value, _i, _len, _ref, _ref1, _results;
      this._domElement = document.createElement(this._tagName);
      _ref = this._attributes;
      for (name in _ref) {
        value = _ref[name];
        this._domElement.setAttribute(name, value);
      }
      ElementCollection.__super__.mount.call(this);
      _ref1 = this.children;
      _results = [];
      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
        child = _ref1[_i];
        _results.push(child.mount());
      }
      return _results;
    };

    ElementCollection.prototype.unmount = function () {
      var child, _i, _len, _ref;
      _ref = this.children;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        child = _ref[_i];
        child.unmount();
      }
      return ElementCollection.__super__.unmount.call(this);
    };

    ElementCollection.prototype.blur = void 0;

    ElementCollection.prototype.focus = void 0;

    return ElementCollection;

  })(ContentEdit.Element);

  ContentEdit.ResizableElement = (function (_super) {
    __extends(ResizableElement, _super);

    function ResizableElement(tagName, attributes) {
      ResizableElement.__super__.constructor.call(this, tagName, attributes);
      this._domSizeInfoElement = null;
      this._aspectRatio = 1;
    }

    ResizableElement.prototype.aspectRatio = function () {
      return this._aspectRatio;
    };

    ResizableElement.prototype.maxSize = function () {
      var maxWidth;
      maxWidth = parseInt(this.attr('data-ce-max-width') || 0);
      if (!maxWidth) {
        maxWidth = ContentEdit.DEFAULT_MAX_ELEMENT_WIDTH;
      }
      maxWidth = Math.max(maxWidth, this.size()[0]);
      return [maxWidth, maxWidth * this.aspectRatio()];
    };

    ResizableElement.prototype.minSize = function () {
      var minWidth;
      minWidth = parseInt(this.attr('data-ce-min-width') || 0);
      if (!minWidth) {
        minWidth = ContentEdit.DEFAULT_MIN_ELEMENT_WIDTH;
      }
      minWidth = Math.min(minWidth, this.size()[0]);
      return [minWidth, minWidth * this.aspectRatio()];
    };

    ResizableElement.prototype.type = function () {
      return 'ResizableElement';
    };

    ResizableElement.prototype.mount = function () {
      ResizableElement.__super__.mount.call(this);
      return this._domElement.setAttribute('data-ce-size', this._getSizeInfo());
    };

    ResizableElement.prototype.resize = function (corner, x, y) {
      if (!this.isMounted()) {
        return;
      }
      return ContentEdit.Root.get().startResizing(this, corner, x, y, true);
    };

    ResizableElement.prototype.size = function (newSize) {
      var height, maxSize, minSize, width;
      if (!newSize) {
        width = parseInt(this.attr('width') || 1);
        height = parseInt(this.attr('height') || 1);
        return [width, height];
      }
      newSize[0] = parseInt(newSize[0]);
      newSize[1] = parseInt(newSize[1]);
      minSize = this.minSize();
      newSize[0] = Math.max(newSize[0], minSize[0]);
      newSize[1] = Math.max(newSize[1], minSize[1]);
      maxSize = this.maxSize();
      newSize[0] = Math.min(newSize[0], maxSize[0]);
      newSize[1] = Math.min(newSize[1], maxSize[1]);
      this.attr('width', parseInt(newSize[0]));
      this.attr('height', parseInt(newSize[1]));
      if (this.isMounted()) {
        this._domElement.style.width = "" + newSize[0] + "px";
        this._domElement.style.height = "" + newSize[1] + "px";
        return this._domElement.setAttribute('data-ce-size', this._getSizeInfo());
      }
    };

    ResizableElement.prototype._onMouseDown = function (ev) {
      var corner;
      ResizableElement.__super__._onMouseDown.call(this, ev);
      corner = this._getResizeCorner(ev.clientX, ev.clientY);
      if (corner) {
        return this.resize(corner, ev.clientX, ev.clientY);
      } else {
        clearTimeout(this._dragTimeout);
        return this._dragTimeout = setTimeout((function (_this) {
          return function () {
            return _this.drag(ev.pageX, ev.pageY);
          };
        })(this), 150);
      }
    };

    ResizableElement.prototype._onMouseMove = function (ev) {
      var corner;
      ResizableElement.__super__._onMouseMove.call(this);
      this._removeCSSClass('ce-element--resize-top-left');
      this._removeCSSClass('ce-element--resize-top-right');
      this._removeCSSClass('ce-element--resize-bottom-left');
      this._removeCSSClass('ce-element--resize-bottom-right');
      corner = this._getResizeCorner(ev.clientX, ev.clientY);
      if (corner) {
        return this._addCSSClass("ce-element--resize-" + corner[0] + "-" + corner[1]);
      }
    };

    ResizableElement.prototype._onMouseOut = function (ev) {
      ResizableElement.__super__._onMouseOut.call(this);
      this._removeCSSClass('ce-element--resize-top-left');
      this._removeCSSClass('ce-element--resize-top-right');
      this._removeCSSClass('ce-element--resize-bottom-left');
      return this._removeCSSClass('ce-element--resize-bottom-right');
    };

    ResizableElement.prototype._onMouseUp = function (ev) {
      ResizableElement.__super__._onMouseUp.call(this);
      if (this._dragTimeout) {
        return clearTimeout(this._dragTimeout);
      }
    };

    ResizableElement.prototype._getResizeCorner = function (x, y) {
      var corner, cornerSize, rect, size, _ref;
      rect = this._domElement.getBoundingClientRect();
      _ref = [x - rect.left, y - rect.top], x = _ref[0], y = _ref[1];
      size = this.size();
      cornerSize = ContentEdit.RESIZE_CORNER_SIZE;
      cornerSize = Math.min(cornerSize, Math.max(parseInt(size[0] / 4), 1));
      cornerSize = Math.min(cornerSize, Math.max(parseInt(size[1] / 4), 1));
      corner = null;
      if (x < cornerSize) {
        if (y < cornerSize) {
          corner = ['top', 'left'];
        } else if (y > rect.height - cornerSize) {
          corner = ['bottom', 'left'];
        }
      } else if (x > rect.width - cornerSize) {
        if (y < cornerSize) {
          corner = ['top', 'right'];
        } else if (y > rect.height - cornerSize) {
          corner = ['bottom', 'right'];
        }
      }
      return corner;
    };

    ResizableElement.prototype._getSizeInfo = function () {
      var size;
      size = this.size();
      return "w " + size[0] + " × h " + size[1];
    };

    return ResizableElement;

  })(ContentEdit.Element);

  ContentEdit.Region = (function (_super) {
    __extends(Region, _super);

    function Region(domElement) {
      var c, childNode, childNodes, cls, element, tagNames, _i, _len;
      Region.__super__.constructor.call(this);
      this._domElement = domElement;
      tagNames = ContentEdit.TagNames.get();
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = this._domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      }).call(this);
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        if (childNode.getAttribute("data-ce-tag")) {
          cls = tagNames.match(childNode.getAttribute("data-ce-tag"));
        } else {
          cls = tagNames.match(childNode.tagName);
        }
        element = cls.fromDOMElement(childNode);
        this._domElement.removeChild(childNode);
        if (element) {
          this.attach(element);
        }
      }
    }

    Region.prototype.domElement = function () {
      return this._domElement;
    };

    Region.prototype.isMounted = function () {
      return true;
    };

    Region.prototype.type = function () {
      return 'Region';
    };

    Region.prototype.html = function (indent) {
      var c;
      if (indent == null) {
        indent = '';
      }
      return ((function () {
        var _i, _len, _ref, _results;
        _ref = this.children;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c.html(indent));
        }
        return _results;
      }).call(this)).join('\n').trim();
    };

    return Region;

  })(ContentEdit.NodeCollection);

  _Root = (function (_super) {
    __extends(_Root, _super);

    function _Root() {
      this._onStopResizing = __bind(this._onStopResizing, this);
      this._onResize = __bind(this._onResize, this);
      this._onStopDragging = __bind(this._onStopDragging, this);
      this._onDrag = __bind(this._onDrag, this);
      _Root.__super__.constructor.call(this);
      this._focused = null;
      this._dragging = null;
      this._dropTarget = null;
      this._draggingDOMElement = null;
      this._resizing = null;
      this._resizingInit = null;
    }

    _Root.prototype.dragging = function () {
      return this._dragging;
    };

    _Root.prototype.dropTarget = function () {
      return this._dropTarget;
    };

    _Root.prototype.focused = function () {
      return this._focused;
    };

    _Root.prototype.resizing = function () {
      return this._resizing;
    };

    _Root.prototype.type = function () {
      return 'Root';
    };

    _Root.prototype.cancelDragging = function () {
      if (!this._dragging) {
        return;
      }
      document.body.removeChild(this._draggingDOMElement);
      document.removeEventListener('mousemove', this._onDrag);
      document.removeEventListener('mouseup', this._onStopDragging);
      this._dragging._removeCSSClass('ce-element--dragging');
      this._dragging = null;
      this._dropTarget = null;
      return ContentEdit.removeCSSClass(document.body, 'ce--dragging');
    };

    _Root.prototype.startDragging = function (element, x, y) {
      if (this._dragging) {
        return;
      }
      this._dragging = element;
      this._dragging._addCSSClass('ce-element--dragging');
      this._draggingDOMElement = this._dragging.createDraggingDOMElement();
      document.body.appendChild(this._draggingDOMElement);
      this._draggingDOMElement.style.left = "" + x + "px";
      this._draggingDOMElement.style.top = "" + y + "px";
      document.addEventListener('mousemove', this._onDrag);
      document.addEventListener('mouseup', this._onStopDragging);
      return ContentEdit.addCSSClass(document.body, 'ce--dragging');
    };

    _Root.prototype._getDropPlacement = function (x, y) {
      var horz, rect, vert, _ref;
      if (!this._dropTarget) {
        return null;
      }
      rect = this._dropTarget.domElement().getBoundingClientRect();
      _ref = [x - rect.left, y - rect.top], x = _ref[0], y = _ref[1];
      horz = 'center';
      if (x < ContentEdit.DROP_EDGE_SIZE) {
        horz = 'left';
      } else if (x > rect.width - ContentEdit.DROP_EDGE_SIZE) {
        horz = 'right';
      }
      vert = 'above';
      if (y > rect.height / 2) {
        vert = 'below';
      }
      return [vert, horz];
    };

    _Root.prototype._onDrag = function (ev) {
      var placement, _ref, _ref1;
      ContentSelect.Range.unselectAll();
      this._draggingDOMElement.style.left = "" + ev.pageX + "px";
      this._draggingDOMElement.style.top = "" + ev.pageY + "px";
      if (this._dropTarget) {
        placement = this._getDropPlacement(ev.clientX, ev.clientY);
        this._dropTarget._removeCSSClass('ce-element--drop-above');
        this._dropTarget._removeCSSClass('ce-element--drop-below');
        this._dropTarget._removeCSSClass('ce-element--drop-center');
        this._dropTarget._removeCSSClass('ce-element--drop-left');
        this._dropTarget._removeCSSClass('ce-element--drop-right');
        if (_ref = placement[0], __indexOf.call(this._dragging.constructor.placements, _ref) >= 0) {
          this._dropTarget._addCSSClass("ce-element--drop-" + placement[0]);
        }
        if (_ref1 = placement[1], __indexOf.call(this._dragging.constructor.placements, _ref1) >= 0) {
          return this._dropTarget._addCSSClass("ce-element--drop-" + placement[1]);
        }
      }
    };

    _Root.prototype._onStopDragging = function (ev) {
      var placement;
      placement = this._getDropPlacement(ev.clientX, ev.clientY);
      this._dragging.drop(this._dropTarget, placement);
      return this.cancelDragging();
    };

    _Root.prototype.startResizing = function (element, corner, x, y, fixed) {
      var measureDom, parentDom;
      if (this._resizing) {
        return;
      }
      this._resizing = element;
      this._resizingInit = {
        corner: corner,
        fixed: fixed,
        origin: [x, y],
        size: element.size()
      };
      this._resizing._addCSSClass('ce-element--resizing');
      parentDom = this._resizing.parent().domElement();
      measureDom = document.createElement('div');
      measureDom.setAttribute('class', 'ce-measure');
      parentDom.appendChild(measureDom);
      this._resizingParentWidth = measureDom.getBoundingClientRect().width;
      parentDom.removeChild(measureDom);
      document.addEventListener('mousemove', this._onResize);
      document.addEventListener('mouseup', this._onStopResizing);
      return ContentEdit.addCSSClass(document.body, 'ce--resizing');
    };

    _Root.prototype._onResize = function (ev) {
      var height, width, x, y;
      ContentSelect.Range.unselectAll();
      x = this._resizingInit.origin[0] - ev.clientX;
      if (this._resizingInit.corner[1] === 'right') {
        x = -x;
      }
      width = this._resizingInit.size[0] + x;
      width = Math.min(width, this._resizingParentWidth);
      if (this._resizingInit.fixed) {
        height = width * this._resizing.aspectRatio();
      } else {
        y = this._resizingInit.origin[1] - ev.clientY;
        if (this._resizingInit.corner[0] === 'bottom') {
          y = -y;
        }
        height = this._resizingInit.size[1] + y;
      }
      return this._resizing.size([width, height]);
    };

    _Root.prototype._onStopResizing = function (ev) {
      document.removeEventListener('mousemove', this._onResize);
      document.removeEventListener('mouseup', this._onStopResizing);
      this._resizing._removeCSSClass('ce-element--resizing');
      this._resizing = null;
      this._resizingInit = null;
      this._resizingParentWidth = null;
      return ContentEdit.removeCSSClass(document.body, 'ce--resizing');
    };

    return _Root;

  })(ContentEdit.Node);

  ContentEdit.Root = (function () {
    var instance;

    function Root() {}

    instance = null;

    Root.get = function () {
      return instance != null ? instance : instance = new _Root();
    };

    return Root;

  })();

  ContentEdit.Static = (function (_super) {
    __extends(Static, _super);

    function Static(tagName, attributes, content) {
      Static.__super__.constructor.call(this, tagName, attributes);
      this._content = content;
    }

    Static.prototype.cssTypeName = function () {
      return 'static';
    };

    Static.prototype.type = function () {
      return 'Static';
    };

    Static.prototype.typeName = function () {
      return 'Static';
    };

    Static.prototype.createDraggingDOMElement = function () {
      var helper, text;
      if (!this.isMounted()) {
        return;
      }
      helper = Static.__super__.createDraggingDOMElement.call(this);
      text = this._domElement.textContent;
      if (text.length > ContentEdit.HELPER_CHAR_LIMIT) {
        text = text.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      helper.innerHTML = text;
      return helper;
    };

    Static.prototype.html = function (indent) {
      if (indent == null) {
        indent = '';
      }
      if (HTMLString.Tag.SELF_CLOSING[this._tagName]) {
        return "" + indent + "<" + this._tagName + (this._attributesToString()) + ">";
      }
      return ("" + indent + "<" + this._tagName + (this._attributesToString()) + ">") + ("" + this._content) + ("" + indent + "</" + this._tagName + ">");
    };

    Static.prototype.mount = function () {
      var name, value, _ref;
      this._domElement = document.createElement(this._tagName);
      _ref = this._attributes;
      for (name in _ref) {
        value = _ref[name];
        this._domElement.setAttribute(name, value);
      }
      this._domElement.innerHTML = this._content;
      return Static.__super__.mount.call(this);
    };

    Static.prototype.blur = void 0;

    Static.prototype.focus = void 0;

    Static.prototype._onMouseDown = function (ev) {
      Static.__super__._onMouseDown.call(this, ev);
      if (this.attr('data-ce-moveable') !== void 0) {
        clearTimeout(this._dragTimeout);
        return this._dragTimeout = setTimeout((function (_this) {
          return function () {
            return _this.drag(ev.pageX, ev.pageY);
          };
        })(this), 150);
      }
    };

    Static.prototype._onMouseOver = function (ev) {
      Static.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    Static.prototype._onMouseUp = function (ev) {
      Static.__super__._onMouseUp.call(this, ev);
      if (this._dragTimeout) {
        return clearTimeout(this._dragTimeout);
      }
    };

    Static.droppers = {
      'Static': ContentEdit.Element._dropVert
    };

    Static.fromDOMElement = function (domElement) {
      return new this(domElement.tagName, this.getDOMElementAttributes(domElement), domElement.innerHTML);
    };

    return Static;

  })(ContentEdit.Element);

  ContentEdit.TagNames.get().register(ContentEdit.Static, 'static');

  ContentEdit.Text = (function (_super) {
    __extends(Text, _super);

    function Text(tagName, attributes, content) {
      Text.__super__.constructor.call(this, tagName, attributes);
      if (content instanceof HTMLString.String) {
        this.content = content;
      } else {
        this.content = new HTMLString.String(content).trim();
      }
    }

    Text.prototype.cssTypeName = function () {
      return 'text';
    };

    Text.prototype.type = function () {
      return 'Text';
    };

    Text.prototype.typeName = function () {
      return 'Text';
    };

    Text.prototype.blur = function () {
      var error;
      if (this.isMounted()) {
        this._syncContent();
      }
      if (this.content.isWhitespace()) {
        if (this.parent()) {
          this.parent().detach(this);
        }
      } else if (this.isMounted()) {
        try {
          this._domElement.blur();
        } catch (_error) {
          error = _error;
        }
        this._domElement.removeAttribute('contenteditable');
      }
      return Text.__super__.blur.call(this);
    };

    Text.prototype.createDraggingDOMElement = function () {
      var helper, text;
      if (!this.isMounted()) {
        return;
      }
      helper = Text.__super__.createDraggingDOMElement.call(this);
      text = HTMLString.String.encode(this._domElement.textContent);
      if (text.length > ContentEdit.HELPER_CHAR_LIMIT) {
        text = text.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      helper.innerHTML = text;
      return helper;
    };

    Text.prototype.drag = function (x, y) {
      this.storeState();
      this._domElement.removeAttribute('contenteditable');
      return Text.__super__.drag.call(this, x, y);
    };

    Text.prototype.drop = function (element, placement) {
      Text.__super__.drop.call(this, element, placement);
      return this.restoreState();
    };

    Text.prototype.focus = function (supressDOMFocus) {
      if (this.isMounted()) {
        this._domElement.setAttribute('contenteditable', '');
      }
      return Text.__super__.focus.call(this, supressDOMFocus);
    };

    Text.prototype.html = function (indent) {
      var content;
      if (indent == null) {
        indent = '';
      }
      if (!this._lastCached || this._lastCached < this._modified) {
        content = this.content.copy().trim();
        content.optimize();
        this._lastCached = Date.now();
        this._cached = content.html();
      }
      return ("" + indent + "<" + this._tagName + (this._attributesToString()) + ">\n") + ("" + indent + ContentEdit.INDENT + this._cached + "\n") + ("" + indent + "</" + this._tagName + ">");
    };

    Text.prototype.mount = function () {
      var name, value, _ref;
      this._domElement = document.createElement(this._tagName);
      _ref = this._attributes;
      for (name in _ref) {
        value = _ref[name];
        this._domElement.setAttribute(name, value);
      }
      this.updateInnerHTML();
      return Text.__super__.mount.call(this);
    };

    Text.prototype.restoreState = function () {
      if (!this._savedSelection) {
        return;
      }
      if (!(this.isMounted() && this.isFocused())) {
        this._savedSelection = void 0;
        return;
      }
      this._domElement.setAttribute('contenteditable', '');
      this._addCSSClass('ce-element--focused');
      if (document.activeElement !== this.domElement()) {
        this.domElement().focus();
      }
      this._savedSelection.select(this._domElement);
      return this._savedSelection = void 0;
    };

    Text.prototype.selection = function (selection) {
      if (selection === void 0) {
        if (this.isMounted()) {
          return ContentSelect.Range.query(this._domElement);
        } else {
          return new ContentSelect.Range(0, 0);
        }
      }
      return selection.select(this._domElement);
    };

    Text.prototype.storeState = function () {
      if (!(this.isMounted() && this.isFocused())) {
        return;
      }
      return this._savedSelection = ContentSelect.Range.query(this._domElement);
    };

    Text.prototype.updateInnerHTML = function () {
      this._domElement.innerHTML = this.content.html();
      ContentSelect.Range.prepareElement(this._domElement);
      return this._flagIfEmpty();
    };

    Text.prototype._onKeyDown = function (ev) {
      switch (ev.keyCode) {
        case 40:
          return this._keyDown(ev);
        case 37:
          return this._keyLeft(ev);
        case 39:
          return this._keyRight(ev);
        case 38:
          return this._keyUp(ev);
        case 9:
          return this._keyTab(ev);
        case 8:
          return this._keyBack(ev);
        case 46:
          return this._keyDelete(ev);
        case 13:
          return this._keyReturn(ev);
      }
    };

    Text.prototype._onKeyUp = function (ev) {
      Text.__super__._onKeyUp.call(this, ev);
      return this._syncContent();
    };

    Text.prototype._onMouseDown = function (ev) {
      Text.__super__._onMouseDown.call(this, ev);
      clearTimeout(this._dragTimeout);
      this._dragTimeout = setTimeout((function (_this) {
        return function () {
          return _this.drag(ev.pageX, ev.pageY);
        };
      })(this), ContentEdit.DRAG_HOLD_DURATION);
      if (this.content.length() === 0 && ContentEdit.Root.get().focused() === this) {
        ev.preventDefault();
        if (document.activeElement !== this._domElement) {
          this._domElement.focus();
        }
        return new ContentSelect.Range(0, 0).select(this._domElement);
      }
    };

    Text.prototype._onMouseMove = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseMove.call(this, ev);
    };

    Text.prototype._onMouseOut = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseOut.call(this, ev);
    };

    Text.prototype._onMouseUp = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseUp.call(this, ev);
    };

    Text.prototype._keyBack = function (ev) {
      var previous, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(selection.get()[0] === 0 && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      previous = this.previousContent();
      this._syncContent();
      if (previous) {
        return previous.merge(this);
      }
    };

    Text.prototype._keyDelete = function (ev) {
      var next, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(this._atEnd(selection) && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      next = this.nextContent();
      if (next) {
        return this.merge(next);
      }
    };

    Text.prototype._keyDown = function (ev) {
      return this._keyRight(ev);
    };

    Text.prototype._keyLeft = function (ev) {
      var previous, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(selection.get()[0] === 0 && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      previous = this.previousContent();
      if (previous) {
        previous.focus();
        selection = new ContentSelect.Range(previous.content.length(), previous.content.length());
        return selection.select(previous.domElement());
      } else {
        return ContentEdit.Root.get().trigger('previous-region', this.closest(function (node) {
          return node.type() === 'Region';
        }));
      }
    };

    Text.prototype._keyReturn = function (ev) {
      var element, insertAt, lineBreakStr, selection, tail, tip;
      ev.preventDefault();
      if (this.content.isWhitespace()) {
        return;
      }
      ContentSelect.Range.query(this._domElement);
      selection = ContentSelect.Range.query(this._domElement);
      tip = this.content.substring(0, selection.get()[0]);
      tail = this.content.substring(selection.get()[1]);
      if (ev.shiftKey) {
        insertAt = selection.get()[0];
        lineBreakStr = '<br>';
        if (this.content.length() === insertAt) {
          if (!this.content.characters[insertAt - 1].isTag('br')) {
            lineBreakStr = '<br><br>';
          }
        }
        this.content = this.content.insert(insertAt, new HTMLString.String(lineBreakStr, true), true);
        this.updateInnerHTML();
        insertAt += 1;
        selection = new ContentSelect.Range(insertAt, insertAt);
        selection.select(this.domElement());
        return;
      }
      this.content = tip.trim();
      this.updateInnerHTML();
      element = new this.constructor('p', {}, tail.trim());
      this.parent().attach(element, this.parent().children.indexOf(this) + 1);
      if (tip.length()) {
        element.focus();
        selection = new ContentSelect.Range(0, 0);
        selection.select(element.domElement());
      } else {
        selection = new ContentSelect.Range(0, tip.length());
        selection.select(this._domElement);
      }
      return this.taint();
    };

    Text.prototype._keyRight = function (ev) {
      var next, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(this._atEnd(selection) && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      next = this.nextContent();
      if (next) {
        next.focus();
        selection = new ContentSelect.Range(0, 0);
        return selection.select(next.domElement());
      } else {
        return ContentEdit.Root.get().trigger('next-region', this.closest(function (node) {
          return node.type() === 'Region';
        }));
      }
    };

    Text.prototype._keyTab = function (ev) {
      return ev.preventDefault();
    };

    Text.prototype._keyUp = function (ev) {
      return this._keyLeft(ev);
    };

    Text.prototype._atEnd = function (selection) {
      var atEnd;
      atEnd = selection.get()[0] === this.content.length();
      if (selection.get()[0] === this.content.length() - 1 && this.content.characters[this.content.characters.length - 1].isTag('br')) {
        atEnd = true;
      }
      return atEnd;
    };

    Text.prototype._flagIfEmpty = function () {
      if (this.content.length() === 0) {
        return this._addCSSClass('ce-element--empty');
      } else {
        return this._removeCSSClass('ce-element--empty');
      }
    };

    Text.prototype._syncContent = function (ev) {
      var newSnapshot, snapshot;
      snapshot = this.content.html();
      this.content = new HTMLString.String(this._domElement.innerHTML, this.content.preserveWhitespace());
      newSnapshot = this.content.html();
      if (snapshot !== newSnapshot) {
        this.taint();
      }
      return this._flagIfEmpty();
    };

    Text.droppers = {
      'Static': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert
    };

    Text.mergers = {
      'Text': function (element, target) {
        var offset;
        offset = target.content.length();
        if (element.content.length()) {
          target.content = target.content.concat(element.content);
        }
        if (target.isMounted()) {
          target.updateInnerHTML();
        }
        target.focus();
        new ContentSelect.Range(offset, offset).select(target._domElement);
        if (element.parent()) {
          element.parent().detach(element);
        }
        return target.taint();
      }
    };

    Text.fromDOMElement = function (domElement) {
      return new this(domElement.tagName, this.getDOMElementAttributes(domElement), domElement.innerHTML.replace(/^\s+|\s+$/g, ''));
    };

    return Text;

  })(ContentEdit.Element);

  ContentEdit.TagNames.get().register(ContentEdit.Text, 'address', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');

  ContentEdit.PreText = (function (_super) {
    __extends(PreText, _super);

    function PreText(tagName, attributes, content) {
      if (content instanceof HTMLString.String) {
        this.content = content;
      } else {
        this.content = new HTMLString.String(content, true);
      }
      ContentEdit.Element.call(this, tagName, attributes);
    }

    PreText.prototype.cssTypeName = function () {
      return 'pre-text';
    };

    PreText.prototype.type = function () {
      return 'PreText';
    };

    PreText.prototype.typeName = function () {
      return 'Preformatted';
    };

    PreText.prototype.html = function (indent) {
      var content;
      if (indent == null) {
        indent = '';
      }
      if (!this._lastCached || this._lastCached < this._modified) {
        content = this.content.copy();
        content.optimize();
        this._lastCached = Date.now();
        this._cached = content.html();
      }
      return ("" + indent + "<" + this._tagName + (this._attributesToString()) + ">") + ("" + this._cached + "</" + this._tagName + ">");
    };

    PreText.prototype.updateInnerHTML = function () {
      var html;
      html = this.content.html();
      html += '\n';
      this._domElement.innerHTML = html;
      ContentSelect.Range.prepareElement(this._domElement);
      return this._flagIfEmpty();
    };

    PreText.prototype._onKeyUp = function (ev) {
      var html, newSnaphot, snapshot;
      snapshot = this.content.html();
      html = this._domElement.innerHTML.replace(/[\n]$/, '');
      this.content = new HTMLString.String(html, this.content.preserveWhitespace());
      newSnaphot = this.content.html();
      if (snapshot !== newSnaphot) {
        this.taint();
      }
      return this._flagIfEmpty();
    };

    PreText.prototype._keyReturn = function (ev) {
      var cursor, selection, tail, tip;
      ev.preventDefault();
      selection = ContentSelect.Range.query(this._domElement);
      cursor = selection.get()[0] + 1;
      if (selection.get()[0] === 0 && selection.isCollapsed()) {
        this.content = new HTMLString.String('\n', true).concat(this.content);
      } else if (this._atEnd(selection) && selection.isCollapsed()) {
        this.content = this.content.concat(new HTMLString.String('\n', true));
      } else if (selection.get()[0] === 0 && selection.get()[1] === this.content.length()) {
        this.content = new HTMLString.String('\n', true);
        cursor = 0;
      } else {
        tip = this.content.substring(0, selection.get()[0]);
        tail = this.content.substring(selection.get()[1]);
        this.content = tip.concat(new HTMLString.String('\n', true), tail);
      }
      this.updateInnerHTML();
      selection.set(cursor, cursor);
      selection.select(this._domElement);
      return this.taint();
    };

    PreText.droppers = {
      'PreText': ContentEdit.Element._dropVert,
      'Static': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert
    };

    PreText.mergers = {};

    PreText.fromDOMElement = function (domElement) {
      return new this(domElement.tagName, this.getDOMElementAttributes(domElement), domElement.innerHTML);
    };

    return PreText;

  })(ContentEdit.Text);

  ContentEdit.TagNames.get().register(ContentEdit.PreText, 'pre');

  ContentEdit.Image = (function (_super) {
    __extends(Image, _super);

    function Image(attributes, a) {
      var size;
      Image.__super__.constructor.call(this, 'img', attributes);
      this.a = a ? a : null;
      size = this.size();
      this._aspectRatio = size[1] / size[0];
    }

    Image.prototype.cssTypeName = function () {
      return 'image';
    };

    Image.prototype.type = function () {
      return 'Image';
    };

    Image.prototype.typeName = function () {
      return 'Image';
    };

    Image.prototype.createDraggingDOMElement = function () {
      var helper;
      if (!this.isMounted()) {
        return;
      }
      helper = Image.__super__.createDraggingDOMElement.call(this);
      helper.style.backgroundImage = "url(" + this._attributes['src'] + ")";
      return helper;
    };

    Image.prototype.html = function (indent) {
      var attributes, img;
      if (indent == null) {
        indent = '';
      }
      img = "" + indent + "<img" + (this._attributesToString()) + ">";
      if (this.a) {
        attributes = ContentEdit.attributesToString(this.a);
        attributes = "" + attributes + " data-ce-tag=\"img\"";
        return ("" + indent + "<a " + attributes + ">\n") + ("" + ContentEdit.INDENT + img + "\n") + ("" + indent + "</a>");
      } else {
        return img;
      }
    };

    Image.prototype.mount = function () {
      var classes, style;
      this._domElement = document.createElement('div');
      classes = '';
      if (this.a && this.a['class']) {
        classes += ' ' + this.a['class'];
      }
      if (this._attributes['class']) {
        classes += ' ' + this._attributes['class'];
      }
      this._domElement.setAttribute('class', classes);
      style = this._attributes['style'] ? this._attributes['style'] : '';
      style += "background-image:url(" + this._attributes['src'] + ");";
      if (this._attributes['width']) {
        style += "width:" + this._attributes['width'] + "px;";
      }
      if (this._attributes['height']) {
        style += "height:" + this._attributes['height'] + "px;";
      }
      this._domElement.setAttribute('style', style);
      return Image.__super__.mount.call(this);
    };

    Image.droppers = {
      'Image': ContentEdit.Element._dropBoth,
      'PreText': ContentEdit.Element._dropBoth,
      'Static': ContentEdit.Element._dropBoth,
      'Text': ContentEdit.Element._dropBoth
    };

    Image.placements = ['above', 'below', 'left', 'right', 'center'];

    Image.fromDOMElement = function (domElement) {
      var a, attributes, c, childNode, childNodes, _i, _len;
      a = null;
      if (domElement.tagName.toLowerCase() === 'a') {
        a = this.getDOMElementAttributes(domElement);
        childNodes = (function () {
          var _i, _len, _ref, _results;
          _ref = domElement.childNodes;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            _results.push(c);
          }
          return _results;
        })();
        for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
          childNode = childNodes[_i];
          if (childNode.nodeType === 1 && childNode.tagName.toLowerCase() === 'img') {
            domElement = childNode;
            break;
          }
        }
        if (domElement.tagName.toLowerCase() === 'a') {
          domElement = document.createElement('img');
        }
      }
      attributes = this.getDOMElementAttributes(domElement);
      if (attributes['width'] === void 0) {
        if (attributes['height'] === void 0) {
          attributes['width'] = domElement.naturalWidth;
        } else {
          attributes['width'] = domElement.clientWidth;
        }
      }
      if (attributes['height'] === void 0) {
        if (attributes['width'] === void 0) {
          attributes['height'] = domElement.naturalHeight;
        } else {
          attributes['height'] = domElement.clientHeight;
        }
      }
      return new this(attributes, a);
    };

    return Image;

  })(ContentEdit.ResizableElement);

  ContentEdit.TagNames.get().register(ContentEdit.Image, 'img');

  ContentEdit.Video = (function (_super) {
    __extends(Video, _super);

    function Video(tagName, attributes, sources) {
      var size;
      if (sources == null) {
        sources = [];
      }
      Video.__super__.constructor.call(this, tagName, attributes);
      this.sources = sources;
      size = this.size();
      this._aspectRatio = size[1] / size[0];
    }

    Video.prototype.cssTypeName = function () {
      return 'video';
    };

    Video.prototype.type = function () {
      return 'Video';
    };

    Video.prototype.typeName = function () {
      return 'Video';
    };

    Video.prototype._title = function () {
      var src;
      src = '';
      if (this.attr('src')) {
        src = this.attr('src');
      } else {
        if (this.sources.length) {
          src = this.sources[0]['src'];
        }
      }
      src = this.attr('title') || src;

      if (!src) {
        src = 'No video source set';
      }
      if (src.length > ContentEdit.HELPER_CHAR_LIMIT) {
        src = src.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      return src;
    };

    Video.prototype.createDraggingDOMElement = function () {
      var helper;
      if (!this.isMounted()) {
        return;
      }
      helper = Video.__super__.createDraggingDOMElement.call(this);
      helper.innerHTML = this._title();
      return helper;
    };

    Video.prototype.html = function (indent) {
      var attributes, source, sourceStrings, _i, _len, _ref;
      if (indent == null) {
        indent = '';
      }
      if (this.tagName() === 'video') {
        sourceStrings = [];
        _ref = this.sources;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          source = _ref[_i];
          attributes = ContentEdit.attributesToString(source);
          sourceStrings.push("" + indent + ContentEdit.INDENT + "<source " + attributes + ">");
        }
        return ("" + indent + "<video" + (this._attributesToString()) + ">\n") + sourceStrings.join('\n') + ("\n" + indent + "</video>");
      } else {
        return ("" + indent + "<" + this._tagName + (this._attributesToString()) + ">") + ("</" + this._tagName + ">");
      }
    };

    Video.prototype.mount = function () {
      var style;
      this._domElement = document.createElement('div');
      if (this.a && this.a['class']) {
        this._domElement.setAttribute('class', this.a['class']);
      } else if (this._attributes['class']) {
        this._domElement.setAttribute('class', this._attributes['class']);
      }
      style = this._attributes['style'] ? this._attributes['style'] : '';
      if (this._attributes['width']) {
        style += "width:" + this._attributes['width'] + "px;";
      }
      if (this._attributes['height']) {
        style += "height:" + this._attributes['height'] + "px;";
      }
      this._domElement.setAttribute('style', style);
      this._domElement.setAttribute('data-ce-title', this._title());
      return Video.__super__.mount.call(this);
    };

    Video.droppers = {
      'Image': ContentEdit.Element._dropBoth,
      'PreText': ContentEdit.Element._dropBoth,
      'Static': ContentEdit.Element._dropBoth,
      'Text': ContentEdit.Element._dropBoth,
      'Video': ContentEdit.Element._dropBoth
    };

    Video.placements = ['above', 'below', 'left', 'right', 'center'];

    Video.fromDOMElement = function (domElement) {
      var c, childNode, childNodes, sources, _i, _len;
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      })();
      sources = [];
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType === 1 && childNode.tagName.toLowerCase() === 'source') {
          sources.push(this.getDOMElementAttributes(childNode));
        }
      }
      return new this(domElement.tagName, this.getDOMElementAttributes(domElement), sources);
    };

    return Video;

  })(ContentEdit.ResizableElement);

  ContentEdit.TagNames.get().register(ContentEdit.Video, 'iframe', 'video');

  ContentEdit.List = (function (_super) {
    __extends(List, _super);

    function List(tagName, attributes) {
      List.__super__.constructor.call(this, tagName, attributes);
    }

    List.prototype.cssTypeName = function () {
      return 'list';
    };

    List.prototype.type = function () {
      return 'List';
    };

    List.prototype.typeName = function () {
      return 'List';
    };

    List.prototype._onMouseOver = function (ev) {
      if (this.parent().type() === 'ListItem') {
        return;
      }
      List.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    List.droppers = {
      'Image': ContentEdit.Element._dropBoth,
      'List': ContentEdit.Element._dropVert,
      'PreText': ContentEdit.Element._dropVert,
      'Static': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert,
      'Video': ContentEdit.Element._dropBoth
    };

    List.fromDOMElement = function (domElement) {
      var c, childNode, childNodes, list, _i, _len;
      list = new this(domElement.tagName, this.getDOMElementAttributes(domElement));
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      })();
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        if (childNode.tagName.toLowerCase() !== 'li') {
          continue;
        }
        list.attach(ContentEdit.ListItem.fromDOMElement(childNode));
      }
      if (list.children.length === 0) {
        return null;
      }
      return list;
    };

    return List;

  })(ContentEdit.ElementCollection);

  ContentEdit.TagNames.get().register(ContentEdit.List, 'ol', 'ul');

  ContentEdit.ListItem = (function (_super) {
    __extends(ListItem, _super);

    function ListItem(attributes) {
      ListItem.__super__.constructor.call(this, 'li', attributes);
    }

    ListItem.prototype.cssTypeName = function () {
      return 'list-item';
    };

    ListItem.prototype.list = function () {
      if (this.children.length === 2) {
        return this.children[1];
      }
      return null;
    };

    ListItem.prototype.listItemText = function () {
      if (this.children.length > 0) {
        return this.children[0];
      }
      return null;
    };

    ListItem.prototype.type = function () {
      return 'ListItem';
    };

    ListItem.prototype.html = function (indent) {
      var lines;
      if (indent == null) {
        indent = '';
      }
      lines = ["" + indent + "<li" + (this._attributesToString()) + ">"];
      if (this.listItemText()) {
        lines.push(this.listItemText().html(indent + ContentEdit.INDENT));
      }
      if (this.list()) {
        lines.push(this.list().html(indent + ContentEdit.INDENT));
      }
      lines.push("" + indent + "</li>");
      return lines.join('\n');
    };

    ListItem.prototype.indent = function () {
      var sibling;
      if (this.parent().children.indexOf(this) === 0) {
        return;
      }
      sibling = this.previousSibling();
      if (!sibling.list()) {
        sibling.attach(new ContentEdit.List(sibling.parent().tagName()));
      }
      this.listItemText().storeState();
      this.parent().detach(this);
      sibling.list().attach(this);
      return this.listItemText().restoreState();
    };

    ListItem.prototype.remove = function () {
      var child, i, index, _i, _len, _ref;
      if (!this.parent()) {
        return;
      }
      index = this.parent().children.indexOf(this);
      if (this.list()) {
        _ref = this.list().children.slice();
        for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
          child = _ref[i];
          child.parent().detach(child);
          this.parent().attach(child, i + index);
        }
      }
      return this.parent().detach(this);
    };

    ListItem.prototype.unindent = function () {
      var child, grandParent, i, itemIndex, list, parent, parentIndex, selection, sibling, siblings, text, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1;
      parent = this.parent();
      grandParent = parent.parent();
      siblings = parent.children.slice(parent.children.indexOf(this) + 1, parent.children.length);
      if (grandParent.type() === 'ListItem') {
        this.listItemText().storeState();
        parent.detach(this);
        grandParent.parent().attach(this, grandParent.parent().children.indexOf(grandParent) + 1);
        if (siblings.length && !this.list()) {
          this.attach(new ContentEdit.List(parent.tagName()));
        }
        for (_i = 0, _len = siblings.length; _i < _len; _i++) {
          sibling = siblings[_i];
          sibling.parent().detach(sibling);
          this.list().attach(sibling);
        }
        return this.listItemText().restoreState();
      } else {
        text = new ContentEdit.Text('p', this.attr('class') ? {
          'class': this.attr('class')
        } : {}, this.listItemText().content);
        selection = null;
        if (this.listItemText().isFocused()) {
          selection = ContentSelect.Range.query(this.listItemText().domElement());
        }
        parentIndex = grandParent.children.indexOf(parent);
        itemIndex = parent.children.indexOf(this);
        if (itemIndex === 0) {
          list = null;
          if (parent.children.length === 1) {
            if (this.list()) {
              list = new ContentEdit.List(parent.tagName());
            }
            grandParent.detach(parent);
          } else {
            parent.detach(this);
          }
          grandParent.attach(text, parentIndex);
          if (list) {
            grandParent.attach(list, parentIndex + 1);
          }
          if (this.list()) {
            _ref = this.list().children.slice();
            for (i = _j = 0, _len1 = _ref.length; _j < _len1; i = ++_j) {
              child = _ref[i];
              child.parent().detach(child);
              if (list) {
                list.attach(child);
              } else {
                parent.attach(child, i);
              }
            }
          }
        } else if (itemIndex === parent.children.length - 1) {
          parent.detach(this);
          grandParent.attach(text, parentIndex + 1);
          if (this.list()) {
            grandParent.attach(this.list(), parentIndex + 2);
          }
        } else {
          parent.detach(this);
          grandParent.attach(text, parentIndex + 1);
          list = new ContentEdit.List(parent.tagName());
          grandParent.attach(list, parentIndex + 2);
          if (this.list()) {
            _ref1 = this.list().children.slice();
            for (_k = 0, _len2 = _ref1.length; _k < _len2; _k++) {
              child = _ref1[_k];
              child.parent().detach(child);
              list.attach(child);
            }
          }
          for (_l = 0, _len3 = siblings.length; _l < _len3; _l++) {
            sibling = siblings[_l];
            sibling.parent().detach(sibling);
            list.attach(sibling);
          }
        }
        if (selection) {
          text.focus();
          return selection.select(text.domElement());
        }
      }
    };

    ListItem.prototype._onMouseOver = function (ev) {
      ListItem.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    ListItem.prototype._addDOMEventListeners = function () {};

    ListItem.prototype._removeDOMEventListners = function () {};

    ListItem.fromDOMElement = function (domElement) {
      var childNode, content, listDOMElement, listElement, listItem, listItemText, _i, _len, _ref, _ref1;
      listItem = new this(this.getDOMElementAttributes(domElement));
      content = '';
      listDOMElement = null;
      _ref = domElement.childNodes;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        childNode = _ref[_i];
        if (childNode.nodeType === 1) {
          if ((_ref1 = childNode.tagName.toLowerCase()) === 'ul' || _ref1 === 'li') {
            if (!listDOMElement) {
              listDOMElement = childNode;
            }
          } else {
            content += childNode.outerHTML;
          }
        } else {
          content += HTMLString.String.encode(childNode.textContent);
        }
      }
      content = content.replace(/^\s+|\s+$/g, '');
      listItemText = new ContentEdit.ListItemText(content);
      listItem.attach(listItemText);
      if (listDOMElement) {
        listElement = ContentEdit.List.fromDOMElement(listDOMElement);
        listItem.attach(listElement);
      }
      return listItem;
    };

    return ListItem;

  })(ContentEdit.ElementCollection);

  ContentEdit.ListItemText = (function (_super) {
    __extends(ListItemText, _super);

    function ListItemText(content) {
      ListItemText.__super__.constructor.call(this, 'div', {}, content);
    }

    ListItemText.prototype.cssTypeName = function () {
      return 'list-item-text';
    };

    ListItemText.prototype.type = function () {
      return 'ListItemText';
    };

    ListItemText.prototype.typeName = function () {
      return 'List item';
    };

    ListItemText.prototype.blur = function () {
      if (this.content.isWhitespace()) {
        this.parent().remove();
      } else if (this.isMounted()) {
        this._domElement.blur();
        this._domElement.removeAttribute('contenteditable');
      }
      return ContentEdit.Element.prototype.blur.call(this);
    };

    ListItemText.prototype.html = function (indent) {
      var content;
      if (indent == null) {
        indent = '';
      }
      if (!this._lastCached || this._lastCached < this._modified) {
        content = this.content.copy().trim();
        content.optimize();
        this._lastCached = Date.now();
        this._cached = content.html();
      }
      return "" + indent + this._cached;
    };

    ListItemText.prototype._onMouseDown = function (ev) {
      var initDrag;
      ContentEdit.Element.prototype._onMouseDown.call(this, ev);
      initDrag = (function (_this) {
        return function () {
          var listRoot;
          if (ContentEdit.Root.get().dragging() === _this) {
            ContentEdit.Root.get().cancelDragging();
            listRoot = _this.closest(function (node) {
              return node.parent().type() === 'Region';
            });
            return listRoot.drag(ev.pageX, ev.pageY);
          } else {
            _this.drag(ev.pageX, ev.pageY);
            return _this._dragTimeout = setTimeout(initDrag, ContentEdit.DRAG_HOLD_DURATION * 2);
          }
        };
      })(this);
      clearTimeout(this._dragTimeout);
      return this._dragTimeout = setTimeout(initDrag, ContentEdit.DRAG_HOLD_DURATION);
    };

    ListItemText.prototype._onMouseMove = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return ContentEdit.Element.prototype._onMouseMove.call(this, ev);
    };

    ListItemText.prototype._onMouseUp = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return ContentEdit.Element.prototype._onMouseUp.call(this, ev);
    };

    ListItemText.prototype._keyTab = function (ev) {
      ev.preventDefault();
      if (ev.shiftKey) {
        return this.parent().unindent();
      } else {
        return this.parent().indent();
      }
    };

    ListItemText.prototype._keyReturn = function (ev) {
      var grandParent, list, listItem, selection, tail, tip;
      ev.preventDefault();
      if (this.content.isWhitespace()) {
        this.parent().unindent();
        return;
      }
      ContentSelect.Range.query(this._domElement);
      selection = ContentSelect.Range.query(this._domElement);
      tip = this.content.substring(0, selection.get()[0]);
      tail = this.content.substring(selection.get()[1]);
      if (tip.length() + tail.length() === 0) {
        this.parent().unindent();
        return;
      }
      this.content = tip.trim();
      this.updateInnerHTML();
      grandParent = this.parent().parent();
      listItem = new ContentEdit.ListItem(this.attr('class') ? {
        'class': this.attr('class')
      } : {});
      grandParent.attach(listItem, grandParent.children.indexOf(this.parent()) + 1);
      listItem.attach(new ContentEdit.ListItemText(tail.trim()));
      list = this.parent().list();
      if (list) {
        this.parent().detach(list);
        listItem.attach(list);
      }
      if (tip.length()) {
        listItem.listItemText().focus();
        selection = new ContentSelect.Range(0, 0);
        return selection.select(listItem.listItemText().domElement());
      } else {
        selection = new ContentSelect.Range(0, tip.length());
        return selection.select(this._domElement);
      }
    };

    ListItemText.droppers = {
      'ListItemText': function (element, target, placement) {
        var elementParent, insertIndex, listItem, targetParent;
        elementParent = element.parent();
        targetParent = target.parent();
        elementParent.remove();
        elementParent.detach(element);
        listItem = new ContentEdit.ListItem(elementParent._attributes);
        listItem.attach(element);
        if (targetParent.list() && placement[0] === 'below') {
          targetParent.list().attach(listItem, 0);
          return;
        }
        insertIndex = targetParent.parent().children.indexOf(targetParent);
        if (placement[0] === 'below') {
          insertIndex += 1;
        }
        return targetParent.parent().attach(listItem, insertIndex);
      },
      'Text': function (element, target, placement) {
        var cssClass, insertIndex, listItem, targetParent, text;
        if (element.type() === 'Text') {
          targetParent = target.parent();
          element.parent().detach(element);
          cssClass = element.attr('class');
          listItem = new ContentEdit.ListItem(cssClass ? {
            'class': cssClass
          } : {});
          listItem.attach(new ContentEdit.ListItemText(element.content));
          if (targetParent.list() && placement[0] === 'below') {
            targetParent.list().attach(listItem, 0);
            return;
          }
          insertIndex = targetParent.parent().children.indexOf(targetParent);
          if (placement[0] === 'below') {
            insertIndex += 1;
          }
          targetParent.parent().attach(listItem, insertIndex);
          listItem.listItemText().focus();
          if (element._savedSelection) {
            return element._savedSelection.select(listItem.listItemText().domElement());
          }
        } else {
          cssClass = element.attr('class');
          text = new ContentEdit.Text('p', cssClass ? {
            'class': cssClass
          } : {}, element.content);
          element.parent().remove();
          insertIndex = target.parent().children.indexOf(target);
          if (placement[0] === 'below') {
            insertIndex += 1;
          }
          target.parent().attach(text, insertIndex);
          text.focus();
          if (element._savedSelection) {
            return element._savedSelection.select(text.domElement());
          }
        }
      }
    };

    ListItemText.mergers = {
      'ListItemText': function (element, target) {
        var offset;
        offset = target.content.length();
        if (element.content.length()) {
          target.content = target.content.concat(element.content);
        }
        if (target.isMounted()) {
          target._domElement.innerHTML = target.content.html();
        }
        target.focus();
        new ContentSelect.Range(offset, offset).select(target._domElement);
        if (element.type() === 'Text') {
          if (element.parent()) {
            element.parent().detach(element);
          }
        } else {
          element.parent().remove();
        }
        return target.taint();
      }
    };

    return ListItemText;

  })(ContentEdit.Text);

  _mergers = ContentEdit.ListItemText.mergers;

  _mergers['Text'] = _mergers['ListItemText'];

  ContentEdit.Table = (function (_super) {
    __extends(Table, _super);

    function Table(attributes) {
      Table.__super__.constructor.call(this, 'table', attributes);
    }

    Table.prototype.cssTypeName = function () {
      return 'table';
    };

    Table.prototype.typeName = function () {
      return 'Table';
    };

    Table.prototype.type = function () {
      return 'Table';
    };

    Table.prototype.firstSection = function () {
      var section;
      if (section = this.thead()) {
        return section;
      } else if (section = this.tbody()) {
        return section;
      } else if (section = this.tfoot()) {
        return section;
      }
      return null;
    };

    Table.prototype.lastSection = function () {
      var section;
      if (section = this.tfoot()) {
        return section;
      } else if (section = this.tbody()) {
        return section;
      } else if (section = this.thead()) {
        return section;
      }
      return null;
    };

    Table.prototype.tbody = function () {
      return this._getChild('tbody');
    };

    Table.prototype.tfoot = function () {
      return this._getChild('tfoot');
    };

    Table.prototype.thead = function () {
      return this._getChild('thead');
    };

    Table.prototype._onMouseOver = function (ev) {
      Table.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    Table.prototype._getChild = function (tagName) {
      var child, _i, _len, _ref;
      _ref = this.children;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        child = _ref[_i];
        if (child.tagName() === tagName) {
          return child;
        }
      }
      return null;
    };

    Table.droppers = {
      'Image': ContentEdit.Element._dropBoth,
      'List': ContentEdit.Element._dropVert,
      'PreText': ContentEdit.Element._dropVert,
      'Static': ContentEdit.Element._dropVert,
      'Table': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert,
      'Video': ContentEdit.Element._dropBoth
    };

    Table.fromDOMElement = function (domElement) {
      var c, childNode, childNodes, orphanRows, row, section, table, tagName, _i, _j, _len, _len1;
      table = new this(this.getDOMElementAttributes(domElement));
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      })();
      orphanRows = [];
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        tagName = childNode.tagName.toLowerCase();
        if (table._getChild(tagName)) {
          continue;
        }
        switch (tagName) {
          case 'tbody':
          case 'tfoot':
          case 'thead':
            section = ContentEdit.TableSection.fromDOMElement(childNode);
            table.attach(section);
            break;
          case 'tr':
            orphanRows.push(ContentEdit.TableRow.fromDOMElement(childNode));
        }
      }
      if (orphanRows.length > 0) {
        if (!table._getChild('tbody')) {
          table.attach(new ContentEdit.TableSection('tbody'));
        }
        for (_j = 0, _len1 = orphanRows.length; _j < _len1; _j++) {
          row = orphanRows[_j];
          table.tbody().attach(row);
        }
      }
      if (table.children.length === 0) {
        return null;
      }
      return table;
    };

    return Table;

  })(ContentEdit.ElementCollection);

  ContentEdit.TagNames.get().register(ContentEdit.Table, 'table');

  ContentEdit.TableSection = (function (_super) {
    __extends(TableSection, _super);

    function TableSection(tagName, attributes) {
      TableSection.__super__.constructor.call(this, tagName, attributes);
    }

    TableSection.prototype.cssTypeName = function () {
      return 'table-section';
    };

    TableSection.prototype.type = function () {
      return 'TableSection';
    };

    TableSection.prototype._onMouseOver = function (ev) {
      TableSection.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    TableSection.fromDOMElement = function (domElement) {
      var c, childNode, childNodes, section, _i, _len;
      section = new this(domElement.tagName, this.getDOMElementAttributes(domElement));
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      })();
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        if (childNode.tagName.toLowerCase() !== 'tr') {
          continue;
        }
        section.attach(ContentEdit.TableRow.fromDOMElement(childNode));
      }
      return section;
    };

    return TableSection;

  })(ContentEdit.ElementCollection);

  ContentEdit.TableRow = (function (_super) {
    __extends(TableRow, _super);

    function TableRow(attributes) {
      TableRow.__super__.constructor.call(this, 'tr', attributes);
    }

    TableRow.prototype.cssTypeName = function () {
      return 'table-row';
    };

    TableRow.prototype.isEmpty = function () {
      var cell, text, _i, _len, _ref;
      _ref = this.children;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        cell = _ref[_i];
        text = cell.tableCellText();
        if (text && text.content.length() > 0) {
          return false;
        }
      }
      return true;
    };

    TableRow.prototype.type = function () {
      return 'TableRow';
    };

    TableRow.prototype.typeName = function () {
      return 'Table row';
    };

    TableRow.prototype._onMouseOver = function (ev) {
      TableRow.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    TableRow.droppers = {
      'TableRow': ContentEdit.Element._dropVert
    };

    TableRow.fromDOMElement = function (domElement) {
      var c, childNode, childNodes, row, tagName, _i, _len;
      row = new this(this.getDOMElementAttributes(domElement));
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = domElement.childNodes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      })();
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        tagName = childNode.tagName.toLowerCase();
        if (!(tagName === 'td' || tagName === 'th')) {
          continue;
        }
        row.attach(ContentEdit.TableCell.fromDOMElement(childNode));
      }
      return row;
    };

    return TableRow;

  })(ContentEdit.ElementCollection);

  ContentEdit.TableCell = (function (_super) {
    __extends(TableCell, _super);

    function TableCell(tagName, attributes) {
      TableCell.__super__.constructor.call(this, tagName, attributes);
    }

    TableCell.prototype.cssTypeName = function () {
      return 'table-cell';
    };

    TableCell.prototype.tableCellText = function () {
      if (this.children.length > 0) {
        return this.children[0];
      }
      return null;
    };

    TableCell.prototype.type = function () {
      return 'TableCell';
    };

    TableCell.prototype.html = function (indent) {
      var lines;
      if (indent == null) {
        indent = '';
      }
      lines = ["" + indent + "<" + (this.tagName()) + (this._attributesToString()) + ">"];
      if (this.tableCellText()) {
        lines.push(this.tableCellText().html(indent + ContentEdit.INDENT));
      }
      lines.push("" + indent + "</" + (this.tagName()) + ">");
      return lines.join('\n');
    };

    TableCell.prototype._onMouseOver = function (ev) {
      TableCell.__super__._onMouseOver.call(this, ev);
      return this._removeCSSClass('ce-element--over');
    };

    TableCell.prototype._addDOMEventListeners = function () {};

    TableCell.prototype._removeDOMEventListners = function () {};

    TableCell.fromDOMElement = function (domElement) {
      var tableCell, tableCellText;
      tableCell = new this(domElement.tagName, this.getDOMElementAttributes(domElement));
      tableCellText = new ContentEdit.TableCellText(domElement.innerHTML.replace(/^\s+|\s+$/g, ''));
      tableCell.attach(tableCellText);
      return tableCell;
    };

    return TableCell;

  })(ContentEdit.ElementCollection);

  ContentEdit.TableCellText = (function (_super) {
    __extends(TableCellText, _super);

    function TableCellText(content) {
      TableCellText.__super__.constructor.call(this, 'div', {}, content);
    }

    TableCellText.prototype.cssTypeName = function () {
      return 'table-cell-text';
    };

    TableCellText.prototype.type = function () {
      return 'TableCellText';
    };

    TableCellText.prototype._isInFirstRow = function () {
      var cell, row, section, table;
      cell = this.parent();
      row = cell.parent();
      section = row.parent();
      table = section.parent();
      if (section !== table.firstSection()) {
        return false;
      }
      return row === section.children[0];
    };

    TableCellText.prototype._isInLastRow = function () {
      var cell, row, section, table;
      cell = this.parent();
      row = cell.parent();
      section = row.parent();
      table = section.parent();
      if (section !== table.lastSection()) {
        return false;
      }
      return row === section.children[section.children.length - 1];
    };

    TableCellText.prototype._isLastInSection = function () {
      var cell, row, section;
      cell = this.parent();
      row = cell.parent();
      section = row.parent();
      if (row !== section.children[section.children.length - 1]) {
        return false;
      }
      return cell === row.children[row.children.length - 1];
    };

    TableCellText.prototype.blur = function () {
      if (this.isMounted()) {
        this._domElement.blur();
        this._domElement.removeAttribute('contenteditable');
      }
      return ContentEdit.Element.prototype.blur.call(this);
    };

    TableCellText.prototype.html = function (indent) {
      var content;
      if (indent == null) {
        indent = '';
      }
      if (!this._lastCached || this._lastCached < this._modified) {
        content = this.content.copy().trim();
        content.optimize();
        this._lastCached = Date.now();
        this._cached = content.html();
      }
      return "" + indent + this._cached;
    };

    TableCellText.prototype._onMouseDown = function (ev) {
      var initDrag;
      ContentEdit.Element.prototype._onMouseDown.call(this, ev);
      initDrag = (function (_this) {
        return function () {
          var cell, table;
          cell = _this.parent();
          if (ContentEdit.Root.get().dragging() === cell.parent()) {
            ContentEdit.Root.get().cancelDragging();
            table = cell.parent().parent().parent();
            return table.drag(ev.pageX, ev.pageY);
          } else {
            cell.parent().drag(ev.pageX, ev.pageY);
            return _this._dragTimeout = setTimeout(initDrag, ContentEdit.DRAG_HOLD_DURATION * 2);
          }
        };
      })(this);
      clearTimeout(this._dragTimeout);
      return this._dragTimeout = setTimeout(initDrag, ContentEdit.DRAG_HOLD_DURATION);
    };

    TableCellText.prototype._keyBack = function (ev) {
      var cell, previous, row, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(selection.get()[0] === 0 && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      cell = this.parent();
      row = cell.parent();
      if (this.content.length() === 0 && row.children.indexOf(cell) === 0) {
        if (row.isEmpty()) {
          previous = this.previousContent();
          if (previous) {
            previous.focus();
            selection = new ContentSelect.Range(previous.content.length(), previous.content.length());
            selection.select(previous.domElement());
          }
          return row.parent().detach(row);
        }
      }
    };

    TableCellText.prototype._keyDelete = function (ev) {
      var lastChild, nextElement, row, selection;
      row = this.parent().parent();
      if (row.isEmpty()) {
        ev.preventDefault();
        lastChild = row.children[row.children.length - 1];
        nextElement = lastChild.tableCellText().nextContent();
        if (nextElement) {
          nextElement.focus();
          selection = new ContentSelect.Range(0, 0);
          selection.select(nextElement.domElement());
        }
        return row.parent().detach(row);
      }
    };

    TableCellText.prototype._keyDown = function (ev) {
      var cell, cellIndex, lastCell, next, nextRow, row;
      ev.preventDefault();
      cell = this.parent();
      if (this._isInLastRow()) {
        row = cell.parent();
        lastCell = row.children[row.children.length - 1].tableCellText();
        next = lastCell.nextContent();
        if (next) {
          return next.focus();
        } else {
          return ContentEdit.Root.get().trigger('next-region', this.closest(function (node) {
            return node.type() === 'Region';
          }));
        }
      } else {
        nextRow = cell.parent().nextWithTest(function (node) {
          return node.type() === 'TableRow';
        });
        cellIndex = cell.parent().children.indexOf(cell);
        cellIndex = Math.min(cellIndex, nextRow.children.length);
        return nextRow.children[cellIndex].tableCellText().focus();
      }
    };

    TableCellText.prototype._keyReturn = function (ev) {
      ev.preventDefault();
      return this._keyTab({
        'shiftKey': false,
        'preventDefault': function () {}
      });
    };

    TableCellText.prototype._keyTab = function (ev) {
      var cell, child, grandParent, newCell, newCellText, row, section, _i, _len, _ref;
      ev.preventDefault();
      cell = this.parent();
      if (ev.shiftKey) {
        if (this._isInFirstRow() && cell.parent().children[0] === cell) {
          return;
        }
        return this.previousContent().focus();
      } else {
        grandParent = cell.parent().parent();
        if (grandParent.tagName() === 'tbody' && this._isLastInSection()) {
          row = new ContentEdit.TableRow();
          _ref = cell.parent().children;
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            child = _ref[_i];
            newCell = new ContentEdit.TableCell(child.tagName(), child._attributes);
            newCellText = new ContentEdit.TableCellText('');
            newCell.attach(newCellText);
            row.attach(newCell);
          }
          section = this.closest(function (node) {
            return node.type() === 'TableSection';
          });
          section.attach(row);
          return row.children[0].tableCellText().focus();
        } else {
          return this.nextContent().focus();
        }
      }
    };

    TableCellText.prototype._keyUp = function (ev) {
      var cell, cellIndex, previous, previousRow, row;
      ev.preventDefault();
      cell = this.parent();
      if (this._isInFirstRow()) {
        row = cell.parent();
        previous = row.children[0].previousContent();
        if (previous) {
          return previous.focus();
        } else {
          return ContentEdit.Root.get().trigger('previous-region', this.closest(function (node) {
            return node === 'Region';
          }));
        }
      } else {
        previousRow = cell.parent().previousWithTest(function (node) {
          return node.type() === 'TableRow';
        });
        cellIndex = cell.parent().children.indexOf(cell);
        cellIndex = Math.min(cellIndex, previousRow.children.length);
        return previousRow.children[cellIndex].tableCellText().focus();
      }
    };

    TableCellText.droppers = {};

    TableCellText.mergers = {};

    return TableCellText;

  })(ContentEdit.Text);

}).call(this);
/*
 * Overwrite Region to add `region-ready` event
 */
(function () {
  var __hasProp = {}.hasOwnProperty,
          __extends = function (child, parent) {
            for (var key in parent) {
              if (__hasProp.call(parent, key))
                child[key] = parent[key];
            }
            function ctor() {
              this.constructor = child;
            }
            ctor.prototype = parent.prototype;
            child.prototype = new ctor();
            child.__super__ = parent.prototype;
            return child;
          };

  ContentEdit.ElementCollection.prototype.html = function (indent) {
    var c, children;
    if (indent == null) {
      indent = '';
    }

    var _i, _len, _ref, _results;
    _ref = this.children;
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      c = _ref[_i];
      if (c._domElement.hasAttribute('edit-mode-only')) {
        continue;
      }

      _results.push(c.html(indent + ContentEdit.INDENT));
    }

    return ("" + indent + "<" + (this.tagName()) + (this._attributesToString()) + ">\n") + ("" + (_results.join('\n')) + "\n") + ("" + indent + "</" + (this.tagName()) + ">");
  };

  ContentEdit.Region = (function (_super) {
    __extends(Region, _super);

    function Region(domElement) {
      var c, childNode, childNodes, cls, element, tagNames, _i, _len;
      Region.__super__.constructor.call(this);
      this._domElement = domElement;
      tagNames = ContentEdit.TagNames.get();
      childNodes = (function () {
        var _i, _len, _ref, _results;
        _ref = this._domElement.childNodes;
        _results = [
        ];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c);
        }
        return _results;
      }).call(this);
      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        if (childNode.getAttribute("data-ce-tag")) {
          cls = tagNames.match(childNode.getAttribute("data-ce-tag"));
        } else {
          cls = tagNames.match(childNode.tagName);
        }
        element = cls.fromDOMElement(childNode);
        this._domElement.removeChild(childNode);
        if (element) {
          this.attach(element);
        }
      }
      ContentEdit.Root.get().trigger('region-ready', this);
    }

    Region.prototype.domElement = function () {
      return this._domElement;
    };

    Region.prototype.isMounted = function () {
      return true;
    };

    Region.prototype.type = function () {
      return 'Region';
    };

    Region.prototype.html = function (indent) {
      var c;
      if (indent == null) {
        indent = '';
      }
      return ((function () {
        var _i, _len, _ref, _results;
        _ref = this.children;
        _results = [
        ];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          c = _ref[_i];
          _results.push(c.html(indent));
        }
        return _results;
      }).call(this)).join('\n').trim();
    };

    return Region;

  })(ContentEdit.NodeCollection);



  ContentEdit.Image = (function (_super) {
    __extends(Image, _super);

    function Image(attributes, a) {
      var size;

      Image.__super__.constructor.call(this, 'img', attributes);
      this.a = a ? a : null;
      size = this.size();
      this._aspectRatio = size[1] / size[0];
    }

    Image.prototype.cssTypeName = function () {
      return 'image';
    };

    Image.prototype.type = function () {
      return 'Image';
    };

    Image.prototype.typeName = function () {
      return 'Image';
    };

    Image.prototype.createDraggingDOMElement = function () {
      var helper;
      if (!this.isMounted()) {
        return;
      }
      helper = Image.__super__.createDraggingDOMElement.call(this);
      helper.style.backgroundImage = "url(" + this._attributes['src'] + ")";
      return helper;
    };

    Image.prototype.html = function (indent) {
      var attributes, img;
      if (indent == null) {
        indent = '';
      }
      this._attributes['alt'] = this._attributes['alt'] || '';

      img = "" + indent + "<img" + (this._attributesToString()) + ">";
      if (this.a) {
        this.a['data-ce-tag'] = 'img';
        if (this._attributes['content-field']) {
          this.a['content-field'] = this._attributes['content-field'];
        }

        var newAttributes = {};
        for (var attr in this._attributes) {
          if (this._attributes.hasOwnProperty(attr) && attr !== 'content-field') {
            newAttributes[attr] = this._attributes[attr];
          }
        }

        img = "" + indent + "<img " + ContentEdit.attributesToString(newAttributes) + ">";
        attributes = ContentEdit.attributesToString(this.a);
        attributes = "" + attributes;
        return ("" + indent + "<a " + attributes + ">\n") + ("" + ContentEdit.INDENT + img + "\n") + ("" + indent + "</a>");
      } else {
        return img;
      }
    };

    Image.prototype.mount = function () {
      var classes, style;

      this._domElement = document.createElement('div');
      if (this._attributes['content-field']) {
        this._domElement.setAttribute('content-field', this._attributes['content-field']);
      }
      var img = document.createElement('img');

      img.style.display = 'block';
      //img.style.height = '100%';

      classes = '';
      if (this.a && this.a['class']) {
        classes += ' ' + this.a['class'];
      }
      if (this._attributes['class']) {
        classes += ' ' + this._attributes['class'];
      }
      this._domElement.setAttribute('class', classes);
      style = this._attributes['style'] ? this._attributes['style'] : '';
      //style += "background-image:url(" + this._attributes['src'] + ");";
      if (this._attributes['width']) {
        style += "width:" + this._attributes['width'] + "px;";
      }
      if (this._attributes['height']) {
        //style += "height:" + this._attributes['height'] + "px;";
      }
      this._domElement.setAttribute('style', style);
      img.src = this._attributes['src'];
      this._domElement.appendChild(img);
      return Image.__super__.mount.call(this);
    };

    Image.droppers = {
      'Image': ContentEdit.Element._dropBoth,
      'PreText': ContentEdit.Element._dropBoth,
      'Static': ContentEdit.Element._dropBoth,
      'Text': ContentEdit.Element._dropBoth
    };

    Image.placements = ['above',
      'below',
      'left',
      'right',
      'center'];

    Image.fromDOMElement = function (domElement) {
      var a, attributes, c, childNode, childNodes, _i, _len;
      a = null;
      if (domElement.tagName.toLowerCase() === 'a') {
        a = this.getDOMElementAttributes(domElement);
        childNodes = (function () {
          var _i, _len, _ref, _results;
          _ref = domElement.childNodes;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            _results.push(c);
          }
          return _results;
        })();
        for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
          childNode = childNodes[_i];
          if (childNode.nodeType === 1 && childNode.tagName.toLowerCase() === 'img') {
            domElement = childNode;
            break;
          }
        }
        if (domElement.tagName.toLowerCase() === 'a') {
          domElement = document.createElement('img');
        }
      }
      attributes = this.getDOMElementAttributes(domElement);
      if (attributes['width'] === void 0) {
        if (attributes['height'] === void 0) {
          attributes['width'] = domElement.naturalWidth;
        } else {
          attributes['width'] = domElement.clientWidth;
        }
      }
      if (attributes['height'] === void 0) {
        if (attributes['width'] === void 0) {
          attributes['height'] = domElement.naturalHeight;
        } else {
          attributes['height'] = domElement.clientHeight;
        }
      }

      if (a && a['content-field']) {
        attributes['content-field'] = a['content-field'];
        delete a['content-field'];
      }

      return new this(attributes, a);
    };

    return Image;

  })(ContentEdit.ResizableElement);

  ContentEdit.TagNames.get().register(ContentEdit.Image, 'img');

  // ------ //

  ContentEdit.Text = (function (_super) {
    __extends(Text, _super);

    function Text(tagName, attributes, content) {
      Text.__super__.constructor.call(this, tagName, attributes);
      if (content instanceof HTMLString.String) {
        this.content = content;
      } else {
        this.content = new HTMLString.String(content).trim();
      }
    }

    Text.prototype.cssTypeName = function () {
      return 'text';
    };

    Text.prototype.type = function () {
      return 'Text';
    };

    Text.prototype.typeName = function () {
      return 'Text';
    };

    Text.prototype.blur = function () {
      var error;
      if (this.isMounted()) {
        this._syncContent();
      }
      if (this.content.isWhitespace()) {
        if (this.parent()) {
          this.parent().detach(this);
        }
      } else if (this.isMounted()) {
        try {
          this._domElement.blur();
        } catch (_error) {
          error = _error;
        }
        this._domElement.removeAttribute('contenteditable');
      }
      return Text.__super__.blur.call(this);
    };

    Text.prototype.createDraggingDOMElement = function () {
      var helper, text;
      if (!this.isMounted()) {
        return;
      }
      helper = Text.__super__.createDraggingDOMElement.call(this);
      text = HTMLString.String.encode(this._domElement.textContent);
      if (text.length > ContentEdit.HELPER_CHAR_LIMIT) {
        text = text.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      helper.innerHTML = text;
      return helper;
    };

    Text.prototype.drag = function (x, y) {
      this.storeState();
      this._domElement.removeAttribute('contenteditable');
      return Text.__super__.drag.call(this, x, y);
    };

    Text.prototype.drop = function (element, placement) {
      Text.__super__.drop.call(this, element, placement);
      return this.restoreState();
    };

    Text.prototype.focus = function (supressDOMFocus) {
      if (this.isMounted()) {
        this._domElement.setAttribute('contenteditable', '');
      }
      return Text.__super__.focus.call(this, supressDOMFocus);
    };

    Text.prototype.html = function (indent) {
      var content;
      if (indent == null) {
        indent = '';
      }
      if (!this._lastCached || this._lastCached < this._modified) {
        content = this.content.copy().trim();
        content.optimize();
        this._lastCached = Date.now();
        this._cached = content.html();
      }

      if (!this._cached || this._cached.length === 0) {
        return '';
      }

      return ("" + indent + "<" + this._tagName + (this._attributesToString()) + ">\n") + ("" + indent + ContentEdit.INDENT + this._cached + "\n") + ("" + indent + "</" + this._tagName + ">");
    };

    Text.prototype.mount = function () {
      var name, value, _ref;
      this._domElement = document.createElement(this._tagName);
      this._domElement.setAttribute('dir', 'auto');
      _ref = this._attributes;
      for (name in _ref) {
        value = _ref[name];
        this._domElement.setAttribute(name, value);
      }
      this.updateInnerHTML();
      return Text.__super__.mount.call(this);
    };

    Text.prototype.restoreState = function () {
      if (!this._savedSelection) {
        return;
      }
      if (!(this.isMounted() && this.isFocused())) {
        this._savedSelection = void 0;
        return;
      }
      this._domElement.setAttribute('contenteditable', '');
      this._addCSSClass('ce-element--focused');
      if (document.activeElement !== this.domElement()) {
        this.domElement().focus();
      }
      this._savedSelection.select(this._domElement);
      return this._savedSelection = void 0;
    };

    Text.prototype.selection = function (selection) {
      if (selection === void 0) {
        if (this.isMounted()) {
          return ContentSelect.Range.query(this._domElement);
        } else {
          return new ContentSelect.Range(0, 0);
        }
      }
      return selection.select(this._domElement);
    };

    Text.prototype.storeState = function () {
      if (!(this.isMounted() && this.isFocused())) {
        return;
      }
      return this._savedSelection = ContentSelect.Range.query(this._domElement);
    };

    Text.prototype.updateInnerHTML = function () {
      this._domElement.innerHTML = this.content.html();
      ContentSelect.Range.prepareElement(this._domElement);
      return this._flagIfEmpty();
    };

    Text.prototype._onKeyDown = function (ev) {
      switch (ev.keyCode) {
        case 40:
          return this._keyDown(ev);
        case 37:
          return this._keyLeft(ev);
        case 39:
          return this._keyRight(ev);
        case 38:
          return this._keyUp(ev);
        case 9:
          return this._keyTab(ev);
        case 8:
          return this._keyBack(ev);
        case 46:
          return this._keyDelete(ev);
        case 13:
          return this._keyReturn(ev);
      }
    };

    Text.prototype._onKeyUp = function (ev) {
      Text.__super__._onKeyUp.call(this, ev);
      return this._syncContent();
    };

    Text.prototype._onMouseDown = function (ev) {
      Text.__super__._onMouseDown.call(this, ev);
      clearTimeout(this._dragTimeout);
      this._dragTimeout = setTimeout((function (_this) {
        return function () {
          return _this.drag(ev.pageX, ev.pageY);
        };
      })(this), ContentEdit.DRAG_HOLD_DURATION);
      if (this.content.length() === 0 && ContentEdit.Root.get().focused() === this) {
        ev.preventDefault();
        if (document.activeElement !== this._domElement) {
          this._domElement.focus();
        }
        return new ContentSelect.Range(0, 0).select(this._domElement);
      }
    };

    Text.prototype._onMouseMove = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseMove.call(this, ev);
    };

    Text.prototype._onMouseOut = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseOut.call(this, ev);
    };

    Text.prototype._onMouseUp = function (ev) {
      if (this._dragTimeout) {
        clearTimeout(this._dragTimeout);
      }
      return Text.__super__._onMouseUp.call(this, ev);
    };

    Text.prototype._keyBack = function (ev) {
      var previous, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(selection.get()[0] === 0 && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      previous = this.previousContent();
      this._syncContent();
      if (previous) {
        return previous.merge(this);
      }
    };

    Text.prototype._keyDelete = function (ev) {
      var next, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(this._atEnd(selection) && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      next = this.nextContent();
      if (next) {
        return this.merge(next);
      }
    };

    Text.prototype._keyDown = function (ev) {
      return this._keyRight(ev);
    };

    Text.prototype._keyLeft = function (ev) {
      var previous, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(selection.get()[0] === 0 && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      previous = this.previousContent();
      if (previous) {
        previous.focus();
        selection = new ContentSelect.Range(previous.content.length(), previous.content.length());
        return selection.select(previous.domElement());
      } else {
        return ContentEdit.Root.get().trigger('previous-region', this.closest(function (node) {
          return node.type() === 'Region';
        }));
      }
    };

    Text.prototype._keyReturn = function (ev) {
      var element, insertAt, lineBreakStr, selection, tail, tip;
      ev.preventDefault();
      if (this.content.isWhitespace()) {
        return;
      }
      ContentSelect.Range.query(this._domElement);
      selection = ContentSelect.Range.query(this._domElement);
      tip = this.content.substring(0, selection.get()[0]);
      tail = this.content.substring(selection.get()[1]);
      if (ev.shiftKey) {
        insertAt = selection.get()[0];
        lineBreakStr = '<br>';
        if (this.content.length() === insertAt) {
          if (!this.content.characters[insertAt - 1].isTag('br')) {
            lineBreakStr = '<br><br>';
          }
        }
        this.content = this.content.insert(insertAt, new HTMLString.String(lineBreakStr, true), true);
        this.updateInnerHTML();
        insertAt += 1;
        selection = new ContentSelect.Range(insertAt, insertAt);
        selection.select(this.domElement());
        return;
      }
      this.content = tip.trim();
      this.updateInnerHTML();
      element = new this.constructor('p', {}, tail.trim());
      this.parent().attach(element, this.parent().children.indexOf(this) + 1);
      if (tip.length()) {
        element.focus();
        selection = new ContentSelect.Range(0, 0);
        selection.select(element.domElement());
      } else {
        selection = new ContentSelect.Range(0, tip.length());
        selection.select(this._domElement);
      }
      return this.taint();
    };

    Text.prototype._keyRight = function (ev) {
      var next, selection;
      selection = ContentSelect.Range.query(this._domElement);
      if (!(this._atEnd(selection) && selection.isCollapsed())) {
        return;
      }
      ev.preventDefault();
      next = this.nextContent();
      if (next) {
        next.focus();
        selection = new ContentSelect.Range(0, 0);
        return selection.select(next.domElement());
      } else {
        return ContentEdit.Root.get().trigger('next-region', this.closest(function (node) {
          return node.type() === 'Region';
        }));
      }
    };

    Text.prototype._keyTab = function (ev) {
      return ev.preventDefault();
    };

    Text.prototype._keyUp = function (ev) {
      return this._keyLeft(ev);
    };

    Text.prototype._atEnd = function (selection) {
      var atEnd;
      atEnd = selection.get()[0] === this.content.length();
      if (selection.get()[0] === this.content.length() - 1 && this.content.characters[this.content.characters.length - 1].isTag('br')) {
        atEnd = true;
      }
      return atEnd;
    };

    Text.prototype._flagIfEmpty = function () {
      if (this.content.length() === 0) {
        return this._addCSSClass('ce-element--empty');
      } else {
        return this._removeCSSClass('ce-element--empty');
      }
    };

    Text.prototype._syncContent = function (ev) {
      var newSnapshot, snapshot;
      snapshot = this.content.html();
      this.content = new HTMLString.String(this._domElement.innerHTML, this.content.preserveWhitespace());
      newSnapshot = this.content.html();
      if (snapshot !== newSnapshot) {
        this.taint();
      }
      return this._flagIfEmpty();
    };

    Text.droppers = {
      'Static': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert
    };

    Text.mergers = {
      'Text': function (element, target) {
        var offset;
        offset = target.content.length();
        if (element.content.length()) {
          target.content = target.content.concat(element.content);
        }
        if (target.isMounted()) {
          target.updateInnerHTML();
        }
        target.focus();
        new ContentSelect.Range(offset, offset).select(target._domElement);
        if (element.parent()) {
          element.parent().detach(element);
        }
        return target.taint();
      }
    };

    Text.fromDOMElement = function (domElement) {
      return new this(domElement.tagName, this.getDOMElementAttributes(domElement), domElement.innerHTML.replace(/^\s+|\s+$/g, ''));
    };

    return Text;

  })(ContentEdit.Element);

  ContentEdit.TagNames.get().register(ContentEdit.Text, 'address', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'a');

  function Resizable(element) {
    var _this = this;
    _this.element = element;
    _this.resizing = false;
    _this.threshold = 10;
    _this.counter = 0;

    this.resizerTool = document.createElement('span');
    this.resizerTool.classList.add('inline-tool--div-resize');

    element.appendChild(this.resizerTool);

    this.resizerTool.addEventListener('mousedown', function (event) {
      _this.start(event.x, event.y);
    });

    window.addEventListener('mousemove', _this.move.bind(this));

    window.addEventListener('mouseup', function (event) {
      _this.stop();
    });
  }

  Resizable.prototype.start = function (x, y) {
    var _this = this;
    this.oldX = this.startX = x;
    this.starty = y;

    var defaultWidth = this.element.clientWidth;
    this.element.style.width = '100%';

    window.requestAnimationFrame(function () {
      _this.maxWidth = _this.element.clientWidth;
      _this.element.style.width = defaultWidth + 'px';

      var width = Math.round((defaultWidth * 100) / _this.maxWidth);

      if (_this.orginalWidth !== width) {
        _this.orginalWidth = width;
        _this.element.style.width = _this.orginalWidth + '%';
      }

      _this.resizing = true;
    });

  };

  Resizable.prototype.move = function (event) {
    if (!this.resizing) {
      return;
    }

    this.oldX = event.x;
    var newWidth = this.orginalWidth + (Math.round(((event.x - this.startX) * 100) / this.maxWidth));

    if (this.oldWidth !== newWidth && newWidth >= 1 && newWidth <= 100) {
      this.element.style.width = newWidth + '%';
    }
  };

  Resizable.prototype.stop = function (x, y) {
    this.resizing = false;
  };

  ContentEdit.Div = (function (_super) {
    __extends(Div, _super);

    function Div(attributes, child) {
      Div.__super__.constructor.call(this, 'div', attributes);
      this.child = child;
//      this.content = new HTMLString.String('').trim();

      if (child) {
        var content = new ContentEdit.Text('p');
        this.attach(content);
      }
    }

    Div.prototype.setupTools = function () {
//      new Resizable(this._domElement, this.parent()._domElement);
      var _this = this;

      var toolsBar = document.createElement('div');
      toolsBar.setAttribute('edit-mode-only', true);
      toolsBar.classList.add('ce-element-tools-bar');
      toolsBar.addEventListener('click', function () {
        var root;
        root = ContentEdit.Root.get();
        if (_this.isFocused()) {
          return;
        }
        if (root.focused()) {
          root.focused().blur();
        }
        _this._addCSSClass('ce-element--focused');
        root._focused = _this;

        root.trigger('focus', _this);
      });

      var remove = document.createElement('button');
      remove.className = 'btn i-remove';
      remove.type = 'button';
      remove.addEventListener('click', function () {
        _this._parent.detach(_this);
      });
      toolsBar.appendChild(remove);

      if (this._domElement.classList.contains('flex-box')) {
        var direction = document.createElement('button');
        direction.innerText = 'Row';
        direction.className = 'btn btn-text';
        direction.type = 'button';
        direction.addEventListener('click', function () {
          if (_this._domElement.classList.contains('flex-box-column')) {
            _this.removeCSSClass('flex-box-column');
            _this.addCSSClass('flex-box-row');
            direction.innerText = 'Row';
          } else {
            _this.removeCSSClass('flex-box-row');
            _this.addCSSClass('flex-box-column');
            direction.innerText = 'Column';
          }
        });
        toolsBar.appendChild(direction);
      }

      this._domElement.insertBefore(toolsBar, this._domElement.firstChild);
      this._toolsBar = toolsBar;
    };

    Div.prototype.cssTypeName = function () {
      return 'div';
    };

    Div.prototype.type = function () {
      return 'Div';
    };

    Div.prototype.typeName = function () {
      return 'Div';
    };

    Div.prototype.mount = function () {
      Div.__super__.mount.call(this);

      if (!this._parent._domElement.classList.contains('flex-box')) {
        this.addCSSClass('flex-box');
      }

      this.setupTools();
    };


    Div.fromDOMElement = function (dom) {
      var div = new Div(this.getDOMElementAttributes(dom));

      var childNode, childNodes, cls, element, tagNames, _i, _len;
      tagNames = ContentEdit.TagNames.get();
      childNodes = dom.childNodes || [];

      for (_i = 0, _len = childNodes.length; _i < _len; _i++) {
        childNode = childNodes[_i];
        if (childNode.nodeType !== 1) {
          continue;
        }
        if (childNode.getAttribute("data-ce-tag")) {
          cls = tagNames.match(childNode.getAttribute("data-ce-tag"));
        } else {
          cls = tagNames.match(childNode.tagName);
        }
        element = cls.fromDOMElement(childNode);
        //this._domElement.removeChild(childNode);
        if (element) {
          div.attach(element);
        }
      }

      return div;
    };

    Div.prototype.focus = function () {
      var root;
      var _this = this;
      root = ContentEdit.Root.get();

      if (_this.isFocused()) {
        return;
      }

      if (root._focused && _this._domElement.contains(root._focused._domElement)) {
        return;
      }

      if (root.focused()) {
        root.focused().blur();
      }

      _this._addCSSClass('ce-element--focused');
      root._focused = _this;

      if (_this.isMounted()) {
        _this.domElement().focus();
      }

      return root.trigger('focus', _this);
    };

    Div.prototype.blur = function (param) {
      var root;
      root = ContentEdit.Root.get();
      if (this.isFocused()) {
        this._removeCSSClass('ce-element--focused');
        root._focused = null;
        return root.trigger('blur', this);
      }
    };

    Div.prototype._onMouseDown = function (ev) {
      if (ev.target !== this._domElement && ev.target !== this._toolsBar) {
        return;
      }

      clearTimeout(this._dragTimeout);
      this._dragTimeout = setTimeout((function (_this) {
        return function () {
          return _this.drag(ev.pageX, ev.pageY);
        };
      })(this), ContentEdit.DRAG_HOLD_DURATION);
    };

    Div.prototype._onMouseUp = function (ev) {
      Div.__super__._onMouseDown.call(this, ev);
      clearTimeout(this._dragTimeout);
    };

    Div.droppers = {
      'Static': ContentEdit.Element._dropVert,
      'Text': ContentEdit.Element._dropVert,
      'Div': ContentEdit.Element._dropVert,
      'Image': ContentEdit.Element._dropVert
    };

    Div.prototype.createDraggingDOMElement = function () {
      var helper, text;
      if (!this.isMounted()) {
        return;
      }
      helper = Div.__super__.createDraggingDOMElement.call(this);
      text = this._domElement.textContent;
      if (text.length > ContentEdit.HELPER_CHAR_LIMIT) {
        text = text.substr(0, ContentEdit.HELPER_CHAR_LIMIT);
      }
      helper.innerHTML = text;
      return helper;
    };

    Div.prototype._onOver = function (ev) {
      var dragging, root;
      this._addCSSClass('ce-element--over');
      root = ContentEdit.Root.get();
      dragging = root.dragging();
      if (!dragging) {
        return;
      }

      if (dragging === this || dragging._parent === this) {
        return;
      }

      if (root._dropTarget) {
        return;
      }

      if (this.constructor.droppers[dragging.type()] || dragging.constructor.droppers[this.type()]) {
        this._addCSSClass('ce-element--drop');
        return root._dropTarget = this;
      }
    };

    return Div;

  })(ContentEdit.ElementCollection);

  ContentEdit.TagNames.get().register(ContentEdit.Div, 'div');

})(this);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// Generated by CoffeeScript 1.10.0
(function () {
  window.ContentTools = {
    Tools: {},
    DEFAULT_TOOLS: [
      [
        'bold',
        'italic',
        'link',
        'align-left',
        'align-center',
        'align-right'
      ],
      [
        'heading',
        'heading2',
        'paragraph',
        'preformatted',
        'link-block',
        'unordered-list',
        'ordered-list',
        'table',
        'indent',
        'unindent',
        'line-break'
      ],
      [
        //'image-link',
        'ew-media',
        'image',
        'video',
        'widget-embed',        
        'content-field',
        'flex-box'
      ],
      [
        'undo',
        'redo',
        'remove'
      ]
    ],
    DEFAULT_VIDEO_HEIGHT: 300,
    DEFAULT_VIDEO_WIDTH: 400,
    HIGHLIGHT_HOLD_DURATION: 2000,
    INSPECTOR_IGNORED_ELEMENTS: [
      'ListItemText',
      'Region',
      'TableCellText'
    ],
    IMAGE_UPLOADER: null,
    MIN_CROP: 10,
    RESTRICTED_ATTRIBUTES: {
      'img': [
        'height',
        'src',
        'width',
        'data-ce-max-width',
        'data-ce-min-width'
      ],
      'iframe': [
        'height',
        'width'
      ]
    },
    getEmbedVideoURL: function (url) {
      var domains, i, id, kv, len, m, netloc, params, paramsStr, parser, path, ref;
      domains = {
        'www.youtube.com': 'youtube',
        'youtu.be': 'youtube',
        'vimeo.com': 'vimeo',
        'player.vimeo.com': 'vimeo'
      };
      parser = document.createElement('a');
      parser.href = url;
      netloc = parser.hostname.toLowerCase();
      path = parser.pathname;
      if (path !== null && path.substr(0, 1) !== "/") {
        path = "/" + path;
      }
      params = {};
      paramsStr = parser.search.slice(1);
      ref = paramsStr.split('&');
      for (i = 0, len = ref.length; i < len; i++) {
        kv = ref[i];
        kv = kv.split("=");
        params[kv[0]] = kv[1];
      }
      switch (domains[netloc]) {
        case 'youtube':
          if (path.toLowerCase() === '/watch') {
            if (!params['v']) {
              return null;
            }
            id = params['v'];
          } else {
            m = path.match(/\/([A-Za-z0-9_-]+)$/i);
            if (!m) {
              return null;
            }
            id = m[1];
          }
          return "https://www.youtube.com/embed/" + id;
        case 'vimeo':
          m = path.match(/\/(\w+\/\w+\/){0,1}(\d+)/i);
          if (!m) {
            return null;
          }
          return "https://player.vimeo.com/video/" + m[2];
      }
      return null;
    }
  };

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var slice = [].slice,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.ComponentUI = (function() {
    function ComponentUI() {
      this._bindings = {};
      this._parent = null;
      this._children = [];
      this._domElement = null;
    }

    ComponentUI.prototype.children = function() {
      return this._children.slice();
    };

    ComponentUI.prototype.domElement = function() {
      return this._domElement;
    };

    ComponentUI.prototype.isMounted = function() {
      return this._domElement !== null;
    };

    ComponentUI.prototype.parent = function() {
      return this._parent;
    };

    ComponentUI.prototype.attach = function(component, index) {
      if (component.parent()) {
        component.parent().detach(component);
      }
      component._parent = this;
      if (index !== void 0) {
        return this._children.splice(index, 0, component);
      } else {
        return this._children.push(component);
      }
    };

    ComponentUI.prototype.addCSSClass = function(className) {
      if (!this.isMounted()) {
        return;
      }
      return ContentEdit.addCSSClass(this._domElement, className);
    };

    ComponentUI.prototype.detatch = function(component) {
      var componentIndex;
      componentIndex = this._children.indexOf(component);
      if (componentIndex === -1) {
        return;
      }
      return this._children.splice(componentIndex, 1);
    };

    ComponentUI.prototype.mount = function() {};

    ComponentUI.prototype.removeCSSClass = function(className) {
      if (!this.isMounted()) {
        return;
      }
      return ContentEdit.removeCSSClass(this._domElement, className);
    };

    ComponentUI.prototype.unmount = function() {
      if (!this.isMounted()) {
        return;
      }
      this._removeDOMEventListeners();
      this._domElement.parentNode.removeChild(this._domElement);
      return this._domElement = null;
    };

    ComponentUI.prototype.bind = function(eventName, callback) {
      if (this._bindings[eventName] === void 0) {
        this._bindings[eventName] = [];
      }
      this._bindings[eventName].push(callback);
      return callback;
    };

    ComponentUI.prototype.trigger = function() {
      var args, callback, eventName, j, len, ref, results;
      eventName = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
      if (!this._bindings[eventName]) {
        return;
      }
      ref = this._bindings[eventName];
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        callback = ref[j];
        if (!callback) {
          continue;
        }
        results.push(callback.call.apply(callback, [this].concat(slice.call(args))));
      }
      return results;
    };

    ComponentUI.prototype.unbind = function(eventName, callback) {
      var i, j, len, ref, results, suspect;
      if (!eventName) {
        this._bindings = {};
        return;
      }
      if (!callback) {
        this._bindings[eventName] = void 0;
        return;
      }
      if (!this._bindings[eventName]) {
        return;
      }
      ref = this._bindings[eventName];
      results = [];
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        suspect = ref[i];
        if (suspect === callback) {
          results.push(this._bindings[eventName].splice(i, 1));
        } else {
          results.push(void 0);
        }
      }
      return results;
    };

    ComponentUI.prototype._addDOMEventListeners = function() {};

    ComponentUI.prototype._removeDOMEventListeners = function() {};

    ComponentUI.createDiv = function(classNames, attributes, content) {
      var domElement, name, value;
      domElement = document.createElement('div');
      if (classNames && classNames.length > 0) {
        domElement.setAttribute('class', classNames.join(' '));
      }
      if (attributes) {
        for (name in attributes) {
          value = attributes[name];
          domElement.setAttribute(name, value);
        }
      }
      if (content) {
        domElement.innerHTML = content;
      }
      return domElement;
    };

    return ComponentUI;

  })();

  ContentTools.WidgetUI = (function(superClass) {
    extend(WidgetUI, superClass);

    function WidgetUI() {
      return WidgetUI.__super__.constructor.apply(this, arguments);
    }

    WidgetUI.prototype.attach = function(component, index) {
      WidgetUI.__super__.attach.call(this, component, index);
      if (!this.isMounted()) {
        return component.mount();
      }
    };

    WidgetUI.prototype.detatch = function(component) {
      WidgetUI.__super__.detatch.call(this, component);
      if (this.isMounted()) {
        return component.unmount();
      }
    };

    WidgetUI.prototype.show = function() {
      var fadeIn;
      if (!this.isMounted()) {
        this.mount();
      }
      fadeIn = (function(_this) {
        return function() {
          return _this.addCSSClass('ct-widget--active');
        };
      })(this);
      return setTimeout(fadeIn, 100);
    };

    WidgetUI.prototype.hide = function() {
      var monitorForHidden;
      this.removeCSSClass('ct-widget--active');
      monitorForHidden = (function(_this) {
        return function() {
          if (!window.getComputedStyle) {
            _this.unmount();
            return;
          }
          if (parseFloat(window.getComputedStyle(_this._domElement).opacity) < 0.01) {
            return _this.unmount();
          } else {
            return setTimeout(monitorForHidden, 250);
          }
        };
      })(this);
      if (this.isMounted()) {
        return setTimeout(monitorForHidden, 250);
      }
    };

    return WidgetUI;

  })(ContentTools.ComponentUI);

  ContentTools.AnchoredComponentUI = (function(superClass) {
    extend(AnchoredComponentUI, superClass);

    function AnchoredComponentUI() {
      return AnchoredComponentUI.__super__.constructor.apply(this, arguments);
    }

    AnchoredComponentUI.prototype.mount = function(domParent, before) {
      if (before == null) {
        before = null;
      }
      domParent.insertBefore(this._domElement, before);
      return this._addDOMEventListeners();
    };

    return AnchoredComponentUI;

  })(ContentTools.ComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.FlashUI = (function(superClass) {
    extend(FlashUI, superClass);

    function FlashUI(modifier) {
      FlashUI.__super__.constructor.call(this);
      this.mount(modifier);
    }

    FlashUI.prototype.mount = function(modifier) {
      var monitorForHidden;
      this._domElement = this.constructor.createDiv(['ct-flash', 'ct-flash--active', "ct-flash--" + modifier, 'ct-widget', 'ct-widget--active']);
      FlashUI.__super__.mount.call(this, ContentTools.EditorApp.get().domElement());
      monitorForHidden = (function(_this) {
        return function() {
          if (!window.getComputedStyle) {
            _this.unmount();
            return;
          }
          if (parseFloat(window.getComputedStyle(_this._domElement).opacity) < 0.01) {
            return _this.unmount();
          } else {
            return setTimeout(monitorForHidden, 250);
          }
        };
      })(this);
      return setTimeout(monitorForHidden, 250);
    };

    return FlashUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.IgnitionUI = (function(superClass) {
    extend(IgnitionUI, superClass);

    function IgnitionUI(conf) {
      IgnitionUI.__super__.constructor.call(this);
      this._conf = conf;
      this._busy = false;
    }

    IgnitionUI.prototype.busy = function(busy) {
      if (busy === void 0) {
        return this._busy;
      }
      if (this._busy === busy) {
        return;
      }
      this._busy = busy;
      if (busy) {
        return this.addCSSClass('ct-ignition--busy');
      } else {
        return this.removeCSSClass('ct-ignition--busy');
      }
    };

    IgnitionUI.prototype.changeState = function(state) {
      if (state === 'editing') {
        this.addCSSClass('ct-ignition--editing');
        return this.removeCSSClass('ct-ignition--ready');
      } else if (state === 'ready') {
        this.removeCSSClass('ct-ignition--editing');
        return this.addCSSClass('ct-ignition--ready');
      }
    };

    IgnitionUI.prototype.mount = function() {
      IgnitionUI.__super__.mount.call(this);
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-ignition', 'ct-ignition--ready']);
      this.parent().domElement().appendChild(this._domElement);
      this._domEdit = this.constructor.createDiv(['ct-ignition__button', 'ct-ignition__button--edit']);
      this._domElement.appendChild(this._domEdit);
      this._domConfirm = this.constructor.createDiv(['ct-ignition__button', 'ct-ignition__button--confirm']);
      this._domElement.appendChild(this._domConfirm);
      this._domCancel = this.constructor.createDiv(['ct-ignition__button', 'ct-ignition__button--cancel']);
      this._domElement.appendChild(this._domCancel);
      this._domBusy = this.constructor.createDiv(['ct-ignition__button', 'ct-ignition__button--busy']);
      this._domElement.appendChild(this._domBusy);
      return this._addDOMEventListeners();
    };

    IgnitionUI.prototype.unmount = function() {
      IgnitionUI.__super__.unmount.call(this);
      this._domEdit = null;
      this._domConfirm = null;
      return this._domCancel = null;
    };

    IgnitionUI.prototype._addDOMEventListeners = function() {
      this._domEdit.addEventListener('click', (function(_this) {
        return function(ev) {
          ev.preventDefault();
          _this.addCSSClass('ct-ignition--editing');
          _this.removeCSSClass('ct-ignition--ready');
          return _this.trigger('start');
        };
      })(this));
      this._domConfirm.addEventListener('click', (function(_this) {
        return function(ev) {
          ev.preventDefault();
          _this.removeCSSClass('ct-ignition--editing');
          _this.addCSSClass('ct-ignition--ready');
          return _this.trigger('stop', true);
        };
      })(this));
      return this._domCancel.addEventListener('click', (function(_this) {
        return function(ev) {
          ev.preventDefault();
          _this.removeCSSClass('ct-ignition--editing');
          _this.addCSSClass('ct-ignition--ready');
          return _this.trigger('stop', false);
        };
      })(this));
    };

    return IgnitionUI;

  })(ContentTools.WidgetUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty,
    bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  ContentTools.InspectorUI = (function(superClass) {
    extend(InspectorUI, superClass);

    function InspectorUI() {
      InspectorUI.__super__.constructor.call(this);
      this._tagUIs = [];
    }

    InspectorUI.prototype.mount = function() {
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-top-inspector']);
      this.parent().domElement().appendChild(this._domElement);
      this._domTags = this.constructor.createDiv(['ct-inspector__tags', 'ct-tags']);
      this._domElement.appendChild(this._domTags);
      this._addDOMEventListeners();
      this._handleFocusChange = (function(_this) {
        return function() {
          return _this.updateTags();
        };
      })(this);
      ContentEdit.Root.get().bind('blur', this._handleFocusChange);
      ContentEdit.Root.get().bind('focus', this._handleFocusChange);
      return ContentEdit.Root.get().bind('mount', this._handleFocusChange);
    };

    InspectorUI.prototype.unmount = function() {
      InspectorUI.__super__.unmount.call(this);
      this._domTags = null;
      ContentEdit.Root.get().unbind('blur', this._handleFocusChange);
      ContentEdit.Root.get().unbind('focus', this._handleFocusChange);
      return ContentEdit.Root.get().unbind('mount', this._handleFocusChange);
    };

    InspectorUI.prototype.updateTags = function() {
      var element, elements, i, j, len, len1, ref, results, tag;
      element = ContentEdit.Root.get().focused();
      ref = this._tagUIs;
      for (i = 0, len = ref.length; i < len; i++) {
        tag = ref[i];
        tag.unmount();
      }
      this._tagUIs = [];
      if (!element) {
        return;
      }
      elements = element.parents();
      elements.reverse();
      elements.push(element);
      results = [];
      for (j = 0, len1 = elements.length; j < len1; j++) {
        element = elements[j];
        if (ContentTools.INSPECTOR_IGNORED_ELEMENTS.indexOf(element.constructor.name) !== -1) {
          continue;
        }
        tag = new ContentTools.TagUI(element);
        this._tagUIs.push(tag);
        results.push(tag.mount(this._domTags));
      }
      return results;
    };

    return InspectorUI;

  })(ContentTools.WidgetUI);

  ContentTools.TagUI = (function(superClass) {
    extend(TagUI, superClass);

    function TagUI(element1) {
      this.element = element1;
      this._onMouseDown = bind(this._onMouseDown, this);
      TagUI.__super__.constructor.call(this);
    }

    TagUI.prototype.mount = function(domParent, before) {
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv(['ct-tag']);
      this._domElement.textContent = this.element.tagName();
      return TagUI.__super__.mount.call(this, domParent, before);
    };

    TagUI.prototype._addDOMEventListeners = function() {
      return this._domElement.addEventListener('mousedown', this._onMouseDown);
    };

    TagUI.prototype._onMouseDown = function(ev) {
      var app, dialog, modal;
      ev.preventDefault();
      if (this.element.storeState) {
        this.element.storeState();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI();      
      dialog = new ContentTools.PropertiesDialog(this.element);
      dialog.bind('cancel', (function(_this) {
        return function() {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (_this.element.restoreState) {
            return _this.element.restoreState();
          }
        };
      })(this));
      dialog.bind('save', (function(_this) {
        return function(attributes, styles, innerHTML) {
          var applied, className, classNames, cssClass, element, i, j, len, len1, name, ref, ref1, value;
          dialog.unbind('save');
          for (name in attributes) {
            value = attributes[name];
            if (name === 'class') {
              if (value === null) {
                value = '';
              }
              classNames = {};
              ref = value.split(' ');
              for (i = 0, len = ref.length; i < len; i++) {
                className = ref[i];
                className = className.trim();
                if (!className) {
                  continue;
                }
                classNames[className] = true;
                if (!_this.element.hasCSSClass(className)) {
                  _this.element.addCSSClass(className);
                }
              }
              ref1 = _this.element.attr('class').split(' ');
              for (j = 0, len1 = ref1.length; j < len1; j++) {
                className = ref1[j];
                className = className.trim();
                if (classNames[className] === void 0) {
                  _this.element.removeCSSClass(className);
                }
              }
            } else {
              if (value === null) {
                _this.element.removeAttr(name);
              } else {
                _this.element.attr(name, value);
              }
            }
          }
          for (cssClass in styles) {
            applied = styles[cssClass];
            if (applied) {
              _this.element.addCSSClass(cssClass);
            } else {
              _this.element.removeCSSClass(cssClass);
            }
          }
          if (innerHTML !== null) {
            if (innerHTML !== dialog.getElementInnerHTML()) {
              element = _this.element;
              if (!element.content) {
                element = element.children[0];
              }
              element.content = new HTMLString.String(innerHTML, element.content.preserveWhitespace());
              element.updateInnerHTML();
              element.taint();
              element.selection(new ContentSelect.Range(0, 0));
              element.storeState();
            }
          }
          modal.hide();
          dialog.hide();
          if (_this.element.restoreState) {
            return _this.element.restoreState();
          }
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };

    return TagUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  },
          hasProp = {}.hasOwnProperty,
          bind = function (fn, me) {
            return function () {
              return fn.apply(me, arguments);
            };
          };

  ContentTools.InspectorUI = (function (superClass) {
    extend(InspectorUI, superClass);

    function InspectorUI(app, root) {
      InspectorUI.__super__.constructor.call(this);
      this._tagUIs = [];
      this._app = app;
      this._root = root;
    }

    InspectorUI.prototype.mount = function () {
      var inspectorUI = this;
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-top-inspector']);
      this.parent().domElement().appendChild(this._domElement);
      this._domTags = this.constructor.createDiv(['ct-inspector__tags', 'ct-tags']);
      this._domElement.appendChild(this._domTags);
      this._addDOMEventListeners();
      this._handleFocusChange = function () {
        return inspectorUI.updateTags();
      };

      this._root.bind('blur', this._handleFocusChange);
      this._root.bind('focus', this._handleFocusChange);
      return this._root.bind('mount', this._handleFocusChange);
    };

    InspectorUI.prototype.unmount = function () {
      InspectorUI.__super__.unmount.call(this);
      this._domTags = null;
      this._root.unbind('blur', this._handleFocusChange);
      this._root.unbind('focus', this._handleFocusChange);
      return this._root.unbind('mount', this._handleFocusChange);
    };

    InspectorUI.prototype.updateTags = function () {
      var element, elements, i, j, len, len1, ref, results, tag;
      element = this._root.focused();
      ref = this._tagUIs;
      for (i = 0, len = ref.length; i < len; i++) {
        tag = ref[i];
        tag.unmount();
      }
      this._tagUIs = [];
      if (!element) {
        return;
      }
      elements = element.parents();
      elements.reverse();
      elements.push(element);
      results = [];
      for (j = 0, len1 = elements.length; j < len1; j++) {
        element = elements[j];
        if (ContentTools.INSPECTOR_IGNORED_ELEMENTS.indexOf(element.constructor.name) !== -1) {
          continue;
        }
        tag = new ContentTools.TagUI(element, this._app);
        this._tagUIs.push(tag);
        results.push(tag.mount(this._domTags));
      }
      return results;
    };

    return InspectorUI;

  })(ContentTools.WidgetUI);

  ContentTools.TagUI = (function (superClass) {
    extend(TagUI, superClass);

    function TagUI(element1, app) {
      this.element = element1;
      this._app = app;
      this._onMouseDown = bind(this._onMouseDown, this);
      TagUI.__super__.constructor.call(this);
    }

    TagUI.prototype.mount = function (domParent, before) {
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv(['ct-tag']);
      this._domElement.textContent = this.element.tagName();
      return TagUI.__super__.mount.call(this, domParent, before);
    };

    TagUI.prototype._addDOMEventListeners = function () {
      return this._domElement.addEventListener('mousedown', this._onMouseDown);
    };

    TagUI.prototype._onMouseDown = function (ev) {
      var app, dialog, modal;
      ev.preventDefault();
      if (this.element.storeState) {
        this.element.storeState();
      }
      app = this._app;
      modal = new ContentTools.ModalUI();
      dialog = new ContentTools.PropertiesDialog(this.element);
      dialog.bind('cancel', (function (_this) {
        return function () {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (_this.element.restoreState) {
            return _this.element.restoreState();
          }
        };
      })(this));
      dialog.bind('save', (function (_this) {
        return function (attributes, styles, innerHTML) {
          var applied, className, classNames, cssClass, element, i, j, len, len1, name, ref, ref1, value;
          dialog.unbind('save');
          for (name in attributes) {
            value = attributes[name];
            if (name === 'class') {
              if (value === null) {
                value = '';
              }
              classNames = {};
              ref = value.split(' ');
              for (i = 0, len = ref.length; i < len; i++) {
                className = ref[i];
                className = className.trim();
                if (!className) {
                  continue;
                }
                classNames[className] = true;
                if (!_this.element.hasCSSClass(className)) {
                  _this.element.addCSSClass(className);
                }
              }
              ref1 = _this.element.attr('class').split(' ');
              for (j = 0, len1 = ref1.length; j < len1; j++) {
                className = ref1[j];
                className = className.trim();
                if (classNames[className] === void 0) {
                  _this.element.removeCSSClass(className);
                }
              }
            } else {
              if (value === null) {
                _this.element.removeAttr(name);
              } else {
                _this.element.attr(name, value);
              }
            }
          }
          for (cssClass in styles) {
            applied = styles[cssClass];
            if (applied) {
              _this.element.addCSSClass(cssClass);
            } else {
              _this.element.removeCSSClass(cssClass);
            }
          }
          if (innerHTML !== null) {
            if (innerHTML !== dialog.getElementInnerHTML()) {
              element = _this.element;
              if (!element.content) {
                element = element.children[0];
              }
              element.content = new HTMLString.String(innerHTML, element.content.preserveWhitespace());
              element.updateInnerHTML();
              element.taint();
              element.selection(new ContentSelect.Range(0, 0));
              element.storeState();
            }
          }
          modal.hide();
          dialog.hide();
          if (_this.element.restoreState) {
            return _this.element.restoreState();
          }
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };

    return TagUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.ModalUI = (function(superClass) {
    extend(ModalUI, superClass);

    function ModalUI(transparent, allowScrolling) {
      ModalUI.__super__.constructor.call(this);
      this._transparent = transparent;
      this._allowScrolling = allowScrolling;
    }

    ModalUI.prototype.mount = function() {
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-modal']);
      this.parent()._editorContainer.appendChild(this._domElement);
      if (this._transparent) {
        this.addCSSClass('ct-modal--transparent');
      }
      if (!this._allowScrolling) {
        ContentEdit.addCSSClass(document.body, 'ct--no-scroll');
      }
      return this._addDOMEventListeners();
    };

    ModalUI.prototype.unmount = function() {
      if (!this._allowScrolling) {
        ContentEdit.removeCSSClass(document.body, 'ct--no-scroll');
      }
      return ModalUI.__super__.unmount.call(this);
    };

    ModalUI.prototype._addDOMEventListeners = function() {
      return this._domElement.addEventListener('click', (function(_this) {
        return function(ev) {
          return _this.trigger('click');
        };
      })(this));
    };

    return ModalUI;

  })(ContentTools.WidgetUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function () {
  var bind = function (fn, me) {
    return function () {
      return fn.apply(me, arguments);
    };
  },
          extend = function (child, parent) {
            for (var key in parent) {
              if (hasProp.call(parent, key))
                child[key] = parent[key];
            }
            function ctor() {
              this.constructor = child;
            }
            ctor.prototype = parent.prototype;
            child.prototype = new ctor();
            child.__super__ = parent.prototype;
            return child;
          },
          hasProp = {}.hasOwnProperty;

  ContentTools.ToolboxUI = (function (superClass) {
    extend(ToolboxUI, superClass);

    function ToolboxUI(tools) {
      this._onStopDragging = bind(this._onStopDragging, this);
      this._onStartDragging = bind(this._onStartDragging, this);
      this._onDrag = bind(this._onDrag, this);
      ToolboxUI.__super__.constructor.call(this);
      this._tools = tools;
      this._dragging = false;
      this._draggingOffset = null;
      this._domGrip = null;
      this._toolUIs = {};
    }

    ToolboxUI.prototype.isDragging = function () {
      return this._dragging;
    };

    ToolboxUI.prototype.hide = function () {
      this._removeDOMEventListeners();
      return ToolboxUI.__super__.hide.call(this);
    };

    ToolboxUI.prototype.tools = function (tools) {
      if (tools === void 0) {
        return this._tools;
      }
      this._tools = tools;
      this.unmount();
      return this.mount();
    };

    ToolboxUI.prototype.mount = function () {
      var coord, domToolGroup, i, j, k, len, len1, position, ref, restore, tool, toolGroup, toolName;
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-top-toolbox', 'ce-element--type-static']);
      this.parent().domElement().appendChild(this._domElement);
      this._domGrip = this.constructor.createDiv(['ct-toolbox__grip', 'ct-grip']);
      //this._domElement.appendChild(this._domGrip);
      this._domGrip.appendChild(this.constructor.createDiv(['ct-grip__bump']));
      this._domGrip.appendChild(this.constructor.createDiv(['ct-grip__bump']));
      this._domGrip.appendChild(this.constructor.createDiv(['ct-grip__bump']));
      ref = this._tools;
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        toolGroup = ref[i];
        domToolGroup = this.constructor.createDiv(['ct-tool-group']);
        this._domElement.appendChild(domToolGroup);
        for (k = 0, len1 = toolGroup.length; k < len1; k++) {
          toolName = toolGroup[k];
          tool = ContentTools.ToolShelf.fetch(toolName);
          this._toolUIs[toolName] = new ContentTools.ToolUI(tool);
          this._toolUIs[toolName].mount(domToolGroup);
          this._toolUIs[toolName].disabled(true);
          this._toolUIs[toolName].bind('apply', (function (_this) {
            return function () {
              return _this.updateTools();
            };
          })(this));
        }
      }
      restore = window.localStorage.getItem('ct-toolbox-position');
      if (restore && /^\d+,\d+$/.test(restore)) {
        position = (function () {
          var l, len2, ref1, results;
          ref1 = restore.split(',');
          results = [];
          for (l = 0, len2 = ref1.length; l < len2; l++) {
            coord = ref1[l];
            results.push(parseInt(coord));
          }
          return results;
        })();
        this._domElement.style.left = position[0] + "px";
        this._domElement.style.top = position[1] + "px";
        this._contain();
      }
      return this._addDOMEventListeners();
    };

    ToolboxUI.prototype.updateTools = function () {
      var element, name, ref, results, selection, toolUI;
      element = ContentEdit.Root.get().focused();
      selection = null;
      if (element && element.selection) {
        selection = element.selection();
      }
      ref = this._toolUIs;
      results = [];
      for (name in ref) {
        toolUI = ref[name];
        results.push(toolUI.update(element, selection));
      }
      return results;
    };

    ToolboxUI.prototype.unmount = function () {
      ToolboxUI.__super__.unmount.call(this);
      return this._domGrip = null;
    };

    ToolboxUI.prototype._addDOMEventListeners = function () {
      this._domGrip.addEventListener('mousedown', this._onStartDragging);
      this._handleResize = (function (_this) {
        return function (ev) {
          var containResize;
          if (_this._resizeTimeout) {
            clearTimeout(_this._resizeTimeout);
          }
          containResize = function () {
            return _this._contain();
          };
          return _this._resizeTimeout = setTimeout(containResize, 250);
        };
      })(this);
      window.addEventListener('resize', this._handleResize);
      this._updateTools = (function (_this) {
        return function () {
          var app, element, name, ref, results, selection, toolUI, update;
          app = ContentTools.EditorApp.get();
          update = false;
          element = ContentEdit.Root.get().focused();
          selection = null;
          if (element === _this._lastUpdateElement) {
            if (element && element.selection) {
              selection = element.selection();
              if (_this._lastUpdateSelection && selection.eq(_this._lastUpdateSelection)) {
                update = true;
              }
            }
          } else {
            update = true;
          }
          if (app.history) {
            if (_this._lastUpdateHistoryLength !== app.history.length()) {
              update = true;
            }
            _this._lastUpdateHistoryLength = app.history.length();
          }
          _this._lastUpdateElement = element;
          _this._lastUpdateSelection = selection;
          ref = _this._toolUIs;
          results = [];
          for (name in ref) {
            toolUI = ref[name];
            results.push(toolUI.update(element, selection));
          }
          return results;
        };
      })(this);
      this._updateToolsTimeout = setInterval(this._updateTools, 100);
      this._handleKeyDown = (function (_this) {
        return function (ev) {
          var element, os, redo, undo, version;
          if (ev.keyCode === 46) {
            element = ContentEdit.Root.get().focused();
            if (element && !element.content) {
              ContentTools.Tools.Remove.apply(element, null, function () {});
            }
          }
          version = navigator.appVersion;
          os = 'linux';
          if (version.indexOf('Mac') !== -1) {
            os = 'mac';
          } else if (version.indexOf('Win') !== -1) {
            os = 'windows';
          }
          redo = false;
          undo = false;
          switch (os) {
            case 'linux':
              if (ev.keyCode === 90 && ev.ctrlKey) {
                redo = ev.shiftKey;
                undo = !redo;
              }
              break;
            case 'mac':
              if (ev.keyCode === 90 && ev.metaKey) {
                redo = ev.shiftKey;
                undo = !redo;
              }
              break;
            case 'windows':
              if (ev.keyCode === 89 && ev.ctrlKey) {
                redo = true;
              }
              if (ev.keyCode === 90 && ev.ctrlKey) {
                undo = true;
              }
          }
          if (undo && ContentTools.Tools.Undo.canApply(null, null)) {
            ContentTools.Tools.Undo.apply(null, null, function () {});
          }
          if (redo && ContentTools.Tools.Redo.canApply(null, null)) {
            return ContentTools.Tools.Redo.apply(null, null, function () {});
          }
        };
      })(this);
      return window.addEventListener('keydown', this._handleKeyDown);
    };

    ToolboxUI.prototype._contain = function () {
      var rect;
      if (!this.isMounted()) {
        return;
      }
      rect = this._domElement.getBoundingClientRect();
      if (rect.left + rect.width > window.innerWidth) {
        this._domElement.style.left = (window.innerWidth - rect.width) + "px";
      }
      if (rect.top + rect.height > window.innerHeight) {
        this._domElement.style.top = (window.innerHeight - rect.height) + "px";
      }
      if (rect.left < 0) {
        this._domElement.style.left = '0px';
      }
      if (rect.top < 0) {
        this._domElement.style.top = '0px';
      }
      rect = this._domElement.getBoundingClientRect();
      return window.localStorage.setItem('ct-toolbox-position', rect.left + "," + rect.top);
    };

    ToolboxUI.prototype._removeDOMEventListeners = function () {
      if (this.isMounted()) {
        this._domGrip.removeEventListener('mousedown', this._onStartDragging);
      }
      window.removeEventListener('keydown', this._handleKeyDown);
      window.removeEventListener('resize', this._handleResize);
      window.removeEventListener('resize', this._handleResize);
      return clearInterval(this._updateToolsTimeout);
    };

    ToolboxUI.prototype._onDrag = function (ev) {
      ContentSelect.Range.unselectAll();
      this._domElement.style.left = (ev.clientX - this._draggingOffset.x) + "px";
      return this._domElement.style.top = (ev.clientY - this._draggingOffset.y) + "px";
    };

    ToolboxUI.prototype._onStartDragging = function (ev) {
      var rect;
      ev.preventDefault();
      if (this.isDragging()) {
        return;
      }
      this._dragging = true;
      this.addCSSClass('ct-toolbox--dragging');
      rect = this._domElement.getBoundingClientRect();
      this._draggingOffset = {
        x: ev.clientX - rect.left,
        y: ev.clientY - rect.top
      };
      document.addEventListener('mousemove', this._onDrag);
      document.addEventListener('mouseup', this._onStopDragging);
      return ContentEdit.addCSSClass(document.body, 'ce--dragging');
    };

    ToolboxUI.prototype._onStopDragging = function (ev) {
      if (!this.isDragging()) {
        return;
      }
      this._contain();
      document.removeEventListener('mousemove', this._onDrag);
      document.removeEventListener('mouseup', this._onStopDragging);
      this._draggingOffset = null;
      this._dragging = false;
      this.removeCSSClass('ct-toolbox--dragging');
      return ContentEdit.removeCSSClass(document.body, 'ce--dragging');
    };

    return ToolboxUI;

  })(ContentTools.WidgetUI);

  ContentTools.ToolUI = (function (superClass) {
    extend(ToolUI, superClass);

    function ToolUI(tool) {
      this._onMouseUp = bind(this._onMouseUp, this);
      this._onMouseLeave = bind(this._onMouseLeave, this);
      this._onMouseDown = bind(this._onMouseDown, this);
      this._addDOMEventListeners = bind(this._addDOMEventListeners, this);
      ToolUI.__super__.constructor.call(this);
      this.tool = tool;
      this._mouseDown = false;
      this._disabled = false;
    }

    ToolUI.prototype.apply = function (element, selection) {
      var callback;
      if (!this.tool.canApply(element, selection)) {
        return;
      }
      callback = (function (_this) {
        return function (applied) {
          if (applied) {
            return _this.trigger('apply');
          }
        };
      })(this);
      return this.tool.apply(element, selection, callback);
    };

    ToolUI.prototype.disabled = function (disabledState) {
      if (disabledState === void 0) {
        return this._disabled;
      }
      if (this._disabled === disabledState) {
        return;
      }
      this._disabled = disabledState;
      if (disabledState) {
        this._mouseDown = false;
        this.addCSSClass('ct-tool--disabled');
        return this.removeCSSClass('ct-tool--applied');
      } else {
        return this.removeCSSClass('ct-tool--disabled');
      }
    };

    ToolUI.prototype.mount = function (domParent, before) {
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv(['ct-tool', "ct-tool--" + this.tool.icon]);
      this._domElement.setAttribute('data-tooltip', ContentEdit._(this.tool.label));
      if (this.tool.title)
        this._domElement.innerHTML = this.tool.title;
      return ToolUI.__super__.mount.call(this, domParent, before);
    };

    ToolUI.prototype.update = function (element, selection) {
      if (!(element && element.isMounted())) {
        this.disabled(true);
        return;
      }
      if (this.tool.canApply(element, selection)) {
        this.disabled(false);
      } else {
        this.disabled(true);
        return;
      }
      if (this.tool.isApplied(element, selection)) {
        return this.addCSSClass('ct-tool--applied');
      } else {
        return this.removeCSSClass('ct-tool--applied');
      }
    };

    ToolUI.prototype._addDOMEventListeners = function () {
      this._domElement.addEventListener('mousedown', this._onMouseDown);
      this._domElement.addEventListener('mouseleave', this._onMouseLeave);
      return this._domElement.addEventListener('mouseup', this._onMouseUp);
    };

    ToolUI.prototype._onMouseDown = function (ev) {
      ev.preventDefault();
      if (this.disabled()) {
        return;
      }
      this._mouseDown = true;
      return this.addCSSClass('ct-tool--down');
    };

    ToolUI.prototype._onMouseLeave = function () {
      this._mouseDown = false;
      return this.removeCSSClass('ct-tool--down');
    };

    ToolUI.prototype._onMouseUp = function () {
      var element, selection;
      if (this._mouseDown) {
        element = ContentEdit.Root.get().focused();
        if (!(element && element.isMounted())) {
          return;
        }
        selection = null;
        if (element.selection) {
          selection = element.selection();
        }
        this.apply(element, selection);
      }
      this._mouseDown = false;
      return this.removeCSSClass('ct-tool--down');
    };

    return ToolUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  },
          hasProp = {}.hasOwnProperty;

  ContentTools.AnchoredDialogUI = (function (superClass) {
    extend(AnchoredDialogUI, superClass);

    function AnchoredDialogUI() {
      AnchoredDialogUI.__super__.constructor.call(this);
      this._position = [0, 0];
    }

    AnchoredDialogUI.prototype.mount = function () {
      this._domElement = this.constructor.createDiv(['ct-widget', 'ct-anchored-dialog']);
      this.parent()._editorContainer.appendChild(this._domElement);
      this._domElement.style.top = this._position[1] + "px";
      return this._domElement.style.left = this._position[0] + "px";
    };

    AnchoredDialogUI.prototype.position = function (newPosition) {
      if (newPosition === void 0) {
        return this._position.slice();
      }
      this._position = newPosition.slice();
      if (this.isMounted()) {
        this._domElement.style.top = this._position[1] + "px";
        return this._domElement.style.left = this._position[0] + "px";
      }
    };

    return AnchoredDialogUI;

  })(ContentTools.WidgetUI);

  ContentTools.DialogUI = (function (superClass) {
    extend(DialogUI, superClass);

    function DialogUI(caption) {
      if (caption == null) {
        caption = '';
      }
      DialogUI.__super__.constructor.call(this);
      this._busy = false;
      this._caption = caption;
    }

    DialogUI.prototype.busy = function (busy) {
      if (busy === void 0) {
        return this._busy;
      }
      if (this._busy === busy) {
        return;
      }
      this._busy = busy;
      if (!this.isMounted()) {
        return;
      }
      if (this._busy) {
        return ContentEdit.addCSSClass(this._domElement, 'ct-dialog--busy');
      } else {
        return ContentEdit.removeCSSClass(this._domElement, 'ct-dialog--busy');
      }
    };

    DialogUI.prototype.caption = function (caption) {
      if (caption === void 0) {
        return this._caption;
      }
      this._caption = caption;
      return this._domCaption.textContent = ContentEdit._(caption);
    };

    DialogUI.prototype.mount = function () {
      var dialogCSSClasses, domBody, domHeader;
      dialogCSSClasses = ['ct-widget', 'ct-dialog'];
      if (this._busy) {
        dialogCSSClasses.push('ct-dialog--busy');
      }
      this._domElement = this.constructor.createDiv(dialogCSSClasses);
      this.parent()._editorContainer.appendChild(this._domElement);
      domHeader = this.constructor.createDiv(['ct-dialog__header']);
      this._domElement.appendChild(domHeader);
      this._domCaption = this.constructor.createDiv(['ct-dialog__caption']);
      domHeader.appendChild(this._domCaption);
      this.caption(this._caption);
      this._domClose = this.constructor.createDiv(['ct-dialog__close']);
      domHeader.appendChild(this._domClose);
      domBody = this.constructor.createDiv(['ct-dialog__body']);
      this._domElement.appendChild(domBody);
      this._domView = this.constructor.createDiv(['ct-dialog__view']);
      domBody.appendChild(this._domView);
      this._domControls = this.constructor.createDiv(['ct-dialog__controls']);
      domBody.appendChild(this._domControls);
      this._domBusy = this.constructor.createDiv(['ct-dialog__busy']);
      return this._domElement.appendChild(this._domBusy);
    };

    DialogUI.prototype.unmount = function () {
      DialogUI.__super__.unmount.call(this);
      this._domBusy = null;
      this._domCaption = null;
      this._domClose = null;
      this._domControls = null;
      return this._domView = null;
    };

    DialogUI.prototype._addDOMEventListeners = function () {
      this._handleEscape = (function (_this) {
        return function (ev) {
          if (_this._busy) {
            return;
          }
          if (ev.keyCode === 27) {
            return _this.trigger('cancel');
          }
        };
      })(this);
      document.addEventListener('keyup', this._handleEscape);
      return this._domClose.addEventListener('click', (function (_this) {
        return function (ev) {
          ev.preventDefault();
          if (_this._busy) {
            return;
          }
          return _this.trigger('cancel');
        };
      })(this));
    };

    DialogUI.prototype._removeDOMEventListeners = function () {
      return document.removeEventListener('keyup', this._handleEscape);
    };

    return DialogUI;

  })(ContentTools.WidgetUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function () {
  var CropMarksUI,
    extend = function (child, parent) {
      for (var key in parent) {
        if (hasProp.call(parent, key))
          child[key] = parent[key];
      }
      function ctor() {
        this.constructor = child;
      }
      ctor.prototype = parent.prototype;
      child.prototype = new ctor();
      child.__super__ = parent.prototype;
      return child;
    },
    hasProp = {}.hasOwnProperty;

  ContentTools.ImageDialog = (function (superClass) {
    extend(ImageDialog, superClass);

    function ImageDialog() {
      ImageDialog.__super__.constructor.call(this, 'Insert image');
      this._cropMarks = null;
      this._imageURL = null;
      this._imageSize = null;
      this._progress = 0;
      this._state = 'empty';
      if (ContentTools.IMAGE_UPLOADER) {
        ContentTools.IMAGE_UPLOADER(this);
      }
    }

    ImageDialog.prototype.cropRegion = function () {
      if (this._cropMarks) {
        return this._cropMarks.region();
      }
      return [
        0,
        0,
        1,
        1
      ];
    };

    ImageDialog.prototype.addCropMarks = function () {
      if (this._cropMarks) {
        return;
      }
      this._cropMarks = new CropMarksUI(this._imageSize);
      this._cropMarks.mount(this._domView);
      return ContentEdit.addCSSClass(this._domCrop, 'ct-control--active');
    };

    ImageDialog.prototype.clear = function () {
      if (this._domImage) {
        this._domImage.parentNode.removeChild(this._domImage);
        this._domImage = null;
      }
      this._imageURL = null;
      this._imageSize = null;
      return this.state('empty');
    };

    ImageDialog.prototype.mount = function () {
      var domActions, domProgressBar, domTools;
      ImageDialog.__super__.mount.call(this);
      ContentEdit.addCSSClass(this._domElement, 'ct-image-dialog');
      ContentEdit.addCSSClass(this._domElement, 'ct-image-dialog--empty');
      ContentEdit.addCSSClass(this._domView, 'ct-image-dialog__view');
      domTools = this.constructor.createDiv([
        'ct-control-group',
        'ct-control-group--left'
      ]);
      this._domControls.appendChild(domTools);
      this._domRotateCCW = this.constructor.createDiv([
        'ct-control',
        'ct-control--icon',
        'ct-control--rotate-ccw'
      ]);
      this._domRotateCCW.setAttribute('data-tooltip', ContentEdit._('Rotate') + ' -90Â°');
      domTools.appendChild(this._domRotateCCW);
      this._domRotateCW = this.constructor.createDiv([
        'ct-control',
        'ct-control--icon',
        'ct-control--rotate-cw'
      ]);
      this._domRotateCW.setAttribute('data-tooltip', ContentEdit._('Rotate') + ' 90Â°');
      domTools.appendChild(this._domRotateCW);
      this._domCrop = this.constructor.createDiv([
        'ct-control',
        'ct-control--icon',
        'ct-control--crop'
      ]);
      this._domCrop.setAttribute('data-tooltip', ContentEdit._('Crop marks'));
      domTools.appendChild(this._domCrop);
      domProgressBar = this.constructor.createDiv([
        'ct-progress-bar'
      ]);
      domTools.appendChild(domProgressBar);
      this._domProgress = this.constructor.createDiv([
        'ct-progress-bar__progress'
      ]);
      domProgressBar.appendChild(this._domProgress);
      domActions = this.constructor.createDiv([
        'ct-control-group',
        'ct-control-group--right'
      ]);
      this._domControls.appendChild(domActions);

      this._domURLInput = document.createElement('input');
      this._domURLInput.setAttribute('class', 'ct-image-dialog__input ct-control--fetch');
      this._domURLInput.setAttribute('name', 'url');
      this._domURLInput.setAttribute('placeholder', ContentEdit._('Or paste image URL') + '...');
      this._domURLInput.setAttribute('type', 'text');
      domActions.appendChild(this._domURLInput);

      this._domFetch = this.constructor.createDiv([
        'ct-control',
        'ct-control--text',
        'ct-control--fetch'
      ]);
      this._domFetch.textContent = ContentEdit._('Fetch');
      domActions.appendChild(this._domFetch);

      this._domUpload = this.constructor.createDiv([
        'ct-control',
        'ct-control--text',
        'ct-control--upload'
      ]);
      this._domUpload.textContent = ContentEdit._('Upload');
      domActions.appendChild(this._domUpload);

      this._domInput = document.createElement('input');
      this._domInput.setAttribute('class', 'ct-image-dialog__file-upload');
      this._domInput.setAttribute('name', 'file');
      this._domInput.setAttribute('type', 'file');
      this._domInput.setAttribute('accept', 'image/*');
      this._domUpload.appendChild(this._domInput);

      this._domInsert = this.constructor.createDiv([
        'ct-control',
        'ct-control--text',
        'ct-control--insert'
      ]);
      this._domInsert.textContent = ContentEdit._('Insert');
      domActions.appendChild(this._domInsert);
      this._domCancelUpload = this.constructor.createDiv([
        'ct-control',
        'ct-control--text',
        'ct-control--cancel'
      ]);
      this._domCancelUpload.textContent = ContentEdit._('Cancel');
      domActions.appendChild(this._domCancelUpload);
      this._domClear = this.constructor.createDiv([
        'ct-control',
        'ct-control--text',
        'ct-control--clear'
      ]);
      this._domClear.textContent = ContentEdit._('Clear');
      domActions.appendChild(this._domClear);
      this._addDOMEventListeners();
      return this.trigger('imageUploader.mount');
    };

    ImageDialog.prototype.populate = function (imageURL, imageSize) {
      this._imageURL = imageURL;
      this._imageSize = imageSize;
      if (!this._domImage) {
        this._domImage = this.constructor.createDiv([
          'ct-image-dialog__image'
        ]);
        this._domView.appendChild(this._domImage);
      }
      this._domImage.style['background-image'] = "url(" + imageURL + ")";
      return this.state('populated');
    };

    ImageDialog.prototype.progress = function (progress) {
      if (progress === void 0) {
        return this._progress;
      }
      this._progress = progress;
      if (!this.isMounted()) {
        return;
      }
      return this._domProgress.style.width = this._progress + "%";
    };

    ImageDialog.prototype.removeCropMarks = function () {
      if (!this._cropMarks) {
        return;
      }
      this._cropMarks.unmount();
      this._cropMarks = null;
      return ContentEdit.removeCSSClass(this._domCrop, 'ct-control--active');
    };

    ImageDialog.prototype.save = function (imageURL, imageSize, imageAttrs) {
      return this.trigger('save', imageURL, imageSize, imageAttrs);
    };

    ImageDialog.prototype.fetchImage = function (imageURL) {
      var _this = this;
      // Create image object in order to load image and determine its dimension
      var img = new Image();
      img.onerror = function (e) {
        alert(ContentEdit._('Image is invalid'));
      };

      img.onload = function () {
        _this._imageSize = [
          img.width,
          img.height
        ];
        _this._imageURL = img.src;
        _this.populate(_this._imageURL, _this._imageSize);
      };
      img.src = imageURL;
    };



    ImageDialog.prototype.state = function (state) {
      var prevState;
      if (state === void 0) {
        return this._state;
      }
      if (this._state === state) {
        return;
      }
      prevState = this._state;
      this._state = state;
      if (!this.isMounted()) {
        return;
      }
      ContentEdit.addCSSClass(this._domElement, "ct-image-dialog--" + this._state);
      return ContentEdit.removeCSSClass(this._domElement, "ct-image-dialog--" + prevState);
    };

    ImageDialog.prototype.unmount = function () {
      ImageDialog.__super__.unmount.call(this);
      this._domCancelUpload = null;
      this._domClear = null;
      this._domCrop = null;
      this._domInput = null;
      this._domInsert = null;
      this._domProgress = null;
      this._domRotateCCW = null;
      this._domRotateCW = null;
      this._domUpload = null;
      return this.trigger('imageUploader.unmount');
    };

    ImageDialog.prototype._addDOMEventListeners = function () {
      ImageDialog.__super__._addDOMEventListeners.call(this);

      // when user hit return 
      this._domURLInput.addEventListener('keydown', (function (_this) {
        return function (ev) {
          if (ev.keyCode !== 13 || !_this._domURLInput.value)
            return;

          _this.fetchImage(_this._domURLInput.value);

          return _this.trigger('imageUploader.fetchReady', {});
        };
      })(this));

      this._domFetch.addEventListener('click', (function (_this) {
        return function (ev) {
          if (!_this._domURLInput.value)
            return;

          _this.fetchImage(_this._domURLInput.value);

          return _this.trigger('imageUploader.fetchReady', {});
        };
      })(this));

      this._domInput.addEventListener('change', (function (_this) {
        return function (ev) {
          var file;
          file = ev.target.files[0];
          ev.target.value = '';
          if (ev.target.value) {
            ev.target.type = 'text';
            ev.target.type = 'file';
          }
          return _this.trigger('imageUploader.fileReady', file);
        };
      })(this));
      this._domCancelUpload.addEventListener('click', (function (_this) {
        return function (ev) {
          return _this.trigger('imageUploader.cancelUpload');
        };
      })(this));
      this._domClear.addEventListener('click', (function (_this) {
        return function (ev) {
          _this.removeCropMarks();
          _this.clear();
          return _this.trigger('imageUploader.clear');
        };
      })(this));
      this._domRotateCCW.addEventListener('click', (function (_this) {
        return function (ev) {
          _this.removeCropMarks();
          return _this.trigger('imageUploader.rotateCCW');
        };
      })(this));
      this._domRotateCW.addEventListener('click', (function (_this) {
        return function (ev) {
          _this.removeCropMarks();
          return _this.trigger('imageUploader.rotateCW');
        };
      })(this));
      this._domCrop.addEventListener('click', (function (_this) {
        return function (ev) {
          if (_this._cropMarks) {
            return _this.removeCropMarks();
          } else {
            return _this.addCropMarks();
          }
        };
      })(this));
      return this._domInsert.addEventListener('click', (function (_this) {
        return function (ev) {
          _this.save(_this._imageURL, _this._imageSize, {});
          return _this.trigger('imageUploader.save');
        };
      })(this));
    };

    return ImageDialog;

  })(ContentTools.DialogUI);

  CropMarksUI = (function (superClass) {
    extend(CropMarksUI, superClass);

    function CropMarksUI(imageSize) {
      CropMarksUI.__super__.constructor.call(this);
      this._bounds = null;
      this._dragging = null;
      this._draggingOrigin = null;
      this._imageSize = imageSize;
    }

    CropMarksUI.prototype.mount = function (domParent, before) {
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv([
        'ct-crop-marks'
      ]);
      this._domClipper = this.constructor.createDiv([
        'ct-crop-marks__clipper'
      ]);
      this._domElement.appendChild(this._domClipper);
      this._domRulers = [
        this.constructor.createDiv([
          'ct-crop-marks__ruler',
          'ct-crop-marks__ruler--top-left'
        ]),
        this.constructor.createDiv([
          'ct-crop-marks__ruler',
          'ct-crop-marks__ruler--bottom-right'
        ])
      ];
      this._domClipper.appendChild(this._domRulers[0]);
      this._domClipper.appendChild(this._domRulers[1]);
      this._domHandles = [
        this.constructor.createDiv([
          'ct-crop-marks__handle',
          'ct-crop-marks__handle--top-left'
        ]),
        this.constructor.createDiv([
          'ct-crop-marks__handle',
          'ct-crop-marks__handle--bottom-right'
        ])
      ];
      this._domElement.appendChild(this._domHandles[0]);
      this._domElement.appendChild(this._domHandles[1]);
      CropMarksUI.__super__.mount.call(this, domParent, before);
      return this._fit(domParent);
    };

    CropMarksUI.prototype.region = function () {
      return [
        parseFloat(this._domHandles[0].style.top) / this._bounds[1],
        parseFloat(this._domHandles[0].style.left) / this._bounds[0],
        parseFloat(this._domHandles[1].style.top) / this._bounds[1],
        parseFloat(this._domHandles[1].style.left) / this._bounds[0]
      ];
    };

    CropMarksUI.prototype.unmount = function () {
      CropMarksUI.__super__.unmount.call(this);
      this._domClipper = null;
      this._domHandles = null;
      return this._domRulers = null;
    };

    CropMarksUI.prototype._addDOMEventListeners = function () {
      CropMarksUI.__super__._addDOMEventListeners.call(this);
      this._domHandles[0].addEventListener('mousedown', (function (_this) {
        return function (ev) {
          if (ev.button === 0) {
            return _this._startDrag(0, ev.clientY, ev.clientX);
          }
        };
      })(this));
      return this._domHandles[1].addEventListener('mousedown', (function (_this) {
        return function (ev) {
          if (ev.button === 0) {
            return _this._startDrag(1, ev.clientY, ev.clientX);
          }
        };
      })(this));
    };

    CropMarksUI.prototype._drag = function (top, left) {
      var height, minCrop, offsetLeft, offsetTop, width;
      if (this._dragging === null) {
        return;
      }
      ContentSelect.Range.unselectAll();
      offsetTop = top - this._draggingOrigin[1];
      offsetLeft = left - this._draggingOrigin[0];
      height = this._bounds[1];
      left = 0;
      top = 0;
      width = this._bounds[0];
      minCrop = Math.min(Math.min(ContentTools.MIN_CROP, height), width);
      if (this._dragging === 0) {
        height = parseInt(this._domHandles[1].style.top) - minCrop;
        width = parseInt(this._domHandles[1].style.left) - minCrop;
      } else {
        left = parseInt(this._domHandles[0].style.left) + minCrop;
        top = parseInt(this._domHandles[0].style.top) + minCrop;
      }
      offsetTop = Math.min(Math.max(top, offsetTop), height);
      offsetLeft = Math.min(Math.max(left, offsetLeft), width);
      this._domHandles[this._dragging].style.top = offsetTop + "px";
      this._domHandles[this._dragging].style.left = offsetLeft + "px";
      this._domRulers[this._dragging].style.top = offsetTop + "px";
      return this._domRulers[this._dragging].style.left = offsetLeft + "px";
    };

    CropMarksUI.prototype._fit = function (domParent) {
      var height, heightScale, left, ratio, rect, top, width, widthScale;
      rect = domParent.getBoundingClientRect();
      widthScale = rect.width / this._imageSize[0];
      heightScale = rect.height / this._imageSize[1];
      ratio = Math.min(widthScale, heightScale);
      width = ratio * this._imageSize[0];
      height = ratio * this._imageSize[1];
      left = (rect.width - width) / 2;
      top = (rect.height - height) / 2;
      this._domElement.style.width = width + "px";
      this._domElement.style.height = height + "px";
      this._domElement.style.top = top + "px";
      this._domElement.style.left = left + "px";
      this._domHandles[0].style.top = '0px';
      this._domHandles[0].style.left = '0px';
      this._domHandles[1].style.top = height + "px";
      this._domHandles[1].style.left = width + "px";
      this._domRulers[0].style.top = '0px';
      this._domRulers[0].style.left = '0px';
      this._domRulers[1].style.top = height + "px";
      this._domRulers[1].style.left = width + "px";
      return this._bounds = [
        width,
        height
      ];
    };

    CropMarksUI.prototype._startDrag = function (handleIndex, top, left) {
      var domHandle;
      domHandle = this._domHandles[handleIndex];
      this._dragging = handleIndex;
      this._draggingOrigin = [
        left - parseInt(domHandle.style.left),
        top - parseInt(domHandle.style.top)
      ];
      this._onMouseMove = (function (_this) {
        return function (ev) {
          return _this._drag(ev.clientY, ev.clientX);
        };
      })(this);
      document.addEventListener('mousemove', this._onMouseMove);
      this._onMouseUp = (function (_this) {
        return function (ev) {
          return _this._stopDrag();
        };
      })(this);
      return document.addEventListener('mouseup', this._onMouseUp);
    };

    CropMarksUI.prototype._stopDrag = function () {
      document.removeEventListener('mousemove', this._onMouseMove);
      document.removeEventListener('mouseup', this._onMouseUp);
      this._dragging = null;
      return this._draggingOrigin = null;
    };

    return CropMarksUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  },
    hasProp = {}.hasOwnProperty;

  ContentTools.LinkDialog = (function (superClass) {
    var NEW_WINDOW_TARGET;

    extend(LinkDialog, superClass);

    NEW_WINDOW_TARGET = '_blank';

    function LinkDialog(href, target) {
      if (href == null) {
        href = '';
      }
      if (target == null) {
        target = '';
      }
      LinkDialog.__super__.constructor.call(this);
      this._href = href;
      this._target = target;
    }

    LinkDialog.prototype.mount = function () {
      LinkDialog.__super__.mount.call(this);
      this._domInput = document.createElement('input');
      this._domInput.setAttribute('class', 'ct-anchored-dialog__input');
      this._domInput.setAttribute('name', 'href');
      this._domInput.setAttribute('placeholder', ContentEdit._('Enter a link') + '...');
      this._domInput.setAttribute('type', 'text');
      this._domInput.setAttribute('value', this._href);
      this._domElement.appendChild(this._domInput);
      this._domTargetButton = this.constructor.createDiv([
        'ct-anchored-dialog__button ct-btn-target'
      ]);
      
      if (this._target === NEW_WINDOW_TARGET) {
        ContentEdit.addCSSClass(this._domTargetButton, 'active');
      }
      this._domButton = this.constructor.createDiv([
        'ct-anchored-dialog__button'
      ]);
      this._domElement.appendChild(this._domButton);
      this._domElement.appendChild(this._domTargetButton);
      return this._addDOMEventListeners();
    };

    LinkDialog.prototype.save = function () {
      var linkAttr;
      if (!this.isMounted) {
        return this.trigger('save', '');
      }
      linkAttr = {};
      linkAttr.href = this._domInput.value.trim();
      if (this._target) {
        linkAttr.target = this._target;
      }
      return this.trigger('save', linkAttr);
    };

    LinkDialog.prototype.show = function () {
      LinkDialog.__super__.show.call(this);
      this._domInput.focus();
      if (this._href) {
        return this._domInput.select();
      }
    };

    LinkDialog.prototype.unmount = function () {
      if (this.isMounted()) {
        this._domInput.blur();
      }
      LinkDialog.__super__.unmount.call(this);
      this._domButton = null;
      return this._domInput = null;
    };

    LinkDialog.prototype._addDOMEventListeners = function () {
      this._domInput.addEventListener('keypress', (function (_this) {
        return function (ev) {
          if (ev.keyCode === 13) {
            return _this.save();
          }
        };
      })(this));
      this._domTargetButton.addEventListener('click', (function (_this) {
        return function (ev) {
          ev.preventDefault();
          if (_this._target === NEW_WINDOW_TARGET) {
            _this._target = '';
            return ContentEdit.removeCSSClass(_this._domTargetButton, 'active');
          } else {
            _this._target = NEW_WINDOW_TARGET;
            return ContentEdit.addCSSClass(_this._domTargetButton, 'button--active');
          }
        };
      })(this));
      return this._domButton.addEventListener('click', (function (_this) {
        return function (ev) {
          ev.preventDefault();
          return _this.save();
        };
      })(this));
    };

    return LinkDialog;

  })(ContentTools.AnchoredDialogUI);
})();
// Generated by CoffeeScript 1.10.0
(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  },
          hasProp = {}.hasOwnProperty;

  ContentTools.ContentFieldDialog = (function (superClass) {
    extend(ContentFieldDialog, superClass);

    function ContentFieldDialog(initialValue) {
      if (initialValue == null) {
        initialValue = '';
      }
      ContentFieldDialog.__super__.constructor.call(this);
      this._initialValue = initialValue;
    }

    ContentFieldDialog.prototype.mount = function () {
      ContentFieldDialog.__super__.mount.call(this);
      this._domInput = document.createElement('input');
      this._domInput.setAttribute('class', 'ct-anchored-dialog__input');
      this._domInput.setAttribute('name', 'href');
      this._domInput.setAttribute('placeholder', ContentEdit._('Enter a identifier') + '...');
      this._domInput.setAttribute('type', 'text');
      this._domInput.setAttribute('value', this._initialValue);
      this._domElement.appendChild(this._domInput);
      this._domButton = this.constructor.createDiv(['ct-anchored-dialog__button']);
      this._domElement.appendChild(this._domButton);
      return this._addDOMEventListeners();
    };

    ContentFieldDialog.prototype.save = function () {
      if (!this.isMounted) {
        return this.trigger('save', '');
      }
      return this.trigger('save', this._domInput.value.trim());
    };

    ContentFieldDialog.prototype.show = function () {
      ContentFieldDialog.__super__.show.call(this);
      this._domInput.focus();
      if (this._initialValue) {
        return this._domInput.select();
      }
    };

    ContentFieldDialog.prototype.unmount = function () {
      if (this.isMounted()) {
        this._domInput.blur();
      }
      ContentFieldDialog.__super__.unmount.call(this);
      this._domButton = null;
      return this._domInput = null;
    };

    ContentFieldDialog.prototype._addDOMEventListeners = function () {
      this._domInput.addEventListener('keypress', (function (_this) {
        return function (ev) {
          if (ev.keyCode === 13) {
            return _this.save();
          }
        };
      })(this));
      return this._domButton.addEventListener('click', (function (_this) {
        return function (ev) {
          ev.preventDefault();
          return _this.save();
        };
      })(this));
    };

    return ContentFieldDialog;

  })(ContentTools.AnchoredDialogUI);

}).call(this);

(function () {
  var AttributeUI, StyleUI,
          __hasProp = {}.hasOwnProperty,
          __extends = function (child, parent) {
            for (var key in parent) {
              if (__hasProp.call(parent, key))
                child[key] = parent[key];
            }
            function ctor() {
              this.constructor = child;
            }
            ctor.prototype = parent.prototype;
            child.prototype = new ctor();
            child.__super__ = parent.prototype;
            return child;
          };

  ContentTools.PropertiesDialog = (function (_super) {
    __extends(PropertiesDialog, _super);

    function PropertiesDialog(element) {
      var _ref;
      this.element = element;
      PropertiesDialog.__super__.constructor.call(this, 'Properties');
      this._attributeUIs = [];
      this._focusedAttributeUI = null;
      this._styleUIs = [];
      this._supportsCoding = element.content;
      if ((_ref = element.constructor.name) === 'ListItem' || _ref === 'TableCell') {
        this._supportsCoding = true;
      }
    }

    PropertiesDialog.prototype.caption = function (caption) {
      if (caption === void 0) {
        return this._caption;
      }
      this._caption = caption;
      return this._domCaption.textContent = ContentEdit._(caption) + (": " + (this.element.tagName()));
    };

    PropertiesDialog.prototype.changedAttributes = function () {
      var attributeUI, attributes, changedAttributes, name, restricted, value, _i, _len, _ref, _ref1;
      attributes = {};
      changedAttributes = {};
      _ref = this._attributeUIs;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        attributeUI = _ref[_i];
        name = attributeUI.name();
        value = attributeUI.value();
        if (name === '') {
          continue;
        }
        attributes[name.toLowerCase()] = true;
        if (this.element.attr(name) !== value) {
          changedAttributes[name] = value;
        }
      }
      restricted = ContentTools.RESTRICTED_ATTRIBUTES[this.element.tagName()];
      _ref1 = this.element.attributes();
      for (name in _ref1) {
        value = _ref1[name];
        if (restricted && restricted.indexOf(name.toLowerCase()) !== -1) {
          continue;
        }
        if (attributes[name] === void 0) {
          changedAttributes[name] = null;
        }
      }
      return changedAttributes;
    };

    PropertiesDialog.prototype.changedStyles = function () {
      var cssClass, styleUI, styles, _i, _len, _ref;
      styles = {};
      _ref = this._styleUIs;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        styleUI = _ref[_i];
        cssClass = styleUI.style.cssClass();
        if (this.element.hasCSSClass(cssClass) !== styleUI.applied()) {
          styles[cssClass] = styleUI.applied();
        }
      }
      return styles;
    };

    PropertiesDialog.prototype.getElementInnerHTML = function () {
      if (!this._supportsCoding) {
        return null;
      }
      if (this.element.content) {
        return this.element.content.html();
      }
      return this.element.children[0].content.html();
    };

    PropertiesDialog.prototype.mount = function () {
      var attributeNames, attributes, domActions, domTabs, lastTab, name, restricted, style, styleUI, value, _i, _j, _len, _len1, _ref;
      PropertiesDialog.__super__.mount.call(this);
      ContentEdit.addCSSClass(this._domElement, 'ct-properties-dialog');
      ContentEdit.addCSSClass(this._domView, 'ct-properties-dialog__view');
      this._domStyles = this.constructor.createDiv(['ct-properties-dialog__styles']);
      this._domStyles.setAttribute('data-ct-empty', ContentEdit._('No styles available for this tag'));
      this._domView.appendChild(this._domStyles);
      _ref = ContentTools.StylePalette.styles(this.element.tagName());
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        style = _ref[_i];
        styleUI = new StyleUI(style, this.element.hasCSSClass(style.cssClass()));
        this._styleUIs.push(styleUI);
        styleUI.mount(this._domStyles);
      }
      this._domAttributes = this.constructor.createDiv(['ct-properties-dialog__attributes']);
      this._domView.appendChild(this._domAttributes);
      restricted = ContentTools.RESTRICTED_ATTRIBUTES[this.element.tagName()];
      attributes = this.element.attributes();
      attributeNames = [];
      for (name in attributes) {
        value = attributes[name];
        if (restricted && restricted.indexOf(name.toLowerCase()) !== -1) {
          continue;
        }
        attributeNames.push(name);
      }
      attributeNames.sort();
      for (_j = 0, _len1 = attributeNames.length; _j < _len1; _j++) {
        name = attributeNames[_j];
        value = attributes[name];
        this._addAttributeUI(name, value);
      }
      this._addAttributeUI('', '');
      this._domCode = this.constructor.createDiv(['ct-properties-dialog__code']);
      this._domView.appendChild(this._domCode);
      this._domInnerHTML = document.createElement('textarea');
      this._domInnerHTML.setAttribute('class', 'ct-properties-dialog__inner-html');
      this._domInnerHTML.setAttribute('name', 'code');
      this._domInnerHTML.value = this.getElementInnerHTML();
      this._domCode.appendChild(this._domInnerHTML);
      domTabs = this.constructor.createDiv(['ct-control-group', 'ct-control-group--left']);
      this._domControls.appendChild(domTabs);
      this._domStylesTab = this.constructor.createDiv(['ct-control', 'ct-control--icon', 'ct-control--styles']);
      this._domStylesTab.setAttribute('data-tooltip', ContentEdit._('Styles'));
      domTabs.appendChild(this._domStylesTab);
      this._domAttributesTab = this.constructor.createDiv(['ct-control', 'ct-control--icon', 'ct-control--attributes']);
      this._domAttributesTab.setAttribute('data-tooltip', ContentEdit._('Attributes'));
      domTabs.appendChild(this._domAttributesTab);
      this._domCodeTab = this.constructor.createDiv(['ct-control', 'ct-control--icon', 'ct-control--code']);
      this._domCodeTab.setAttribute('data-tooltip', ContentEdit._('Code'));
      domTabs.appendChild(this._domCodeTab);
      if (!this._supportsCoding) {
        ContentEdit.addCSSClass(this._domCodeTab, 'ct-control--muted');
      }
      this._domRemoveAttribute = this.constructor.createDiv(['ct-control', 'ct-control--icon', 'ct-control--remove', 'ct-control--muted']);
      this._domRemoveAttribute.setAttribute('data-tooltip', ContentEdit._('Remove'));
      domTabs.appendChild(this._domRemoveAttribute);
      domActions = this.constructor.createDiv(['ct-control-group', 'ct-control-group--right']);
      this._domControls.appendChild(domActions);
      this._domApply = this.constructor.createDiv(['ct-control', 'ct-control--text', 'ct-control--apply']);
      this._domApply.textContent = ContentEdit._('Apply');
      domActions.appendChild(this._domApply);
      lastTab = window.localStorage.getItem('ct-properties-dialog-tab');
      if (lastTab === 'attributes') {
        ContentEdit.addCSSClass(this._domElement, 'ct-properties-dialog--attributes');
        ContentEdit.addCSSClass(this._domAttributesTab, 'ct-control--active');
      } else if (lastTab === 'code' && this._supportsCoding) {
        ContentEdit.addCSSClass(this._domElement, 'ct-properties-dialog--code');
        ContentEdit.addCSSClass(this._domCodeTab, 'ct-control--active');
      } else {
        ContentEdit.addCSSClass(this._domElement, 'ct-properties-dialog--styles');
        ContentEdit.addCSSClass(this._domStylesTab, 'ct-control--active');
      }
      return this._addDOMEventListeners();
    };

    PropertiesDialog.prototype.save = function () {
      var innerHTML;
      innerHTML = null;
      if (this._supportsCoding) {
        innerHTML = this._domInnerHTML.value;
      }
      return this.trigger('save', this.changedAttributes(), this.changedStyles(), innerHTML);
    };

    PropertiesDialog.prototype._addAttributeUI = function (name, value) {
      var attributeUI, dialog;
      dialog = this;
      attributeUI = new AttributeUI(name, value);
      this._attributeUIs.push(attributeUI);
      attributeUI.bind('blur', function () {
        var index, lastAttributeUI, length;
        dialog._focusedAttributeUI = null;
        ContentEdit.addCSSClass(dialog._domRemoveAttribute, 'ct-control--muted');
        index = dialog._attributeUIs.indexOf(this);
        length = dialog._attributeUIs.length;
        if (this.name() === '' && index < (length - 1)) {
          this.unmount();
          dialog._attributeUIs.splice(index, 1);
        }
        lastAttributeUI = dialog._attributeUIs[length - 1];
        if (lastAttributeUI) {
          if (lastAttributeUI.name() && lastAttributeUI.value()) {
            return dialog._addAttributeUI('', '');
          }
        }
      });
      attributeUI.bind('focus', function () {
        dialog._focusedAttributeUI = this;
        return ContentEdit.removeCSSClass(dialog._domRemoveAttribute, 'ct-control--muted');
      });
      attributeUI.bind('namechange', function () {
        var element, otherAttributeUI, restricted, valid, _i, _len, _ref;
        element = dialog.element;
        name = this.name().toLowerCase();
        restricted = ContentTools.RESTRICTED_ATTRIBUTES[element.tagName()];
        valid = true;
        if (restricted && restricted.indexOf(name) !== -1) {
          valid = false;
        }
        _ref = dialog._attributeUIs;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          otherAttributeUI = _ref[_i];
          if (name === '') {
            continue;
          }
          if (otherAttributeUI === this) {
            continue;
          }
          if (otherAttributeUI.name().toLowerCase() !== name) {
            continue;
          }
          valid = false;
        }
        this.valid(valid);
        if (valid) {
          return ContentEdit.removeCSSClass(dialog._domApply, 'ct-control--muted');
        } else {
          return ContentEdit.addCSSClass(dialog._domApply, 'ct-control--muted');
        }
      });
      attributeUI.mount(this._domAttributes);
      return attributeUI;
    };

    PropertiesDialog.prototype._addDOMEventListeners = function () {
      var selectTab, validateCode,
              _this = this;
      PropertiesDialog.__super__._addDOMEventListeners.call(this);
      selectTab = function (selected) {
        var selectedCap, tab, tabCap, tabs, _i, _len;
        tabs = ['attributes', 'code', 'styles'];
        for (_i = 0, _len = tabs.length; _i < _len; _i++) {
          tab = tabs[_i];
          if (tab === selected) {
            continue;
          }
          tabCap = tab.charAt(0).toUpperCase() + tab.slice(1);
          ContentEdit.removeCSSClass(_this._domElement, "ct-properties-dialog--" + tab);
          ContentEdit.removeCSSClass(_this["_dom" + tabCap + "Tab"], 'ct-control--active');
        }
        selectedCap = selected.charAt(0).toUpperCase() + selected.slice(1);
        ContentEdit.addCSSClass(_this._domElement, "ct-properties-dialog--" + selected);
        ContentEdit.addCSSClass(_this["_dom" + selectedCap + "Tab"], 'ct-control--active');
        return window.localStorage.setItem('ct-properties-dialog-tab', selected);
      };
      this._domStylesTab.addEventListener('mousedown', function () {
        return selectTab('styles');
      });
      this._domAttributesTab.addEventListener('mousedown', function () {
        return selectTab('attributes');
      });
      this._domCodeTab.addEventListener('mousedown', function () {
        return selectTab('code');
      });
      this._domRemoveAttribute.addEventListener('mousedown', function (ev) {
        var index, last;
        ev.preventDefault();
        if (_this._focusedAttributeUI) {
          index = _this._attributeUIs.indexOf(_this._focusedAttributeUI);
          last = index === (_this._attributeUIs.length - 1);
          _this._focusedAttributeUI.unmount();
          _this._attributeUIs.splice(index, 1);
          if (last) {
            return _this._addAttributeUI('', '');
          }
        }
      });
      validateCode = function (ev) {
        var content;
        try {
          content = new HTMLString.String(_this._domInnerHTML.value);
          ContentEdit.removeCSSClass(_this._domInnerHTML, 'ct-properties-dialog__inner-html--invalid');
          return ContentEdit.removeCSSClass(_this._domApply, 'ct-control--muted');
        } catch (_error) {
          ContentEdit.addCSSClass(_this._domInnerHTML, 'ct-properties-dialog__inner-html--invalid');
          return ContentEdit.addCSSClass(_this._domApply, 'ct-control--muted');
        }
      };
      this._domInnerHTML.addEventListener('input', validateCode);
      this._domInnerHTML.addEventListener('propertychange', validateCode);
      return this._domApply.addEventListener('click', function (ev) {
        var cssClass;
        ev.preventDefault();
        cssClass = _this._domApply.getAttribute('class');
        if (cssClass.indexOf('ct-control--muted') === -1) {
          return _this.save();
        }
      });
    };

    return PropertiesDialog;

  })(ContentTools.DialogUI);

  StyleUI = (function (_super) {
    __extends(StyleUI, _super);

    function StyleUI(style, applied) {
      this.style = style;
      StyleUI.__super__.constructor.call(this);
      this._applied = applied;
    }

    StyleUI.prototype.applied = function (applied) {
      if (applied === void 0) {
        return this._applied;
      }
      if (this._applied === applied) {
        return;
      }
      this._applied = applied;
      if (this._applied) {
        return ContentEdit.addCSSClass(this._domElement, 'ct-section--applied');
      } else {
        return ContentEdit.removeCSSClass(this._domElement, 'ct-section--applied');
      }
    };

    StyleUI.prototype.mount = function (domParent, before) {
      var label;
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv(['ct-section']);
      if (this._applied) {
        ContentEdit.addCSSClass(this._domElement, 'ct-section--applied');
      }
      label = this.constructor.createDiv(['ct-section__label']);
      label.textContent = this.style.name();
      this._domElement.appendChild(label);
      this._domElement.appendChild(this.constructor.createDiv(['ct-section__switch']));
      return StyleUI.__super__.mount.call(this, domParent, before);
    };

    StyleUI.prototype._addDOMEventListeners = function () {
      var toggleSection,
              _this = this;
      toggleSection = function (ev) {
        ev.preventDefault();
        if (_this.applied()) {
          return _this.applied(false);
        } else {
          return _this.applied(true);
        }
      };
      return this._domElement.addEventListener('click', toggleSection);
    };

    return StyleUI;

  })(ContentTools.AnchoredComponentUI);

  AttributeUI = (function (_super) {
    __extends(AttributeUI, _super);

    function AttributeUI(name, value) {
      AttributeUI.__super__.constructor.call(this);
      this._initialName = name;
      this._initialValue = value;
    }

    AttributeUI.prototype.name = function () {
      return this._domName.value.trim();
    };

    AttributeUI.prototype.value = function () {
      return this._domValue.value.trim();
    };

    AttributeUI.prototype.mount = function (domParent, before) {
      if (before == null) {
        before = null;
      }
      this._domElement = this.constructor.createDiv(['ct-attribute']);
      this._domName = document.createElement('input');
      this._domName.setAttribute('class', 'ct-attribute__name');
      this._domName.setAttribute('name', 'name');
      this._domName.setAttribute('placeholder', ContentEdit._('Name'));
      this._domName.setAttribute('type', 'text');
      this._domName.setAttribute('value', this._initialName);
      this._domElement.appendChild(this._domName);
      this._domValue = document.createElement('input');
      this._domValue.setAttribute('class', 'ct-attribute__value');
      this._domValue.setAttribute('name', 'value');
      this._domValue.setAttribute('placeholder', ContentEdit._('Value'));
      this._domValue.setAttribute('type', 'text');
      this._domValue.setAttribute('value', this._initialValue);
      this._domElement.appendChild(this._domValue);
      return AttributeUI.__super__.mount.call(this, domParent, before);
    };

    AttributeUI.prototype.valid = function (valid) {
      if (valid) {
        return ContentEdit.removeCSSClass(this._domName, 'ct-attribute__name--invalid');
      } else {
        return ContentEdit.addCSSClass(this._domName, 'ct-attribute__name--invalid');
      }
    };

    AttributeUI.prototype._addDOMEventListeners = function () {
      var _this = this;
      this._domName.addEventListener('blur', function () {
        var name, nextDomAttribute, nextNameDom;
        name = _this.name();
        nextDomAttribute = _this._domElement.nextSibling;
        _this.trigger('blur');
        if (name === '' && nextDomAttribute) {
          nextNameDom = nextDomAttribute.querySelector('.ct-attribute__name');
          return nextNameDom.focus();
        }
      });
      this._domName.addEventListener('focus', function () {
        return _this.trigger('focus');
      });
      this._domName.addEventListener('input', function () {
        return _this.trigger('namechange');
      });
      this._domName.addEventListener('keydown', function (ev) {
        if (ev.keyCode === 13) {
          return _this._domValue.focus();
        }
      });
      this._domValue.addEventListener('blur', function () {
        return _this.trigger('blur');
      });
      this._domValue.addEventListener('focus', function () {
        return _this.trigger('focus');
      });
      return this._domValue.addEventListener('keydown', function (ev) {
        var nextDomAttribute, nextNameDom;
        if (ev.keyCode !== 13 && (ev.keyCode !== 9 || ev.shiftKey)) {
          return;
        }
        ev.preventDefault();
        nextDomAttribute = _this._domElement.nextSibling;
        if (!nextDomAttribute) {
          _this._domValue.blur();
          nextDomAttribute = _this._domElement.nextSibling;
        }
        if (nextDomAttribute) {
          nextNameDom = nextDomAttribute.querySelector('.ct-attribute__name');
          return nextNameDom.focus();
        }
      });
    };

    return AttributeUI;

  })(ContentTools.AnchoredComponentUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.TableDialog = (function(superClass) {
    extend(TableDialog, superClass);

    function TableDialog(table) {
      this.table = table;
      if (this.table) {
        TableDialog.__super__.constructor.call(this, 'Update table');
      } else {
        TableDialog.__super__.constructor.call(this, 'Insert table');
      }
    }

    TableDialog.prototype.mount = function() {
      var cfg, domBodyLabel, domControlGroup, domFootLabel, domHeadLabel, footCSSClasses, headCSSClasses;
      TableDialog.__super__.mount.call(this);
      cfg = {
        columns: 3,
        foot: false,
        head: true
      };
      if (this.table) {
        cfg = {
          columns: this.table.firstSection().children[0].children.length,
          foot: this.table.tfoot(),
          head: this.table.thead()
        };
      }
      ContentEdit.addCSSClass(this._domElement, 'ct-table-dialog');
      ContentEdit.addCSSClass(this._domView, 'ct-table-dialog__view');
      headCSSClasses = ['ct-section'];
      if (cfg.head) {
        headCSSClasses.push('ct-section--applied');
      }
      this._domHeadSection = this.constructor.createDiv(headCSSClasses);
      this._domView.appendChild(this._domHeadSection);
      domHeadLabel = this.constructor.createDiv(['ct-section__label']);
      domHeadLabel.textContent = ContentEdit._('Table head');
      this._domHeadSection.appendChild(domHeadLabel);
      this._domHeadSwitch = this.constructor.createDiv(['ct-section__switch']);
      this._domHeadSection.appendChild(this._domHeadSwitch);
      this._domBodySection = this.constructor.createDiv(['ct-section', 'ct-section--applied', 'ct-section--contains-input']);
      this._domView.appendChild(this._domBodySection);
      domBodyLabel = this.constructor.createDiv(['ct-section__label']);
      domBodyLabel.textContent = ContentEdit._('Table body (columns)');
      this._domBodySection.appendChild(domBodyLabel);
      this._domBodyInput = document.createElement('input');
      this._domBodyInput.setAttribute('class', 'ct-section__input');
      this._domBodyInput.setAttribute('maxlength', '2');
      this._domBodyInput.setAttribute('name', 'columns');
      this._domBodyInput.setAttribute('type', 'text');
      this._domBodyInput.setAttribute('value', cfg.columns);
      this._domBodySection.appendChild(this._domBodyInput);
      footCSSClasses = ['ct-section'];
      if (cfg.foot) {
        footCSSClasses.push('ct-section--applied');
      }
      this._domFootSection = this.constructor.createDiv(footCSSClasses);
      this._domView.appendChild(this._domFootSection);
      domFootLabel = this.constructor.createDiv(['ct-section__label']);
      domFootLabel.textContent = ContentEdit._('Table foot');
      this._domFootSection.appendChild(domFootLabel);
      this._domFootSwitch = this.constructor.createDiv(['ct-section__switch']);
      this._domFootSection.appendChild(this._domFootSwitch);
      domControlGroup = this.constructor.createDiv(['ct-control-group', 'ct-control-group--right']);
      this._domControls.appendChild(domControlGroup);
      this._domApply = this.constructor.createDiv(['ct-control', 'ct-control--text', 'ct-control--apply']);
      this._domApply.textContent = 'Apply';
      domControlGroup.appendChild(this._domApply);
      return this._addDOMEventListeners();
    };

    TableDialog.prototype.save = function() {
      var footCSSClass, headCSSClass, tableCfg;
      footCSSClass = this._domFootSection.getAttribute('class');
      headCSSClass = this._domHeadSection.getAttribute('class');
      tableCfg = {
        columns: parseInt(this._domBodyInput.value),
        foot: footCSSClass.indexOf('ct-section--applied') > -1,
        head: headCSSClass.indexOf('ct-section--applied') > -1
      };
      return this.trigger('save', tableCfg);
    };

    TableDialog.prototype.unmount = function() {
      TableDialog.__super__.unmount.call(this);
      this._domBodyInput = null;
      this._domBodySection = null;
      this._domApply = null;
      this._domHeadSection = null;
      this._domHeadSwitch = null;
      this._domFootSection = null;
      return this._domFootSwitch = null;
    };

    TableDialog.prototype._addDOMEventListeners = function() {
      var toggleSection;
      TableDialog.__super__._addDOMEventListeners.call(this);
      toggleSection = function(ev) {
        ev.preventDefault();
        if (this.getAttribute('class').indexOf('ct-section--applied') > -1) {
          return ContentEdit.removeCSSClass(this, 'ct-section--applied');
        } else {
          return ContentEdit.addCSSClass(this, 'ct-section--applied');
        }
      };
      this._domHeadSection.addEventListener('click', toggleSection);
      this._domFootSection.addEventListener('click', toggleSection);
      this._domBodySection.addEventListener('click', (function(_this) {
        return function(ev) {
          return _this._domBodyInput.focus();
        };
      })(this));
      this._domBodyInput.addEventListener('input', (function(_this) {
        return function(ev) {
          var valid;
          valid = /^[1-9]\d{0,1}$/.test(ev.target.value);
          if (valid) {
            ContentEdit.removeCSSClass(_this._domBodyInput, 'ct-section__input--invalid');
            return ContentEdit.removeCSSClass(_this._domApply, 'ct-control--muted');
          } else {
            ContentEdit.addCSSClass(_this._domBodyInput, 'ct-section__input--invalid');
            return ContentEdit.addCSSClass(_this._domApply, 'ct-control--muted');
          }
        };
      })(this));
      return this._domApply.addEventListener('click', (function(_this) {
        return function(ev) {
          var cssClass;
          ev.preventDefault();
          cssClass = _this._domApply.getAttribute('class');
          if (cssClass.indexOf('ct-control--muted') === -1) {
            return _this.save();
          }
        };
      })(this));
    };

    return TableDialog;

  })(ContentTools.DialogUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  ContentTools.VideoDialog = (function(superClass) {
    extend(VideoDialog, superClass);

    function VideoDialog() {
      VideoDialog.__super__.constructor.call(this, 'Insert video');
    }

    VideoDialog.prototype.clearPreview = function() {
      if (this._domPreview) {
        this._domPreview.parentNode.removeChild(this._domPreview);
        return this._domPreview = void 0;
      }
    };

    VideoDialog.prototype.mount = function() {
      var domControlGroup;
      VideoDialog.__super__.mount.call(this);
      ContentEdit.addCSSClass(this._domElement, 'ct-video-dialog');
      ContentEdit.addCSSClass(this._domView, 'ct-video-dialog__preview');
      domControlGroup = this.constructor.createDiv(['ct-control-group']);
      this._domControls.appendChild(domControlGroup);
      this._domInput = document.createElement('input');
      this._domInput.setAttribute('class', 'ct-video-dialog__input');
      this._domInput.setAttribute('name', 'url');
      this._domInput.setAttribute('placeholder', ContentEdit._('Paste YouTube or Vimeo URL') + '...');
      this._domInput.setAttribute('type', 'text');
      domControlGroup.appendChild(this._domInput);
      this._domButton = this.constructor.createDiv(['ct-control', 'ct-control--text', 'ct-control--insert', 'ct-control--muted']);
      this._domButton.textContent = ContentEdit._('Insert');
      domControlGroup.appendChild(this._domButton);
      return this._addDOMEventListeners();
    };

    VideoDialog.prototype.preview = function(url) {
      this.clearPreview();
      this._domPreview = document.createElement('iframe');
      this._domPreview.setAttribute('frameborder', '0');
      this._domPreview.setAttribute('height', '100%');
      this._domPreview.setAttribute('src', url);
      this._domPreview.setAttribute('width', '100%');
      return this._domView.appendChild(this._domPreview);
    };

    VideoDialog.prototype.save = function() {
      var embedURL, videoURL;
      videoURL = this._domInput.value.trim();
      embedURL = ContentTools.getEmbedVideoURL(videoURL);
      if (embedURL) {
        return this.trigger('save', embedURL);
      } else {
        return this.trigger('save', videoURL);
      }
    };

    VideoDialog.prototype.show = function() {
      VideoDialog.__super__.show.call(this);
      return this._domInput.focus();
    };

    VideoDialog.prototype.unmount = function() {
      if (this.isMounted()) {
        this._domInput.blur();
      }
      VideoDialog.__super__.unmount.call(this);
      this._domButton = null;
      this._domInput = null;
      return this._domPreview = null;
    };

    VideoDialog.prototype._addDOMEventListeners = function() {
      VideoDialog.__super__._addDOMEventListeners.call(this);
      this._domInput.addEventListener('input', (function(_this) {
        return function(ev) {
          var updatePreview;
          if (ev.target.value) {
            ContentEdit.removeCSSClass(_this._domButton, 'ct-control--muted');
          } else {
            ContentEdit.addCSSClass(_this._domButton, 'ct-control--muted');
          }
          if (_this._updatePreviewTimeout) {
            clearTimeout(_this._updatePreviewTimeout);
          }
          updatePreview = function() {
            var embedURL, videoURL;
            videoURL = _this._domInput.value.trim();
            embedURL = ContentTools.getEmbedVideoURL(videoURL);
            if (embedURL) {
              return _this.preview(embedURL);
            } else {
              return _this.clearPreview();
            }
          };
          return _this._updatePreviewTimeout = setTimeout(updatePreview, 500);
        };
      })(this));
      this._domInput.addEventListener('keypress', (function(_this) {
        return function(ev) {
          if (ev.keyCode === 13) {
            return _this.save();
          }
        };
      })(this));
      return this._domButton.addEventListener('click', (function(_this) {
        return function(ev) {
          var cssClass;
          ev.preventDefault();
          cssClass = _this._domButton.getAttribute('class');
          if (cssClass.indexOf('ct-control--muted') === -1) {
            return _this.save();
          }
        };
      })(this));
    };

    return VideoDialog;

  })(ContentTools.DialogUI);

}).call(this);

// Generated by CoffeeScript 1.10.0
(function () {
  var _EditorApp,
          extend = function (child, parent) {
            for (var key in parent) {
              if (hasProp.call(parent, key))
                child[key] = parent[key];
            }
            function ctor() {
              this.constructor = child;
            }
            ctor.prototype = parent.prototype;
            child.prototype = new ctor();
            child.__super__ = parent.prototype;
            return child;
          },
          hasProp = {}.hasOwnProperty,
          slice = [
          ].slice;

  _EditorApp = (function (superClass) {
    extend(_EditorApp, superClass);

    function _EditorApp() {
      _EditorApp.__super__.constructor.call(this);
      this.history = null;
      this._state = ContentTools.EditorApp.DORMANT;
      this._regions = null;
      this._orderedRegions = null;
      this._rootLastModified = null;
      this._regionsLastModified = {};
      this._ignition = null;
      this._inspector = null;
      this._toolbox = null;
      this._updatePositionInterval = null;
      this._editorContainer = null;
      this._contentContainer = null;
      this.oldRect = {};
      this._root = null;
    }

    _EditorApp.prototype.ctrlDown = function () {
      return this._ctrlDown;
    };

    _EditorApp.prototype.domRegions = function () {
      return this._domRegions;
    };

    _EditorApp.prototype.orderedRegions = function () {
      var name;
      return (function () {
        var j, len, ref, results;
        ref = this._orderedRegions;
        results = [
        ];
        for (j = 0, len = ref.length; j < len; j++) {
          name = ref[j];
          results.push(this._regions[name]);
        }
        return results;
      }).call(this);
    };

    _EditorApp.prototype.regions = function () {
      return this._regions;
    };

    _EditorApp.prototype.shiftDown = function () {
      return this._shiftDown;
    };

    _EditorApp.prototype.busy = function (busy) {
      //return this._ignition.busy(busy);
      return false;
    };

    _EditorApp.prototype.init = function (queryOrDOMElements, namingProp) {
      if (namingProp == null) {
        namingProp = 'id';
      }
      this._namingProp = namingProp;
      if (queryOrDOMElements.length > 0 && queryOrDOMElements[0].nodeType === Node.ELEMENT_NODE) {
        this._domRegions = queryOrDOMElements;
      } else {
        this._domRegions = document.querySelectorAll(queryOrDOMElements);
      }
      if (this._domRegions.length === 0) {
        return;
      }
      this.mount();
      this._toolbox = new ContentTools.ToolboxUI(ContentTools.DEFAULT_TOOLS);
      this.attach(this._toolbox);
      this._inspector = new ContentTools.InspectorUI(this, ContentEdit.Root.get());
      this.attach(this._inspector);
      this._state = ContentTools.EditorApp.READY;

      ContentEdit.Root.get().unbind('detach');
      ContentEdit.Root.get().unbind('paste');
      ContentEdit.Root.get().unbind('next-region');
      ContentEdit.Root.get().unbind('previous-region');

      ContentEdit.Root.get().bind('detach', (function (_this) {
        return function (element) {
          return _this._preventEmptyRegions();
        };
      })(this));
      ContentEdit.Root.get().bind('paste', (function (_this) {
        return function (element, ev) {
          return _this.paste(element, ev.clipboardData);
        };
      })(this));
      ContentEdit.Root.get().bind('next-region', (function (_this) {
        return function (region) {
          var child, element, index, j, len, ref, regions;
          regions = _this.orderedRegions();
          index = regions.indexOf(region);
          if (index >= (regions.length - 1)) {
            return;
          }
          region = regions[index + 1];
          element = null;
          ref = region.descendants();
          for (j = 0, len = ref.length; j < len; j++) {
            child = ref[j];
            if (child.content !== void 0) {
              element = child;
              break;
            }
          }
          if (element) {
            element.focus();
            element.selection(new ContentSelect.Range(0, 0));
            return;
          }
          return ContentEdit.Root.get().trigger('next-region', region);
        };
      })(this));
      return ContentEdit.Root.get().bind('previous-region', (function (_this) {
        return function (region) {
          var child, descendants, element, index, j, len, length, regions;
          regions = _this.orderedRegions();
          index = regions.indexOf(region);
          if (index <= 0) {
            return;
          }
          region = regions[index - 1];
          element = null;
          descendants = region.descendants();
          descendants.reverse();
          for (j = 0, len = descendants.length; j < len; j++) {
            child = descendants[j];
            if (child.content !== void 0) {
              element = child;
              break;
            }
          }
          if (element) {
            length = element.content.length();
            element.focus();
            element.selection(new ContentSelect.Range(length, length));
            return;
          }
          return ContentEdit.Root.get().trigger('previous-region', region);
        };
      })(this));
    };

    _EditorApp.prototype.destroy = function () {
      this.stop();
      return this.unmount();
    };

    _EditorApp.prototype.highlightRegions = function (highlight) {
      var domRegion, j, len, ref, results;
      ref = this._domRegions;
      results = [
      ];
      for (j = 0, len = ref.length; j < len; j++) {
        domRegion = ref[j];
        if (highlight) {
          results.push(ContentEdit.addCSSClass(domRegion, 'ct--highlight'));
        } else {
          results.push(ContentEdit.removeCSSClass(domRegion, 'ct--highlight'));
        }
      }
      return results;
    };

    _EditorApp.prototype.mount = function () {
      var _this = this;
      this._domElement = this.constructor.createDiv([
        'ct-app'
      ]);
      this._domElement.style.display = "none";
      this._domElement.style.position = "absolute";

      if (this._domRegions.length === 1) {
        this._editorContainer = this._domRegions[0];
        var html = this._editorContainer.innerHTML;

        ContentEdit.addCSSClass(this._editorContainer, 'ct-editor-container');

        this._editorContainer.innerHTML = '';
        this._editorContainer.appendChild(this._domElement);

        this._contentContainer = this.constructor.createDiv([
          'ct-content-container'
        ]);
        this._contentContainer.setAttribute('dir', 'auto');
        this._contentContainer.innerHTML = html;

        this._editorContainer.appendChild(this._contentContainer);

        this._domRegions = [
          this._contentContainer
        ];
        this.oldRect = {};
        this._updatePositionInterval = setInterval(function () {
          _this.updateToolboxPosition();
        }, 300);

      } else {
        document.body.insertBefore(this._domElement, null);
      }
      return this._addDOMEventListeners();
    };

    _EditorApp.prototype.updateToolboxPosition = function () {
      if (!this._domRegions) {
        return;
      }

      var rect = this._editorContainer.getBoundingClientRect();
      if (rect.width <= 0 || rect.height <= 0) {
        this._domElement.style.display = "none";
        return;
      }

      this._domElement.style.display = "";
      var toolBarRect = this._domElement.getBoundingClientRect();

      if ((this.oldRect.top === toolBarRect.top && this.oldRect.height === toolBarRect.height) || toolBarRect.height <= 0) {
        return;
      }

      this._contentContainer.style.marginTop = toolBarRect.height + 'px';
      this._domElement.style.width = rect.width + 'px';
      this.oldRect = toolBarRect;
      ContentEdit.Root.get().trigger('update-position', this);
    };

    _EditorApp.prototype.paste = function (element, clipboardData) {
      var className, content, cursor, encodeHTML, i, insertAt, insertIn, insertNode, item, itemText, j, lastItem, len, line, lineLength, lines, selection, tail, tip;
      content = clipboardData.getData('text/plain');
      lines = content.split('\n');
      lines = lines.filter(function (line) {
        return line.trim() !== '';
      });
      if (!lines) {
        return;
      }
      encodeHTML = HTMLString.String.encode;
      className = element.constructor.name;
      if ((lines.length > 1 || !element.content) && className !== 'PreText') {
        if (className === 'ListItemText') {
          insertNode = element.parent();
          insertIn = element.parent().parent();
          insertAt = insertIn.children.indexOf(insertNode) + 1;
        } else {
          insertNode = element;
          if (insertNode.parent().constructor.name !== 'Region') {
            insertNode = element.closest(function (node) {
              return node.parent().constructor.name === 'Region';
            });
          }
          insertIn = insertNode.parent();
          insertAt = insertIn.children.indexOf(insertNode) + 1;
        }
        for (i = j = 0, len = lines.length; j < len; i = ++j) {
          line = lines[i];
          line = encodeHTML(line);
          if (className === 'ListItemText') {
            item = new ContentEdit.ListItem();
            itemText = new ContentEdit.ListItemText(line);
            item.attach(itemText);
            lastItem = itemText;
          } else {
            item = new ContentEdit.Text('p', {}, line);
            lastItem = item;
          }
          insertIn.attach(item, insertAt + i);
        }
        lineLength = lastItem.content.length();
        lastItem.focus();
        return lastItem.selection(new ContentSelect.Range(lineLength, lineLength));
      } else {
        content = encodeHTML(content);
        content = new HTMLString.String(content, className === 'PreText');
        selection = element.selection();
        cursor = selection.get()[0] + content.length();
        tip = element.content.substring(0, selection.get()[0]);
        tail = element.content.substring(selection.get()[1]);
        element.content = tip.concat(content);
        element.content = element.content.concat(tail, false);
        element.updateInnerHTML();
        element.taint();
        selection.set(cursor, cursor);
        return element.selection(selection);
      }
    };

    _EditorApp.prototype.unmount = function () {
      if (!this.isMounted()) {
        return;
      }
      clearInterval(this._updatePositionInterval);
      this._domElement.parentNode.removeChild(this._domElement);
      this._domElement = null;
      this._removeDOMEventListeners();
      this._ignition = null;
      this._inspector = null;
      return this._toolbox = null;
    };

    _EditorApp.prototype.revert = function () {
      var confirmMessage;
      confirmMessage = ContentEdit._('Your changes have not been saved, do you really want to lose them?');
      if (ContentEdit.Root.get().lastModified() > this._rootLastModified && !window.confirm(confirmMessage)) {
        return false;
      }
      this.revertToSnapshot(this.history.goTo(0), false);
      return true;
    };

    _EditorApp.prototype.revertToSnapshot = function (snapshot, restoreEditable) {
      var domRegion, i, j, len, name, ref, ref1, region;
      if (restoreEditable == null) {
        restoreEditable = true;
      }
      ref = this._regions;
      for (name in ref) {
        region = ref[name];
        region.domElement().innerHTML = snapshot.regions[name];
      }
      if (restoreEditable) {
        this._regions = {};
        ref1 = this._domRegions;
        for (i = j = 0, len = ref1.length; j < len; i = ++j) {
          domRegion = ref1[i];
          name = domRegion.getAttribute(this._namingProp);
          if (!name) {
            name = i;
          }
          this._regions[name] = new ContentEdit.Region(domRegion);
        }
        this.history.replaceRegions(this._regions);
        return this.history.restoreSelection(snapshot);
      }
    };

    _EditorApp.prototype.save = function () {
      var args, child, html, modifiedRegions, name, passive, ref, region, root;
      passive = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [
      ];
      root = ContentEdit.Root.get();
      if (root.lastModified() === this._rootLastModified && passive) {
        return;
      }
      modifiedRegions = {};
      ref = this._regions;
      for (name in ref) {
        region = ref[name];
        html = region.html();
        if (region.children.length === 1) {
          child = region.children[0];
          if (child.content && !child.content.html()) {
            html = '';
          }
        }
        if (!passive) {
          region.domElement().innerHTML = html;
        }
        if (region.lastModified() === this._regionsLastModified[name]) {
          continue;
        }
        modifiedRegions[name] = html;
      }
      return this.trigger.apply(this, [
        'save',
        modifiedRegions
      ].concat(slice.call(args)));
    };

    _EditorApp.prototype.setRegionOrder = function (regionNames) {
      return this._orderedRegions = regionNames.slice();
    };

    _EditorApp.prototype.addStandByParagraph = function () {
      var firstRegion = this.orderedRegions()[0];
      var lastChild = firstRegion.children[firstRegion.children.length - 1];

      if (lastChild && lastChild.type() === 'Text') {
        lastChild.focus();
      } else {
        var p = new ContentEdit.Text('p', {});
        firstRegion.attach(p);
        p.focus();
      }
    };

    _EditorApp.prototype.focusLastElement = function (e) {
      if (e.target !== this._contentContainer)
        return;

      var lastChild = this.regions()[0].children[this.regions()[0].children.length - 1];

      if (lastChild) {
        if (lastChild.selection) {
          var range = new ContentSelect.Range(lastChild._domElement.innerHTML.length, lastChild._domElement.innerHTML.length);
          lastChild.selection(range);
        }

        if (lastChild.focus) {
          var root = ContentEdit.Root.get();
          if (root.focused()) {
            root.focused().blur();
          }
          
          lastChild.focus();
        } else {
          lastChild._domElement.focus();
        }
      }
    };

    _EditorApp.prototype.start = function () {
      var domRegion, i, j, len, name, ref;
      this.busy(true);
      this._regions = {};
      this._orderedRegions = [
      ];
      ref = this._domRegions;
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        domRegion = ref[i];
        name = domRegion.getAttribute(this._namingProp);
        if (!name) {
          name = i;
        }
        this._regions[name] = new ContentEdit.Region(domRegion);
        this._orderedRegions.push(name);
        this._regionsLastModified[name] = this._regions[name].lastModified();
      }
      //this._preventEmptyRegions();
      this._toolbox.show();
      this._inspector.show();
      this.addStandByParagraph();
      this._rootLastModified = ContentEdit.Root.get().lastModified();
      this.history = new ContentTools.History(this._regions);
      this.history.watch();
      this._state = ContentTools.EditorApp.EDITING;

      return this.busy(false);
    };

    _EditorApp.prototype.stop = function () {
      if (ContentEdit.Root.get().focused()) {
        ContentEdit.Root.get().focused().blur();
      }
      clearInterval(this._updatePositionInterval);
      if (this.history) {
        this.history.stopWatching();
        this.history = null;
      }
      //this._toolbox.hide();
      //this._inspector.hide();
      this._regions = {};
      this._domRegions[0].style.marginTop = '';

      return this._state = ContentTools.EditorApp.READY;
    };

    _EditorApp.prototype._addDOMEventListeners = function () {
      var _this = this;
      this._handleHighlightOn = (function (_this) {
        return function (ev) {
          var ref;
          if ((ref = ev.keyCode) === 17 || ref === 224) {
            _this._ctrlDown = true;
            return;
          }
          if (ev.keyCode === 16) {
            if (_this._highlightTimeout) {
              return;
            }
            _this._shiftDown = true;
            return _this._highlightTimeout = setTimeout(function () {
              return _this.highlightRegions(true);
            }, ContentTools.HIGHLIGHT_HOLD_DURATION);
          }
        };
      })(this);
      this._handleHighlightOff = (function (_this) {
        return function (ev) {
          var ref;
          if ((ref = ev.keyCode) === 17 || ref === 224) {
            _this._ctrlDown = false;
            return;
          }
          if (ev.keyCode === 16) {
            _this._shiftDown = false;
            if (_this._highlightTimeout) {
              clearTimeout(_this._highlightTimeout);
              _this._highlightTimeout = null;
            }
            return _this.highlightRegions(false);
          }
        };
      })(this);

      this._editorContainer.addEventListener('click', function (e) {
        _this.focusLastElement(e);
      });

      document.addEventListener('keydown', this._handleHighlightOn);
      document.addEventListener('keyup', this._handleHighlightOff);
      window.onbeforeunload = (function (_this) {
        return function (ev) {
          if (ContentEdit.Root.get().lastModified() > _this._rootLastModified && _this._state === ContentTools.EditorApp.EDITING) {
            return ContentEdit._('Your changes have not been saved, do you really want to lose them?');
          }
        };
      })(this);
      return window.addEventListener('unload', (function (_this) {
        return function (ev) {
          return _this.destroy();
        };
      })(this));
    };

    _EditorApp.prototype._preventEmptyRegions = function () {
      var name, placeholder, ref, region, results;
      ref = this._regions;
      results = [
      ];
      for (name in ref) {
        region = ref[name];
        if (region.children.length > 0) {
          continue;
        }
        placeholder = new ContentEdit.Text('p', {}, '');
        region.attach(placeholder);
        results.push(region.commit());
      }
      return results;
    };

    _EditorApp.prototype._removeDOMEventListeners = function () {
      window.onbeforeunload = null;
      document.removeEventListener('keydown', this._handleHighlightOn);
      return document.removeEventListener('keyup', this._handleHighlightOff);
    };

    return _EditorApp;

  })(ContentTools.ComponentUI);

  ContentTools.EditorApp = (function () {
    var instance;

    function EditorApp() {
    }

    EditorApp.DORMANT = 'dormant';

    EditorApp.READY = 'ready';

    EditorApp.EDITING = 'editing';

    instance = null;

    EditorApp.get = function () {
      var cls;
      cls = ContentTools.EditorApp.getCls();
      return instance != null ? instance : instance = new cls();
    };

    EditorApp.getNew = function () {
      var cls;
      cls = ContentTools.EditorApp.getCls();
      ContentEdit.Root.reset();
      instance = new cls();
      return instance;
    };

    EditorApp.getCls = function () {
      return _EditorApp;
    };

    return EditorApp;
  })();

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  ContentTools.History = (function() {
    function History(regions) {
      this._lastSnapshotTaken = null;
      this._regions = {};
      this.replaceRegions(regions);
      this._snapshotIndex = -1;
      this._snapshots = [];
      this._store();
    }

    History.prototype.canRedo = function() {
      return this._snapshotIndex < this._snapshots.length - 1;
    };

    History.prototype.canUndo = function() {
      return this._snapshotIndex > 0;
    };

    History.prototype.length = function() {
      return this._snapshots.length;
    };

    History.prototype.snapshot = function() {
      return this._snapshots[this._snapshotIndex];
    };

    History.prototype.goTo = function(index) {
      this._snapshotIndex = Math.min(this._snapshots.length - 1, Math.max(0, index));
      return this.snapshot();
    };

    History.prototype.redo = function() {
      return this.goTo(this._snapshotIndex + 1);
    };

    History.prototype.replaceRegions = function(regions) {
      var k, results, v;
      this._regions = {};
      results = [];
      for (k in regions) {
        v = regions[k];
        results.push(this._regions[k] = v);
      }
      return results;
    };

    History.prototype.restoreSelection = function(snapshot) {
      var element, region;
      if (!snapshot.selected) {
        return;
      }
      region = this._regions[snapshot.selected.region];
      element = region.descendants()[snapshot.selected.element];
      element.focus();
      if (element.selection && snapshot.selected.selection) {
        return element.selection(snapshot.selected.selection);
      }
    };

    History.prototype.stopWatching = function() {
      if (this._watchInterval) {
        clearInterval(this._watchInterval);
      }
      if (this._delayedStoreTimeout) {
        return clearTimeout(this._delayedStoreTimeout);
      }
    };

    History.prototype.undo = function() {
      return this.goTo(this._snapshotIndex - 1);
    };

    History.prototype.watch = function() {
      var watch;
      this._lastSnapshotTaken = Date.now();
      watch = (function(_this) {
        return function() {
          var delayedStore, lastModified;
          lastModified = ContentEdit.Root.get().lastModified();
          if (lastModified === null) {
            return;
          }
          if (lastModified > _this._lastSnapshotTaken) {
            if (_this._delayedStoreRequested === lastModified) {
              return;
            }
            if (_this._delayedStoreTimeout) {
              clearTimeout(_this._delayedStoreTimeout);
            }
            delayedStore = function() {
              _this._lastSnapshotTaken = lastModified;
              return _this._store();
            };
            _this._delayedStoreRequested = lastModified;
            return _this._delayedStoreTimeout = setTimeout(delayedStore, 500);
          }
        };
      })(this);
      return this._watchInterval = setInterval(watch, 100);
    };

    History.prototype._store = function() {
      var element, name, other_region, ref, ref1, region, snapshot;
      snapshot = {
        regions: {},
        selected: null
      };
      ref = this._regions;
      for (name in ref) {
        region = ref[name];
        snapshot.regions[name] = region.html();
      }
      element = ContentEdit.Root.get().focused();
      if (element) {
        snapshot.selected = {};
        region = element.closest(function(node) {
          return node.constructor.name === 'Region';
        });
        if (!region) {
          return;
        }
        ref1 = this._regions;
        for (name in ref1) {
          other_region = ref1[name];
          if (region === other_region) {
            snapshot.selected.region = name;
            break;
          }
        }
        snapshot.selected.element = region.descendants().indexOf(element);
        if (element.selection) {
          snapshot.selected.selection = element.selection();
        }
      }
      if (this._snapshotIndex < (this._snapshots.length - 1)) {
        this._snapshots = this._snapshots.slice(0, this._snapshotIndex + 1);
      }
      this._snapshotIndex++;
      return this._snapshots.splice(this._snapshotIndex, 0, snapshot);
    };

    return History;

  })();

}).call(this);

// Generated by CoffeeScript 1.10.0
(function() {
  ContentTools.StylePalette = (function() {
    function StylePalette() {}

    StylePalette._styles = [];

    StylePalette.add = function(styles) {
      return this._styles = this._styles.concat(styles);
    };

    StylePalette.styles = function(tagName) {
      if (tagName === void 0) {
        return this._styles.slice();
      }
      return this._styles.filter(function(style) {
        if (!style._applicableTo) {
          return true;
        }
        return style._applicableTo.indexOf(tagName) !== -1;
      });
    };

    return StylePalette;

  })();

  ContentTools.Style = (function() {
    function Style(name, cssClass, applicableTo) {
      this._name = name;
      this._cssClass = cssClass;
      if (applicableTo) {
        this._applicableTo = applicableTo;
      } else {
        this._applicableTo = null;
      }
    }

    Style.prototype.applicableTo = function() {
      return this._applicableTo;
    };

    Style.prototype.cssClass = function() {
      return this._cssClass;
    };

    Style.prototype.name = function() {
      return this._name;
    };

    return Style;

  })();

}).call(this);

(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  },
    hasProp = {}.hasOwnProperty;

  ContentTools.ToolShelf = (function () {
    function ToolShelf() {}

    ToolShelf._tools = {};

    ToolShelf.stow = function (cls, name) {
      return this._tools[name] = cls;
    };

    ToolShelf.fetch = function (name) {
      if (!this._tools[name]) {
        throw new Error("`" + name + "` has not been stowed on the tool shelf");
      }
      return this._tools[name];
    };

    return ToolShelf;

  })();

  ContentTools.Tool = (function () {
    function Tool() {}

    Tool.label = 'Tool';

    Tool.icon = 'tool';

    Tool.canApply = function (element, selection) {
      return false;
    };

    Tool.isApplied = function (element, selection) {
      return false;
    };

    Tool.apply = function (element, selection, callback) {
      throw new Error('Not implemented');
    };

    Tool._insertAt = function (element) {
      var insertIndex, insertNode;
      insertNode = element;
      if (insertNode.parent().type() !== 'Region') {
        insertNode = element.closest(function (node) {
          return node.parent().type() === 'Region';
        });
      }
      insertIndex = insertNode.parent().children.indexOf(insertNode) + 1;
      return [
        insertNode,
        insertIndex
      ];
    };

    return Tool;

  })();

  ContentTools.Tools.Bold = (function (superClass) {
    extend(Bold, superClass);

    function Bold() {
      return Bold.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Bold, 'bold');

    Bold.label = 'Bold';

    Bold.icon = 'bold';

    Bold.tagName = 'b';

    Bold.canApply = function (element, selection) {
      if (!element.content) {
        return false;
      }
      return selection && !selection.isCollapsed();
    };

    Bold.isApplied = function (element, selection) {
      var from, ref, to;
      if (element.content === void 0 || !element.content.length()) {
        return false;
      }
      ref = selection.get(), from = ref[0], to = ref[1];
      if (from === to) {
        to += 1;
      }
      return element.content.slice(from, to).hasTags(this.tagName, true);
    };

    Bold.apply = function (element, selection, callback) {
      var from, ref, to;
      element.storeState();
      ref = selection.get(), from = ref[0], to = ref[1];
      if (this.isApplied(element, selection)) {
        element.content = element.content.unformat(from, to, new HTMLString.Tag(this.tagName));
      } else {
        element.content = element.content.format(from, to, new HTMLString.Tag(this.tagName));
      }
      element.content.optimize();
      element.updateInnerHTML();
      element.taint();
      element.restoreState();
      return callback(true);
    };

    return Bold;

  })(ContentTools.Tool);

  ContentTools.Tools.Italic = (function (superClass) {
    extend(Italic, superClass);

    function Italic() {
      return Italic.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Italic, 'italic');

    Italic.label = 'Italic';

    Italic.icon = 'italic';

    Italic.tagName = 'i';

    return Italic;

  })(ContentTools.Tools.Bold);

  ContentTools.Tools.Link = (function (superClass) {
    extend(Link, superClass);

    function Link() {
      return Link.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Link, 'link');

    Link.label = 'Link';

    Link.icon = 'link';

    Link.tagName = 'a';

    Link.getAttr = function (attrName, element, selection) {
      var c, from, j, k, len, len1, ref, ref1, ref2, selectedContent, tag, to;
      if (element.type() === 'Image') {
        if (element.a) {
          return element.a[attrName];
        }
      } else {
        ref = selection.get(), from = ref[0], to = ref[1];
        selectedContent = element.content.slice(from, to);
        ref1 = selectedContent.characters;
        for (j = 0, len = ref1.length; j < len; j++) {
          c = ref1[j];
          if (!c.hasTags('a')) {
            continue;
          }
          ref2 = c.tags();
          for (k = 0, len1 = ref2.length; k < len1; k++) {
            tag = ref2[k];
            if (tag.name() === 'a') {
              return tag.attr(attrName);
            }
          }
        }
      }
      return '';
    };

    Link.canApply = function (element, selection) {
      if (element.type() === 'Image') {
        return true;
      } else {
        return Link.__super__.constructor.canApply.call(this, element, selection);
      }
    };

    Link.isApplied = function (element, selection) {
      if (element.type() === 'Image') {
        return element.a;
      } else {
        return Link.__super__.constructor.isApplied.call(this, element, selection);
      }
    };

    Link.apply = function (element, selection, callback) {
      var allowScrolling, app, applied, dialog, domElement, from, measureSpan, modal, rect, ref, selectTag, to, transparent;
      applied = false;
      if (element.type() === 'Image') {
        rect = element.domElement().getBoundingClientRect();
      } else {
        element.storeState();
        selectTag = new HTMLString.Tag('span', {
          'class': 'ct--puesdo-select'
        });
        ref = selection.get(), from = ref[0], to = ref[1];
        element.content = element.content.format(from, to, selectTag);
        element.updateInnerHTML();
        domElement = element.domElement();
        measureSpan = domElement.getElementsByClassName('ct--puesdo-select');
        rect = measureSpan[0].getBoundingClientRect();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI(transparent = true, allowScrolling = true);
      modal.bind('click', function () {
        this.unmount();
        dialog.hide();
        if (element.content) {
          element.content = element.content.unformat(from, to, selectTag);
          element.updateInnerHTML();
          element.restoreState();
        }
        return callback(applied);
      });
      dialog = new ContentTools.LinkDialog(this.getAttr('href', element, selection), this.getAttr('target', element, selection));
      dialog.position([
        rect.left + (rect.width / 2) + window.scrollX,
        rect.top + (rect.height / 2) + window.scrollY
      ]);
      dialog.bind('save', function (linkAttr) {
        var a, alignmentClassNames, className, j, k, len, len1, linkClasses;
        dialog.unbind('save');
        applied = true;
        if (element.type() === 'Image') {
          alignmentClassNames = [
            'align-center',
            'align-left',
            'align-right'
          ];
          if (linkAttr.href) {
            element.a = {
              href: linkAttr.href,
              target: linkAttr.target ? linkAttr.target : '',
              "class": element.a ? element.a['class'] : ''
            };
            for (j = 0, len = alignmentClassNames.length; j < len; j++) {
              className = alignmentClassNames[j];
              if (element.hasCSSClass(className)) {
                element.removeCSSClass(className);
                element.a['class'] = className;
                break;
              }
            }
          } else {
            linkClasses = [
            ];
            if (element.a['class']) {
              linkClasses = element.a['class'].split(' ');
            }
            for (k = 0, len1 = alignmentClassNames.length; k < len1; k++) {
              className = alignmentClassNames[k];
              if (linkClasses.indexOf(className) > -1) {
                element.addCSSClass(className);
                break;
              }
            }
            element.a = null;
          }
          element.unmount();
          element.mount();
        } else {
          element.content = element.content.unformat(from, to, 'a');
          if (linkAttr.href) {
            a = new HTMLString.Tag('a', linkAttr);
            element.content = element.content.format(from, to, a);
            element.content.optimize();
          }
          element.updateInnerHTML();
        }
        element.taint();
        return modal.trigger('click');
      });
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      
      return dialog.show();
    };

    return Link;

  })(ContentTools.Tools.Bold);

  ContentTools.Tools.Heading = (function (superClass) {
    extend(Heading, superClass);

    function Heading() {
      return Heading.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Heading, 'heading');

    Heading.label = 'Heading';

    Heading.icon = 'heading';

    Heading.tagName = 'h1';

    Heading.canApply = function (element, selection) {
      return element.content !== void 0 && element.parent().type() === 'Region';
    };

    Heading.apply = function (element, selection, callback) {
      var content, insertAt, parent, textElement;
      element.storeState();
      if (element.type() === 'PreText') {
        content = element.content.html().replace(/&nbsp;/g, ' ');
        textElement = new ContentEdit.Text(this.tagName, {}, content);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(textElement, insertAt);
        element.blur();
        textElement.focus();
        textElement.selection(selection);
      } else {
        element.tagName(this.tagName);
        element.restoreState();
      }
      return callback(true);
    };

    return Heading;

  })(ContentTools.Tool);

  ContentTools.Tools.Subheading = (function (superClass) {
    extend(Subheading, superClass);

    function Subheading() {
      return Subheading.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Subheading, 'subheading');

    Subheading.label = 'Subheading';

    Subheading.icon = 'subheading';

    Subheading.tagName = 'h2';

    return Subheading;

  })(ContentTools.Tools.Heading);

  ContentTools.Tools.Paragraph = (function (superClass) {
    extend(Paragraph, superClass);

    function Paragraph() {
      return Paragraph.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Paragraph, 'paragraph');

    Paragraph.label = 'Paragraph';

    Paragraph.icon = 'paragraph';

    Paragraph.tagName = 'p';

    Paragraph.canApply = function (element, selection) {
      return element !== void 0;
    };

    Paragraph.apply = function (element, selection, callback) {
      var app, forceAdd, paragraph, region;
      app = ContentTools.EditorApp.get();
      forceAdd = app.ctrlDown();
      if (ContentTools.Tools.Heading.canApply(element) && !forceAdd) {
        return Paragraph.__super__.constructor.apply.call(this, element, selection, callback);
      } else {
        if (element.parent().type() !== 'Region') {
          element = element.closest(function (node) {
            return node.parent().type() === 'Region';
          });
        }
        region = element.parent();
        paragraph = new ContentEdit.Text('p');
        region.attach(paragraph, region.children.indexOf(element) + 1);
        paragraph.focus();
        return callback(true);
      }
    };

    return Paragraph;

  })(ContentTools.Tools.Heading);

  ContentTools.Tools.Preformatted = (function (superClass) {
    extend(Preformatted, superClass);

    function Preformatted() {
      return Preformatted.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Preformatted, 'preformatted');

    Preformatted.label = 'Preformatted';

    Preformatted.icon = 'preformatted';

    Preformatted.tagName = 'pre';

    Preformatted.apply = function (element, selection, callback) {
      var insertAt, parent, preText, text;
      text = element.content.text();
      preText = new ContentEdit.PreText('pre', {}, HTMLString.String.encode(text));
      parent = element.parent();
      insertAt = parent.children.indexOf(element);
      parent.detach(element);
      parent.attach(preText, insertAt);
      element.blur();
      preText.focus();
      preText.selection(selection);
      return callback(true);
    };

    return Preformatted;

  })(ContentTools.Tools.Heading);

  ContentTools.Tools.AlignLeft = (function (superClass) {
    extend(AlignLeft, superClass);

    function AlignLeft() {
      return AlignLeft.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(AlignLeft, 'align-left');

    AlignLeft.label = 'Align left';

    AlignLeft.icon = 'align-left';

    AlignLeft.className = 'text-left';

    AlignLeft.canApply = function (element, selection) {
      return element.content !== void 0;
    };

    AlignLeft.isApplied = function (element, selection) {
      var ref;
      if (!this.canApply(element)) {
        return false;
      }
      if ((ref = element.type()) === 'ListItemText' || ref === 'TableCellText') {
        element = element.parent();
      }
      return element.hasCSSClass(this.className);
    };

    AlignLeft.apply = function (element, selection, callback) {
      var className, j, len, ref, ref1;
      if ((ref = element.type()) === 'ListItemText' || ref === 'TableCellText') {
        element = element.parent();
      }
      ref1 = [
        'text-center',
        'text-left',
        'text-right'
      ];
      for (j = 0, len = ref1.length; j < len; j++) {
        className = ref1[j];
        if (element.hasCSSClass(className)) {
          element.removeCSSClass(className);
          if (className === this.className) {
            return callback(true);
          }
        }
      }
      element.addCSSClass(this.className);
      return callback(true);
    };

    return AlignLeft;

  })(ContentTools.Tool);

  ContentTools.Tools.AlignCenter = (function (superClass) {
    extend(AlignCenter, superClass);

    function AlignCenter() {
      return AlignCenter.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(AlignCenter, 'align-center');

    AlignCenter.label = 'Align center';

    AlignCenter.icon = 'align-center';

    AlignCenter.className = 'text-center';

    return AlignCenter;

  })(ContentTools.Tools.AlignLeft);

  ContentTools.Tools.AlignRight = (function (superClass) {
    extend(AlignRight, superClass);

    function AlignRight() {
      return AlignRight.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(AlignRight, 'align-right');

    AlignRight.label = 'Align right';

    AlignRight.icon = 'align-right';

    AlignRight.className = 'text-right';

    return AlignRight;

  })(ContentTools.Tools.AlignLeft);

  ContentTools.Tools.UnorderedList = (function (superClass) {
    extend(UnorderedList, superClass);

    function UnorderedList() {
      return UnorderedList.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(UnorderedList, 'unordered-list');

    UnorderedList.label = 'Bullet list';

    UnorderedList.icon = 'unordered-list';

    UnorderedList.listTag = 'ul';

    UnorderedList.canApply = function (element, selection) {
      var ref;
      return element.content !== void 0 && ((ref = element.parent().type()) === 'Region' || ref === 'ListItem');
    };

    UnorderedList.apply = function (element, selection, callback) {
      var insertAt, list, listItem, listItemText, parent;
      if (element.parent().type() === 'ListItem') {
        element.storeState();
        list = element.closest(function (node) {
          return node.type() === 'List';
        });
        list.tagName(this.listTag);
        element.restoreState();
      } else {
        listItemText = new ContentEdit.ListItemText(element.content.copy());
        listItem = new ContentEdit.ListItem();
        listItem.attach(listItemText);
        list = new ContentEdit.List(this.listTag, {});
        list.attach(listItem);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(list, insertAt);
        listItemText.focus();
        listItemText.selection(selection);
      }
      return callback(true);
    };

    return UnorderedList;

  })(ContentTools.Tool);

  ContentTools.Tools.OrderedList = (function (superClass) {
    extend(OrderedList, superClass);

    function OrderedList() {
      return OrderedList.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(OrderedList, 'ordered-list');

    OrderedList.label = 'Numbers list';

    OrderedList.icon = 'ordered-list';

    OrderedList.listTag = 'ol';

    return OrderedList;

  })(ContentTools.Tools.UnorderedList);

  ContentTools.Tools.Table = (function (superClass) {
    extend(Table, superClass);

    function Table() {
      return Table.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Table, 'table');

    Table.label = 'Table';

    Table.icon = 'table';

    Table.canApply = function (element, selection) {
      return element !== void 0;
    };

    Table.apply = function (element, selection, callback) {
      var app, dialog, modal, table;
      if (element.storeState) {
        element.storeState();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI();
      table = element.closest(function (node) {
        return node && node.type() === 'Table';
      });
      dialog = new ContentTools.TableDialog(table);
      dialog.bind('cancel', (function (_this) {
        return function () {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (element.restoreState) {
            element.restoreState();
          }
          return callback(false);
        };
      })(this));
      dialog.bind('save', (function (_this) {
        return function (tableCfg) {
          var index, keepFocus, node, ref;
          dialog.unbind('save');
          keepFocus = true;
          if (table) {
            _this._updateTable(tableCfg, table);
            keepFocus = element.closest(function (node) {
              return node && node.type() === 'Table';
            });
          } else {
            table = _this._createTable(tableCfg);
            ref = _this._insertAt(element), node = ref[0], index = ref[1];
            node.parent().attach(table, index);
            keepFocus = false;
          }
          if (keepFocus) {
            element.restoreState();
          } else {
            table.firstSection().children[0].children[0].children[0].focus();
          }
          modal.hide();
          dialog.hide();
          return callback(true);
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };

    Table._adjustColumns = function (section, columns) {
      var cell, cellTag, cellText, currentColumns, diff, i, j, len, ref, results, row;
      ref = section.children;
      results = [
      ];
      for (j = 0, len = ref.length; j < len; j++) {
        row = ref[j];
        cellTag = row.children[0].tagName();
        currentColumns = row.children.length;
        diff = columns - currentColumns;
        if (diff < 0) {
          results.push((function () {
            var k, ref1, results1;
            results1 = [
            ];
            for (i = k = ref1 = diff; ref1 <= 0 ? k < 0 : k > 0; i = ref1 <= 0 ? ++k : --k) {
              cell = row.children[row.children.length - 1];
              results1.push(row.detach(cell));
            }
            return results1;
          })());
        } else if (diff > 0) {
          results.push((function () {
            var k, ref1, results1;
            results1 = [
            ];
            for (i = k = 0, ref1 = diff; 0 <= ref1 ? k < ref1 : k > ref1; i = 0 <= ref1 ? ++k : --k) {
              cell = new ContentEdit.TableCell(cellTag);
              row.attach(cell);
              cellText = new ContentEdit.TableCellText('');
              results1.push(cell.attach(cellText));
            }
            return results1;
          })());
        } else {
          results.push(void 0);
        }
      }
      return results;
    };

    Table._createTable = function (tableCfg) {
      var body, foot, head, table;
      table = new ContentEdit.Table();
      if (tableCfg.head) {
        head = this._createTableSection('thead', 'th', tableCfg.columns);
        table.attach(head);
      }
      body = this._createTableSection('tbody', 'td', tableCfg.columns);
      table.attach(body);
      if (tableCfg.foot) {
        foot = this._createTableSection('tfoot', 'td', tableCfg.columns);
        table.attach(foot);
      }
      return table;
    };

    Table._createTableSection = function (sectionTag, cellTag, columns) {
      var cell, cellText, i, j, ref, row, section;
      section = new ContentEdit.TableSection(sectionTag);
      row = new ContentEdit.TableRow();
      section.attach(row);
      for (i = j = 0, ref = columns; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
        cell = new ContentEdit.TableCell(cellTag);
        row.attach(cell);
        cellText = new ContentEdit.TableCellText('');
        cell.attach(cellText);
      }
      return section;
    };

    Table._updateTable = function (tableCfg, table) {
      var columns, foot, head, j, len, ref, section;
      if (!tableCfg.head && table.thead()) {
        table.detach(table.thead());
      }
      if (!tableCfg.foot && table.tfoot()) {
        table.detach(table.tfoot());
      }
      columns = table.firstSection().children[0].children.length;
      if (tableCfg.columns !== columns) {
        ref = table.children;
        for (j = 0, len = ref.length; j < len; j++) {
          section = ref[j];
          this._adjustColumns(section, tableCfg.columns);
        }
      }
      if (tableCfg.head && !table.thead()) {
        head = this._createTableSection('thead', 'th', tableCfg.columns);
        table.attach(head);
      }
      if (tableCfg.foot && !table.tfoot()) {
        foot = this._createTableSection('tfoot', 'td', tableCfg.columns);
        return table.attach(foot);
      }
    };

    return Table;

  })(ContentTools.Tool);

  ContentTools.Tools.Indent = (function (superClass) {
    extend(Indent, superClass);

    function Indent() {
      return Indent.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Indent, 'indent');

    Indent.label = 'Indent';

    Indent.icon = 'indent';

    Indent.canApply = function (element, selection) {
      return element.parent().type() === 'ListItem' && element.parent().parent().children.indexOf(element.parent()) > 0;
    };

    Indent.apply = function (element, selection, callback) {
      element.parent().indent();
      return callback(true);
    };

    return Indent;

  })(ContentTools.Tool);

  ContentTools.Tools.Unindent = (function (superClass) {
    extend(Unindent, superClass);

    function Unindent() {
      return Unindent.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Unindent, 'unindent');

    Unindent.label = 'Unindent';

    Unindent.icon = 'unindent';

    Unindent.canApply = function (element, selection) {
      return element.parent().type() === 'ListItem';
    };

    Unindent.apply = function (element, selection, callback) {
      element.parent().unindent();
      return callback(true);
    };

    return Unindent;

  })(ContentTools.Tool);

  ContentTools.Tools.LineBreak = (function (superClass) {
    extend(LineBreak, superClass);

    function LineBreak() {
      return LineBreak.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(LineBreak, 'line-break');

    LineBreak.label = 'Line break';

    LineBreak.icon = 'line-break';

    LineBreak.canApply = function (element, selection) {
      return element.content;
    };

    LineBreak.apply = function (element, selection, callback) {
      var br, cursor, tail, tip;
      cursor = selection.get()[0] + 1;
      tip = element.content.substring(0, selection.get()[0]);
      tail = element.content.substring(selection.get()[1]);
      br = new HTMLString.String('<br>', element.content.preserveWhitespace());
      element.content = tip.concat(br, tail);
      element.updateInnerHTML();
      element.taint();
      selection.set(cursor, cursor);
      element.selection(selection);
      return callback(true);
    };

    return LineBreak;

  })(ContentTools.Tool);

  ContentTools.Tools.Image = (function (superClass) {
    extend(Image, superClass);

    function Image() {
      return Image.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Image, 'image');

    Image.label = 'Image';

    Image.icon = 'image';

    Image.canApply = function (element, selection) {
      return true;
    };

    Image.apply = function (element, selection, callback) {
      var app, dialog, modal;
      if (element.storeState) {
        element.storeState();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI();
      dialog = new ContentTools.ImageDialog();
      dialog.bind('cancel', (function (_this) {
        return function () {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (element.restoreState) {
            element.restoreState();
          }
          return callback(false);
        };
      })(this));
      dialog.bind('save', (function (_this) {
        return function (imageURL, imageSize, imageAttrs) {
          var image, index, node, ref;
          dialog.unbind('save');
          if (!imageAttrs) {
            imageAttrs = {};
          }
          imageAttrs.height = imageSize[1];
          imageAttrs.src = imageURL;
          imageAttrs.width = imageSize[0];
          image = new ContentEdit.Image(imageAttrs);
          ref = _this._insertAt(element), node = ref[0], index = ref[1];
          node.parent().attach(image, index);
          image.focus();
          modal.hide();
          dialog.hide();
          return callback(true);
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };

    return Image;

  })(ContentTools.Tool);

  ContentTools.Tools.Video = (function (superClass) {
    extend(Video, superClass);

    function Video() {
      return Video.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Video, 'video');

    Video.label = 'Video';

    Video.icon = 'video';

    Video.canApply = function (element, selection) {
      return true;
    };

    Video.apply = function (element, selection, callback) {
      var app, dialog, modal;
      if (element.storeState) {
        element.storeState();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI();
      dialog = new ContentTools.VideoDialog();
      dialog.bind('cancel', (function (_this) {
        return function () {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (element.restoreState) {
            element.restoreState();
          }
          return callback(false);
        };
      })(this));
      dialog.bind('save', (function (_this) {
        return function (videoURL) {
          var index, node, ref, video;
          dialog.unbind('save');
          if (videoURL) {
            video = new ContentEdit.Video('iframe', {
              'frameborder': 0,
              'height': ContentTools.DEFAULT_VIDEO_HEIGHT,
              'src': videoURL,
              'width': ContentTools.DEFAULT_VIDEO_WIDTH
            });
            ref = _this._insertAt(element), node = ref[0], index = ref[1];
            node.parent().attach(video, index);
            video.focus();
          } else {
            if (element.restoreState) {
              element.restoreState();
            }
          }
          modal.hide();
          dialog.hide();
          return callback(videoURL !== '');
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };

    return Video;

  })(ContentTools.Tool);

  ContentTools.Tools.Undo = (function (superClass) {
    extend(Undo, superClass);

    function Undo() {
      return Undo.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Undo, 'undo');

    Undo.label = 'Undo';

    Undo.icon = 'undo';

    Undo.canApply = function (element, selection) {
      var app;
      app = ContentTools.EditorApp.get();
      return app.history && app.history.canUndo();
    };

    Undo.apply = function (element, selection, callback) {
      var app, snapshot;
      app = ContentTools.EditorApp.get();
      app.history.stopWatching();
      snapshot = app.history.undo();
      app.revertToSnapshot(snapshot);
      return app.history.watch();
    };

    return Undo;

  })(ContentTools.Tool);

  ContentTools.Tools.Redo = (function (superClass) {
    extend(Redo, superClass);

    function Redo() {
      return Redo.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Redo, 'redo');

    Redo.label = 'Redo';

    Redo.icon = 'redo';

    Redo.canApply = function (element, selection) {
      var app;
      app = ContentTools.EditorApp.get();
      return app.history && app.history.canRedo();
    };

    Redo.apply = function (element, selection, callback) {
      var app, snapshot;
      app = ContentTools.EditorApp.get();
      app.history.stopWatching();
      snapshot = app.history.redo();
      app.revertToSnapshot(snapshot);
      return app.history.watch();
    };

    return Redo;

  })(ContentTools.Tool);

  ContentTools.Tools.Remove = (function (superClass) {
    extend(Remove, superClass);

    function Remove() {
      return Remove.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Remove, 'remove');

    Remove.label = 'Remove';

    Remove.icon = 'remove';

    Remove.canApply = function (element, selection) {
      return true;
    };

    Remove.apply = function (element, selection, callback) {
      var app, list, row, table;
      app = ContentTools.EditorApp.get();
      element.blur();
      if (element.nextContent()) {
        element.nextContent().focus();
      } else if (element.previousContent()) {
        element.previousContent().focus();
      }
      switch (element.type()) {
        case 'ListItemText':
          if (app.ctrlDown()) {
            list = element.closest(function (node) {
              return node.parent().type() === 'Region';
            });
            list.parent().detach(list);
          } else {
            element.parent().parent().detach(element.parent());
          }
          break;
        case 'TableCellText':
          if (app.ctrlDown()) {
            table = element.closest(function (node) {
              return node.type() === 'Table';
            });
            table.parent().detach(table);
          } else {
            row = element.parent().parent();
            row.parent().detach(row);
          }
          break;
        default:
          element.parent().detach(element);
          break;
      }
      return callback(true);
    };

    return Remove;

  })(ContentTools.Tool);
})();
(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  }, hasProp = {}.hasOwnProperty;

  ContentTools.Tool = (function () {
    function Tool() {}

    Tool.label = 'Tool';

    Tool.icon = 'tool';

    Tool.canApply = function (element, selection) {
      return false;
    };

    Tool.isApplied = function (element, selection) {
      return false;
    };

    Tool.apply = function (element, selection, callback) {
      throw new Error('Not implemented');
    };

    Tool._insertAt = function (element) {
      var insertIndex, insertNode;
      insertNode = element;
      if (insertNode.parent().type() !== 'Region') {
        insertNode = element.closest(function (node) {
          return node.parent().type() === 'Region';
        });
      }
      insertIndex = insertNode.parent().children.indexOf(insertNode) + 1;
      return [
        insertNode,
        insertIndex
      ];
    };

    return Tool;

  })();

  ContentTools.Tools.Bold = (function (superClass) {
    extend(Bold, superClass);

    function Bold() {
      return Bold.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Bold, 'bold');

    Bold.label = 'Bold';

    Bold.icon = 'bold';

    Bold.tagName = 'strong';

    Bold.oldTagName = 'b';

    Bold.canApply = function (element, selection) {
      if (!element.content) {
        return false;
      }
      return selection && !selection.isCollapsed();
    };

    Bold.isApplied = function (element, selection) {
      var from, ref, to;
      if (element.content === void 0 || !element.content.length()) {
        return false;
      }
      ref = selection.get(), from = ref[0], to = ref[1];
      if (from === to) {
        to += 1;
      }
      return element.content.slice(from, to).hasTags(this.tagName, true);
    };

    Bold.apply = function (element, selection, callback) {
      var from, ref, to;
      element.storeState();
      ref = selection.get(), from = ref[0], to = ref[1];
      if (this.isApplied(element, selection)) {
        element.content = element.content.unformat(from, to, new HTMLString.Tag(this.tagName));

        if (this.oldTagName) {
          element.content = element.content.unformat(from, to, new HTMLString.Tag(this.oldTagName)); // backward compatibility
        }
      } else {
        element.content = element.content.format(from, to, new HTMLString.Tag(this.tagName));
      }
      element.content.optimize();
      element.updateInnerHTML();
      element.taint();
      element.restoreState();
      return callback(true);
    };

    return Bold;

  })(ContentTools.Tool);

  ContentTools.Tools.Italic = (function (superClass) {
    extend(Italic, superClass);

    function Italic() {
      return Italic.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Italic, 'italic');

    Italic.label = 'Italic';

    Italic.icon = 'italic';

    Italic.tagName = 'em';

    Italic.oldTagName = 'i';

    return Italic;

  })(ContentTools.Tools.Bold);

  ContentTools.Tools.Heading = (function (superClass) {
    extend(Heading, superClass);

    function Heading() {
      return Heading.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Heading, 'heading');

    Heading.label = 'Heading';

    Heading.icon = 'heading';

    Heading.tagName = 'h1';

    Heading.canApply = function (element, selection) {
      return element.content !== void 0;
    };

    Heading.isApplied = function (element, selection) {
      return element._tagName === this.tagName;
    };

    Heading.apply = function (element, selection, callback) {
      var content, insertAt, parent, textElement;
      element.storeState();
      if (element.type() === 'PreText') {
        content = element.content.html().replace(/&nbsp;/g, ' ');
        textElement = new ContentEdit.Text(this.tagName, {}, content);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(textElement, insertAt);
        element.blur();
        textElement.focus();
        textElement.selection(selection);
      } else {
        element.tagName(this.tagName);
        element.restoreState();
      }
      return callback(true);
    };

    return Heading;

  })(ContentTools.Tool);

  ContentTools.Tools.HeadingTwo = (function (superClass) {
    extend(HeadingTwo, superClass);

    function HeadingTwo() {
      return HeadingTwo.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(HeadingTwo, 'heading2');

    HeadingTwo.label = 'Subheading';

    HeadingTwo.icon = 'heading-2';

    HeadingTwo.tagName = 'h2';

    return HeadingTwo;

  })(ContentTools.Tools.Heading);

  ContentTools.Tools.LinkBlock = (function (superClass) {
    extend(LinkBlock, superClass);

    function LinkBlock() {
      return LinkBlock.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(LinkBlock, 'link-block');

    LinkBlock.label = 'Link';
    LinkBlock.icon = 'link';
    LinkBlock.tagName = 'a';

    LinkBlock.canApply = function (element, selection) {
      return element._domElement.getElementsByTagName("a").length ? false : true;
    };

    LinkBlock.apply = function (element, selection, callback) {
      var content, insertAt, parent, textElement;

      if (element.type() === 'PreText') {
        element.storeState();
        content = element.content.html().replace(/&nbsp;/g, ' ');
        textElement = new HTMLString.Tag(this.tagName, {
          href: ""
        });
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(textElement, insertAt);
        element.blur();
        element.restoreState();
        textElement.focus();
        textElement.selection(selection);
      } else {
        element.tagName(this.tagName);
        //element.restoreState();
        ContentTools.Tools.Link.apply(element, selection, callback);
      }
      return callback(true);
    };

    return LinkBlock;
  })(ContentTools.Tools.Heading);

  ContentTools.Tools.Paragraph = (function (superClass) {
    extend(Paragraph, superClass);

    function Paragraph() {
      return Paragraph.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Paragraph, 'paragraph');

    Paragraph.label = 'Paragraph';

    Paragraph.icon = 'paragraph';

    Paragraph.tagName = 'p';

    Paragraph.canApply = function (element, selection) {
      return element !== void 0;
    };

    Paragraph.apply = function (element, selection, callback) {
      var app, forceAdd, paragraph, region;
      app = ContentTools.EditorApp.get();
      forceAdd = app.ctrlDown();

      if (ContentTools.Tools.Heading.canApply(element) && !forceAdd) {
        return Paragraph.__super__.constructor.apply.call(this, element, selection, callback);
      } else {
        if (element.parent().type() !== 'Region') {
          element = element.closest(function (node) {
            return node.parent().type() === 'Region';
          });
        }
        region = element.parent();
        paragraph = new ContentEdit.Text('p');
        region.attach(paragraph, region.children.indexOf(element) + 1);
        paragraph.focus();
        return callback(true);
      }
    };

    return Paragraph;

  })(ContentTools.Tools.Heading);

// content-fields

  ContentTools.Tools.ContentFields = (function (superClass) {
    extend(ContentField, superClass);

    function ContentField() {
      return ContentField.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(ContentField, 'content-field');

    var setImage = function (element, callback) {
      var app, forceAdd, paragraph, region;
      app = ContentTools.EditorApp.get();
      var imageChooserDialog = EW.createModal({
        autoOpen: false,
        class: "center"
      });
      //imageChooserDialog.append("<div class='form-content grid tabs-bar no-footer'></div>");
      System.loadModule({
        url: "html/admin/content-management/link-chooser/link-chooser-media.php"
      }, function (module, data) {
        imageChooserDialog.html(data);
        //imageChooserDialog.prepend("<div class='header-pane tabs-bar row'><h1 class='form-title'>Media</h1></div>");
        var ref = ContentField._insertAt(element), node = ref[0], index = ref[1];
//        imageChooserDialog[0].selectMedia = function (image) {
//          var image = new ContentEdit.Image(image);
//          node.parent().attach(image, index);
//          node.parent().detach(element);
//          toContentField(image, element.attr('content-field'));
//          imageChooserDialog.dispose();
//        };
        module.scope.selectMedia = function (item) {
          var selectedItem = System.entity('services/media_chooser').selectItem(item);

          switch (selectedItem.type) {
            case 'text':
              var text = new ContentEdit.Text('p', {}, selectedItem.text);
              if (node.parent()) {
                node.parent().attach(text, index);
              } else {
                var firstRegion = app.orderedRegions()[0];
                firstRegion.attach(text, index);
              }
              toContentField(text, element.attr('content-field'));
              text.focus();

              break;

            case 'image':
              var image = new ContentEdit.Image({
                src: selectedItem.src,
                width: selectedItem.width,
                hight: selectedItem.height
              });

              node.parent().detach(element);

              if (node.parent()) {
                node.parent().attach(image, index);
              } else {
                var firstRegion = app.orderedRegions()[0];
                firstRegion.attach(image, index);
              }
              toContentField(image, element.attr('content-field'));
              image.focus();

              break;
          }

          imageChooserDialog.dispose();
        };
      });

      imageChooserDialog.open();
      //return callback(true);
    };

    ContentField.label = 'Content Field';
    ContentField.icon = 'content-field';

    ContentEdit.Root.get().bind('mount', function (element) {
      if (element.attr("content-field")) {
        toContentField(element, element.attr("content-field"));
      }
    });

    ContentEdit.Root.get().bind('unmount', function (element) {
      if (element._contentField && element._contentField.element.parentNode) {
        element._contentField.element.parentNode.removeChild(element._contentField.element);
      }
    });

    var toContentField = function (element, initValue) {
      var container = document.createElement("span"),
              input = document.createElement("input"),
              removeButton = document.createElement("button"),
              img = document.createElement("button"),
              title = document.createElement("p");

      var ewContentField = {
        element: container,
        input: input,
        title: title
      };

      container.className = "ew-content-field__bar";
      container.setAttribute("contenteditable", false);
      title.className = "ew-content-field__title";
      input.className = "ew-content-field__input";
      input.value = element.attr("content-field");
      removeButton.className = "ew-content-field__remove btn btn-danger";
      img.className = 'ew-content-field__img-btn btn btn-default';
      img.innerHTML = 'M';
      img.type = 'button';

      container.appendChild(input);
      container.appendChild(removeButton);
      container.appendChild(img);

      img.addEventListener('click', function (e) {
        setImage(element);
        console.log(element);
      });

      input.addEventListener("keydown", function (e) {
        if (e.keyCode === 32) {
          e.preventDefault();
          e.target.value += '-';
        } else if (String.fromCharCode(e.keyCode).match(/[A-Z]/) && e.shiftKey) {
          e.preventDefault();
          e.target.value += String.fromCharCode(e.keyCode).toLowerCase();
        } else if (e.keyCode === 13) {
          e.preventDefault();
          input.blur();

          if (element.content) {
            var l = element.content.length();
            element.selection(new ContentSelect.Range(l, l));
            element.focus();
          }
        }
        e.stopPropagation();
      });

      input.addEventListener("blur", function (e) {
        if (!input.value) {
          var d = new Date();
          var id = "cf-" + d.getMinutes() + d.getSeconds() + d.getMilliseconds();
          input.value = id;
        }
        element.attr("content-field", input.value);

        e.preventDefault();
      });

      removeButton.addEventListener("click", function () {
        //oldContentField = element.attr("content-field");
        element.removeAttr("content-field");
        element.removeCSSClass("ew-content-field");
        container.parentNode.removeChild(container);
      });
      input.value = title.innerHTML = initValue;
      element.attr("content-field", initValue);
      //element.addCSSClass("ew-content-field");

      container.style.position = "absolute";
      var app = ContentTools.EditorApp.get();
      app._contentContainer.appendChild(container);

      var parentRect = app._contentContainer.getBoundingClientRect(),
              rect = element._domElement.getBoundingClientRect();

      container.style.top = rect.top - parentRect.top + "px";
      container.style.left = rect.left - parentRect.left + "px";
      container.style.width = rect.width + "px";

      var oldRect = {};
      var cache = {};
      element.updatePosition = function () {
        cache = element._domElement.getBoundingClientRect();

        if (oldRect.left !== cache.left) {
          parentRect = app._contentContainer.getBoundingClientRect();
          container.style.left = cache.left - parentRect.left + "px";
          oldRect.left = cache.left - parentRect.left;
        }

        if (oldRect.width !== cache.width) {
          container.style.width = cache.width + "px";
          oldRect.width = cache.width;
        }

        if (oldRect.top !== cache.top + (app._contentContainer.scrollTop - parentRect.top)) {
          parentRect = app._contentContainer.getBoundingClientRect();
          container.style.top = cache.top + (app._contentContainer.scrollTop - parentRect.top) + "px";
          oldRect.top = cache.top + (app._contentContainer.scrollTop - parentRect.top);
        }

        setTimeout(function () {
          if (element._domElement && app._domElement) {
            element.updatePosition();
          }
        }, 100);

      };

      element.updatePosition();

      element._contentField = ewContentField;

      return input;
    };

    ContentField.canApply = function (element, selection) {
      return element.parent().constructor.name === 'Region' ||
              element._parent.constructor.name === 'ListItem' ||
              element.parent().constructor.name === 'ElementCollection' ||
              element.parent().constructor.name === 'Div';
    };

    //var oldContentField = null;
    ContentField.apply = function (element, selection, callback) {
      if (element.attr("content-field")) {

      } else {
        if (element._parent.constructor.name === 'ListItem') {
          toContentField(element._parent._parent, "").focus();
        } else {
          toContentField(element, "").focus();
        }
      }
    };

    ContentField.isApplied = function (element, selection) {
      return element.attr("content-field") ? true : false;
    };

    return ContentField;

  })(ContentTools.Tool);

  ContentTools.Tools.Link = (function (superClass) {
    extend(Link, superClass);

    function Link() {
      return Link.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Link, 'link');

    Link.label = 'Link';

    Link.icon = 'link';

    Link.tagName = 'a';

    Link.getAttr = function (attrName, element, selection) {
      var c, from, j, k, len, len1, ref, ref1, ref2, selectedContent, tag, to;
      if (element.type() === 'Image') {
        if (element.a) {
          return element.a[attrName];
        }
      } else if (element._tagName === "a") {
        return element.domElement()[attrName];
      } else {
        ref = selection.get(), from = ref[0], to = ref[1];
        selectedContent = element.content.slice(from, to);
        ref1 = selectedContent.characters;
        for (j = 0, len = ref1.length; j < len; j++) {
          c = ref1[j];
          if (!c.hasTags('a')) {
            continue;
          }
          ref2 = c.tags();
          for (k = 0, len1 = ref2.length; k < len1; k++) {
            tag = ref2[k];
            if (tag.name() === 'a') {
              return tag.attr(attrName);
            }
          }
        }
      }
      return '';
    };

    Link.canApply = function (element, selection) {
      if (element.type() === 'Image') {
        return true;
      } else if (element._tagName === "a") {
        return false;
      } else {
        return Link.__super__.constructor.canApply.call(this, element, selection);
      }
      return false;
    };

    Link.isApplied = function (element, selection) {
      if (element.type() === 'Image') {
        return element.a;
      } else if (element._tagName === "a") {
        return true;
      } else {
        return Link.__super__.constructor.isApplied.call(this, element, selection);
      }
    };

    Link.apply = function (element, selection, callback) {
      var allowScrolling, app, applied, dialog, domElement, from, measureSpan, modal, rect, ref, selectTag, to, transparent;
      applied = false;   
      if (element.type() === 'Image') {
        rect = element.domElement().getBoundingClientRect();
      } else if (element._tagName === "a") {
        rect = element.domElement().getBoundingClientRect();
      } else {
        element.storeState();
        selectTag = new HTMLString.Tag('span', {
          'class': 'ct--puesdo-select'
        });
        ref = selection.get(), from = ref[0], to = ref[1];
        element.content = element.content.format(from, to, selectTag);
        element.updateInnerHTML();
        domElement = element.domElement();
        measureSpan = domElement.getElementsByClassName('ct--puesdo-select');
        rect = measureSpan[0].getBoundingClientRect();
      }

      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI(transparent = true, allowScrolling = true);
      modal.bind('click', function () {
        this.unmount();
        dialog.hide();
        if (element.content) {
          element.content = element.content.unformat(from, to, selectTag);
          element.updateInnerHTML();
          element.restoreState();
        }
        return callback(applied);
      });

      dialog = new ContentTools.LinkDialog(element._domElement.getAttribute('href'), this.getAttr('target', element, selection));
      
      /*dialog.position([
       rect.left + (rect.width / 2) + window.scrollX,
       rect.top + (rect.height / 2) + window.scrollY
       ]);*/
      dialog.bind('save', function (linkAttr) {
        var a, alignmentClassNames, className, j, k, len, len1, linkClasses;
        dialog.unbind('save');
        applied = true;
        if (element.type() === 'Image') {
          alignmentClassNames = [
            'align-center',
            'align-left',
            'align-right'
          ];
          if (linkAttr.href) {
            element.a = {
              href: linkAttr.href,
              target: linkAttr.target ? linkAttr.target : '',
              "class": element.a ? element.a['class'] : ''
            };
            for (j = 0, len = alignmentClassNames.length; j < len; j++) {
              className = alignmentClassNames[j];
              if (element.hasCSSClass(className)) {
                element.removeCSSClass(className);
                element.a['class'] = className;
                break;
              }
            }
          } else {
            linkClasses = [
            ];
            if (element.a['class']) {
              linkClasses = element.a['class'].split(' ');
            }
            for (k = 0, len1 = alignmentClassNames.length; k < len1; k++) {
              className = alignmentClassNames[k];
              if (linkClasses.indexOf(className) > -1) {
                element.addCSSClass(className);
                break;
              }
            }
            element.a = null;
          }
          element.unmount();
          element.mount();
        } else if (element._tagName === "a") {
          element.attr("href", linkAttr.href);
          element.attr("target", linkAttr.target);
        } else {
          element.content = element.content.unformat(from, to, 'a');
          if (linkAttr.href) {
            a = new HTMLString.Tag('a', linkAttr);
            element.content = element.content.format(from, to, a);
            element.content.optimize();
          }
          element.updateInnerHTML();
        }
        element.taint();
        return modal.trigger('click');
      });
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      dialog.show();
      var containerRect = app._editorContainer.getBoundingClientRect();
      var dialogRect = dialog._domElement.getBoundingClientRect();
      var x = (rect.left + (rect.width / 2)) - containerRect.left - (dialogRect.width / 2),
              y = rect.top - containerRect.top;
      if (x + dialogRect.width > containerRect.width) {
        x = containerRect.width - dialogRect.width;
      }
      dialog.position([
        x > 0 ? x : 0,
        y > 0 ? y : 0
      ]);
      return true;
    };

    return Link;

  })(ContentTools.Tools.Bold);

  ContentTools.Tools.Preformatted = (function (superClass) {
    extend(Preformatted, superClass);

    function Preformatted() {
      return Preformatted.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(Preformatted, 'preformatted');

    Preformatted.label = 'Preformatted';

    Preformatted.icon = 'preformatted';

    Preformatted.tagName = 'pre';

    Preformatted.apply = function (element, selection, callback) {
      var insertAt, parent, preText, text;
      text = element.content.text();
      preText = new ContentEdit.PreText('pre', {}, HTMLString.String.encode(text));
      parent = element.parent();
      insertAt = parent.children.indexOf(element);
      parent.detach(element);
      parent.attach(preText, insertAt);
      element.blur();
      preText.focus();
      preText.selection(selection);
      return callback(true);
    };

    return Preformatted;

  })(ContentTools.Tools.Heading);

  ContentTools.Tools.UnorderedList = (function (superClass) {
    extend(UnorderedList, superClass);

    function UnorderedList() {
      return UnorderedList.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(UnorderedList, 'unordered-list');

    UnorderedList.label = 'Bullet list';

    UnorderedList.icon = 'unordered-list';

    UnorderedList.listTag = 'ul';

    UnorderedList.canApply = function (element, selection) {
      var ref;
      return element.content !== void 0 && ((ref = element.parent().type()) === 'Region' || ref === 'ListItem');
    };

    UnorderedList.isApplied = function (element, selection) {
      var parent = element.parent().parent();
      return parent && parent._tagName === 'ul';
    };

    UnorderedList.apply = function (element, selection, callback) {
      var insertAt, list, listItem, listItemText, parent;
      if (element.parent().type() === 'ListItem') {
        element.storeState();
        list = element.closest(function (node) {
          return node.type() === 'List';
        });
        list.tagName(this.listTag);
        element.restoreState();
      } else {
        listItemText = new ContentEdit.ListItemText(element.content.copy());
        listItem = new ContentEdit.ListItem();
        listItem.attach(listItemText);
        list = new ContentEdit.List(this.listTag, {});
        list.attach(listItem);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(list, insertAt);
        listItemText.focus();
        listItemText.selection(selection);
      }
      return callback(true);
    };

    return UnorderedList;

  })(ContentTools.Tool);

  ContentTools.Tools.OrderedList = (function (superClass) {
    extend(OrderedList, superClass);

    function OrderedList() {
      return OrderedList.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(OrderedList, 'ordered-list');

    OrderedList.label = 'Numbers list';

    OrderedList.icon = 'ordered-list';

    OrderedList.listTag = 'ol';

    OrderedList.isApplied = function (element, selection) {
      var parent = element.parent().parent();
      return parent && parent._tagName === 'ol';
    };

    return OrderedList;

  })(ContentTools.Tools.UnorderedList);

  ContentTools.Tools.FlexBox = (function (superClass) {
    extend(FlexBox, superClass);

    function FlexBox() {
      return FlexBox.__super__.constructor.apply(this, arguments);
    }

    FlexBox.label = 'Flex Box';
    FlexBox.icon = 'flex-box';

    ContentTools.ToolShelf.stow(FlexBox, 'flex-box');

    FlexBox.canApply = function (element, selection) {
      return element.parent().constructor.name === 'Region' || element._parent.constructor.name === 'ListItem';
    };

    //var oldContentField = null;
    FlexBox.apply = function (element, selection, callback) {


      if (element._domElement.classList.contains('flex-box')) {
        var layer = new ContentEdit.Div({}, true);
        element.attach(layer);
      } else {
        var layer = new ContentEdit.Div({});

        var region = element.parent();
        region.attach(layer);

        layer.focus();

        var firstChild = new ContentEdit.Div({}, true);
        layer.attach(firstChild);
      }
    };

    FlexBox.isApplied = function (element, selection) {
      return false;
    };

  })(ContentTools.Tool);
})(this);

(function () {
  var extend = function (child, parent) {
    for (var key in parent) {
      if (hasProp.call(parent, key))
        child[key] = parent[key];
    }
    function ctor() {
      this.constructor = child;
    }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
    child.__super__ = parent.prototype;
    return child;
  }, hasProp = {}.hasOwnProperty;
  ContentTools.SoundcloudDialog = (function (superClass) {
    extend(WidgetIFrame, superClass);
    function WidgetIFrame() {
      WidgetIFrame.__super__.constructor.call(this, 'Insert widget iframe');
    }

    WidgetIFrame.prototype.clearPreview = function () {
      if (this._domPreview) {
        ContentEdit.addCSSClass(this._domButton, 'ct-control--muted');
        this._domPreview.parentNode.removeChild(this._domPreview);
        return this._domPreview = void 0;
      }
    };
    WidgetIFrame.prototype.mount = function () {
      var domControlGroup;
      WidgetIFrame.__super__.mount.call(this);
      ContentEdit.addCSSClass(this._domElement, 'ct-iframe-dialog');
      ContentEdit.addCSSClass(this._domView, 'ct-video-dialog__preview');
      domControlGroup = this.constructor.createDiv(['ct-control-group']);
      this._domControls.appendChild(domControlGroup);
      this._domInput = document.createElement('textarea');
      this._domInput.setAttribute('class', 'ct-iframe-dialog__input');
      this._domInput.setAttribute('name', 'url');
      this._domInput.setAttribute('placeholder', ContentEdit._('Paste widget embed code') + '...');
      this._domInput.setAttribute('type', 'text');
      domControlGroup.appendChild(this._domInput);
      this._domButton = this.constructor.createDiv(['ct-control', 'ct-control--text', 'ct-control--insert', 'ct-control--muted']);
      this._domButton.textContent = ContentEdit._('Insert');
      domControlGroup.appendChild(this._domButton);
      return this._addDOMEventListeners();
    };

    WidgetIFrame.prototype.parseInput = function (text) {
      var parser = document.createElement('div');
      parser.innerHTML = text;
      var widgetIFrame = parser.querySelector('iframe');
      this.clearPreview();

      if (!widgetIFrame)
        return;

      return {
        scrolling: widgetIFrame.getAttribute('scrolling'),
        height: widgetIFrame.getAttribute('height'),
        width: widgetIFrame.getAttribute('width'),
        src: widgetIFrame.getAttribute('src'),
        title: widgetIFrame.getAttribute('src').substr(0, 82)
      };
    };

    WidgetIFrame.prototype.preview = function (url) {
      var parser = document.createElement('div');
      parser.innerHTML = url;
      var widgetIFrame = parser.querySelector('iframe');
      this.clearPreview();

      if (!widgetIFrame)
        return;

      this._domPreview = document.createElement('iframe');
      this._domPreview.setAttribute('frameborder', '0');
      this._domPreview.setAttribute('scrolling', widgetIFrame.getAttribute('scrolling'));
      this._domPreview.setAttribute('height', widgetIFrame.getAttribute('height'));
      this._domPreview.setAttribute('src', widgetIFrame.getAttribute('src'));
      this._domPreview.setAttribute('width', '100%');
      ContentEdit.removeCSSClass(this._domButton, 'ct-control--muted');
      return this._domView.appendChild(this._domPreview);
    };
    WidgetIFrame.prototype.save = function () {
      var embedURL, videoURL;
      videoURL = this._domInput.value.trim();
      return this.trigger('save', this.parseInput(videoURL));
      //}
    };
    WidgetIFrame.prototype.show = function () {
      WidgetIFrame.__super__.show.call(this);
      return this._domInput.focus();
    };
    WidgetIFrame.prototype.unmount = function () {
      if (this.isMounted()) {
        this._domInput.blur();
      }
      WidgetIFrame.__super__.unmount.call(this);
      this._domButton = null;
      this._domInput = null;
      return this._domPreview = null;
    };
    WidgetIFrame.prototype._addDOMEventListeners = function () {
      var _this = this;
      WidgetIFrame.__super__._addDOMEventListeners.call(this);

      this._domInput.addEventListener('input', function (ev) {
        var updatePreview;
        if (_this._updatePreviewTimeout) {
          clearTimeout(_this._updatePreviewTimeout);
        }

        updatePreview = function () {
          var embedURL, soundcloudURL;
          soundcloudURL = _this._domInput.value.trim();
          embedURL = soundcloudURL;
          if (embedURL) {
            return _this.preview(embedURL);
          } else {
            return _this.clearPreview();
          }
        };

        return _this._updatePreviewTimeout = setTimeout(updatePreview, 500);
      });

      this._domInput.addEventListener('keypress', (function (_this) {
        return function (ev) {
          if (ev.keyCode === 13) {
            return _this.save();
          }
        };
      })(this));
      return this._domButton.addEventListener('click', (function (_this) {
        return function (ev) {
          var cssClass;
          ev.preventDefault();
          cssClass = _this._domButton.getAttribute('class');
          if (cssClass.indexOf('ct-control--muted') === -1) {
            return _this.save();
          }
        };
      })(this));
    };
    return WidgetIFrame;
  })(ContentTools.DialogUI);
  ContentTools.Tools.WidgetIFrameEmbed = (function (superClass) {
    extend(SoundcloudEmbed, superClass);
    function SoundcloudEmbed() {
      return SoundcloudEmbed.__super__.constructor.apply(this, arguments);
    }

    ContentTools.ToolShelf.stow(SoundcloudEmbed, 'widget-embed');
    SoundcloudEmbed.label = 'Widget iframe';
    SoundcloudEmbed.icon = 'iframe';
    SoundcloudEmbed.canApply = function (element, selection) {
      return true;
    };
    SoundcloudEmbed.apply = function (element, selection, callback) {
      var app, dialog, modal;
      if (element.storeState) {
        element.storeState();
      }
      app = ContentTools.EditorApp.get();
      modal = new ContentTools.ModalUI();
      dialog = new ContentTools.SoundcloudDialog();
      dialog.bind('cancel', (function (_this) {
        return function () {
          dialog.unbind('cancel');
          modal.hide();
          dialog.hide();
          if (element.restoreState) {
            element.restoreState();
          }
          return callback(false);
        };
      })(this));

      dialog.bind('save', (function (_this) {
        return function (videoURL) {
          var index, node, ref, video;
          dialog.unbind('save');
          if (videoURL) {
            video = new ContentEdit.Video('iframe', {
              'frameborder': 0,
              'height': videoURL.height,
              'src': videoURL.src,
              title: videoURL.title,
              'width': videoURL.width
            });
            ref = _this._insertAt(element), node = ref[0], index = ref[1];
            node.parent().attach(video, index);
            video.focus();
          } else {
            if (element.restoreState) {
              element.restoreState();
            }
          }
          modal.hide();
          dialog.hide();
          return callback(videoURL !== '');
        };
      })(this));
      app.attach(modal);
      app.attach(dialog);
      modal.show();
      return dialog.show();
    };
    return SoundcloudEmbed;
  })(ContentTools.Tool);
}).call(this);
