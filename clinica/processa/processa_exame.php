<?php
session_start();
require_once '../database/conecta_DB.php';

// Apenas administradores podem acessar esta funcionalidade
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<script>alert('Acesso negado. Você não tem permissão para esta ação.'); window.location.href='../painel.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $idAgendamento = $_POST['id_agendamento'] ?? null;
            $idPaciente = $_POST['id_paciente'] ?? null; // Preenchido via JS do agendamento
            $idMedico = $_POST['id_medico'] ?? null;     // Preenchido via JS do agendamento
            $dataExame = $_POST['data_exame'] ?? null;   // Preenchido via JS do agendamento
            $tipoExame = $_POST['tipo_exame'] ?? '';
            $resultado = $_POST['resultado'] ?? null;
            $valorExame = $_POST['valor_exame'] ?? 0;

            if (empty($idAgendamento) || empty($tipoExame) || empty($valorExame)) {
                echo "<script>alert('Agendamento, Tipo de Exame e Valor do Exame são campos obrigatórios.'); window.history.back();</script>";
                exit;
            }

            try {
                // Inserir na tabela exames
                $stmt = $pdo->prepare("INSERT INTO exames (id_agendamento, id_paciente, id_medico, data_exame, tipo_exame, resultado, valor) VALUES (:id_agendamento, :id_paciente, :id_medico, :data_exame, :tipo_exame, :resultado, :valor)");
                $stmt->execute([
                    ':id_agendamento' => $idAgendamento,
                    ':id_paciente' => $idPaciente,
                    ':id_medico' => $idMedico,
                    ':data_exame' => $dataExame,
                    ':tipo_exame' => $tipoExame,
                    ':resultado' => $resultado,
                    ':valor' => $valorExame
                ]);

                // Atualizar o status do agendamento para 'concluido' ou 'realizado'
                $stmtUpdateAgendamento = $pdo->prepare("UPDATE agendamentos SET status = 'concluido' WHERE id = :id_agendamento");
                $stmtUpdateAgendamento->execute([':id_agendamento' => $idAgendamento]);

                echo "<script>alert('Exame registrado com sucesso!'); window.location.href='../painel.php?page=exames';</script>";

            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE key (id_agendamento)
                    echo "<script>alert('Erro: Este agendamento já possui um exame registrado.'); window.history.back();</script>";
                } else {
                    die("Erro ao adicionar exame: " . $e->getMessage());
                }
            }
            break;
        // Adicionar cases para 'edit' e 'delete' aqui
        default:
            echo "<script>alert('Ação inválida.'); window.history.back();</script>";
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'delete' && isset($_GET['id'])) {
        $id_exame = $_GET['id'];
        try {
            // Reverter status do agendamento (opcional, dependendo da regra de negócio)
            $stmtGetAgendamento = $pdo->prepare("SELECT id_agendamento FROM exames WHERE id = :id");
            $stmtGetAgendamento->execute([':id' => $id_exame]);
            $agendamentoId = $stmtGetAgendamento->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM exames WHERE id = :id");
            $stmt->execute([':id' => $id_exame]);

            if ($agendamentoId) {
                // Reverte para agendado, ou outro status adequado
                $stmtUpdateAgendamento = $pdo->prepare("UPDATE agendamentos SET status = 'agendado' WHERE id = :id_agendamento");
                $stmtUpdateAgendamento->execute([':id_agendamento' => $agendamentoId]);
            }
            
            echo "<script>alert('Exame excluído com sucesso!'); window.location.href='../painel.php?page=exames';</script>";
        } catch (PDOException $e) {
            die("Erro ao excluir exame: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Ação inválida.'); window.history.back();</script>";
    }
} else {
    header("Location: ../painel.php?page=exames");
    exit;
}
?>