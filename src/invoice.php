<?php
  require_once("db_conn.php");

  class Invoice {

    function create($data){
      global $pdo;

      try {
        $pdo->beginTransaction();

        // Create Invoice
$query = <<< 'SQL'
        INSERT INTO invoice
        (CustomerId, InvoiceDate, BillingAddress, BillingCity, BillingState, BillingCountry, BillingPostalCode, Total)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
SQL;

        $invoiceDate = date('Y-m-d H:i:s');

        $insert_data = [
          $data['customerId'], $invoiceDate, $data['billingAddress'],
          $data['billingCity'], $data['billingState'], $data['billingCountry'],
          $data['billingPostalCode'], $data['total']
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($insert_data);

        $invoiceId = $pdo->lastInsertId();

        // Create InvoiceLine items
        if(isset($data['items'])){
          foreach ($data['items'] as $item) {

$query = <<< 'SQL'
          INSERT INTO invoiceline
          (InvoiceId, TrackId, UnitPrice, Quantity)
          VALUES (?, ?, ?, ?)
SQL;
          $insert_data = [
            $invoiceId, $item['trackId'], $item['price'], $item['quantity']
          ];
          $stmt = $pdo->prepare($query);
          $stmt->execute($insert_data);
          }
        }
        $pdo->commit();
        $invoiceId = $pdo->lastInsertId();
        # Check if query was successful
        return json_encode(array("status"=>"invoice created", "invoiceId"=>$invoiceId));
      
      } catch (Exception $e) {
        $pdo->rollBack();
        return json_encode(array("status"=>"creation failed"));
      }
    }
  }
?>