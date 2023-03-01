<?php

chdir(dirname(__FILE__));
require_once('functions.php');

switch ($_POST['service']) {
    case 'create':
        create($_POST);
        break;
    case 'edit':
        edit($_POST);
        break;
    case 'delete':
        delete($_POST['id']);
        break;
    default:
        return FALSE;
}

function delete($id) {
    $sql_delete_items = "UPDATE items
                         SET deleted_at = NOW()
                         WHERE invoice_id = {$id}";

    executeQuery($sql_delete_items);

    $sql_delete_items = "UPDATE invoices
                         SET deleted_at = NOW()
                         WHERE id = {$id}";

    executeQuery($sql_delete_items);
    return True;
}

function create($data) {
    $mysqli = Connection::getConnection();

    $sql_invoice_insert = "INSERT INTO invoices (supplier, customer,street,street_number,postcode,city)
                           VALUES('{$data['supplier']}',
                                  '{$data['customer']}',
                                  '{$data['street']}',
                                  '{$data['street_number']}',
                                  '{$data['postcode']}',
                                  '{$data['city']}')";

    mysqli_query($mysqli, $sql_invoice_insert);

    $invoice_id = intval(mysqli_insert_id($mysqli));
    $counter = 0;

    foreach ($data['item'] as $item) {

        if ($item == '') break;

        $sql_item_insert = "INSERT INTO items (invoice_id, item_number,quantity,price)
                           VALUES({$invoice_id},
                                  '{$item}',
                                  {$data['quantity'][$counter]},
                                  {$data['price'][$counter]})";

        executeQuery($sql_item_insert);
        $counter++;
    }
}

function edit($data) {

    $sql_invoice_insert = "UPDATE  invoices 
                           SET  supplier = '{$data['supplier']}',
                                customer = '{$data['customer']}',
                                street = '{$data['street']}',
                                street_number = '{$data['street_number']}',
                                postcode = '{$data['postcode']}',
                                city = '{$data['city']}'
                           WHERE id = {$data['id']}";

    executeQuery($sql_invoice_insert);

    $timestamp = date("Y-m-d H:i:s");
    $counter = 0;

    foreach ($data['item'] as $item) {

        if ($item == '') break;

        $sql_item_upnsert = "INSERT INTO items (invoice_id, item_number,quantity,price,updated_at)
                             VALUES({$data['id']},
                                  '{$item}',
                                  {$data['quantity'][$counter]},
                                  {$data['price'][$counter]},
                                  '{$timestamp}')
                             ON DUPLICATE KEY UPDATE
                                invoice_id = {$data['id']},
                                item_number = '{$item}',
                                quantity = {$data['quantity'][$counter]},
                                price = {$data['price'][$counter]},
                                updated_at = '{$timestamp}'";

        executeQuery($sql_item_upnsert);
        $counter++;
    }

    $sql_delete_items = "UPDATE items
                         SET deleted_at = NOW()
                         WHERE invoice_id = {$data['id']}
                         AND updated_at < '{$timestamp}'";

    executeQuery($sql_delete_items);
}
