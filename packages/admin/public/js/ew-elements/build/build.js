/* global Vue, System */
(function () {
  var properties = {
    id: String,
    autoInit: {
      type: Boolean,
      default: true
    },
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
    apiParams: {
      type: Object
    },
    lazyLoad: {
      type: Boolean
    },
    maxPageSize: {
      type: Number,
      default: 30
    },
    loading: {
      type: Boolean,
      default: false
    },
    onLoad: Function
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
    },
    params: function () {
      var component = this;

      return System.utility.extend({
        start: component.list.start,
        page_size: component.list.page_size,
        filter: component.filter
      }, component.apiParams || {});
    }
  };

  var methods = {
    onReady: function () {
      if (this.autoInit) {
        this.refresh();
      }
    },
    refresh: function () {
      var component = this;
      var params = component.params;
      params.start = component.list.start || 0;
      component.loading = true;

      component.$dispatch(component.id + '/load', component);

      $.get(this.list.url, params, function (response) {

        component.$dispatch(component.id + '/loaded', component, response);

        component.list = response;
        component.loading = false;
      });
    },
    next: function () {
      var component = this;
      var start = component.till;
      component.loading = true;

      var params = component.params;
      params.start = start > component.total ? component.total : start;

      component.$dispatch(component.id + '/load', component);

      $.get(this.list.url, params, function (response) {
        component.$dispatch(component.id + '/loaded', component, response);

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

      var params = component.params;
      params.start = start < 0 ? 0 : start;

      component.$dispatch(component.id + '/load', component);

      $.get(this.list.url, params, function (response) {
        component.$dispatch(component.id + '/loaded', component, response);

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
    template: "<div class=\"ew-pagination\">\r\n  <button class=\"btn btn-circle\" v-bind:disabled=\"from <= 0\" v-on:click=\"previous\">\r\n    <i class=\"icon-left-open-1\"></i>\r\n  </button>\r\n  <p>\r\n    <span class=\"from-till\">{{ from }} - {{ till }}</span>\r\n    <span class=\"of\"> <i class=\"icon-menu\"></i> </span>\r\n    <strong>{{ total }}</strong>\r\n  </p>\r\n  <button class=\"btn btn-circle\" v-bind:disabled=\"disableNext\" v-on:click=\"next\">\r\n    <i class=\"icon-right-open-1\"></i>\r\n  </button>\r\n</div>",
    props: properties,
    compiled: methods.onReady,
    methods: methods,
    computed: computeds,
    watch: watchers,
    events: events
  });

  Vue.component('ew-pagination', EWPagination);
})();
