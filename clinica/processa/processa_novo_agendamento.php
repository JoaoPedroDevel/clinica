<?php
session_start();
require_once '../database/conecta_DB.php';

if (!isset($_SESSION['usuario_id']) || !isset($_POST['id_paciente'])) {
    header("Location: ../login/login.php");
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
        // Verificar se o paciente logado é o mesmo que está tentando agendar
        $stmtCheckPaciente = $pdo->prepare("SELECT id FROM pacientes WHERE email = :email");
        $stmtCheckPaciente->execute([':email' => $_SESSION['usuario_email']]);
        $pacienteLogado = $stmtCheckPaciente->fetch(PDO::FETCH_ASSOC);

        if (!$pacienteLogado || $pacienteLogado['id'] != $id_paciente) {
            echo "<script>alert('Você não tem permissão para agendar para este paciente.'); window.history.back();</script>";
            exit;
        }

        // Inserir agendamento com status 'pendente'
        $stmt = $pdo->prepare("INSERT INTO agendamentos (id_paciente, id_medico, data_agendamento, hora_agendamento, tipo_agendamento, observacoes, status) VALUES (:id_paciente, :id_medico, :data_agendamento, :hora_agendamento, :tipo_agendamento, :observacoes, 'pendente')");
        $stmt->execute([
            ':id_paciente' => $id_paciente,
            ':id_medico' => $id_medico,
            ':data_agendamento' => $data_agendamento,
            ':hora_agendamento' => $hora_agendamento,
            ':tipo_agendamento' => $tipo_agendamento,
            ':observacoes' => $observacoes
        ]);

        echo "<script>alert('Sua solicitação de agendamento foi enviada e está aguardando aprovação!'); window.location.href = '../painel_usuario.php?page=meus_agendamentos';</script>";

    } catch (PDOException $e) {
        die("Erro ao solicitar agendamento: " . $e->getMessage());
    }
} else {
    header("Location: ../painel_usuario.php?page=novo_agendamento");
    exit;
}
?>