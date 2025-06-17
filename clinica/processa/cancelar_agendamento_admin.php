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
        // Opcional: Você pode querer marcar como 'cancelado_por_admin' ou apenas 'cancelado'
        $stmtUpdate = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = :id");
        $stmtUpdate->execute([':id' => $id_agendamento]);

        if ($stmtUpdate->rowCount() > 0) {
            echo "<script>alert('Agendamento excluído/cancelado pelo administrador com sucesso!'); window.location.href='../painel.php?page=agendamentos';</script>";
        } else {
            echo "<script>alert('Agendamento não encontrado.'); window.location.href='../painel.php?page=agendamentos';</script>";
        }

    } catch (PDOException $e) {
        die("Erro ao excluir/cancelar agendamento: " . $e->getMessage());
    }
} else {
    header("Location: ../painel.php?page=agendamentos");
    exit;
}
?>