<?php

require_once('functions.php');

if(isset($_SESSION['user_id'])){
    header("location:invoices.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $mysqli = Connection::getConnection();
    $user_service = new UserService($mysqli,$username,$password);

    if ($user_id = $user_service->login()) {
        header('Location: invoices.php' );
        exit;
    } else {
        $loginError =  'Invalid login.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <link rel="stylesheet" type="text/css" href="login_style.css">

</head>
<body>
<div class="wrapper">
    <form class="form-signin" action="/login.php" method="post">
        <h2 class="form-signin-heading">Invoices</h2>
        <?php if ($loginError ?? false) { ?>
            <div class="alert alert-warning"><?php echo $loginError?? 'Login Error!'; ?></div>
        <?php } ?>
        <input type="text" class="form-control" name="username" placeholder="Email Address" required="" autofocus=""/>
        <input type="password" class="form-control" name="password" placeholder="Password" required=""/>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
    </form>
</div>
</body>
</html>
