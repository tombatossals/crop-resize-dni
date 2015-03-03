<?php

session_start();

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
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
  $template = $twig->loadTemplate('front.tmpl');
  
  // set template variables
  // render template
  echo $template->render(array(
    'name' => 'Clark Kent',
    'username' => 'ckent',
    'password' => 'krypt0n1te',
  ));
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
