/* global Vue, System */
(function () {
  var properties = {
    list: {
      twoWay: true,
      default: {
        page_size: 10,
        page: 0
      }
    },
    filter: {
      default: function () {
        return {};
      }
    },
    lazyLoad: {
      type: Boolean
    },
    maxPageSize: {
      type: Number,
      default: 30
    }
  };

  var computeds = {
    current_page: function () {
      if (!this.list.start) {
        return 1;
      }

      return this.list.start + 1;
    },
    total_pages: function () {
      return Math.ceil(this.list.total / this.list.page_size) || 1;
    },
    disableNext: function () {
      if (this.lazyLoad) {
        return this.till >= this.total || (this.list.data ? this.list.data.length < this.maxPageSize : false);
      } else {
        return this.till >= this.total;
      }
    },
    from: function () {
      return this.list.start || 0;
    },
    till: function () {
      var till = this.from + this.list.page_size;
      return (till > this.list.total ? this.list.total : till) || 0;
    },
    total: function () {
      return this.list.total || 0;
    }
  };

  var methods = {
    onReady: function () {
      this.refresh();
    },
    refresh: function () {
      var component = this;
      $.get(this.list.url, {
        page_size: component.list.page_size,
        start: component.list.start || 0,
        filter: this.filter
      }, function (response) {
        component.list = response;
      });
    },
    next: function () {
      var component = this;
      var start = component.till;
      component.loading = true;

      $.get(this.list.url, {
        page_size: component.list.page_size,
        start: start > component.total ? component.total : start
      }, function (response) {
        component.list = response;
        component.loading = false;
      });
    },
    previous: function () {
      var component = this;
      var start = component.list.start - component.list.page_size;
      component.loading = true;

      if (this.lazyLoad) {
        start = component.list.start - this.maxPageSize;
      }

      $.get(this.list.url, {
        page_size: component.list.page_size,
        start: start < 0 ? 0 : start
      }, function (response) {
        component.list = response;
        component.loading = false;
      });
    }
  };

  var watchers = {
    'list.page_size': function (value, oldValue) {
      if (oldValue !== value) {
        this.refresh();
      }
    }
  };

  var events = {
    refresh: methods.refresh
  };

  var EWPagination = Vue.extend({
    template: {gulp_inject: './ew-pagination.html'},
    props: properties,
    compiled: methods.onReady,
    methods: methods,
    computed: computeds,
    watch: watchers,
    events: events
  });

  Vue.component('ew-pagination', EWPagination);
})();
