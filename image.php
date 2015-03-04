<?php

session_start();

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['idni'])) {
  exit;
}

$idni = intval($_GET['idni']);
$idu = $_SESSION['idu'];

if ($idni <= 0) {
  exit;
}

require "database.php";

$db = new Database();

$db->query('SELECT * FROM dnis, usuarios where idni=:idni and usuarios.idu=dnis.frm and usuarios.idu=:idu');
$db->bind(':idni', $idni);
$db->bind(':idu', $idu);
$db->execute();

$row = $db->single();

if ($row) {
    header("Content-type: image/jpeg");
    print_r($row["img"]);
}


function thumbnail($im) {
    $info = getimagesize($inputFileName);
 
    $type = isset($info['type']) ? $info['type'] : $info[2];

    // Check support of file type
    if ( !(imagetypes() & $type) )
    {
        // Server does not support file type
        return false;
    }

    $width  = isset($info['width'])  ? $info['width']  : $info[0];

    $height = isset($info['height']) ? $info['height'] : $info[1];

    // Calculate aspect ratio
    $wRatio = $maxSize / $width;
    $hRatio = $maxSize / $height;

    // Using imagecreatefromstring will automatically detect the file type
    $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));

    // Calculate a proportional width and height no larger than the max size.
    if ( ($width <= $maxSize) && ($height <= $maxSize) )
    {
        // Input is smaller than thumbnail, do nothing
        return $sourceImage;
    }
    elseif ( ($wRatio * $height) < $maxSize )
    {
        // Image is horizontal
        $tHeight = ceil($wRatio * $height);
        $tWidth  = $maxSize;
    }
    else
    {
        // Image is vertical
        $tWidth  = ceil($hRatio * $width);
        $tHeight = $maxSize;
    }

    $thumb = imagecreatetruecolor($tWidth, $tHeight);

    if ( $sourceImage === false )
    {
        // Could not load image
        return false;
    }

    // Copy resampled makes a smooth thumbnail
    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
    imagedestroy($sourceImage);

    return $thumb;
}
