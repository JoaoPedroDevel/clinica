<?php
// Conteúdo para a página de Consultas
require_once BASE_PATH . '/database/conecta_DB.php';

$consultas = [];
try {
    $stmt = $pdo->query("
        SELECT 
            c.id, 
            p.nome_completo AS paciente, 
            m.nome_completo AS medico, 
            a.data_agendamento, 
            a.hora_agendamento,
            a.status,
            c.diagnostico,
            c.valor
        FROM consultas c
        JOIN agendamentos a ON c.id_agendamento = a.id
        JOIN pacientes p ON a.id_paciente = p.id
        JOIN medicos m ON a.id_medico = m.id
        ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
    ");
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar agendamentos do tipo 'consulta' que ainda não foram convertidos em consulta
    $stmtAgendamentosConsulta = $pdo->query("
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
        WHERE a.tipo_agendamento = 'consulta' AND a.status IN ('agendado', 'confirmado')
        AND NOT EXISTS (SELECT 1 FROM consultas c WHERE c.id_agendamento = a.id)
        ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC
    ");
    $agendamentosParaConsulta = $stmtAgendamentosConsulta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar consultas: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Gerenciar Consultas</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="document.getElementById('add-consulta-form-container').style.display = 'block';">Registrar Nova Consulta</button>
    </div>

    <div id="add-consulta-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Registrar Nova Consulta</h3>
        <form action="../clinica/processa/processa_consulta.php" method="POST">
            <input type="hidden" name="action" value="add">

            <div class="input-group">
                <label for="id_agendamento">Agendamento de Consulta Existente:</label>
                <select id="id_agendamento" name="id_agendamento" required>
                    <option value="">Selecione um agendamento de consulta...</option>
                    <?php foreach ($agendamentosParaConsulta as $agendamento): ?>
                        <option value="<?php echo htmlspecialchars($agendamento['id']); ?>"
                            data-paciente-id="<?php echo htmlspecialchars($agendamento['id_paciente'] ?? ''); ?>"
                            data-medico-id="<?php echo htmlspecialchars($agendamento['id_medico'] ?? ''); ?>"
                            data-data="<?php echo htmlspecialchars($agendamento['data_agendamento']); ?> <?php echo htmlspecialchars($agendamento['hora_agendamento']); ?>">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($agendamento['data_agendamento']))); ?> <?php echo htmlspecialchars($agendamento['hora_agendamento']); ?> - Paciente: <?php echo htmlspecialchars($agendamento['paciente']); ?> - Médico: <?php echo htmlspecialchars($agendamento['medico']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <input type="hidden" id="paciente_id_hidden" name="id_paciente">
            <input type="hidden" id="medico_id_hidden" name="id_medico">
            <input type="hidden" id="data_consulta_hidden" name="data_consulta">


            <div class="input-group">
                <label for="diagnostico">Diagnóstico:</label>
                <textarea id="diagnostico" name="diagnostico" rows="4" required></textarea>
            </div>
            <div class="input-group">
                <label for="receita">Receita (opcional):</label>
                <textarea id="receita" name="receita" rows="4"></textarea>
            </div>
            <div class="input-group">
                <label for="valor_consulta">Valor da Consulta (R$):</label>
                <input type="number" id="valor_consulta" name="valor_consulta" step="0.01" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Consulta</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-consulta-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>

    <?php if (empty($consultas)): ?>
        <p class="no-data">Nenhuma consulta encontrada.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Data/Hora</th>
                        <th>Status Agend.</th>
                        <th>Diagnóstico</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consulta['id']); ?></td>
                            <td><?php echo htmlspecialchars($consulta['paciente']); ?></td>
                            <td><?php echo htmlspecialchars($consulta['medico']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($consulta['data_agendamento'] . ' ' . $consulta['hora_agendamento']))); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($consulta['status']); ?>"><?php echo htmlspecialchars($consulta['status']); ?></span></td>
                            <td><?php echo htmlspecialchars(substr($consulta['diagnostico'], 0, 50)) . (strlen($consulta['diagnostico']) > 50 ? '...' : ''); ?></td>
                            <td>R$ <?php echo htmlspecialchars(number_format($consulta['valor'], 2, ',', '.')); ?></td>
                            <td>
                                <button class="action-btn view-btn" onclick="alert('Ver detalhes da consulta ID: <?php echo $consulta['id']; ?>')">Ver</button>
                                <button class="action-btn edit-btn" onclick="alert('Editar consulta ID: <?php echo $consulta['id']; ?>')">Editar</button>
                                <button class="action-btn delete-btn" onclick="if(confirm('Tem certeza que deseja excluir a consulta ID: <?php echo $consulta['id']; ?>?')) { window.location.href='../clinica/processa/processa_consulta.php?action=delete&id=<?php echo $consulta['id']; ?>'; }">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    document.getElementById('id_agendamento').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        // Ensure data-paciente-id and data-medico-id exist on the option
        if (selectedOption.dataset.pacienteId) {
            document.getElementById('paciente_id_hidden').value = selectedOption.dataset.pacienteId;
        } else {
            console.warn('data-paciente-id is missing for selected option.');
        }
        if (selectedOption.dataset.medicoId) {
            document.getElementById('medico_id_hidden').value = selectedOption.dataset.medicoId;
        } else {
            console.warn('data-medico-id is missing for selected option.');
        }
        document.getElementById('data_consulta_hidden').value = selectedOption.dataset.data; // Pega a data e hora do agendamento
    });
</script>