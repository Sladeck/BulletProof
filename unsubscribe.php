<?php
require_once(realpath(dirname(__FILE__))."./config/dbconfig.php");

try {
    $pdo = new PDO('mysql:host='.$config["host"].';dbname='.$config["dbname"], $config["user"], $config["password"]);
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
}
session_start();

if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
    $deleted_user = $_SESSION['user'];

    $q = $pdo->prepare('DELETE FROM user WHERE nickname = :nickname');
    $q->bindParam(':nickname', $deleted_user, PDO::PARAM_STR);
    $q->execute();
    $deleteResults = $q->execute();

    if($deleteResults){
    session_unset();
    session_destroy();
    header("location:index.php");
    exit();
    }

}
?>
