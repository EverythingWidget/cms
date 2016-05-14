<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
  <head>
    <base href="<?php echo EW_ROOT_URL ?>">
    <meta charset="UTF-8">
    <title>EW Admin2</title>
    <script src="https://vuejs.org/js/vue.js" ></script>
    <script src="https://cdn.jsdelivr.net/vue.router/0.7.10/vue-router.min.js"></script>
    <script src="~ew-admin/public/js/app.js"></script>
  </head>
  <body id="ew-app">

    <ul id="apps">
      <li v-for="item in apps">        
        <a > {{ item.title }} </a>
      </li>
    </ul>

  <router-view> </router-view>

</body>
</html>
