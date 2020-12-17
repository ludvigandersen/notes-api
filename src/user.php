<?php
  require_once('db_conn.php');  
  
  class User {

    public $userId;
    public $email;
    public $firstName;
    public $lastName;
    public $company;
    public $address;
    public $city;
    public $state;
    public $country;
    public $postalCode;
    public $phone;
    public $fax;
    
    # Insert user into database
    function create($data){
      global $pdo;

      if ($data['password'] != $data['passwordRepeat']){
        echo json_encode("passwords are not matching");
        return;
      }

$query = <<<'SQL'
      INSERT INTO customer
      (FirstName, LastName, Password, Company, Address, City, State, Country, PostalCode, Phone, Fax, Email)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

      $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $stmt = $pdo->prepare($query);
      $insert_data = array(
        $data['firstName'], $data['lastName'], $pass_hash,
        $data['company'], $data['address'], $data['city'],
        $data['state'], $data['country'], $data['postalCode'],
        $data['phoneNumber'], $data['fax'], $data['email']
      );
      $result = $stmt->execute($insert_data);

      # Check if query was successful
      if($result){
        return true;
      } else {
        return false;
      }
    }

    function update($id, $data){
      global $pdo;

      if (isset($data['password'])){

$query = <<< 'SQL'
        UPDATE customer
        SET Password=?
        WHERE CustomerId=?
SQL;
        $pass_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $update_data = array($pass_hash, $id);
        
      } else {

$query = <<< 'SQL'
        UPDATE customer
        SET FirstName=?, LastName=?, Company=?, Address=?, City=?, 
            State=?, Country=?, PostalCode=?, Phone=?, Fax=?, Email=?
        WHERE CustomerId=?
SQL;
        $update_data = array(
          $data['firstName'], $data['lastName'], $data['company'],
          $data['address'], $data['city'], $data['state'], $data['country'],
          $data['postalCode'], $data['phone'], $data['fax'], $data['email'], $id
        );
      }

      $stmt = $pdo->prepare($query);
      $result = $stmt->execute($update_data);

      # Check if query was successful
      if($result){
        return true;
      } else {
        return false;
      }
    }
  }
?>