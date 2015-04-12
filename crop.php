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


$idni = intval($_GET['idni']);

$db->query('SELECT * FROM dnis where dnis.frm=:idu and dnis.idni=:idni');
$db->bind(':idu', $idu);
$db->bind(':idni', $idni);
$db->execute();

$image = $db->single();

// include and register Twig auto-loader
include 'vendor/autoload.php';

Twig_Autoloader::register();

  $admin = 0;
  if (array_key_exists("admin", $_SESSION)) {
      $admin = $_SESSION["admin"];
  }


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
    'image' => $image,
    'admin' => $admin
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
