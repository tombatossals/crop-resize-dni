<?php

session_start();

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
}

$idu = $_SESSION['idu'];

if ($idu <= 0) {
  exit;
}

#require('fpdf.php');
require('mem_image.php');
require('database.php');

$db = new Database();

if (array_key_exists('pagina', $_GET)) {
  $pagina = $_GET['pagina'];
} else {
  exit;
}

$images = $db->getPage($pagina, $idu);

$pdf = new PDF_MemImage();
$pdf->AddPage();

$y = 10;
foreach ($images["number"] as $image) {
        $pdf->MemImage(thumbnail($image[0]['img']), 10, $y);
        $pdf->MemImage(thumbnail($image[1]['img']), 120, $y);
        $y = $y + 55;
}

$pdf->Output();

function thumbnail($im) {
    $info = getimagesizefromstring($im);

    $type = isset($info['type']) ? $info['type'] : $info[2];

    // Check support of file type
    if ( !(imagetypes() & $type) ) {
        // Server does not support file type
        return false;
    }

    $width  = isset($info['width'])  ? $info['width']  : $info[0];
    $height = isset($info['height']) ? $info['height'] : $info[1];

    $maxSize = 300;

    // Calculate aspect ratio
    $wRatio = $maxSize / $width;
    $hRatio = $maxSize / $height;

    // Using imagecreatefromstring will automatically detect the file type
    $sourceImage = imagecreatefromstring($im);

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

    if ( $sourceImage === false ) {
        return false;
    }

    // Copy resampled makes a smooth thumbnail
    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
    imagedestroy($sourceImage);

    ob_start();
    imagejpeg($thumb);
    $img = ob_get_contents();
    ob_end_clean();

    return $img;
}
