var path = require('path');

var System = require(path.resolve('./packages/admin/public/js/lib/') + '/system').System;

System.init(); 
//var g = require('../groups/component');

var assert = require('chai').assert;


//console.log(System);
describe('Array', function () {
  describe('#indexOf()', function () {
    it('should return -1 when the value is not present', function () {
      assert.equal(-1, [1, 2, 3].indexOf(5));
      assert.equal(-1, [1, 2, 3].indexOf(0));
    });
  });
});