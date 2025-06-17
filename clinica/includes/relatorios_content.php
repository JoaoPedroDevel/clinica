<?php
// Conteúdo para a página de Relatórios
require_once BASE_PATH . '/database/conecta_DB.php';

// Aqui você pode adicionar lógica para buscar dados e gerar relatórios simples.
// Por exemplo, total de consultas por mês, médicos mais procurados, etc.

// Exemplo simples: Contagem de agendamentos por status
try {
    $stmtStatus = $pdo->query("SELECT status, COUNT(*) as total FROM agendamentos GROUP BY status");
    $statusCounts = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar dados para relatórios: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Relatórios e Estatísticas</h2>

    <div class="dashboard-cards">
        <?php if (!empty($statusCounts)): ?>
            <?php foreach ($statusCounts as $statusCount): ?>
                <div class="card">
                    <h3>Agendamentos <?php echo htmlspecialchars(ucfirst($statusCount['status'])); ?></h3>
                    <span><?php echo htmlspecialchars($statusCount['total']); ?></span>
                    <p>Total de agendamentos com este status</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">Nenhum dado de agendamento para relatórios.</p>
        <?php endif; ?>
    </div>

    <h3 style="margin-top: 40px;">Gerar Relatório Personalizado</h3>
    <div class="form-container">
        <form action="#" method="GET">
            <div class="input-group">
                <label for="tipo_relatorio">Tipo de Relatório:</label>
                <select id="tipo_relatorio" name="tipo_relatorio">
                    <option value="">Selecione...</option>
                    <option value="agendamentos_periodo">Agendamentos por Período</option>
                    <option value="consultas_medico">Consultas por Médico</option>
                    <option value="pacientes_ativos">Pacientes Ativos</option>
                </select>
            </div>
            <div class="input-group">
                <label for="data_inicio">Data Início:</label>
                <input type="date" id="data_inicio" name="data_inicio">
            </div>
            <div class="input-group">
                <label for="data_fim">Data Fim:</label>
                <input type="date" id="data_fim" name="data_fim">
            </div>
            <button type="submit" class="btn btn-primary">Gerar Relatório</button>
        </form>
    </div>

    <p style="margin-top: 30px; color: #777;">* As funcionalidades de relatório completo seriam desenvolvidas aqui, buscando dados específicos do banco de dados e apresentando-os em tabelas ou gráficos.</p>
</section>