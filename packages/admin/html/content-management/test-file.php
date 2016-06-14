<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "<p>this is the test file</p>";
?>
<script>
  (function () {
    //var test_file_2 = Scope.import('html/admin/content-management/test-file-2.php');
    //console.log('test-file->', test_file_2);

    System.state('forms/test-form', function (state) {
      state.stateKey = 'component';

      state.bind('init', function () {
        console.log('init', this.id);
      });

      state.bind('start', function () {
        console.log('start', this.id);
      });
    });

    Scope.export = {
      name: 'eeliya ' + (new Date()).valueOf() + ':' + performance.now(),
      state: System.state('forms/test-form')
    };
  })();
</script>