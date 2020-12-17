<?php   
  session_start();
  
  require_once('src/functions.php');

  define('ENTITY', 2);
  define('ID', 3);

  $url = strtok($_SERVER['REQUEST_URI'], "?");

  if (substr($url, strlen($url) - 1) == '/') {
    $url = substr($url, 0, strlen($url) - 1);
  }

  header('Content-Type: application/json');
  header('Accept-version: v1');

  # ALLOW ONLY ACCESS FROM NOTES.COM
  # CURRENTLY WILDCARD - INSECURE
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: *");

  $urlPieces = explode('/', urldecode($url));
  $pieces = count($urlPieces);

  if($pieces == "2" && $urlPieces[1] == 'v1') { 
    echo APIDescription();
  } elseif ($pieces > "4" || $pieces == "1" || $urlPieces[1] != 'v1') {
    echo formatError();
  } else {

    $entity = $urlPieces[ENTITY];
    $method = $_SERVER['REQUEST_METHOD'];
  
    switch ($entity) {
      # CRUD operations for User entity
      case 'user':
        require_once('src/user.php');
        $user = new User();
        
        switch ($method) {
          case 'POST':
            if($pieces == 4){
              $id = $urlPieces[ID];
              echo $user->update($id, $_POST);
            } else {
              echo $user->create($_POST);
            }
            break;
        }
        break;
      
      # CRUD operations for Artist entity
      case 'artist':
        require_once('src/artist.php');
        $artist = new Artist();

        switch ($method) {
          case 'GET':
            if(isset($_GET['p'])){
              echo json_encode($artist->list($_GET['p']));
            } else {
              echo json_encode($artist->list(null));
            }
            break;
          
          case 'POST':
            if($pieces == 4){
              $id = $urlPieces[ID];
              echo $artist->update($id, $_POST['name']);
            } else {
              echo $artist->create($_POST['name']);
            }
            break;

          case 'PUT':
            $artistName = file_get_contents('php://input');
            $id = $urlPieces[ID];
            $artist->update($id, $artistName);
          break;

          case 'DELETE':
            $id = $urlPieces[ID];
            echo $artist->delete($id);
            break;
        }
      break;

      # CRUD operations for Album entity
      case 'album':
        require_once('src/album.php');
        $album = new Album();
        switch ($method) {
          case 'GET':
            if(isset($_GET['p'])){
              echo json_encode($album->list($_GET['p']));
            } else {
              echo json_encode($album->list(null));
            }
            break;
          
          case 'POST':
            if($pieces == 4){
              $id = $urlPieces[ID];
              echo $album->update($id, $_POST);
              break;
            } else {
              
              echo $album->create($_POST['name'], $_POST['artistId']);
              break;
            }
            break;

          case 'PUT':
            break;
          
          case 'DELETE':
            $id = $urlPieces[ID];
            echo $album->delete($id);
            break;
        }
      break;

      # CRUD operations for Track entity
      case 'track':
        require_once('src/track.php');
        $track = new Track();
        switch ($method) {
          case 'GET':
            if(isset($_GET['q'])){
              switch ($_GET['t']) {
                case 'title':
                  echo json_encode($track->findByTitle($_GET['q'], $_GET['t']));
                  break;
                case 'album':
                  echo json_encode($track->findByAlbum($_GET['q'], $_GET['t']));
                  break;
              }
              
            } else {
              echo json_encode($track->list($_GET['p']));
            }
            
            break;
          case 'POST':
            if($pieces == 4){
              $id = $urlPieces[ID];
              echo $track->update($id, $_POST);
            } else {
              echo $track->create($_POST);
            }
            break;
          case 'DELETE':
            $id = $urlPieces[ID];
            echo $track->delete($id);
            break;
        }
      break;
      # CRUD operations for Invoice entity
      case 'invoice':
        require_once('src/invoice.php');
        $invoice = new Invoice();
        switch ($method) {
          case 'POST':
            echo $invoice->create($_POST);
            break;
        }
      break;

      # CRUD operations for Genre entity
      case 'genre':
        require_once('src/genre.php');
        $genre = new Genre();
        switch ($method) {
          case 'GET':
            echo json_encode($genre->list());
            break;
          
          default:
            # code...
            break;
        }
      default:
        # code...
        break;
    }    
  }
?>