/* global Vue */
(function () {
  function onReady() {
    this.refresh();
  }

  function refresh() {
    var component = this;
    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: component.list.start || 0,
      filter: this.filter
    }, function (response) {
      component.list = response;
    });
  }

  function next() {
    var component = this;
    var start = component.list.start + component.list.page_size;


    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: start > component.total ? component.total : start
    }, function (response) {
      component.list = response;
    });
  }

  function previous() {
    var component = this;
    var start = component.list.start - component.list.page_size

    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: start < 0 ? 0 : start
    }, function (response) {
      component.list = response;
    });
  }

  var MyComponent = Vue.extend({
    template: {gulp_inject: './ew-pagination.html'},
    props: {
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
      }
    },
    compiled: onReady,
    methods: {
      refresh: refresh,
      next: next,
      previous: previous
    },
    computed: {
      current_page: function () {
        if (!this.list.start) {
          return 1;
        }

        return this.list.start + 1;
      },
      total_pages: function () {
        return Math.ceil(this.list.total / this.list.page_size) || 1;
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
    },
    watch: {
      'list.page_size': function (value, oldValue) {
        if (oldValue !== value) {
          this.refresh();
        }
      }
    },
    events: {
      'refresh': refresh
    }
  });

  Vue.component('ew-pagination', MyComponent);
})();
