<?php
require_once 'Storage.php';
$AccountsStorage = new Storage(new JsonIO('./data/users.json'));
$Accounts = $AccountsStorage->findAll();
$LoginSuccessfull = false;
$UsernameFound = false;
$errors = array();
if (isset($_POST['submit'])) {

    foreach ($Accounts as $k => $Account) {
        if ($Account['username'] == $_POST['username'] && password_verify($_POST['password'] , $Account['password']  ) ) {
            $LoginSuccessfull = true;
            session_start();
            $_SESSION['user'] = $Account['username'];
            $_SESSION['status'] = "LogOn";
            header('location:index.php');
        }
        if ($Account['username'] == $_POST['username'] && !password_verify($_POST['password'] , $Account['password']  )) {
            array_push($errors, "Password is incorrect");
        }
        if ($Account['username'] == $_POST['username']) {
            $UsernameFound = true;
        }
    }
    if (!$UsernameFound) {
        array_push($errors, "Username not found");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./form.css">
    <title>Document</title>
</head>

<body>
    <div id="nav-bar">
        <div id="nav-bar-content">
            <span id="Stadium-Name">ELTE Stadium</span>
            <button><a href="./Login.php">Login</a></button>
            <button><a href="./Create.php">Create Account</a></button>
        </div>
    </div>
    <div class="Create-container">
        <form method="POST" action="Login.php">
            <p> <label for="">Username:</label> <input type="text" name="username"></p>
            <p> <label>Password:</label> <input type="password" name="password"></p>
            <input type="submit" name="submit" value="Login">
        </form>
        <ul>
            <?php if ($LoginSuccessfull) : ?>
                <li> Login was successfull </li>
            <?php else : ?>
                <?php foreach ($errors as $error) : ?>
                    <li> <?php echo $error ?> </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </div>

</body>

</html>