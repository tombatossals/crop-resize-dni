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

$section = "";
if (isset($_GET['seccio']) && $_GET['seccio'] == 'assignar') {
    $section = "assignar";
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

$pagina = 0;
if (array_key_exists('pagina', $_GET)) {
  $pagina = $_GET['pagina'];
}

/*
if (count($paginas) > 0 && !array_key_exists($pagina, $paginas)) {
        $keys = array_keys($paginas);
	header("Location: index.php?pagina=" . array_shift($keys));
	exit;
}
*/

$images = $db->getPage($pagina, $idu);

// include and register Twig auto-loader
include 'vendor/autoload.php';

Twig_Autoloader::register();

try {
  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('front.tmpl');
  
  // set template variables
  // render template
  $pagina = 0;
  if (array_key_exists('pagina', $_GET)) {
    $pagina = $_GET['pagina'];
  }

  $admin = 0;
  if (array_key_exists("admin", $_SESSION)) {
      $admin = $_SESSION["admin"];
  }

  echo $template->render(array(
    'images' => $images,
    'pagina' => $pagina,
    'paginas' => $paginas,
    'section' => $section,
    'admin' => $admin
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
