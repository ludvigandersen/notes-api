<?php
  require_once('db_conn.php');

  class Track {

    # Find tracks by album
    function findByAlbum($q){
      global $pdo;

$query = <<< 'SQL'
        SELECT track.name AS trackName, album.title as albumTitle, genre.name AS genre, track.unitPrice, track.trackId
        FROM track
        LEFT JOIN album ON album.albumid=track.albumid
        LEFT JOIN genre ON genre.genreid=track.genreid
        WHERE album.title LIKE ?
SQL;

      $stmt = $pdo->prepare($query);
      $stmt->execute(['%' . $q . '%']);
      $result = $stmt->fetchAll();

      return $result; 
    }

    # Find tracks by title
    function findByTitle($q, $t){
      global $pdo;

$query = <<< 'SQL'
        SELECT track.name AS trackName, album.title as albumTitle, genre.name AS genre, track.unitPrice, track.trackId
        FROM track
        LEFT JOIN album ON album.albumid=track.albumid
        LEFT JOIN genre ON genre.genreid=track.genreid
        WHERE track.name LIKE ?
SQL;

      $stmt = $pdo->prepare($query);
      $stmt->execute(['%' . $q . '%']);
      $result = $stmt->fetchAll();

      return $result; 
    }

  
    # Lists all existing tracks
    function list($p){
      global $pdo;

      if($p > 0){
        $p = $p * 25 + 1;
      } else {
        $p = 1;
      }

$query = <<< 'SQL'
      SELECT track.name AS trackName, album.title AS albumTitle, genre.name AS genre, track.unitPrice, track.trackId
      FROM track
      LEFT JOIN album ON album.albumid=track.albumid
      LEFT JOIN genre ON genre.genreid=track.genreid
      LIMIT 25 OFFSET :offset
SQL;
      
      $stmt = $pdo->prepare($query);
      $stmt->bindValue(':offset', (int) $p - 1, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll();

      return $result;
    }

    # Create new track
    function create($data){
      global $pdo;

$query = <<< 'SQL'
      INSERT INTO track
      (name, AlbumId, MediaTypeId, GenreId, Composer, UnitPrice, Milliseconds)
      VALUES (?, ?, ?, ?, ?, ?, ?)
SQL;

      # Convert seconds to ms
      $length = $data['length'] * 1000;
      
      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$data['title'], $data['albumId'], $data['mediaTypeId'], 
                              $data['genreId'], $data['composers'], $data['price'], $length]);

      # Check if query was successful
      if($result){
        return true;
      } else {
        return false;
      }
    }

    # Update track
    function update($id, $data){
      global $pdo;

$query = <<< 'SQL'
      UPDATE track
      set name=?, AlbumId=?, MediaTypeId=?, GenreId=?, Composer=?, UnitPrice=?, Milliseconds=?
      WHERE TrackId=?
SQL;

      # Convert seconds to ms
      $length = $data['length'] * 1000;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$data['title'], $data['albumId'], $data['mediaTypeId'], $data['genreId'], 
            $data['composers'], $data['price'], $length, $id]);

      # Check if query was successful
      if($result){
        return true;
      } else {
        return false;
      }
    }

    # Delete track with specified id
    function delete($id){
      global $pdo;

      # Check if track has been purchased
$query = <<< 'SQL'
      SELECT * 
      FROM invoiceline
      WHERE trackId = ?
SQL;
      $stmt = $pdo->prepare($query);
      $stmt->execute([$id]);
      $result = $stmt->fetch();

      if ($result > 0) {
        return json_encode(array('status' => "track has been purchased") );
      }

$query = <<< 'SQL'
      DELETE FROM track
      WHERE trackid=?
SQL;

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute([$id]);

      # Check if query was successful
      if($result){
        echo true;
      } else {
        echo false;
      }
    }
  }
?>