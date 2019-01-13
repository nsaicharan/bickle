<?php

session_start();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['search']) ) {
  
  require "config.php";

  if ( $_POST['key'] == 'incrementSiteClicks' ) {
    $query = $con->prepare("UPDATE sites SET clicks = clicks + 1 WHERE id = :id");
    $query->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
    $query->execute();
  } 

} else {
  header("Location: /");
}