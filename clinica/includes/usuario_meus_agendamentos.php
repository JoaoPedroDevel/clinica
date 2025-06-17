<?php
require_once BASE_PATH . '/database/conecta_DB.php';

// Certifique-se de que $usuario_id está definido (do painel_usuario.php)
if (!isset($usuario_id)) {
    echo "<p class='no-data'>Erro: ID do usuário não disponível.</p>";
    exit;
}

try {
    // Buscar o ID do paciente associado ao usuário logado (assumindo 1:1 ou que o usuário seja o paciente)
    $stmtPaciente = $pdo->prepare("SELECT id FROM pacientes WHERE email = :email");
    $stmtPaciente->execute([':email' => $_SESSION['usuario_email']]);
    $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

    $id_paciente_logado = $paciente['id'] ?? null;

    if (!$id_paciente_logado) {
        echo "<p class='no-data'>Você ainda não está cadastrado como paciente. Por favor, contate a administração.</p>";
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            a.id, 
            a.data_agendamento, 
            a.hora_agendamento, 
            a.tipo_agendamento, 
            a.status,
            m.nome_completo AS nome_medico,
            m.especialidade
        FROM agendamentos a
        JOIN medicos m ON a.id_medico = m.id
        WHERE a.id_paciente = :id_paciente
        ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
    ");
    $stmt->execute([':id_paciente' => $id_paciente_logado]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar agendamentos: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Meus Agendamentos</h2>

    <div class="actions-bar">
        <a href="<?php echo htmlspecialchars($base_link_path); ?>painel_usuario.php?page=novo_agendamento" class="btn btn-primary">Novo Agendamento</a>
    </div>

    <?php if (empty($agendamentos)): ?>
        <p class="no-data">Você não possui agendamentos. Que tal <a href="<?php echo htmlspecialchars($base_link_path); ?>painel_usuario.php?page=novo_agendamento">marcar um novo</a>?</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Médico</th>
                        <th>Especialidade</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agendamento['id']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['hora_agendamento']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($agendamento['tipo_agendamento'])); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['nome_medico']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['especialidade']); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($agendamento['status']); ?>"><?php echo htmlspecialchars($agendamento['status']); ?></span></td>
                            <td>
                                <?php if ($agendamento['status'] == 'agendado' || $agendamento['status'] == 'pendente'): ?>
                                    <button class="action-btn delete-btn" onclick="if(confirm('Tem certeza que deseja cancelar este agendamento?')) { window.location.href='../clinica/processa/cancelar_agendamento.php?id=<?php echo $agendamento['id']; ?>'; }">Cancelar</button>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>