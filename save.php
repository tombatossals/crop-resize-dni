<?php

session_start();

if(stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
}

require "database.php";
$idu = $_SESSION['idu'];
$db = new Database();

if (array_key_exists("pagina", $_POST) && array_key_exists("ndni", $_POST)) {
   $db->setPage($idu, $_POST['ndni'], $_POST['pagina']);
   echo json_encode(array("success" => true));
   exit;
}

if (!array_key_exists('idni', $_GET) && !array_key_exists('idni', $_POST)) {
    $db->insertDni($idu, file_get_contents($_FILES['img']['tmp_name']));
    header("Location: index.php");
    exit;
}


if (isset($_GET['del'])) {
  if (array_key_exists('idni', $_GET)) {
      $idni = intval($_GET['idni']);
      $db->deleteDni($idu, $idni);
  }

  if (array_key_exists('idni2', $_GET)) {
      $idni2 = intval($_GET['idni2']);
      $db->deleteDni($idu, $idni2);
  }
  header("Location: index.php");
  exit;
}

$idni = intval($_POST['idni']);

$ndni = $_POST['ndni'];
$cara = $_POST['cara'];

$db->updateDni($idu, $idni, $_POST['img'], $ndni, $cara);
header("Content-Type: application/json");
echo json_encode(array("success" => true));
