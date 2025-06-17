<?php
require_once BASE_PATH . '/database/conecta_DB.php';

// Certifique-se de que $usuario_id está definido (do painel_usuario.php)
if (!isset($usuario_id)) {
    echo "<p class='no-data'>Erro: ID do usuário não disponível.</p>";
    exit;
}

// Buscar o ID do paciente associado ao usuário logado
$stmtPaciente = $pdo->prepare("SELECT id FROM pacientes WHERE email = :email");
$stmtPaciente->execute([':email' => $_SESSION['usuario_email']]);
$paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);
$id_paciente_logado = $paciente['id'] ?? null;

if (!$id_paciente_logado) {
    echo "<p class='no-data'>Seu perfil de paciente não foi encontrado. Por favor, contate a administração para configurar seu cadastro.</p>";
    exit;
}

// Buscar médicos para preencher o select
try {
    $stmtMedicos = $pdo->query("SELECT id, nome_completo, especialidade FROM medicos ORDER BY nome_completo");
    $medicos = $stmtMedicos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar médicos: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Marcar Novo Agendamento</h2>

    <div class="form-container">
        <form action="../cinica/processa/processa_novo_agendamento.php" method="POST">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($id_paciente_logado); ?>">

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