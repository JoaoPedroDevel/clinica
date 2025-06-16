<?php
// Conteúdo para a página de Médicos
require_once '../database/conecta_DB.php'; // Inclua sua conexão com o BD aqui

// Exemplo de como você buscaria dados (APENAS UM ESQUELETO!)
// $stmt = $pdo->query("SELECT * FROM medicos ORDER BY nome");
// $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$medicos = [
    ['id' => 1, 'nome' => 'Dr. João Silva', 'especialidade' => 'Clínico Geral', 'email' => 'joao@clinmed.com'],
    ['id' => 2, 'nome' => 'Dra. Ana Costa', 'especialidade' => 'Pediatra', 'email' => 'ana@clinmed.com'],
    ['id' => 3, 'nome' => 'Dr. Pedro Santos', 'especialidade' => 'Dermatologista', 'email' => 'pedro@clinmed.com'],
];
?>
<section class="page-section">
    <h2>Gerenciar Médicos</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="alert('Funcionalidade de Adicionar Médico')">Novo Médico</button>
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
                        <th>E-mail</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicos as $medico): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medico['id']); ?></td>
                            <td><?php echo htmlspecialchars($medico['nome']); ?></td>
                            <td><?php echo htmlspecialchars($medico['especialidade']); ?></td>
                            <td><?php echo htmlspecialchars($medico['email']); ?></td>
                            <td>
                                <button class="action-btn view-btn" onclick="alert('Ver detalhes do médico ID: <?php echo $medico['id']; ?>')">Ver</button>
                                <button class="action-btn edit-btn" onclick="alert('Editar médico ID: <?php echo $medico['id']; ?>')">Editar</button>
                                <button class="action-btn delete-btn" onclick="confirm('Tem certeza que deseja excluir o médico ID: <?php echo $medico['id']; ?>?')">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- Adicionar estilos para tabelas e botões específicos aqui (no painel.css) -->
