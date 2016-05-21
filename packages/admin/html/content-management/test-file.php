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
      });

      state.bind('start', function () {
      });
    });
  })();
</script>