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

    # Validate user based on $email and $password
    function sign_in($email, $password){
      global $pdo;
      // session_start();

$query = <<<'SQL'
      SELECT password, email, firstName, lastName, company, address, city, state, country, PostalCode, phone, fax, customerId
      FROM customer
      WHERE email=?
SQL;

      # Prepare and execute statement for retrieving user based on email
      $stmt = $pdo->prepare($query);
      $stmt->execute(array($email));
      $user = $stmt->fetch();

      $verify = password_verify($password, $user['password']);
      if($verify){

$query = <<<'SQL'
        SELECT *
        FROM admin
SQL;

        # Prepare and execute statement for retrieving Admin password
        # If given $password is equal to Admin password, set admin session token
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($pass_hash));
        $admin = $stmt->fetch();


        $this->userId = $user['customerId'];
        $this->email = $email;
        $this->firstName = $user['firstName'];
        $this->lastName = $user['lastName'];
        $this->company = $user['company'];
        $this->address = $user['address'];
        $this->city = $user['city'];
        $this->state = $user['state'];
        $this->country = $user['country'];
        $this->postalCode = $user['PostalCode'];
        $this->phone = $user['phone'];
        $this->fax = $user['fax'];
        
        // $_SESSION['email'] = $_POST['email'];
        if(password_verify($password, $admin[0])) {
          $admin = "true";
        } else {
          $admin = "false";
        }
        return [password_verify($password, $user['password']), $admin];  
      }
      return [password_verify($password, $user['password'])];
    }
  }
?>