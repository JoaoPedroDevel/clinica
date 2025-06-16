<?php

require_once '../database/conecta_DB.php'; // Inclui o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nomeCompleto = $_POST['nome_completo'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? ''; // Campo novo
    $senha = $_POST['senha'] ?? '';
    $confirmSenha = $_POST['confirm_password'] ?? ''; // Para validação da senha

    // Validação básica dos campos
    if (empty($nomeCompleto) || empty($email) || empty($senha) || empty($confirmSenha)) {
        echo "<script>alert('Todos os campos obrigatórios (Nome, E-mail e Senha) devem ser preenchidos.'); window.history.back();</script>";
        exit;
    }

    // Valida se as senhas coincidem
    if ($senha !== $confirmSenha) {
        echo "<script>alert('As senhas não coincidem. Por favor, digite a mesma senha nos dois campos.'); window.history.back();</script>";
        exit;
    }

    // Hash da senha antes de armazenar no banco de dados por segurança
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // Prepara a query de inserção com os novos campos
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, telefone, senha) VALUES (:nome_completo, :email, :telefone, :senha)");

        // Vincula os parâmetros aos valores
        $stmt->execute([
            ':nome_completo' => $nomeCompleto,
            ':email' => $email,
            ':telefone' => $telefone, // Vincula o novo campo telefone
            ':senha' => $senhaHash
        ]);

        // Se o cadastro foi bem-sucedido
        echo "<script>alert('Usuário cadastrado com sucesso! Você já pode fazer login.'); window.location.href = './login.php';</script>";

    } catch (PDOException $e) {
        // Captura exceções do PDO (erros do banco de dados)
        if ($e->getCode() == 23000) { // Código 23000 é para violação de chave única (UNIQUE constraint)
            echo "<script>alert('Este e-mail já está cadastrado. Por favor, use outro e-mail.'); window.history.back();</script>";
        } else {
            // Outros erros de banco de dados
            // Em ambiente de produção, logar o erro e exibir uma mensagem genérica ao usuário
            die("Erro ao cadastrar usuário: " . $e->getMessage());
        }
    }
} else {
    // Se a requisição não for POST (acesso direto ao arquivo), redireciona para a página de login
    header("Location: ../login.php"); // Altere para o caminho da sua página de login
    exit;
}
?>
