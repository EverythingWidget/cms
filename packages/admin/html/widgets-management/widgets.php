<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>

   var t = EW.createTable({name: "widgets-types-list", 
      headers: {Name: {}, Description: {}}, 
      columns: ["title", "description"], rowCount: true, url: "<?php echo EW_ROOT_URL; ?>~webroot-api/widgets-management/get_widgets_types", pageSize: 30});
   $('#main-content').html(t.container);


</script>