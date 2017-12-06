<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>BulletProof</title>
    </head>
    <body>
        <?php
        require_once(realpath(dirname(__FILE__))."./config/dbconfig.php");

        try {
            $pdo = new PDO('mysql:host='.$config["host"].';dbname='.$config["dbname"], $config["user"], $config["password"]);
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
        }
        session_start();
        if(isset($_SESSION['user'])){
            ?>
            <ul>
              <li><a href="disconnect.php">Se déconnecter</a></li>
              <li><a href="unsubscribe.php">Se désinscrire</a></li>
            </ul>
            <?php
        }
        ?>
    <div class="content-page">
<?php

if(isset($_SESSION['user'])){
    echo "<h2>Bienvenue ".($_SESSION['user'])."!</h2>";

?>  <br>
    <form method="POST">
    <label>Article: <br> <textarea cols="100" rows="9" name="content"></textarea></label><br/>
    <input type="submit" class="button" value="Envoyer"/>
</form><br/>

<br/>
    <br />

    <?php
    if(isset($_POST['content']) && !empty($_POST['content'])){
        $content = htmlspecialchars($_POST['content']);
        $content = htmlentities($content);
        $content = strip_tags($content);
        $content = trim($content);
        $poster = $_SESSION['user'];

        if($content){
            $q = $pdo->prepare('SELECT * FROM user WHERE nickname = :nickname');
            $q->bindParam(':nickname', $poster, PDO::PARAM_STR);
            $q->execute();
            $queryResults = $q->fetch(PDO::FETCH_ASSOC);

            if(!empty($queryResults['id']) && !empty($queryResults['nickname']) ){

                $user_id = $queryResults['id'];
                $user_nickname = $queryResults['nickname'];

                $post = $pdo->prepare('INSERT INTO post (user_id, nickname_user, content) VALUES (:user_id, :nickname_user, :content)');
                $post->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $post->bindParam(':nickname_user', $user_nickname, PDO::PARAM_STR);
                $post->bindParam(':content', $content, PDO::PARAM_STR);
                $post->execute();

            }

        }
    }

    $display = $pdo->prepare('SELECT * FROM post');
    $display->execute();
    $displayResults = $display->fetchAll();
    foreach($displayResults as $result) {
        echo "<div class=article><h4>Article by ".$result['nickname_user']."</h4>";
        echo "<p>".$result['content']."</p></div><br /><hr />";
        }

}else{
 ?>

<form method="POST" action="index.php">
    <table>
        <tr>
            <td>Pseudo: </td>
            <td><input type="text" name="nickname_login"/></td>
        </tr>
        <tr>
            <td>Mot de passe: </td>
            <td><input type="password" name="password_login"/></td>
        </tr>
    </table>
    <input type="submit" class="button" value="Me connecter"/>
</form>
<br>
<hr>

<?php
if(isset($_POST['nickname_login']) && !empty($_POST['nickname_login']) && isset($_POST['password_login']) && !empty($_POST['password_login'])){
    $nickname_login = htmlspecialchars($_POST['nickname_login']);
    $nickname_login = htmlentities($nickname_login);
    $nickname_login = strip_tags($nickname_login);
    $nickname_login = trim($nickname_login);

    $password_login = htmlspecialchars($_POST['password_login']);
    $password_login = htmlentities($password_login);
    $password_login = strip_tags($password_login);
    $password_login = trim($password_login);

    $q = $pdo->prepare('SELECT * FROM user WHERE nickname = :nickname');
    $q->bindParam(':nickname', $nickname_login, PDO::PARAM_STR);
    $q->execute();
    $queryResults = $q->fetch(PDO::FETCH_ASSOC);
    if(password_verify($password_login, $queryResults['password'])){
        $_SESSION['user'] = $nickname_login;
        header("Refresh:0");
    }else{
        echo "<p class=error>Mauvais identifiants</p>";
    }
}
?>

<form method="POST" action="index.php">
    <table>
        <tr>
            <td>Pseudo: </td>
            <td><input type="text" name="nickname_register"/></td>
        </tr>
        <tr>
            <td>Mot de passe: </td>
            <td><input type="password" name="password_register"/></td>
        </tr>
    </table>
<input type="submit" class="button" value="M'inscrire"/>
</form>

<?php
if(isset($_POST['nickname_register']) && !empty($_POST['nickname_register']) && isset($_POST['password_register']) && !empty($_POST['password_register'])){
    $nickname_register = htmlspecialchars($_POST['nickname_register']);
    $nickname_register = htmlentities($nickname_register);
    $nickname_register = strip_tags($nickname_register);
    $nickname_register = trim($nickname_register);

    $password_register = htmlspecialchars($_POST['password_register']);
    $password_register = htmlentities($password_register);
    $password_register = strip_tags($password_register);
    $password_register = trim($password_register);

    $password_hash = password_hash($password_register, PASSWORD_DEFAULT);

    $q = $pdo->prepare('INSERT INTO user (nickname, password) VALUES (:nickname, :password)');
    $q->bindParam(':nickname', $nickname_register, PDO::PARAM_STR);
    $q->bindParam(':password', $password_hash, PDO::PARAM_STR);
    $register = $q->execute();
    if($register){
         $_SESSION['user'] = $nickname_register;
         header("Refresh:0");
     }else{
         echo "<p class=error>Une erreur s'est produite lors de l'inscription !</p>";
     }
    }
}
?>
</div>
</body>
</html>

<style media="screen">
/********************** RESET CSS *********************************/
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed,
figure, figcaption, footer, header, hgroup,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
margin: 0;
padding: 0;
border: 0;
font-size: 100%;
font: inherit;
vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure,
footer, header, hgroup, menu, nav, section {
display: block;
}
body {
line-height: 1;
}
ol, ul {
list-style: none;
}
blockquote, q {
quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
content: '';
content: none;
}
table {
border-collapse: collapse;
border-spacing: 0;
}
/*************************************************************/
    h2 {
        font-size: 25px;
        font-weight: bold;
        text-align: center;
    }
    .button {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        border: 1px #4CAF50 solid;
        transition-duration: 0.2s;
    }
    .button:hover {
        background-color: white;
        color: #4CAF50;
        border: 1px #4CAF50 solid;
    }
    hr {
        width: 30%;
    }
    .article {
        margin: auto;
        width: 60%;
    }
    .article p{
        background-color: lightgrey;
        padding: 10px;
        margin-top: 5px;
        text-align: justify;
    }
    .article h4 {
        font-size: 20px;
        text-align: center;
    }
    .active {
        background-color: #4CAF50;
    }
    ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
        position: fixed;
        top: 0;
        width: 100%;
    }

    li {
        float: left;
    }

    li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    li a:hover {
        background-color: #111;
    }
    .content-page {
        margin-top: 100px;
        margin-left: 10px;
    }
    p.error{
        font-size: 15px;
        margin: 15px;
        background-color: red;
        font-weight: bold;
        display: block;
    }
    form, table {
        display: block;
        position: relative;
        margin: auto;
        text-align: center;
        width: 40%;
    }
</style>
