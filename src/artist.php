<?php 
  require_once('db_conn.php');  
  
  class Artist {

    # Lists all existing artists
    function list($p){
      global $pdo;

      if($p == null) {
$query = <<< 'SQL'
        SELECT * FROM artist
SQL;

      } else {

        if($p > 0){
          $p = $p * 25 + 1;
        } else {
          $p = 1;
        }

$query = <<< 'SQL'
        SELECT * FROM artist
        LIMIT 25 OFFSET :offset
SQL;
      }
      
      $stmt = $pdo->prepare($query);
      $stmt->bindValue(':offset', (int) $p - 1, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll();
      
      return $result;
    }

    # Create a new artist
    function create($name){
      global $pdo;

$query = <<< 'SQL'
      INSERT INTO artist (name)
      VALUES (?)
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$name]);
      $artistId = $pdo->lastInsertId();

      # Check if query was successful
      if($result){
        return json_encode(array("status"=>"artist created", "artistId"=>$artistId));
      } else {
        return json_encode(array("status"=>"creation failed"));
      }
    }

    # Update artist by id
    function update($id, $name){
      global $pdo;

$query = <<< 'SQL'
      UPDATE artist
      SET name=?
      WHERE ArtistId=?
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$name, $id]);

      if($result){
        return json_encode(array('status'=>'artist updated', 'artistId'=>$id));
      } else {
        return json_encode(array('status'=>'update failed'));
      }
    }

    # Delete artist by id
    function delete($id){
      global $pdo;

      # Check if artist has an album
      # If yes then return message
$query = <<< 'SQL'
      SELECT AlbumId
      FROM album
      WHERE ArtistId=?
SQL;

      $stmt = $pdo->prepare($query);
      $stmt->execute([$id]);
      $result = $stmt->fetch();

      if($result > 1) {
        return json_encode(array('status'=>'artist has an album'));
      }

      # If artist does not have an album, finish deletion
$query = <<< 'SQL'
      DELETE FROM artist
      WHERE ArtistId=?
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$id]);

      if($result){
        return json_encode(array('status'=>'artist deleted', 'artistId'=>$id));
      } else {
        return json_encode(array('status'=>'deletion failed'));
      }
    }
  }
?>