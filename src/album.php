<?php
  require_once('db_conn.php'); 

  class Album {

    # Lists all existing albums
    function list($p){
      global $pdo;

      if ($p == null){
$query = <<< 'SQL'
        SELECT artist.name, album.title, album.albumId, artist.artistId
        FROM album
        LEFT JOIN artist ON album.ArtistId = artist.ArtistID
        ORDER BY artist.name
SQL;
      } else {

        if($p > 0){
          $p = $p * 25 + 1;
        } else {
          $p = 1;
        }
$query = <<< 'SQL'
        SELECT artist.name, album.title, album.albumId, artist.artistId
        FROM album
        LEFT JOIN artist ON album.ArtistId = artist.ArtistID
        ORDER BY artist.name
        LIMIT 25 OFFSET :offset 

SQL;
      }

      $stmt = $pdo->prepare($query);
      $stmt->bindValue(':offset', (int) $p - 1, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll();
      
      return $result;
    }

    # Create a new album
    function create($name, $artistId){
      global $pdo;

$query = <<< 'SQL'
      INSERT INTO album (title, artistId)
      VALUES (?, ?)
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$name, $artistId]);
      $albumId = $pdo->lastInsertId();

      # Check if query was successful
      if($result){
        return json_encode(array("status"=>"album created", "albumId"=>$albumId));
      } else {
        return json_encode(array("status"=>"creation failed"));
      }
    }

    # Update an album
    function update($id, $data){
      global $pdo;

$query = <<< 'SQL'
      UPDATE album
      SET title=?, ArtistId=?
      WHERE AlbumId=?
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$data['title'], $data['artistId'], $id]);
      
      # Check if query was successful
      if($result){
        return json_encode(array('status'=>'album updated', 'albumId'=>$id));
      } else {
        return json_encode(array('status'=>'update failed'));
      }
    }

    # Delete an album
    function delete($id){
      global $pdo;

      # Check if album has a track
      # If yes then return message
$query = <<< 'SQL'
      SELECT * FROM track
      WHERE AlbumId=?
SQL;

      # 
      $stmt = $pdo->prepare($query);
      $stmt->execute([$id]);
      $result = $stmt->fetch();
      
      if($result >= 1) {
        # Maybe Return instead of json_encode?
        return json_encode(array('status'=>'album has associated tracks'));
      }

      # Delete album query
$query = <<< 'SQL'
      DELETE FROM album
      WHERE AlbumId=?
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$id]);

      # Check if query was successful
      if($result){
        return json_encode(array('status'=>'album deleted', 'albumId'=>$id));
      } else {
        return json_encode(array('status'=>'deletion failed'));
      }
    }
  }
?>