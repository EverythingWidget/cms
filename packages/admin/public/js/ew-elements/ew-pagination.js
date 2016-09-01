/* global Vue */

var MyComponent = Vue.extend({
  template: '<div>A custom component! <slot></slot></div>'
});

Vue.component('ew-pagination', MyComponent);