<?php
if ($secId)
{
    ?>
    <a class="BackButton" href="index.php?compId=<?php echo $compId ?>"></a>    
    <?php
}
else if ($compId)
{
    ?>
    <a class="BackButton" href="."></a>    
    <?php
}
?>
<span id="PageTitle">
    <?php
    echo $pageTitle;
    //echo $_SERVER['QUERY_STRING'];
    ?>

</span>
<?php
if ($_SESSION['login'])
{
    ?>
    <a class="ExitBtn" href="Logout.php" ></a>    
    <?php
}
?>