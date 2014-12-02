<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
$menuId = mysql_real_escape_string($_REQUEST['menuId']);
$result  = mysql_query("SELECT * FROM menus , sub_menus WHERE menus.id = sub_menus.menu_id AND menus.id = '$menuId' ORDER BY sub_menus.order") or die(mysql_error());
while ($row = mysql_fetch_array($result))
{
    ?>		
    <a class="Item" href="./<?php echo $row['path'] ?>">
        <span></span>
        <?php echo $row['title']; ?>
    </a>
    <?php
}
?>
