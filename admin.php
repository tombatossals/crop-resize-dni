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

if (array_key_exists("user", $_GET)) {
    $_SESSION['idu'] = $_GET["user"];
    header('Location: index.php');
    exit;
}

$idu = $_SESSION['idu'];

require "database.php";

$db = new Database();

$usuarios = $db->getUsers();
$total = $db->getNumTotalDnis();

// include and register Twig auto-loader
include 'vendor/autoload.php';

Twig_Autoloader::register();

try {
  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('admin.tmpl');
  
  echo $template->render(array(
    'usuarios' => $usuarios,
    'total' => $total
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
