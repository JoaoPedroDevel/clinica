<?php
session_start();

// Define o caminho base do diretório atual para includes PHP.
define('BASE_PATH', __DIR__);

// O caminho de redirecionamento para o login.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./login/login.php");
    exit();
}
$nome_usuario = $_SESSION['usuario_nome'] ?? "Usuário";
// Define a página atual para controlar o conteúdo exibido
$page = $_GET['page'] ?? 'dashboard'; // Padrão é 'dashboard'

// --- NOVO: Define o caminho base para os links (href) ---
// Obtém o caminho do script atual relativo ao Document Root do servidor (ex: /seu_projeto/painel_admin.php)
$scriptName = $_SERVER['PHP_SELF'];
// Remove o nome do arquivo atual (painel_admin.php) para obter apenas o diretório
$base_link_path = dirname($scriptName);
// Garante que haja uma barra no final, a menos que seja a raiz do Document Root
$base_link_path = rtrim($base_link_path, '/') . '/';
// --- FIM NOVO ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinMed - Painel Administrativo</title>
    <!-- O caminho para o CSS permanece relativo à localização do painel_admin.php -->
    <link rel="stylesheet" href="assets/style/painel.css">
    <!-- Font Awesome para ícones (substitua 'a076d05399' pelo seu próprio kit se tiver um novo) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span class="logo-text-small">ClinMed</span>
        </div>
        <ul class="menu">
            <!-- Links de navegação atualizados para usar $base_link_path -->
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=dashboard" class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=agendamentos" class="<?php echo ($page == 'agendamentos') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Agendamentos</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=consultas" class="<?php echo ($page == 'consultas') ? 'active' : ''; ?>"><i class="fas fa-stethoscope"></i> Consultas</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=exames" class="<?php echo ($page == 'exames') ? 'active' : ''; ?>"><i class="fas fa-flask"></i> Exames</a></li>
            <!-- Link para a nova página de médicos -->
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=medicos" class="<?php echo ($page == 'medicos') ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Médicos</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=pacientes" class="<?php echo ($page == 'pacientes') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Pacientes</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=relatorios" class="<?php echo ($page == 'relatorios') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Relatórios</a></li>
            <li><a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=configuracoes" class="<?php echo ($page == 'configuracoes') ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Configurações</a></li>
            <!-- O link de logout permanece relativo ao painel_admin.php, pois ele está na mesma "sub-pasta" do login/logout -->
            <li><a href="./login/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h1>Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
        </header>
        <main>
            <?php
            // Conteúdo dinâmico baseado no parâmetro 'page'
            switch ($page) {
                case 'dashboard':
                    // Usando BASE_PATH para garantir o caminho absoluto para includes
                    include BASE_PATH . '/includes/dashboard_content.php';
                    break;
                case 'agendamentos':
                    include BASE_PATH . '/includes/agendamentos_content.php';
                    break;
                case 'consultas':
                    echo '<section class="page-section"><h2>Gerenciar Consultas</h2><p>Conteúdo para gerenciar consultas aqui.</p></section>';
                    break;
                case 'exames':
                    echo '<section class="page-section"><h2>Gerenciar Exames</h2><p>Conteúdo para gerenciar exames aqui.</p></section>';
                    break;
                case 'medicos':
                    include BASE_PATH . '/includes/medicos_content.php';
                    break;
                case 'pacientes':
                    echo '<section class="page-section"><h2>Gerenciar Pacientes</h2><p>Conteúdo para gerenciar pacientes aqui.</p></section>';
                    break;
                case 'relatorios':
                    echo '<section class="page-section"><h2>Relatórios</h2><p>Conteúdo para relatórios e estatísticas aqui.</p></section>';
                    break;
                case 'configuracoes':
                    echo '<section class="page-section"><h2>Configurações do Sistema</h2><p>Conteúdo para configurações gerais aqui.</p></section>';
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
