<?php
session_start();
require_once '../database/conecta_DB.php';

// Apenas administradores podem acessar esta funcionalidade
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<script>alert('Acesso negado. Você não tem permissão para esta ação.'); window.location.href='../painel.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = $_POST['id_paciente'];
    $id_medico = $_POST['id_medico'];
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];
    $tipo_agendamento = $_POST['tipo_agendamento'];
    $observacoes = $_POST['observacoes'] ?? null;

    // Validação básica
    if (empty($id_paciente) || empty($id_medico) || empty($data_agendamento) || empty($hora_agendamento) || empty($tipo_agendamento)) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.history.back();</script>";
        exit;
    }

    try {
        // Inserir agendamento com status 'agendado'
        $stmt = $pdo->prepare("INSERT INTO agendamentos (id_paciente, id_medico, data_agendamento, hora_agendamento, tipo_agendamento, observacoes, status) VALUES (:id_paciente, :id_medico, :data_agendamento, :hora_agendamento, :tipo_agendamento, :observacoes, 'agendado')");
        $stmt->execute([
            ':id_paciente' => $id_paciente,
            ':id_medico' => $id_medico,
            ':data_agendamento' => $data_agendamento,
            ':hora_agendamento' => $hora_agendamento,
            ':tipo_agendamento' => $tipo_agendamento,
            ':observacoes' => $observacoes
        ]);

        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href = '../painel.php?page=agendamentos';</script>";

    } catch (PDOException $e) {
        die("Erro ao agendar: " . $e->getMessage());
    }
} else {
    header("Location: ../painel.php?page=agendamentos");
    exit;
}
?>