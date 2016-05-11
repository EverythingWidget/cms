(function () {
  System.Property = SystemProperty;

  function SystemProperty(data) {
    if (typeof data !== 'object') {
      throw new Error('System.Property only accept object as the data');
    }
    var property = this;

    this.consumers = [];

    for (var property in data) {
      if (data.hasOwnProperty(property)) {
        Object.defineProperty(data, property, {
          enumerable: true,
          set: function (value) {
            this['_' + property] = value;
          }
        });
      }
    }

    this.data = data;
    //var oldData = JSON.stringify(data);

//    function watch() {
//      var newData = JSON.stringify(data);
//      if (oldData !== newData) {
//        clearTimeout(property.watcher);
//        property.watcher = null;
//        oldData = newData;
//        property.data = data;
//        property.changed();
//      }
//      property.watcher = setTimeout(watch, 100);
//    }
//
//    watch();
  }

  SystemProperty.prototype.changed = function () {
    for (var i = 0, len = this.consumers.length; i < len; i++) {
      this.consumers[i].data = this;
    }
  };

  SystemProperty.prototype.registerConsumer = function (consumer) {
    if (!consumer.systemPropertyRefId) {
      consumer.systemPropertyRefId = '@' + Date.now() + Math.random() * 16;
    }

    var exist = this.consumers.filter(function (item) {
      return item.systemElementId === consumer.systemPropertyRefId;
    });

    if (!exist.length) {
      this.consumers.push(consumer);
    }
  };

})();