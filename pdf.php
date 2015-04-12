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

function getCache($idu, $idni, $db) {
        $cache = "cache-pdf/" . $idu . "-" . $idni . ".jpg";
        if (file_exists($cache)) {
                return file_get_contents($cache);
        } else {
		$img = $db->getImage($idu, $idni);
		$thumb = thumbnail($img);
                $fp = fopen($cache, "w") or die("no");
                fwrite($fp, $thumb);
                fclose($fp);
		return $thumb;
        }
}

class PDF extends PDF_MemImage {
    function Footer() {
        $this->SetY(-15);
        $this->SetX(-15);
	$this->SetFont('Arial','I',8);
	$this->Cell(0,10,$this->pagina,0,0,'C');
    }
}

$pdf = new PDF();

if (array_key_exists('pagina', $_GET)) {
  $pagina = $_GET['pagina'];
  addPage($pagina, $pdf, $db, $idu);
} else if (array_key_exists('all', $_GET)) {
  $paginas = $db->getPages($idu);
  foreach ($paginas as $pag=>$val) {
      if ($pag > 0) {
          addPage($pag, $pdf, $db, $idu);
      }
  }
} else {
  exit;
}

function addPage($pagina, $pdf, $db, $idu) {
	$pdf->pagina = $pagina;
	$pdf->AddPage();
	$y = 8;

	$images = $db->getPage($pagina, $idu);

	foreach ($images["number"] as $image) {
		$img1 = getCache($idu, $image[0]['idni'], $db);
		$img2 = getCache($idu, $image[1]['idni'], $db);

		if (strlen($img1) > 0) {
        	    $pdf->MemImage($img1, 10, $y, -300);
                }
 
                if (strlen($img2) > 0) {
        	    $pdf->MemImage($img2, 110, $y, -300);
                }

        	$y = $y + 55;
	}
}

if ($pagina > 0 && array_key_exists('dw', $_GET)) {
  $pdf->Output('Hoja' . $pagina . '.pdf', 'D');
  header('Content-Type: unknown/unknown');
} else {
  $pdf->Output('Hoja' . $pagina . '.pdf', 'I');
}

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

    $maxSize = 1024;

    if ($width <= $maxSize && $height <= $maxSize) {
        $i2 = imagecreatetruecolor(1024, $height*1024/$width);
        $i = imagecreatefromstring($im);
        imagecopyresampled($i2, $i, 0, 0, 0, 0, 1024, $height*1024/$width, $width, $height);
        ob_start();
        imagejpeg($i2);
        $img = ob_get_contents();
        ob_end_clean();
        return $im;
    }

    // Calculate aspect ratio
    $wRatio = $maxSize / $width;
    $hRatio = $maxSize / $height;

    // Using imagecreatefromstring will automatically detect the file type
    $sourceImage = imagecreatefromstring($im);

    if ( ($wRatio * $height) < $maxSize )
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
