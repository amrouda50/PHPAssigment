<?php
function CreateNewID($FileStorage)
{
    $RandomID = mt_rand(10000, 99999);
    while ($FileStorage->findById($RandomID)) {
        $RandomID = mt_rand(10000, 99999);
    }

    return $RandomID;
} ?>


<?php
require_once 'Storage.php';


session_start();
$storage = new Storage(new JsonIO('./data/teams.json'));
$MatchesStorage = new Storage(new JsonIO('./data/matches.json'));
$CommentsStorage = new Storage(new JsonIO('./data/comments.json'));
$UsersStorage = new Storage(new JsonIO('./data/users.json'));
$Users = $UsersStorage->findAll();
$team = $storage->findById($_GET['id']);
$matches = $MatchesStorage->findAll();
$teams = $storage->findAll();
$TeamPlayed = false;
$CommentsExists = false;
$ScoreColor;
$error = "";
if (isset($_POST['CommentID'])) {
    $CommentsStorage->delete($_POST['CommentID']);
}
if (isset($_POST['Contact-Message'])) {
    $CommentId = CreateNewID($CommentsStorage);
    foreach ($Users as $id => $userinfo) {

        if ($userinfo['username'] == $_SESSION['user']) {
            $CurrentUserID = $id;
        }
    }
    $text =  trim($_POST['Contact-Message']);
    if ($text == "") {
        $error = "Comment is Empty!! Please add the actual comment";
    } else {
        $CommentsStorage->update($CommentId, ["id" => $CommentId, "author" => $CurrentUserID, "text" => $text, "team" => (int)$_GET['id']]);
    }
}
$comments = $CommentsStorage->findAll();
?>

<?php
function result($HomeScore, $AwayScore)
{
    if ($HomeScore > $AwayScore) {
        return 1;
    } elseif ($HomeScore < $AwayScore) {
        return -1;
    } else {
        return 0;
    }
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
            <?php if (!isset($_SESSION['user'])) : ?>
                <button><a href="./Login.php">Login</a></button>
            <?php else : ?>
                <button><a href="./index.php?status=logoff">Logout</a></button>

            <?php endif; ?>
            <button><a href="./Create.php">Create Account</a></button>
        </div>
    </div>

    <div class="team">
        <?php if (isset($_SESSION['user']) &&  isset($_SESSION['status']) && $_SESSION['status'] == "LogOn") : ?>
            <?php echo "Welcome " . $_SESSION['user'] . " to ELTE Stadium!"; ?>
        <?php endif; ?>
        <h1>Team Name</h1>
        <p style="font-size:larger"> <?php echo $team['name'] ?>(City:<?php echo $team['city'] ?>)</p>
        <h1>Matches</h1>
        <ul class="matchs">
            <span> Home - Away</span> <br>
            <?php foreach ($matches as $k => $v) : ?>
                <?php
                if (isset($_POST['matchid']) && $k == $_POST['matchid']) {
                    $v['home']['score'] = $_POST['Home'];
                    $v['away']['score'] = $_POST['Away'];
                    $MatchesStorage->update($_GET['id'], ["home" => ["teamid" => $v['home']['teamid'], "score" => intval($_POST['Home'])], "away" =>  ["teamid" => $v['away']['teamid'], "score" => intval($_POST['Away'])], "date" => $_POST['date']]);
                }
                $HomeTeam = $v['home'];
                $AwayTeam = $v['away'];
                ?>
                <?php if ($HomeTeam['teamid'] == $_GET['id'] || $AwayTeam['teamid'] == $_GET['id']) : ?>
                    <?php $TeamPlayed = true; ?>
                    <li> <?php echo $teams[$HomeTeam['teamid']]['name'] . "-" . $teams[$AwayTeam['teamid']]['name'] ?>
                        <?php
                        if (result($HomeTeam['score'], $AwayTeam['score']) > 0) {
                            $ScoreColor = "background-color:green";
                        } elseif (result($HomeTeam['score'], $AwayTeam['score']) == 0) {
                            $ScoreColor = "background-color:yellow";
                        } else if (result($HomeTeam['score'], $AwayTeam['score']) < 0) {
                            $ScoreColor = "background-color:red";
                        }
                        ?>
                        <span style="margin-left:50%;"> <?php echo "Date:" . $v['date'] ?></span> <span style="margin-left:70%;<?php echo $ScoreColor ?>" class="score"> <?php echo "Score" . ":"  . $HomeTeam['score'] . "-" . $AwayTeam['score'] ?> </span>
                        <?php if (isset($_SESSION['user'])  && $_SESSION['user'] == $UsersStorage->findById("1")['username']) : ?>
                            <p> Modify score:</p>
                            <script>
                                function handleChange(k) {

                                    var HomeVal = document.getElementById(k + "Home").value
                                    var AwayVal = document.getElementById(k + "Away").value
                                    console.log(HomeVal);
                                    if (HomeVal < 0 || AwayVal < 0 || HomeVal === "" || AwayVal === "") {
                                        alert("Incorrect score input");
                                        return false;
                                    }
                                    return true;
                                }
                            </script>
                            <form method="POST">
                                <input type="text" name="Home" id="<?php echo $k . "Home" ?>" placeholder="Home">
                                <input type="text" name="Away" id="<?php echo $k . "Away" ?>" placeholder="Away">
                                <input type="hidden" name="matchid" value="<?php echo $k ?>">
                                <input type="hidden" name="date" value="<?php echo $v['date'] ?>">
                                <input type="submit" name="edit" onclick="return handleChange(<?php echo $k ?>)"></input>
                            </form>
                        <?php endif; ?>
                    </li>

                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (!$TeamPlayed) : ?>
                <h1>Message:Team Have not played Any Matches</h1>
            <?php endif; ?>
        </ul>
        <h1>Comments</h1>
        <p>Add your current comment:</p>
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 'LogOn') : ?>
            <form method="POST">
                Comment:<br>
                <textarea name="Contact-Message" rows="6″ cols=" 20″>
                      </textarea><br><br>
                <p style="color:red"> <?php echo $error ?> </p>
                <button type="submit" value="Submit">submit</button>
            </form>
        <?php endif; ?>
        <span>Current Comments</span> <br>
        <ul id="comments">
            <?php foreach ($comments as $k => $v) : ?>
                <?php if ($v['team'] == $_GET['id']) : ?>
                    <?php $CommentsExists = true; ?>
                    <li> <?php echo $v['text'] ?> <span>(Made by <?php echo $Users[$v['author']]['username'] ?>)</span>

                        <?php if (isset($_SESSION['user'])  && $_SESSION['user'] == $UsersStorage->findById("1")['username']) : ?>
                            <form style="display:inline-block" method="POST">
                                <input type="hidden" name="CommentID" value="<?php echo $k ?>">
                                <button type="submit" value="DeleteComment" onclick="return confirm('Are you sure you want to delete this comment?')">delete</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!$CommentsExists) : ?>
                <li> <?php echo "No comments exists" ?> </li>
            <?php endif; ?>

        </ul>

    </div>

</body>

</html>