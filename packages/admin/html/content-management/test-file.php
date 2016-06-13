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
    System.state("forms/test-form", function (state) {
      state.stateKey = 'component';

      state.bind('init', function () {
        console.log('init', this.id);
      });

      state.bind('start', function () {
        console.log('start', this.id);
      });
    });

    Scope.export = {name: 'eeliya'};
  })();
</script>