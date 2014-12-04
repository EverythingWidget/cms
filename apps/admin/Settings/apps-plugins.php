<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form name="apps-plugins-form" id="general-form" >
   <div class="row">
      <?php
      /*$apps = json_decode(EWCore::get_apps("all"), true);
      $i = 0;
      foreach ($apps as $app)
      {
         $i++;
         //print_r($app);
         ?>
         <div class="col-lg-3 col-md-4 col-sm-6 margin-bottom">
            <div class="box box-white">
               <div class="row">
                  <div class="col-xs-12">
                     <h2><?php echo $app["name"] ?>
                        <label class="small">version: <?php echo $app["version"] ?></label>
                     </h2>                  
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12" >
                     <h3>Languages</h3>
                  </div>       
               </div>
               <div class="row">
                  <div class="col-xs-12" >
                     <ul class="list indent">
                        <?php
                        $app_root = $app["root"];
                        $app_langs = json_decode(EWCore::get_app_languages($app_root), true);

                        foreach ($app_langs as $key => $lang)
                        {
                           echo "<li><a rel='ajax' class='link' href='app=$app_root,lang=$key,form=lang-editor'>{$lang["name"]}</a></li>";
                        }
                        ?>
                     </ul>
                  </div>       
               </div>
            </div>
         </div>

         <?php
         // Fix row ordering on different screen size
         if (($i % 4) == 0)
            echo '<div class="row-separator hidden-xs hidden-sm hidden-md"></div>';
         if (($i % 3) == 0)
            echo '<div class="row-separator hidden-xs hidden-sm hidden-lg"></div>';
         if (($i % 2) == 0)
            echo '<div class="row-separator hidden-xs hidden-md hidden-lg"></div>';
      }*/
      ?>

   </div>
</form>
<script type="text/javascript">
   EW.createModal({hash: {key: "form", value: "lang-editor"}, onOpen: function () {
         EW.lock(this);
         var modal = this;
         var lang = EW.getHashParameter("lang");
         var app = EW.getHashParameter("app");

         $.post("<?php echo EW_ROOT_URL; ?>app-admin/Settings/lanuage-editor-form.php", {app: app, lang: lang}, function (data) {
            modal.html(data);
         });
      },
      onClose: function () {
         EW.setHashParameter("form", null);
         EW.setHashParameter("lang", null);
         EW.setHashParameter("app", null);
      }});
</script>