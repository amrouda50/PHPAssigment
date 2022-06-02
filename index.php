<?php
require_once 'Storage.php';
$storage = new Storage(new JsonIO('./data/teams.json'));
$teams = $storage->findAll();
$UserMessage;
session_start();
if( (isset($_SESSION['user']) && $_SESSION['status'] == 'logoff' ) || isset($_GET['status'])  ){
    $_SESSION['status'] = "logoff";
    unset($_SESSION['user']);
    unset($_SESSION['status']);
}


        


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css">
    <title>Document</title>
</head>

<body>
    <div id="nav-bar">
   
        <div id="nav-bar-content">
            <span id="Stadium-Name">ELTE Stadium</span>
            <?php if(!isset($_SESSION['user'])):?>
             <button><a href="./Login.php">Login</a></button>
             <?php else:?>
                <button><a href="./index.php?status=logoff">Logout</a></button>
            <?php endif;?>
            
            <button><a href="./Create.php">Create Account</a></button>
         
        </div>
    </div>
    <div class="Teams-Container">
        <?php if(isset($_SESSION['user']) &&  isset($_SESSION['status']) && $_SESSION['status'] == "LogOn") : ?>
            <?php echo "Welcome " . $_SESSION['user'] . " to ELTE Stadium!";?>
            <?php endif;?>   
        <h1 style="margin-left:15px">TEAMS</h1>
        <?php foreach ($teams as $k => $v) : ?>
            <div class="team">

                <div class="team-top-bar">
                    <p> <?php echo $v['name'] ?> </p>                 
                </div>
                <a href="TeamDetails.php?id=<?php echo $v['id']?>">Go To Details</a>

            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>