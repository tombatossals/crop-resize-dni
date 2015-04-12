<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);


session_start();

if (array_key_exists("logout", $_GET)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
}

$idu = $_SESSION['idu'];

require "database.php";

$db = new Database();


$n = $db->getPages($idu);

end($n);
$key = key($n);
reset($n);

$paginas = array();
$paginas["nopage"] = $n["nopage"];

for ($i=0; $i<=$key; $i++) {
    if (array_key_exists($i, $n)) {
        $paginas[$i] = $n[$i];
    } else {
        $paginas[$i] = 0;
    }
}

$message = "";
$color = "";

$res = array();
if (array_key_exists("dni", $_POST)) {
	$res = $db->getDni($_POST['dni'], $idu);
	if (!$res) {
		$message = "El DNI no existeïx a la DB";
		$color = "error";
	} else {
		$message = "El DNI " . $_POST['dni'] . " existeïx";
		$color = "positive";
	}


}

// include and register Twig auto-loader
include 'vendor/autoload.php';

Twig_Autoloader::register();

try {
  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('recerca.tmpl');
  
  echo $template->render(array(
	"message" => $message,
	"color" => $color,
	"paginas" => $paginas,
	"res" => $res
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
