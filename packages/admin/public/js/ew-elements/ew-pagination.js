/* global Vue, System */
(function () {
  function onReady() {
    this.refresh();

//    if (true) {
//      this.setLazyLoading(true);
//    }
  }

  function refresh() {
    var component = this;
    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: component.list.start || 0,
      filter: this.filter
    }, function (response) {
      component.list = response;
      component.from = component.list.start || 0;
      component.till = component.from + component.list.page_size;
    });
  }

  function next(add) {
    var component = this;
    var start = component.list.start + component.list.page_size;

    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: start > component.total ? component.total : start
    }, function (response) {
      component.list = response;
      component.from = component.list.start || 0;
      component.till = component.from + component.list.page_size;
    });
  }

  function loadMore() {
    var component = this;
    var start = component.till;

    if (component.loading || start >= component.total) {
      return;
    }

    component.loading = true;

    var pageSize = component.list.page_size;

    $.get(this.list.url, {
      page_size: pageSize,
      start: start > component.total ? component.total : start
    }, function (response) {
      component.list.data = component.list.data.concat(response.data);
      var till = component.till + pageSize;
      component.till = till >= component.total ? component.total : till;

      component.loading = false;
    });
  }

  function previous() {
    var component = this;
    var start = component.list.start - component.list.page_size;

    $.get(this.list.url, {
      page_size: component.list.page_size,
      start: start < 0 ? 0 : start
    }, function (response) {
      component.list = response;
      component.from = component.list.start || 0;
      component.till = component.from + component.list.page_size;
    });
  }

  function isEndInViewPort() {
    var dimension = this.$el.getBoundingClientRect();

    if (dimension.bottom < window.innerHeight) {
      this.loadMore();
    }
  }

  function setLazyLoading(active) {
    var component = this;
    setInterval(function () {
      component.isEndInViewPort();
    }, 1000);
  }

  var EWPagination = Vue.extend({
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
      previous: previous,
      isEndInViewPort: isEndInViewPort,
      setLazyLoading: setLazyLoading,
      loadMore: loadMore
    },
    data: function () {
      return {
        from: 0,
        till: 0
      };
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
//      from: function () {
//        return this.list.start || 0;
//      },
//      till: function () {
//        var till = this.from + this.list.page_size;
//        return (till > this.list.total ? this.list.total : till) || 0;
//      },
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

  Vue.component('ew-pagination', EWPagination);
})();
