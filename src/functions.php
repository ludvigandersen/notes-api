<?php
  function APIDescription(){
    $baseUrl = "{host}/notes-api/v1/";

    $apiDescription['api-description'] = ['method' => 'GET', 'url' => $baseUrl];

    $apiDescription['artist'] = [
      ['about' => 'Get all artists', 'method' => 'GET', 'url' => $baseUrl . 'artist'],
      ['about' => 'Create new artist', 'method' => 'POST', 'url' => $baseUrl . 'artist', 'request-body' => ["name" => '']],
      ['about' => 'Update artist', 'method' => 'PUT', 'url' => $baseUrl . 'artist/:id', 'request-body' => ["name" => '']],
      ['about' => 'Delete artist', 'method' => 'DELETE', 'url' => $baseUrl . 'artist/:id']
    ];

    $apiDescription['album'] = [
      ['about' => 'Get all albums based on page # - Max 25 records pr. page', 'method' => 'GET', 'url' => $baseUrl . 'album?p={page}'],
      ['about' => 'Create new album', 'method' => 'POST', 'url' => $baseUrl . 'album', 'request-body' => ["name" => '', "artistId" => '']],
      ['about' => 'Update album', 'method' => 'POST', 'url' => $baseUrl . 'album/:id', 'request-body' => ["name" => '']],
      ['about' => 'Delete album', 'method' => 'DELETE', 'url' => $baseUrl . 'album/:id']
    ];

    $apiDescription['track'] = [
      ['about' => 'Get all tracks based on page # - Max 25 records pr. page', 'method' => 'GET', 'url' => $baseUrl . 'track?p={page}'],
      ['about' => 'Create new track', 'method' => 'POST', 'url' => $baseUrl . 'track', 'request-body' => [
        "title" => '', "albumId" => '', "mediaTypeId" => '', "genreId" => '', "composers" => '', "price" => '', "length" => ''
      ]],
      ['about' => 'Update track', 'method' => 'POST', 'url' => $baseUrl . 'track/:id', 'request-body' => [
        "title" => '', "albumId" => '', "mediaTypeId" => '', "genreId" => '', "composers" => '', "price" => '', "length" => ''
      ]],
      ['about' => 'Delete track', 'method' => 'DELETE', 'url' => $baseUrl . 'track/:id']
    ];

    $apiDescription['invoice'] = [

    ];

    return json_encode($apiDescription);
  };
?>