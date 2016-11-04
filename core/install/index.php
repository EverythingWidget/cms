<?php
if ($_REQUEST["install"])
{
  $res = EWCore::import_sql(EW_ROOT_DIR . "core/install/database-import.sql");
  $_REQUEST["install"] = null;
  if ($res)
  {
    header("Location: html/admin/");
    echo "<h1>Database installed successfully</h1>";
    echo "<h3><a href='.'> refresh</a></h3>";
  }
  else
  {
    echo "<h1>Database is NOT installed</h1>";
    echo "<h3><a href='.'> refresh</a></h3>";
  }
  session_destroy();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
  </head>
  <body>
    <h1>Install Database</h1>
    <form method="post" action="">
      <input type="hidden" name="install" value="true"/>
      <button type="submit">Install</button>
    </form>
  </body>
</html>
