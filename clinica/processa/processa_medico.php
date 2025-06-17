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
            $nomeCompleto = $_POST['nome_completo'] ?? '';
            $especialidade = $_POST['especialidade'] ?? '';
            $crm = $_POST['crm'] ?? '';
            $telefone = $_POST['telefone'] ?? null;
            $email = $_POST['email'] ?? null;

            if (empty($nomeCompleto) || empty($especialidade) || empty($crm)) {
                echo "<script>alert('Nome, Especialidade e CRM são campos obrigatórios.'); window.history.back();</script>";
                exit;
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO medicos (nome_completo, especialidade, crm, telefone, email) VALUES (:nome_completo, :especialidade, :crm, :telefone, :email)");
                $stmt->execute([
                    ':nome_completo' => $nomeCompleto,
                    ':especialidade' => $especialidade,
                    ':crm' => $crm,
                    ':telefone' => $telefone,
                    ':email' => $email
                ]);
                echo "<script>alert('Médico cadastrado com sucesso!'); window.location.href='../painel.php?page=medicos';</script>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE key (CRM ou Email)
                    echo "<script>alert('Erro: CRM ou E-mail já cadastrado.'); window.history.back();</script>";
                } else {
                    die("Erro ao adicionar médico: " . $e->getMessage());
                }
            }
            break;

        case 'edit':
            $id = $_POST['id'] ?? null;
            $nomeCompleto = $_POST['nome_completo'] ?? '';
            $especialidade = $_POST['especialidade'] ?? '';
            $crm = $_POST['crm'] ?? '';
            $telefone = $_POST['telefone'] ?? null;
            $email = $_POST['email'] ?? null;

            if (empty($id) || empty($nomeCompleto) || empty($especialidade) || empty($crm)) {
                echo "<script>alert('ID, Nome, Especialidade e CRM são campos obrigatórios para edição.'); window.history.back();</script>";
                exit;
            }

            try {
                $stmt = $pdo->prepare("UPDATE medicos SET nome_completo = :nome_completo, especialidade = :especialidade, crm = :crm, telefone = :telefone, email = :email WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':nome_completo' => $nomeCompleto,
                    ':especialidade' => $especialidade,
                    ':crm' => $crm,
                    ':telefone' => $telefone,
                    ':email' => $email
                ]);
                echo "<script>alert('Médico atualizado com sucesso!'); window.location.href='../painel.php?page=medicos';</script>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE key (CRM ou Email)
                    echo "<script>alert('Erro: CRM ou E-mail já cadastrado para outro médico.'); window.history.back();</script>";
                } else {
                    die("Erro ao atualizar médico: " . $e->getMessage());
                }
            }
            break;

        default:
            echo "<script>alert('Ação inválida.'); window.history.back();</script>";
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'delete' && isset($_GET['id'])) {
        $id_medico = $_GET['id'];
        try {
            // Verificar se o médico possui agendamentos/consultas antes de excluir (opcional, para evitar erros de integridade)
            $stmtCheckDependencies = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE id_medico = :id");
            $stmtCheckDependencies->execute([':id' => $id_medico]);
            if ($stmtCheckDependencies->fetchColumn() > 0) {
                echo "<script>alert('Não é possível excluir o médico, pois ele possui agendamentos vinculados.'); window.location.href='../painel.php?page=medicos';</script>";
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM medicos WHERE id = :id");
            $stmt->execute([':id' => $id_medico]);
            echo "<script>alert('Médico excluído com sucesso!'); window.location.href='../painel.php?page=medicos';</script>";
        } catch (PDOException $e) {
            die("Erro ao excluir médico: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Ação inválida.'); window.history.back();</script>";
    }
}
else {
    header("Location: ../painel.php?page=medicos");
    exit;
}
?>