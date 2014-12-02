<?php
session_start();
include_once ($_SESSION['ROOT_DIR'] . '/config.php');

$menuTitle1 = $widgetParameters['menuTitle1'];
$menuTitle2 = $widgetParameters['menuTitle2'];
$menuTitle3 = $widgetParameters['menuTitle3'];
$menuTitle4 = $widgetParameters['menuTitle4'];
$menuTitle5 = $widgetParameters['menuTitle5'];
$menuTitle6 = $widgetParameters['menuTitle6'];
$menuTitle7 = $widgetParameters['menuTitle7'];

$menuAddress1 = $widgetParameters['menuAddress1'];
$menuAddress2 = $widgetParameters['menuAddress2'];
$menuAddress3 = $widgetParameters['menuAddress3'];
$menuAddress4 = $widgetParameters['menuAddress4'];
$menuAddress5 = $widgetParameters['menuAddress5'];
$menuAddress6 = $widgetParameters['menuAddress6'];
$menuAddress7 = $widgetParameters['menuAddress7'];
?>
<div class="rainbow-menu">
    <?php
    if ($menuTitle1)
    {
        ?>
        <a href="<?php echo $menuAddress1 ?>" style="right:18px;top:32px;">
            <p>
                <?php echo $menuTitle1 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle2)
    {
        ?>
        <a href="<?php echo $menuAddress2 ?>" style="right:157px;top:27px;">
            <p>
                <?php echo $menuTitle2 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle3)
    {
        ?>
        <a href="<?php echo $menuAddress3 ?>" style="right:296px;top:23px;">
            <p>
                <?php echo $menuTitle3 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle4)
    {
        ?>
        <a href="<?php echo $menuAddress4 ?>" style="right:436px;top:17px;">
            <p>
                <?php echo $menuTitle4 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle5)
    {
        ?>
        <a href="<?php echo $menuAddress5 ?>" style="right:577px;top:22px;">
            <p>
                <?php echo $menuTitle5 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle6)
    {
        ?>
        <a href="<?php echo $menuAddress6 ?>" style="right:717px;top:17px;">
            <p>
                <?php echo $menuTitle6 ?>
            </p>
        </a>
        <?php
    }
    if ($menuTitle7)
    {
        ?>
        <a href="<?php echo $menuAddress7 ?>" style="right:856px;top:22px;">
            <p>
                <?php echo $menuTitle7 ?>
            </p>
        </a>
        <?php
    }
    ?>
</div>

