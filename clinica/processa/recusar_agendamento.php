<?php
session_start();
require_once '../database/conecta_DB.php';

// Apenas administradores podem acessar esta funcionalidade
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<script>alert('Acesso negado. Você não tem permissão para esta ação.'); window.location.href='../painel.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id_agendamento = $_GET['id'];

    try {
        // Atualizar o status para 'recusado'
        $stmtUpdate = $pdo->prepare("UPDATE agendamentos SET status = 'recusado' WHERE id = :id AND status = 'pendente'");
        $stmtUpdate->execute([':id' => $id_agendamento]);

        if ($stmtUpdate->rowCount() > 0) {
            echo "<script>alert('Agendamento recusado com sucesso!'); window.location.href='../painel.php?page=agendamentos';</script>";
        } else {
            echo "<script>alert('Agendamento não encontrado ou não está no status pendente.'); window.location.href='../painel.php?page=agendamentos';</script>";
        }

    } catch (PDOException $e) {
        die("Erro ao recusar agendamento: " . $e->getMessage());
    }
} else {
    header("Location: ../painel.php?page=agendamentos");
    exit;
}
?> 