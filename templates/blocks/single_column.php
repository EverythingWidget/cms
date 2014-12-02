<?php
/*
 * title: Single Columns Block
 * description: A block with single pre defined column
 */

class single_column
{

   public static function block_config()
   {
      return null;
   }

   public static function initiate()
   {
      
   }

   public static function block_html()
   {
      ob_start();
      ?>
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row"></div>
         </div>
      </div>
      <?php
      return ob_get_clean();
   }

}
