<?php

chdir(dirname(__FILE__));
require_once('functions.php');

if (!$_SESSION['user_id']) {
    die('<script>window.location.replace("' . MAIN_URL . '");</script>');
}

$sql_invoice_query = "SELECT
                        invoices.id,
                        invoices.supplier,
                        invoices.customer,
                        invoices.street,
                        invoices.street_number,
                        invoices.postcode,
                        invoices.city,
                        (SELECT SUM(quantity*price)
                         FROM items
                         WHERE invoice_id = invoices.id
                         AND items.deleted_at IS NULL) as total_price
                      FROM invoices
                      WHERE invoices.deleted_at IS NULL";

$result = executeQuery($sql_invoice_query);
$select_values = array('supplier', 'customer','street','street_number','postcode','city','total_price');
$key_value = 'id';

$invoices = array();

while ($row = $result->fetch_assoc()) {
    $values = array();
    foreach ($select_values as $value) {
        $values[$value] = $row[$value];
    }
    $invoices[$row[$key_value]] = $values;
}

$sql_items_query = "SELECT
                      invoice_id,
                      item_number,
                      quantity,
                      price
                    FROM items
                    WHERE items.deleted_at IS NULL";

$result = executeQuery($sql_items_query);

$select_values = array('item_number', 'quantity','price');
$key_value = 'invoice_id';

$items = array();

while ($row = $result->fetch_assoc()) {
    $values = array();
    foreach ($select_values as $value) {
        $values[$value] = $row[$value];
    }
    $items[$row[$key_value]][] = $values;
}

Connection::closeConnection();

