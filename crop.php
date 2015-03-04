<?php

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


$db->query('SELECT * FROM dnis where dnis.frm=:idu');
$db->bind(':idu', $idu);
$db->execute();

$image = $db->single();

// include and register Twig auto-loader
include 'vendor/autoload.php';

Twig_Autoloader::register();

try {
  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('crop.tmpl');
  
  // set template variables
  // render template
  echo $template->render(array(
    'image' => $image
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
