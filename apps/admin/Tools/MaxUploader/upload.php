<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
// Edit upload location here
$destination_path = $_SESSION['ROOT_DIR'] . '/images/';

$result = 0;

$filename = pathinfo($_FILES['myfile']['name'], PATHINFO_FILENAME);
$extension = pathinfo($_FILES['myfile']['name'], PATHINFO_EXTENSION);
$FinalFilename = $_FILES['myfile']['name'];
$FileCounter = 1;
while (file_exists($destination_path . $FinalFilename))
{
  $FinalFilename = $filename . '_' . $FileCounter++ . '.' . $extension;
}
$target_path = $destination_path . $FinalFilename;
if (@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path))
{
  $result = 1;
}

sleep(1);
?>

<script language="javascript" type="text/javascript">  
  window.top.window.stopUpload(<?php echo $result; ?> , '<?php echo $HOST_ROOT . '/images/' . $FinalFilename ?>');
</script>   