?>

    <!DOCTYPE html>
    <html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice manager</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script>

            $(function () {
                $('#add-more').on('click', function () {
                    var data = $("#tb tr:eq(2)").clone(true).appendTo("#tb");
                    data.find("input").val('');
                });
                $(document).on('click', '.remove', function () {
                    var trIndex = $(this).closest("tr").index();
                    if (trIndex > 2) {
                        $(this).closest("tr").remove();
                    } else {
                        alert("Sorry!! Can't remove first row!");
                    }
                });
            });

            $(document).ready(function(){
                $("#invoice-form").submit(function(){
                    submitForm();
                    return false;
                });
            });

            function submitForm(){
                $.ajax({
                    type: "POST",
                    url: "actions.php",
                    cache:false,
                    data: $('form#invoice-form').serialize(),
                    success: function(){
                        $("#abstract-modal").modal('hide');
                        window.location.href = window.location.href;
                    },
                    error: function(){
                        alert("Error!");
                    }
                });
            }

        </script>

        <link rel="stylesheet" type="text/css" href="invoice_style.css">

    </head>

    <body>
        <div id="viewport">
            <div id="sidebar">
                <header>
                    <span>InvoiceM</span>
                </header>
                <ul class="nav">
                    <li>
                        <a href="invoices.php">
                            <i class="zmdi zmdi-view-dashboard"></i> Manager
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="zmdi zmdi-link"></i> Something 1
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="zmdi zmdi-widgets"></i> Something 2
                        </a>
                    </li>
                </ul>
            </div>
            <div id="content">
                <nav class="navbar navbar-custom">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav navbar-right">
                            <form class="form-signin" action="logout.php">
                                <button class="btn btn-block" type="submit">Log out</button>
                            </form>
                        </ul>
                    </div>
                </nav>
                <div id="content_body">
                    <div class="table-wrapper">
                        <div class="table-title">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h2>Invoice manager</h2>
                                </div>
                                <div class="col-sm-6">

                                    <form method="post" action="invoices.php">
                                        <input type="hidden" name="create" value="create">
                                        <button type="submit" class="btn btn-success">
                                            <span class="glyphicon glyphicon-plus"></span>
                                            New invoice
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Supplier</th>
                                <th>Customer</th>
                                <th>Street</th>
                                <th>Street number</th>
                                <th>Postcode</th>
                                <th>City</th>
                                <th>Total price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach ($invoices as $invoice_id => $invoice_data) {
                                echo "<tr>
                                        <td>{$invoice_id}</td>
                                        <td>{$invoice_data['supplier']}</td>
                                        <td>{$invoice_data['customer']}</td>
                                        <td>{$invoice_data['street']}</td>
                                        <td>{$invoice_data['street_number']}</td>
                                        <td>{$invoice_data['postcode']}</td>
                                        <td>{$invoice_data['city']}</td>
                                        <td>{$invoice_data['total_price']}</td>";

                                echo '<td id="action-buttons">
                                    <div >
                                    <form method="post" action="invoices.php" style="display:inline-block;">
                                        <input type="hidden" name="edit" value="' . $invoice_id . '">
                                        <button type="submit" class="btn btn-warning">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                        </button>
                                    </form>                                            
                                    <form method="post" action="invoices.php" style="display:inline-block;">
                                        <input type="hidden" name="delete" value="' . $invoice_id . '">
                                        <button type="submit" class="btn btn-danger">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </button>
                                    </form>
                                    </div>
                                </td>
                                </div>
                            </tr>';
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<?php

function getShowModal(){
        echo "<script>
         $(window).load(function(){
             $('#abstract-modal').modal('show');
         });
    </script>";
}

function getItems($items){
    foreach ($items?? array(array('item_number'=>'','quantity'=>'','price'=>'')) as $item) {
        echo "<tr>
                    <td><input type='text' name='item[]' class='form-control' value='{$item['item_number']}'></td>
                    <td><input type='number' name='quantity[]' class='form-control' value='{$item['quantity']}'></td>
                    <td><input type='number' step='0.01' name='price[]' class='form-control' value='{$item['price']}'></td>
                    <td>
                        <a href='javascript:void(0);' class='remove'>
                            <span class='glyphicon glyphicon-remove'></span>
                        </a>
                    </td>
                 </tr>";
    }
}

function getEditModal($id, $data, $items){
    echo '<div id="abstract-modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Invoice</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                            </button>
                        </div>
                        <form id="invoice-form" name="contact" role="form">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <input type="hidden" name="service" value="edit">
                                    <input type="hidden" name="id" value="' . $id . '">
                                    <input type="text" name="supplier" class="form-control" required value="' . $data['supplier']  . '">
                                </div>
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <input type="text" name="customer" class="form-control" required value="' . $data['customer'] . '">
                                </div>
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" name="street" class="form-control" required value="' . $data['street'] . '">
                                </div>
                                <div class="form-group">
                                    <label for="street_number">Street number</label>
                                    <input type="text" name="street_number" class="form-control" required
                                           value="' . $data['street_number'] .'">
                                </div>
                                <div class="form-group">
                                    <label for="postcode">Postcode</label>
                                    <input type="text" name="postcode" class="form-control" required value="' . $data['postcode'] . '">
                                </div>
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control" required value="' . $data['city'] . '">
                                </div>
                                <div class="form-group">
                                    <table class="table table-hover small-text" id="tb">
                                            <th>Number</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>
                                                <a href="javascript:void(0);" style="font-size:18px;" id="add-more" title="Add More">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </th>
                                            <tr><span>Items</span></tr>'; getItems($items);
                             echo '</table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                                <input type="submit" class="btn btn-success" value="Edit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>';

    getShowModal();
}

function getDeleteModal($id){
    echo '<div id="abstract-modal" class="modal faded">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="invoice-form" name="contact" role="form">
                            <div class="modal-header">
                                <h4 class="modal-title">Delete Invoice</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="service" value="delete">
                                <input type="hidden" name="id" value="' . $id .'">
                                <p>Are you sure you want to delete this invoice?</p>
                                <p class="text-warning"><small>This action cannot be undone.</small></p>
                            </div>
                            <div class="modal-footer">
                                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                                <input type="submit" class="btn btn-danger" value="Delete">
                            </div>
                        </form>
                    </div>
                </div>
            </div>';

    getShowModal();
}

function getCreateModal(){
    echo '<div id="abstract-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                    </button>
                </div>
                <form id="invoice-form" name="contact" role="form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <input type="hidden" name="service" value="create">
                            <input type="text" name="supplier" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <label for="customer">Customer</label>
                            <input type="text" name="customer" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <label for="street">Street</label>
                            <input type="text" name="street" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <label for="street_number">Street number</label>
                            <input type="text" name="street_number" class="form-control" required
                                   value="">
                        </div>
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" name="postcode" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" required value="">
                        </div>
                        <div class="form-group">
                            <table class="table table-hover small-text" id="tb">
                                    <th>Number</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>
                                        <a href="javascript:void(0);" style="font-size:18px;" id="add-more" title="Add More">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </a>
                                    </th>
                                    <tr><span>Items</span></tr>
                                    <tr>
                                        <td><input type="text" name="item[]" class="form-control" value=""></td>
                                        <td><input type="number" name="quantity[]" class="form-control" value=""></td>
                                        <td><input type="number" step="0.01" name="price[]" class="form-control" value=""</td>
                                        <td>
                                            <a href="javascript:void(0);" class="remove">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                        </td>
                                    </tr>
                            </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="submit" class="btn btn-success" value="Create">
                        </div>
                    </form>
            </div>
        </div>
    </div>';

    getShowModal();
}

if (isset($_POST['edit'])) {
    getEditModal($_POST['edit'],$invoices[$_POST['edit']],$items[$_POST['edit']]);
}elseif (isset($_POST['delete'])){
    getDeleteModal($_POST['delete']);
}elseif (isset($_POST['create'])){
    getCreateModal();
}
