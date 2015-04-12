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

if (!isset($_SESSION['idu']) || $_SESSION['admin'] != True) {
    header('Location: login.php');
    exit;
}

$idu = 12;

require "database.php";

$db = new Database();

$message = "";
$color = "error";

$dni1="";
if (array_key_exists("dni1", $_POST) && $_POST["dni1"]) {
    $dni1 = $_POST["dni1"];
}

$dni2="";
if (array_key_exists("dni2", $_POST) && $_POST["dni2"]) {
    $dni2 = $_POST["dni2"];
}
$dni3="";
if (array_key_exists("dni3", $_POST) && $_POST["dni3"]) {
    $dni3 = $_POST["dni3"];
}
$dni4="";
if (array_key_exists("dni4", $_POST) && $_POST["dni4"]) {
    $dni4 = $_POST["dni4"];
}
$dni5="";
if (array_key_exists("dni5", $_POST) && $_POST["dni5"]) {
    $dni5 = $_POST["dni5"];
}
if (array_key_exists("dni1", $_POST)) {
	$paginas = $db->getPages($idu);
	$last = 0;
	foreach ($paginas as $key=>$value) {
    		if ($key > 0) {
        		if ((intval($last) + 1) != $key) {
				break;
			}
			$last = $key;
    		}
	}

	$numero_nueva = $last + 1;

	if (!$db->existe_hoja($numero_nueva, $idu)) {
		$lista = array();

		foreach ($_POST as $key=>$dni) {
			if (!$dni) continue;
			if ($db->dni_pendiente($dni, $idu)) {
				$lista[] = $dni;
			} else {
				$message = "El DNI " . $dni . " no está registrado, no tiene las dos imágenes o ya está asignado a una hoja";
				break;
			}
		}

		if (!$message && count($lista)>0) {
			$message = "Todo OK, esos dnis pasan a la hoja " . $numero_nueva ;
			$color = "positive";
			foreach ($lista as $dni) {
   				$db->setPage($idu, $dni, $numero_nueva);
			}
			$dni1 = "";
			$dni2 = "";
			$dni3 = "";
			$dni4 = "";
			$dni5 = "";
		}
		
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
  $template = $twig->loadTemplate('assignar-fulla.tmpl');
  
  echo $template->render(array(
	"message" => $message,
	"color" => $color,
	"dni1" => $dni1,
	"dni2" => $dni2,
	"dni3" => $dni3,
	"dni4" => $dni4,
	"dni5" => $dni5
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
