<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$app_main_form = EWCore::read_registry("ew-section-main-form");
if ($app_main_form && $form_config)
   $app_main_form = array_merge_recursive($app_main_form, $form_config);

if ($app_main_form["sidebar"])
{
   echo '<div id="app-bar-nav" class="app-bar-nav">'
   . $app_main_form["sidebar"]["content"]
   . '</div>';
}
?>

<div id="main-content" class="col-xs-12" role="main">
   <?php
   //$main_content = EWCore::read_registry("ew-section-main-form");
   if ($app_main_form["content"])
   {
      echo $app_main_form["content"]["content"];
   }
   ?>
</div>
<?php
echo $app_main_form["script"];
