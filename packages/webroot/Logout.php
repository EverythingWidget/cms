<?php

session_start();

$_SESSION['userLogin'] = null;

header("Location: index.php");

?>