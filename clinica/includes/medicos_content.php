<?php
// Conteúdo para a página de Médicos
require_once BASE_PATH . '/database/conecta_DB.php';

$medicos = [];
try {
    $stmt = $pdo->query("SELECT * FROM medicos ORDER BY nome_completo");
    $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar médicos: " . $e->getMessage());
}
?>
<section class="page-section">
    <h2>Gerenciar Médicos</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="document.getElementById('add-medico-form-container').style.display = 'block'; document.getElementById('edit-medico-form-container').style.display = 'none';">Novo Médico</button>
    </div>

    <div id="add-medico-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Adicionar Novo Médico</h3>
        <form action="../clinica/processa/processa_medico.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="input-group">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" id="nome_completo" name="nome_completo" required>
            </div>
            <div class="input-group">
                <label for="especialidade">Especialidade:</label>
                <input type="text" id="especialidade" name="especialidade" required>
            </div>
            <div class="input-group">
                <label for="crm">CRM:</label>
                <input type="text" id="crm" name="crm" required>
            </div>
            <div class="input-group">
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" pattern="[0-9]{2} [0-9]{5}-[0-9]{4}|[0-9]{2} [0-9]{4}-[0-9]{4}" placeholder="Ex: 85 98765-4321">
            </div>
            <div class="input-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Médico</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-medico-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>

    <div id="edit-medico-form-container" class="form-container" style="display: none; margin-top: 20px;">
        <h3>Editar Médico</h3>
        <form action="../processa/processa_medico.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_medico_id" name="id">
            <div class="input-group">
                <label for="edit_nome_completo">Nome Completo:</label>
                <input type="text" id="edit_nome_completo" name="nome_completo" required>
            </div>
            <div class="input-group">
                <label for="edit_especialidade">Especialidade:</label>
                <input type="text" id="edit_especialidade" name="especialidade" required>
            </div>
            <div class="input-group">
                <label for="edit_crm">CRM:</label>
                <input type="text" id="edit_crm" name="crm" required>
            </div>
            <div class="input-group">
                <label for="edit_telefone">Telefone:</label>
                <input type="tel" id="edit_telefone" name="telefone" pattern="[0-9]{2} [0-9]{5}-[0-9]{4}|[0-9]{2} [0-9]{4}-[0-9]{4}">
            </div>
            <div class="input-group">
                <label for="edit_email">E-mail:</label>
                <input type="email" id="edit_email" name="email">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Médico</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('edit-medico-form-container').style.display = 'none';">Cancelar</button>
        </form>
    </div>


    <?php if (empty($medicos)): ?>
        <p class="no-data">Nenhum médico cadastrado.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Especialidade</th>
                        <th>CRM</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicos as $medico): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medico['id']); ?></td>
                            <td><?php echo htmlspecialchars($medico['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($medico['especialidade']); ?></td>
                            <td><?php echo htmlspecialchars($medico['crm']); ?></td>
                            <td><?php echo htmlspecialchars($medico['email']); ?></td>
                            <td><?php echo htmlspecialchars($medico['telefone']); ?></td>
                            <td>
                                <button class="action-btn edit-btn"
                                    onclick="editMedico(<?php echo htmlspecialchars(json_encode($medico)); ?>)">
                                    Editar
                                </button>
                                <button class="action-btn delete-btn"
                                    onclick="if(confirm('Tem certeza que deseja excluir o médico ID: <?php echo $medico['id']; ?>?')) { window.location.href='../clinica/processa/processa_medico.php?action=delete&id=<?php echo $medico['id']; ?>'; }">
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
    function editMedico(medico) {
        // Preenche o formulário de edição
        document.getElementById('edit_medico_id').value = medico.id;
        document.getElementById('edit_nome_completo').value = medico.nome_completo;
        document.getElementById('edit_especialidade').value = medico.especialidade;
        document.getElementById('edit_crm').value = medico.crm;
        document.getElementById('edit_telefone').value = medico.telefone;
        document.getElementById('edit_email').value = medico.email;

        // Mostra o formulário de edição e esconde o de adição, se estiver visível
        document.getElementById('add-medico-form-container').style.display = 'none';
        document.getElementById('edit-medico-form-container').style.display = 'block';
        window.scrollTo(0, document.getElementById('edit-medico-form-container').offsetTop); // Rola para o formulário
    }
</script>