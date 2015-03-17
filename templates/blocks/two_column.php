<?php
/*
 * title: Two Columns Block
 * description: A block with two pre defined columns
 */

class two_column
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
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="row"></div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="row"></div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

}
