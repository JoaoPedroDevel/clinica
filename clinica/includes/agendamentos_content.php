<?php
// Conteúdo para a página de Agendamentos
require_once BASE_PATH . '/database/conecta_DB.php';

try {
    $stmt = $pdo->query("
        SELECT 
            a.id, 
            p.nome_completo AS paciente, 
            m.nome_completo AS medico, 
            a.data_agendamento, 
            a.hora_agendamento, 
            a.tipo_agendamento,
            a.status
        FROM agendamentos a
        JOIN pacientes p ON a.id_paciente = p.id
        JOIN medicos m ON a.id_medico = m.id
        ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
    ");
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar agendamentos: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Gerenciar Agendamentos</h2>

    <div class="actions-bar">
        <a href="<?php echo htmlspecialchars($base_link_path); ?>painel.php?page=novo_agendamento_admin" class="btn btn-primary">Novo Agendamento</a>
    </div>

    <?php if (empty($agendamentos)): ?>
        <p class="no-data">Nenhum agendamento encontrado.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agendamento['id']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['paciente']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['medico']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['hora_agendamento']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($agendamento['tipo_agendamento'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($agendamento['status']); ?>"><?php echo htmlspecialchars($agendamento['status']); ?></span></td>
                            <td>
                                <?php if ($agendamento['status'] == 'pendente'): ?>
                                    <button class="action-btn success-btn" onclick="if(confirm('Tem certeza que deseja APROVAR este agendamento?')) { window.location.href='../clinica/processa/aprovar_agendamento.php?id=<?php echo $agendamento['id']; ?>'; }">Aprovar</button>
                                    <button class="action-btn delete-btn" onclick="if(confirm('Tem certeza que deseja RECUSAR este agendamento?')) { window.location.href='../clinica/processa/recusar_agendamento.php?id=<?php echo $agendamento['id']; ?>'; }">Recusar</button>
                                <?php else: ?>
                                    <button class="action-btn view-btn" onclick="alert('Ver detalhes do agendamento ID: <?php echo $agendamento['id']; ?>')">Ver</button>
                                    <button class="action-btn edit-btn" onclick="alert('Editar agendamento ID: <?php echo $agendamento['id']; ?>')">Editar</button>
                                    <button class="action-btn delete-btn" onclick="if(confirm('Tem certeza que deseja excluir o agendamento ID: <?php echo $agendamento['id']; ?>?')) { window.location.href='../clinica/processa/cancelar_agendamento_admin.php?id=<?php echo $agendamento['id']; ?>'; }">Excluir</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>