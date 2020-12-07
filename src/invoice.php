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
        # Check if query was successful
        echo true;
      
      } catch (Exception $e) {
        $pdo->rollBack();
        echo false;
      }
    }
  }
?>