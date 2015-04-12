<?php
  
  try {
    $db = new PDO("mysql:host=;dbname=recogidadnis;charset=UTF8",'recogidadnis','recogidadnisLALALA');
  } catch (PDOException $e) { die($e->getMessage()); }
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  echo "Hojas ";
  list($h)=$db->query("select count(distinct concat(frm,hoja)) from dnis where hoja != 0")->fetch(PDO::FETCH_NUM);
  echo "$h<br>DNIS: ";
  list($d)=$db->query("select count(distinct concat(concat(frm,hoja),ndni)) from dnis where hoja != 0")->fetch(PDO::FETCH_NUM);
  echo "$d<br>";
