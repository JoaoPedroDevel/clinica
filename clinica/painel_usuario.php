<?php
session_start();

define('BASE_PATH', __DIR__);

// Redireciona para o login se o usuário não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./login/login.php");
    exit();
}

$nome_usuario = $_SESSION['usuario_nome'] ?? "Paciente";
$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado

$page = $_GET['page'] ?? 'meus_agendamentos'; // Padrão para usuário é 'meus_agendamentos'

$scriptName = $_SERVER['PHP_SELF'];
$base_link_path = dirname($scriptName);
$base_link_path = rtrim($base_link_path, '/') . '/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinMed - Meu Painel</title>
    <link rel="stylesheet" href="assets/style/painel.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span class="logo-text-small">ClinMed</span>
        </div>
        <ul class="menu">
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel_usuario.php?page=meus_agendamentos" class="<?php echo ($page == 'meus_agendamentos') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Meus Agendamentos</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel_usuario.php?page=novo_agendamento" class="<?php echo ($page == 'novo_agendamento') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Novo Agendamento</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel_usuario.php?page=meu_perfil" class="<?php echo ($page == 'meu_perfil') ? 'active' : ''; ?>"><i class="fas fa-user"></i> Meu Perfil</a></li>
            <li><a href="./login/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h1>Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
        </header>
        <main>
            <?php
            switch ($page) {
                case 'meus_agendamentos':
                    include BASE_PATH . '/includes/usuario_meus_agendamentos.php';
                    break;
                case 'novo_agendamento':
                    include BASE_PATH . '/includes/usuario_novo_agendamento.php';
                    break;
                case 'meu_perfil':
                    echo '<section class="page-section"><h2>Meu Perfil</h2><p>Detalhes do perfil do usuário aqui.</p></section>';
                    // Você pode criar um arquivo includes/usuario_perfil.php similar
                    break;
                default:
                    include BASE_PATH . '/includes/usuario_meus_agendamentos.php';
                    break;
            }
            ?>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> ClinMed. Todos os direitos reservados.</p>
        </footer>
    </div>

</body>
</html>