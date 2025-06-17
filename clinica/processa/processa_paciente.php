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
            $dataNascimento = $_POST['data_nascimento'] ?? '';
            $cpf = $_POST['cpf'] ?? '';
            $telefone = $_POST['telefone'] ?? null;
            $email = $_POST['email'] ?? null;
            $endereco = $_POST['endereco'] ?? null;

            if (empty($nomeCompleto) || empty($dataNascimento) || empty($cpf)) {
                echo "<script>alert('Nome, Data de Nascimento e CPF são campos obrigatórios.'); window.history.back();</script>";
                exit;
            }

            try {
                // Inserir na tabela pacientes
                $stmt = $pdo->prepare("INSERT INTO pacientes (nome_completo, data_nascimento, cpf, telefone, email, endereco) VALUES (:nome_completo, :data_nascimento, :cpf, :telefone, :email, :endereco)");
                $stmt->execute([
                    ':nome_completo' => $nomeCompleto,
                    ':data_nascimento' => $dataNascimento,
                    ':cpf' => $cpf,
                    ':telefone' => $telefone,
                    ':email' => $email,
                    ':endereco' => $endereco
                ]);

                // Opcional: Criar um usuário de acesso para este paciente automaticamente (se o email for fornecido e único)
                if ($email && !empty($email)) {
                    $stmtCheckUser = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
                    $stmtCheckUser->execute([':email' => $email]);
                    if ($stmtCheckUser->rowCount() == 0) {
                        // Gerar uma senha temporária ou pedir para o admin definir
                        $senhaPadrao = "mudar@123"; // Senha padrão para o paciente acessar
                        $senhaHash = password_hash($senhaPadrao, PASSWORD_DEFAULT);
                        $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, telefone, senha, is_admin) VALUES (:nome_completo, :email, :telefone, :senha, FALSE)");
                        $stmtUser->execute([
                            ':nome_completo' => $nomeCompleto,
                            ':email' => $email,
                            ':telefone' => $telefone,
                            ':senha' => $senhaHash
                        ]);
                        echo "<script>alert('Paciente cadastrado com sucesso e usuário de acesso criado com senha padrão: \"mudar@123\" (se necessário, o paciente deve alterar).'); window.location.href='../painel.php?page=pacientes';</script>";
                    } else {
                        echo "<script>alert('Paciente cadastrado com sucesso. ATENÇÃO: Um usuário com este e-mail já existe, nenhum novo usuário de acesso foi criado.'); window.location.href='../painel.php?page=pacientes';</script>";
                    }
                } else {
                    echo "<script>alert('Paciente cadastrado com sucesso! Para que o paciente possa acessar o sistema, crie um usuário para ele com um e-mail válido.'); window.location.href='../painel.php?page=pacientes';</script>";
                }


            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE key (CPF ou Email)
                    echo "<script>alert('Erro: CPF ou E-mail do paciente já cadastrado.'); window.history.back();</script>";
                } else {
                    die("Erro ao adicionar paciente: " . $e->getMessage());
                }
            }
            break;

        case 'edit':
            $id = $_POST['id'] ?? null;
            $nomeCompleto = $_POST['nome_completo'] ?? '';
            $dataNascimento = $_POST['data_nascimento'] ?? '';
            $cpf = $_POST['cpf'] ?? '';
            $telefone = $_POST['telefone'] ?? null;
            $email = $_POST['email'] ?? null;
            $endereco = $_POST['endereco'] ?? null;

            if (empty($id) || empty($nomeCompleto) || empty($dataNascimento) || empty($cpf)) {
                echo "<script>alert('ID, Nome, Data de Nascimento e CPF são campos obrigatórios para edição.'); window.history.back();</script>";
                exit;
            }

            try {
                $stmt = $pdo->prepare("UPDATE pacientes SET nome_completo = :nome_completo, data_nascimento = :data_nascimento, cpf = :cpf, telefone = :telefone, email = :email, endereco = :endereco WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':nome_completo' => $nomeCompleto,
                    ':data_nascimento' => $dataNascimento,
                    ':cpf' => $cpf,
                    ':telefone' => $telefone,
                    ':email' => $email,
                    ':endereco' => $endereco
                ]);

                // Opcional: Atualizar o usuário de acesso se o email do paciente foi alterado e o usuário existe/precisa ser atualizado
                if ($email && !empty($email)) {
                    $stmtCheckUser = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email_old");
                    $stmtCheckUser->execute([':email_old' => $_SESSION['usuario_email_anterior_paciente'] ?? $email]); // Usa email anterior se houver
                    $existingUserId = $stmtCheckUser->fetchColumn();

                    if ($existingUserId) {
                        $stmtUpdateUser = $pdo->prepare("UPDATE usuarios SET nome_completo = :nome_completo, email = :email, telefone = :telefone WHERE email = :email_old");
                        $stmtUpdateUser->execute([
                            ':nome_completo' => $nomeCompleto,
                            ':email' => $email,
                            ':telefone' => $telefone,
                            ':email_old' => $_SESSION['usuario_email_anterior_paciente'] ?? $email
                        ]);
                    }
                }

                echo "<script>alert('Paciente atualizado com sucesso!'); window.location.href='../painel.php?page=pacientes';</script>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE key (CPF ou Email)
                    echo "<script>alert('Erro: CPF ou E-mail do paciente já cadastrado para outro paciente.'); window.history.back();</script>";
                } else {
                    die("Erro ao atualizar paciente: " . $e->getMessage());
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
        $id_paciente = $_GET['id'];
        try {
            // Verificar se o paciente possui agendamentos/consultas antes de excluir (opcional)
            $stmtCheckDependencies = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE id_paciente = :id");
            $stmtCheckDependencies->execute([':id' => $id_paciente]);
            if ($stmtCheckDependencies->fetchColumn() > 0) {
                echo "<script>alert('Não é possível excluir o paciente, pois ele possui agendamentos vinculados.'); window.location.href='../painel.php?page=pacientes';</script>";
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = :id");
            $stmt->execute([':id' => $id_paciente]);
            echo "<script>alert('Paciente excluído com sucesso!'); window.location.href='../painel.php?page=pacientes';</script>";
        } catch (PDOException $e) {
            die("Erro ao excluir paciente: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Ação inválida.'); window.history.back();</script>";
    }
} else {
    header("Location: ../painel.php?page=pacientes");
    exit;
}
?>