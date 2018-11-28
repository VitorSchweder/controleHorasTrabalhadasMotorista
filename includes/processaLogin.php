<?php
ob_start();
include_once("../engine/connect.php");
session_start();

$conexao = conectar();

if (!empty($_POST)) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $busca = $conexao->prepare("SELECT * FROM usuarios WHERE username=:username AND password=:password");
    $busca->bindValue(":username", $username);
    $busca->bindValue(":password", $password);
    $busca->execute();

    $count = $busca->rowCount();

    if ($count == 1) {
        while ($row = $busca->fetch(PDO::FETCH_OBJ)){
            $_SESSION['id'] = $row->id;
            $_SESSION['username'] = $row->username;
        }
        header('Location: ../home');
    } else {
        header('Location: ../login');
    }
}
ob_end_flush();
