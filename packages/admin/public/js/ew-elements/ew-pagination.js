/* global Vue */
(function () {
  function next() {
    var component = this;
    $.get(this.list.url, {
      page_size: component.list.page_size,
      page: component.list.page + 1
    }, function (response) {
      component.list = response;
    });
  }

  function previous() {
    var component = this;
    $.get(this.list.url, {
      page_size: component.list.page_size,
      page: component.list.page - 1
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
          page: 0
        }
      }
    },
    methods: {
      next: next,
      previous: previous
    },
    computed: {
      current_page: function () {
        if (!this.list.page) {
          return 1;
        }

        return this.list.page + 1;
      },
      total_pages: function () {
        return Math.ceil(this.list.total / this.list.page_size) || 1;
      },
      from: function () {
        return this.list.page * this.list.page_size;
      },
      till: function () {
        var till = this.from + this.list.page_size;
        return till > this.list.total ? this.list.total : till;
      },
      total: function () {
        return this.list.total || 0;
      }
    }
  });

  Vue.component('ew-pagination', MyComponent);
})();
