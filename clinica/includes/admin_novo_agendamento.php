<?php
require_once BASE_PATH . '/database/conecta_DB.php';

// Buscar pacientes para preencher o select
try {
    $stmtPacientes = $pdo->query("SELECT id, nome_completo, cpf FROM pacientes ORDER BY nome_completo");
    $pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);

    // Buscar médicos para preencher o select
    $stmtMedicos = $pdo->query("SELECT id, nome_completo, especialidade FROM medicos ORDER BY nome_completo");
    $medicos = $stmtMedicos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Marcar Novo Agendamento (Admin)</h2>

    <div class="form-container">
        <form action="../clinica/processa/processa_novo_agendamento_admin.php" method="POST">
            <div class="input-group">
                <label for="id_paciente">Paciente:</label>
                <select id="id_paciente" name="id_paciente" required>
                    <option value="">Selecione o paciente...</option>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo htmlspecialchars($paciente['id']); ?>">
                            <?php echo htmlspecialchars($paciente['nome_completo']); ?> (CPF: <?php echo htmlspecialchars($paciente['cpf']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label for="tipo_agendamento">Tipo de Agendamento:</label>
                <select id="tipo_agendamento" name="tipo_agendamento" required>
                    <option value="">Selecione...</option>
                    <option value="consulta">Consulta</option>
                    <option value="exame">Exame</option>
                </select>
            </div>

            <div class="input-group">
                <label for="id_medico">Médico:</label>
                <select id="id_medico" name="id_medico" required>
                    <option value="">Selecione o médico...</option>
                    <?php foreach ($medicos as $medico): ?>
                        <option value="<?php echo htmlspecialchars($medico['id']); ?>">
                            <?php echo htmlspecialchars($medico['nome_completo']); ?> (<?php echo htmlspecialchars($medico['especialidade']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label for="data_agendamento">Data:</label>
                <input type="date" id="data_agendamento" name="data_agendamento" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="input-group">
                <label for="hora_agendamento">Hora:</label>
                <input type="time" id="hora_agendamento" name="hora_agendamento" required>
            </div>

            <div class="input-group">
                <label for="observacoes">Observações (opcional):</label>
                <textarea id="observacoes" name="observacoes" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Agendar</button>
        </form>
    </div>
</section>