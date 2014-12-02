<?php

include_once '../../config.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $HOST_ROOT_DIR, $HOST_ROOT_URI;
$dir = $HOST_ROOT_DIR . '/images';
$files = scandir($dir);
$array = "[";
foreach ($files as $f)
{
    if (is_dir($dir . '/' . $f) && $f != '..' && $f != '.')
    {
        $array.=listDir($dir . '/' . $f, $f);
    }
    else if (!strpos($f, '.thumb'))
    {
        if (file_exists($dir . '/' . $f . '.thumb'))
        {
            $array.='{ "thumb": "' . $HOST_ROOT_URI . '/images/' . $f . '.thumb" , "image": "' . $HOST_ROOT_URI . '/images/' . $f . '"},';
        }
        else
        {
            $array.='{ "thumb": "' . $HOST_ROOT_URI . '/images/' . $f . '" , "image": "' . $HOST_ROOT_URI . '/images/' . $f . '"},';
        }
    }
}
$array.="{}]";
echo stripslashes($array);

function listDir($dir, $dn)
{
    global $HOST_ROOT_DIR, $HOST_ROOT_URI;
    $files = scandir($dir);
    foreach ($files as $f)
    {
        if (!strpos($f, '.thumb'))
        {
            if (file_exists($dir . '/' . $dn . '/' . $f . '.thumb'))
            {
                $array.='{ "thumb": "' . $HOST_ROOT_URI . '/images/' . $dn . '/' . $f . '.thumb" , "image": "' . $HOST_ROOT_URI . '/images/' . $dn . '/' . $f . '"},';
            }
            else
            {
                $array.='{ "thumb": "' . $HOST_ROOT_URI . '/images/' . $dn . '/' . $f . '" , "image": "' . $HOST_ROOT_URI . '/images/' . $dn . '/' . $f . '"},';
            }
        }
    }
    return $array;
}

?>
