<?php

// include and register Twig auto-loader
include 'vendor/autoload.php';

if (array_key_exists("password", $_POST) && array_key_exists("username", $_POST)) {

    require "database.php";
    $db = new Database();

    $user = $db->auth($_POST["username"], $_POST["password"]);
    if ($user) {
        session_start();
        $_SESSION["idu"] = $user["idu"];

	if ($user["idu"] == 0) {
        	$_SESSION["admin"] = 1;
       		header("Location: admin.php");
		exit;
	}

       	header("Location: index.php");
        exit;

    }
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
