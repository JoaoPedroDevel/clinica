<?php

$host = 'localhost';
$db   = 'clinica';         
$user = 'root';             
$pass = '';                 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_agendamento = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        // Obter o ID do paciente associado ao usuário logado
        $stmtPaciente = $pdo->prepare("SELECT id FROM pacientes WHERE email = :email");
        $stmtPaciente->execute([':email' => $_SESSION['usuario_email']]);
        $pacienteLogado = $stmtPaciente->fetch(PDO::FETCH_ASSOC);
        $id_paciente_logado = $pacienteLogado['id'] ?? null;

        if (!$id_paciente_logado) {
            echo "<script>alert('Seu perfil de paciente não foi encontrado.'); window.location.href='../painel_usuario.php?page=meus_agendamentos';</script>";
            exit;
        }

        // Verificar se o agendamento pertence ao paciente logado e se o status permite cancelamento
        $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = :id AND id_paciente = :id_paciente AND (status = 'agendado' OR status = 'pendente')");
        $stmt->execute([':id' => $id_agendamento, ':id_paciente' => $id_paciente_logado]);
        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($agendamento) {
            // Atualizar o status para 'cancelado'
            $stmtUpdate = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = :id");
            $stmtUpdate->execute([':id' => $id_agendamento]);
            echo "<script>alert('Agendamento cancelado com sucesso!'); window.location.href='../painel_usuario.php?page=meus_agendamentos';</script>";
        } else {
            echo "<script>alert('Agendamento não encontrado ou não pode ser cancelado.'); window.location.href='../painel_usuario.php?page=meus_agendamentos';</script>";
        }

    } catch (PDOException $e) {
        die("Erro ao cancelar agendamento: " . $e->getMessage());
    }
} else {
    header("Location: ../painel_usuario.php?page=meus_agendamentos");
    exit;
}
?>