<?php
// Conteúdo para a página de Exames
require_once BASE_PATH . '/database/conecta_DB.php';

$exames = [];
try {
    $stmt = $pdo->query("
        SELECT 
            e.id, 
            p.nome_completo AS paciente, 
            m.nome_completo AS medico, 
            a.data_agendamento, 
            a.hora_agendamento,
            a.status,
            e.tipo_exame,
            e.resultado,
            e.valor
        FROM exames e
        JOIN agendamentos a ON e.id_agendamento = a.id
        JOIN pacientes p ON a.id_paciente = p.id
        JOIN medicos m ON a.id_medico = m.id
        ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
    ");
    $exames = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar agendamentos do tipo 'exame' que ainda não foram convertidos em exame
    $stmtAgendamentosExame = $pdo->query("
        SELECT 
            a.id, 
            p.nome_completo AS paciente, 
            m.nome_completo AS medico, 
            a.data_agendamento, 
            a.hora_agendamento,
            a.id_paciente,
            a.id_medico
        FROM agendamentos a
        JOIN pacientes p ON a.id_paciente = p.id
        JOIN medicos m ON a.id_medico = m.id
        WHERE a.tipo_agendamento = 'exame' AND a.status IN ('agendado', 'confirmado')
        AND NOT EXISTS (SELECT 1 FROM exames ex WHERE ex.id_agendamento = a.id)
        ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC
    ");
    $agendamentosParaExame = $stmtAgendamentosExame->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Erro ao buscar exames: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Gerenciar Exames</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="document.getElementById('add-exame-form-container').style.display = 'block';">Registrar Novo Exame</button>
    </div>

    <div id="add-exame-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Registrar Novo Exame</h3>
        <form action="../clinica/processa/processa_exame.php" method="POST">
            <input type="hidden" name="action" value="add">

            <div class="input-group">
                <label for="id_agendamento_exame">Agendamento de Exame Existente:</label>
                <select id="id_agendamento_exame" name="id_agendamento" required>
                    <option value="">Selecione um agendamento de exame...</option>
                    <?php foreach ($agendamentosParaExame as $agendamento): ?>
                        <option value="<?php echo htmlspecialchars($agendamento['id']); ?>"
                            data-paciente-id="<?php echo htmlspecialchars($agendamento['id_paciente'] ?? ''); ?>"
                            data-medico-id="<?php echo htmlspecialchars($agendamento['id_medico'] ?? ''); ?>"
                            data-data="<?php echo htmlspecialchars($agendamento['data_agendamento']); ?> <?php echo htmlspecialchars($agendamento['hora_agendamento']); ?>">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($agendamento['data_agendamento']))); ?> <?php echo htmlspecialchars($agendamento['hora_agendamento']); ?> - Paciente: <?php echo htmlspecialchars($agendamento['paciente']); ?> - Médico: <?php echo htmlspecialchars($agendamento['medico']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <input type="hidden" id="paciente_id_exame_hidden" name="id_paciente">
            <input type="hidden" id="medico_id_exame_hidden" name="id_medico">
            <input type="hidden" id="data_exame_hidden" name="data_exame">

            <div class="input-group">
                <label for="tipo_exame">Tipo de Exame:</label>
                <input type="text" id="tipo_exame" name="tipo_exame" required>
            </div>
            <div class="input-group">
                <label for="resultado">Resultado:</label>
                <textarea id="resultado" name="resultado" rows="4"></textarea>
            </div>
            <div class="input-group">
                <label for="valor_exame">Valor do Exame (R$):</label>
                <input type="number" id="valor_exame" name="valor_exame" step="0.01" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Exame</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-exame-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>

    <?php if (empty($exames)): ?>
        <p class="no-data">Nenhum exame encontrado.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Data</th>
                        <th>Tipo Exame</th>
                        <th>Resultado</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exames as $exame): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($exame['id']); ?></td>
                            <td><?php echo htmlspecialchars($exame['paciente']); ?></td>
                            <td><?php echo htmlspecialchars($exame['medico']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($exame['data_agendamento'] . ' ' . $exame['hora_agendamento']))); ?></td>
                            <td><?php echo htmlspecialchars($exame['tipo_exame']); ?></td>
                            <td><?php echo htmlspecialchars(substr($exame['resultado'], 0, 50)) . (strlen($exame['resultado']) > 50 ? '...' : ''); ?></td>
                            <td>R$ <?php echo htmlspecialchars(number_format($exame['valor'], 2, ',', '.')); ?></td>
                            <td>
                                <button class="action-btn view-btn" onclick="alert('Ver detalhes do exame ID: <?php echo $exame['id']; ?>')">Ver</button>
                                <button class="action-btn edit-btn" onclick="alert('Editar exame ID: <?php echo $exame['id']; ?>')">Editar</button>
                                <button class="action-btn delete-btn" onclick="if(confirm('Tem certeza que deseja excluir o exame ID: <?php echo $exame['id']; ?>?')) { window.location.href='../clinica/processa/processa_exame.php?action=delete&id=<?php echo $exame['id']; ?>'; }">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    document.getElementById('id_agendamento_exame').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.pacienteId) {
            document.getElementById('paciente_id_exame_hidden').value = selectedOption.dataset.pacienteId;
        } else {
            console.warn('data-paciente-id is missing for selected option.');
        }
        if (selectedOption.dataset.medicoId) {
            document.getElementById('medico_id_exame_hidden').value = selectedOption.dataset.medicoId;
        } else {
            console.warn('data-medico-id is missing for selected option.');
        }
        document.getElementById('data_exame_hidden').value = selectedOption.dataset.data; // Pega a data e hora do agendamento
    });
</script>