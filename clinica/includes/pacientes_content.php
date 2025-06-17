<?php
// Conteúdo para a página de Pacientes
require_once BASE_PATH . '/database/conecta_DB.php';

$pacientes = [];
try {
    $stmt = $pdo->query("SELECT * FROM pacientes ORDER BY nome_completo");
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar pacientes: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Gerenciar Pacientes</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="document.getElementById('add-paciente-form-container').style.display = 'block'; document.getElementById('edit-paciente-form-container').style.display = 'none';">Novo Paciente</button>
    </div>

    <div id="add-paciente-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Adicionar Novo Paciente</h3>
        <form action="../clinica/processa/processa_paciente.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="input-group">
                <label for="nome_completo_paciente">Nome Completo:</label>
                <input type="text" id="nome_completo_paciente" name="nome_completo" required>
            </div>
            <div class="input-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>
            <div class="input-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="Ex: 123.456.789-00">
            </div>
            <div class="input-group">
                <label for="telefone_paciente">Telefone:</label>
                <input type="tel" id="telefone_paciente" name="telefone" pattern="[0-9]{2} [0-9]{5}-[0-9]{4}|[0-9]{2} [0-9]{4}-[0-9]{4}" placeholder="Ex: 85 98765-4321">
            </div>
            <div class="input-group">
                <label for="email_paciente">E-mail:</label>
                <input type="email" id="email_paciente" name="email">
            </div>
            <div class="input-group">
                <label for="endereco">Endereço:</label>
                <textarea id="endereco" name="endereco" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Paciente</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-paciente-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>

    <div id="edit-paciente-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Editar Paciente</h3>
        <form action="../clinica/processa/processa_paciente.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_paciente_id" name="id">
            <div class="input-group">
                <label for="edit_nome_completo_paciente">Nome Completo:</label>
                <input type="text" id="edit_nome_completo_paciente" name="nome_completo" required>
            </div>
            <div class="input-group">
                <label for="edit_data_nascimento">Data de Nascimento:</label>
                <input type="date" id="edit_data_nascimento" name="data_nascimento" required>
            </div>
            <div class="input-group">
                <label for="edit_cpf">CPF:</label>
                <input type="text" id="edit_cpf" name="cpf" required pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="Ex: 123.456.789-00">
            </div>
            <div class="input-group">
                <label for="edit_telefone_paciente">Telefone:</label>
                <input type="tel" id="edit_telefone_paciente" name="telefone" pattern="[0-9]{2} [0-9]{5}-[0-9]{4}|[0-9]{2} [0-9]{4}-[0-9]{4}">
            </div>
            <div class="input-group">
                <label for="edit_email_paciente">E-mail:</label>
                <input type="email" id="edit_email_paciente" name="email">
            </div>
            <div class="input-group">
                <label for="edit_endereco">Endereço:</label>
                <textarea id="edit_endereco" name="endereco" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Paciente</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('edit-paciente-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>

    <?php if (empty($pacientes)): ?>
        <p class="no-data">Nenhum paciente cadastrado.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Data Nasc.</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paciente['id']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($paciente['data_nascimento']))); ?></td>
                            <td><?php echo htmlspecialchars($paciente['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['email']); ?></td>
                            <td><?php echo htmlspecialchars($paciente['telefone']); ?></td>
                            <td>
                                <button class="action-btn edit-btn"
                                    onclick="editPaciente(<?php echo htmlspecialchars(json_encode($paciente)); ?>)">
                                    Editar
                                </button>
                                <button class="action-btn delete-btn"
                                    onclick="if(confirm('Tem certeza que deseja excluir o paciente ID: <?php echo $paciente['id']; ?>?')) { window.location.href='../clinica/processa/processa_paciente.php?action=delete&id=<?php echo $paciente['id']; ?>'; }">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    function editPaciente(paciente) {
        // Preenche o formulário de edição
        document.getElementById('edit_paciente_id').value = paciente.id;
        document.getElementById('edit_nome_completo_paciente').value = paciente.nome_completo;
        document.getElementById('edit_data_nascimento').value = paciente.data_nascimento;
        document.getElementById('edit_cpf').value = paciente.cpf;
        document.getElementById('edit_telefone_paciente').value = paciente.telefone;
        document.getElementById('edit_email_paciente').value = paciente.email;
        document.getElementById('edit_endereco').value = paciente.endereco;

        // Mostra o formulário de edição e esconde o de adição, se estiver visível
        document.getElementById('add-paciente-form-container').style.display = 'none';
        document.getElementById('edit-paciente-form-container').style.display = 'block';
        window.scrollTo(0, document.getElementById('edit-paciente-form-container').offsetTop); // Rola para o formulário
    }
</script>