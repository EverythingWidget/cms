<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "<p>this is the test file</p>";
?>
<import name="test_file_2" from="html/admin/content-management/test-file-2.html" />
<script>
  (function () {
    //var test_file_2 = Scope.import('html/admin/content-management/test-file-2.php');
    //console.log('test-file->', test_file_2);

    System.state('forms/test-form', function (state) {
      state.stateKey = 'component';

      state.onInit = function () {
        console.log('init', this.id);
      };

      state.onStart = function () {
        console.log('start', this.id);
      };
    });

//    Scope.export = {
//      name: 'eeliya ' + (new Date()).valueOf() + ':' + performance.now(),
//      state: System.state('forms/test-form')
//    };

    Scope.export = {
      name: 'eeliya ' + (new Date()).valueOf() + ':' + performance.now()
    };
  })();
</script>