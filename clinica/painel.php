<?php
session_start();

define('BASE_PATH', __DIR__);

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ./login/login.php");
    exit();
}

$nome_usuario = $_SESSION['usuario_nome'] ?? "Admin";
$page = $_GET['page'] ?? 'dashboard'; // Padrão é dashboard

$scriptName = $_SERVER['PHP_SELF'];
$base_link_path = dirname($scriptName);
$base_link_path = rtrim($base_link_path, '/') . '/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinMed - Painel Administrativo</title>
    <link rel="stylesheet" href="assets/style/painel.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span class="logo-text-small">ClinMed</span>
        </div>
        <ul class="menu">
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=dashboard" class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=agendamentos" class="<?php echo ($page == 'agendamentos' || $page == 'novo_agendamento_admin') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Agendamentos</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=consultas" class="<?php echo ($page == 'consultas') ? 'active' : ''; ?>"><i class="fas fa-stethoscope"></i> Consultas</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=exames" class="<?php echo ($page == 'exames') ? 'active' : ''; ?>"><i class="fas fa-x-ray"></i> Exames</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=medicos" class="<?php echo ($page == 'medicos') ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Médicos</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=pacientes" class="<?php echo ($page == 'pacientes') ? 'active' : ''; ?>"><i class="fas fa-user-injured"></i> Pacientes</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=relatorios" class="<?php echo ($page == 'relatorios') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=configuracoes" class="<?php echo ($page == 'configuracoes') ? 'active' : ''; ?>"><i class="fas fa-cogs"></i> Configurações</a></li>
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
                case 'dashboard':
                    include BASE_PATH . '/includes/dashboard_content.php';
                    break;
                case 'agendamentos':
                    include BASE_PATH . '/includes/agendamentos_content.php';
                    break;
                case 'novo_agendamento_admin': // Novo caso para agendamento do admin
                    include BASE_PATH . '/includes/admin_novo_agendamento.php';
                    break;
                case 'consultas':
                    include BASE_PATH . '/includes/consultas_content.php';
                    break;
                case 'exames':
                    include BASE_PATH . '/includes/exames_content.php';
                    break;
                case 'medicos':
                    include BASE_PATH . '/includes/medicos_content.php';
                    break;
                case 'pacientes':
                    include BASE_PATH . '/includes/pacientes_content.php';
                    break;
                case 'relatorios':
                    include BASE_PATH . '/includes/relatorios_content.php';
                    break;
                case 'configuracoes':
                    include BASE_PATH . '/includes/configuracoes_content.php';
                    break;
                default:
                    include BASE_PATH . '/includes/dashboard_content.php';
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