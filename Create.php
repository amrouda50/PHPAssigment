<?php
function CreateNewID($FileStorage)
{
    $RandomID = mt_rand(10000, 99999);
    while ($FileStorage->findById($RandomID)) {
        $RandomID = mt_rand(10000, 99999);
    }

    return $RandomID;
}
function AccountIsSet($_P , $FileStorage)
{
    $CurrentUsers = $FileStorage->findAll();

    foreach ($CurrentUsers as $k => $v) {
        if ( $v['username'] ==  $_P['username']) {
            return true;
        }
        if ( $v['email'] ==  $_P['email']) {
            return true;
        }
    }
    return false;
}
?>



<?php
require_once 'Storage.php';
$CreatedAccountStorage = new Storage(new JsonIO('./data/users.json'));
$ErrorFound = false;
$Errors = array();
$AccountCreated = false;
if(isset($_POST['submit'] ) && empty($_POST['username'])){
    array_push($Errors,'No Username Entered');
    $ErrorFound = true;
}
if(isset($_POST['submit'] ) && !empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    array_push($Errors,'Email Entered is incorrect');
    $ErrorFound = true;
}
if(isset($_POST['submit'] ) && empty($_POST['email'])){
    array_push($Errors,'No Email Entered');
    $ErrorFound = true;
}
if(isset($_POST['submit'] ) && empty($_POST['password'])){
    array_push($Errors,'No Password Entered');
    $ErrorFound = true;
}
if (isset($_POST['submit']) && !AccountIsSet($_POST , $CreatedAccountStorage) && !$ErrorFound) {
    $AccountCreated = true;
    $CreatedAccountStorage->update(CreateNewID($CreatedAccountStorage), ["username" => $_POST['username'],  "email" => $_POST['email'], "password" => password_hash($_POST['password'],PASSWORD_DEFAULT) ]);
}
if(isset($_POST['submit']) && AccountIsSet($_POST , $CreatedAccountStorage) ){
    array_push($Errors,'Account Already Exists');
    $ErrorFound = true;
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
        <form method="POST" id ="form" action="Create.php">
            <p> <label for="">Username:</label> <input type="text" name="username"></p>
            <p> <label>Email:</label> <input type="text" name="email"></p>
            <p> <label>Password:</label> <input type="password" name="password"></p>       
            <input type="submit" id="clickk"name="submit" value="Create Account">
        </form>
    </div>
   
    <?php if ($AccountCreated) : ?>
        
       <li style="color:chartreuse"> The account have been created </li>
    <?php else : ?>
        <?php foreach($Errors as $Error) :?>
        <li style="color:red"> <?php echo $Error?> </li>
        <?php endforeach ;?>
    <?php endif; ?>

    </div>
</body>

</html>