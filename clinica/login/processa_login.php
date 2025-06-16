<?php
session_start(); 

require_once '../database/conecta_DB.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
       
        $_SESSION['logado'] = true; 
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'] ?? $usuario['email']; 
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['is_admin'] = !empty($usuario['is_admin']) && $usuario['is_admin'] == 1;

       
        if ($_SESSION['is_admin']) {
            header("Location: ../painel.php");
            exit; 
        } else {
            header("Location: ../painel.php");
            exit;
        }
    } else {
        echo "<script>alert('Acesso Negado: E-mail ou senha inválidos.'); window.history.back();</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
