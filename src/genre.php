<?php
  require_once('db_conn.php');

  class Genre{

    function list(){
      global $pdo;

$query = <<< 'SQL'
      SELECT GenreId AS genreId, name AS genre
      FROM genre
SQL;

      $stmt = $pdo->prepare($query);
      $stmt->execute();
      $result = $stmt->fetchAll();

      return $result;
    }
  }
?>