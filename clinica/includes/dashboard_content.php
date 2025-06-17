<?php
// Conteúdo do Dashboard
require_once BASE_PATH . '/database/conecta_DB.php'; // Usa BASE_PATH para incluir corretamente

try {
    // Total de Consultas Hoje
    $hoje = date('Y-m-d');
    $stmtConsultasHoje = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data_agendamento = :hoje AND tipo_agendamento = 'consulta' AND status = 'agendado'");
    $stmtConsultasHoje->execute([':hoje' => $hoje]);
    $consultasHoje = $stmtConsultasHoje->fetchColumn();

    // Próximos Exames (Ex: nos próximos 7 dias e status 'agendado' ou 'pendente')
    $dataFutura = date('Y-m-d', strtotime('+7 days'));
    $stmtProximosExames = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data_agendamento BETWEEN :hoje AND :data_futura AND tipo_agendamento = 'exame' AND (status = 'agendado' OR status = 'pendente')");
    $stmtProximosExames->execute([':hoje' => $hoje, ':data_futura' => $dataFutura]);
    $proximosExames = $stmtProximosExames->fetchColumn();

    // Novos Pacientes (Ex: últimos 7 dias)
    $data7diasAtras = date('Y-m-d', strtotime('-7 days'));
    $stmtNovosPacientes = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE data_cadastro >= :data_7dias_atras");
    $stmtNovosPacientes->execute([':data_7dias_atras' => $data7diasAtras]);
    $novosPacientes = $stmtNovosPacientes->fetchColumn();

    // Faturamento (Ex: soma do valor de consultas/exames concluídos hoje)
    // Isso exigiria que os valores fossem registrados nas tabelas 'consultas' e 'exames' e que o status fosse 'concluido'
    // Por enquanto, mantém o valor estimado ou busca valores reais se houver dados.
    $faturamentoHoje = "0.00"; // Placeholder
    // Exemplo de busca (se houver valores e status 'concluido')
    $stmtFaturamentoConsultas = $pdo->prepare("SELECT SUM(c.valor) FROM consultas c JOIN agendamentos a ON c.id_agendamento = a.id WHERE a.data_agendamento = :hoje AND a.status = 'concluido'");
    $stmtFaturamentoConsultas->execute([':hoje' => $hoje]);
    $faturamentoConsultas = $stmtFaturamentoConsultas->fetchColumn() ?: 0;

    $stmtFaturamentoExames = $pdo->prepare("SELECT SUM(e.valor) FROM exames e JOIN agendamentos a ON e.id_agendamento = a.id WHERE a.data_agendamento = :hoje AND a.status = 'concluido'");
    $stmtFaturamentoExames->execute([':hoje' => $hoje]);
    $faturamentoExames = $stmtFaturamentoExames->fetchColumn() ?: 0;

    $faturamentoHoje = number_format($faturamentoConsultas + $faturamentoExames, 2, ',', '.');


} catch (PDOException $e) {
    die("Erro ao carregar dados do dashboard: " . $e->getMessage());
}
?>
<section class="dashboard-section">
    <h2>Visão Geral</h2>
    <div class="dashboard-cards">
        <div class="card">
            <h3>Consultas Hoje</h3>
            <span><?php echo htmlspecialchars($consultasHoje); ?></span>
            <p>Agendamentos confirmados</p>
        </div>
        <div class="card">
            <h3>Próximos Exames</h3>
            <span><?php echo htmlspecialchars($proximosExames); ?></span>
            <p>Nos próximos 7 dias</p>
        </div>
        <div class="card">
            <h3>Novos Pacientes</h3>
            <span><?php echo htmlspecialchars($novosPacientes); ?></span>
            <p>Últimos 7 dias</p>
        </div>
        <div class="card">
            <h3>Faturamento</h3>
            <span>R$ <?php echo htmlspecialchars($faturamentoHoje); ?></span>
            <p>Hoje (concluído)</p>
        </div>
    </div>
</section>

<section class="recent-activity">
    <h2>Atividade Recente</h2>
    <?php
    // Exemplo de busca de atividades recentes (últimos 5 agendamentos)
    $stmtAtividades = $pdo->query("SELECT p.nome_completo as paciente, m.nome_completo as medico, a.data_agendamento, a.hora_agendamento, a.tipo_agendamento, a.status FROM agendamentos a JOIN pacientes p ON a.id_paciente = p.id JOIN medicos m ON a.id_medico = m.id ORDER BY a.data_criacao DESC LIMIT 5");
    $atividadesRecentes = $stmtAtividades->fetchAll(PDO::FETCH_ASSOC);

    if (empty($atividadesRecentes)): ?>
        <p class="no-data">Nenhuma atividade recente para exibir.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividadesRecentes as $atividade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($atividade['paciente']); ?></td>
                            <td><?php echo htmlspecialchars($atividade['medico']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($atividade['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($atividade['hora_agendamento']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($atividade['tipo_agendamento'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($atividade['status']); ?>"><?php echo htmlspecialchars($atividade['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>