<?php

// include and register Twig auto-loader
include 'vendor/autoload.php';

if (array_key_exists("password", $_POST) && $_POST["password"] == "demo") {
    session_start();
    $_SESSION["idu"] = 1;
    header("Location: index.php");
    exit;
}

Twig_Autoloader::register();

try {
  // specify where to look for templates
  $loader = new Twig_Loader_Filesystem('templates');
  
  // initialize Twig environment
  $twig = new Twig_Environment($loader);
  
  // load template
  $template = $twig->loadTemplate('login.tmpl');
  
  // set template variables
  // render template
  echo $template->render(array());
  
} catch (Exception $e) {
  die ('ERROR: ' . $e->getMessage());
}
?>
