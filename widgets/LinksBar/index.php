<?php
session_start();
include_once ($_SESSION['ROOT_DIR'] . '/config.php');
$categoryId = mysql_real_escape_string($widgetParameters['categoryId']);
$categoryInfo = mysql_fetch_array(mysql_query("SELECT * FROM content_categories WHERE id = '$categoryId'"));
$result = mysql_query("SELECT * FROM contents WHERE category_id = '$categoryId' ORDER BY contents.order") or die(mysql_error());
?>
<h4 ><?php echo $categoryInfo['title'] ?></h4>
<ul>
  <?php
  while ($row = mysql_fetch_array($result))
  {
    ?>		
    <li>
      <a class="Item"  href="<?php echo articleAddress($row['id']) ?>"><?php echo $row['title'] ?></a>            
    </li>
    <?php
  }
  ?>
</ul>
