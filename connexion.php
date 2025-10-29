<?php
session_start();
require ('db.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']) ?? '';
    $password   = $_POST["password"] ?? "";
        
     $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["id"] = $user["id"];
        $_SESSION["login"] = $user["login"];
        

        header("Location: index.php");
        echo "Vous êtes connécté ! "; // redirection vers l'accueil
        exit;
    } else {
        echo "❌ Identifiants incorrects.";
    }
}
?>