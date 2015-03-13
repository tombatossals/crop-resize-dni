<?php

session_start();

if (!isset($_SESSION['idu'])) {
    header('Location: login.php');
    exit;
}

require "database.php";
$idu = $_SESSION['idu'];
$db = new Database();

if (!array_key_exists('idni', $_GET) && !array_key_exists('idni', $_POST)) {
    $db->insertDni($idu, file_get_contents($_FILES['img']['tmp_name']));
    header("Location: index.php");
    exit;
}


if (isset($_GET['del'])) {
  $idni = intval($_GET['idni']);
  $db->deleteDni($idu, $idni);
  header("Location: index.php");
  exit;
}

$idni = intval($_POST['idni']);

if (array_key_exists("pagina", $_POST)) {
   $db->setPage($idu, $idni, $_POST['pagina']);
   echo json_encode(array("success" => true));
   exit;
}

$ndni = $_POST['ndni'];
$cara = $_POST['cara'];

$db->updateDni($idu, $idni, $_POST['img'], $ndni, $cara);
header("Content-Type: application/json");
echo json_encode(array("success" => true));
