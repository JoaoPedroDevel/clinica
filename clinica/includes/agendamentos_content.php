<?php
// Conteúdo para a página de Agendamentos
require_once '../database/conecta_DB.php';

// Exemplo de como você buscaria dados (APENAS UM ESQUELETO!)
// $stmt = $pdo->query("SELECT * FROM agendamentos ORDER BY data_agendamento DESC");
// $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$agendamentos = [
    ['id' => 1, 'paciente' => 'Maria Silva', 'medico' => 'Dr. João', 'data' => '2024-06-10', 'hora' => '10:00', 'status' => 'Confirmado'],
    ['id' => 2, 'paciente' => 'Carlos Souza', 'medico' => 'Dra. Ana', 'data' => '2024-06-10', 'hora' => '11:30', 'status' => 'Pendente'],
    ['id' => 3, 'paciente' => 'Fernanda Lima', 'medico' => 'Dr. João', 'data' => '2024-06-11', 'hora' => '09:00', 'status' => 'Confirmado'],
];

?>
<section class="page-section">
    <h2>Gerenciar Agendamentos</h2>

    <div class="actions-bar">
        <button class="btn btn-primary" onclick="alert('Funcionalidade de Adicionar Agendamento')">Novo Agendamento</button>
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
                            <td><?php echo htmlspecialchars($agendamento['data']); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['hora']); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($agendamento['status']); ?>"><?php echo htmlspecialchars($agendamento['status']); ?></span></td>
                            <td>
                                <button class="action-btn view-btn" onclick="alert('Ver detalhes do agendamento ID: <?php echo $agendamento['id']; ?>')">Ver</button>
                                <button class="action-btn edit-btn" onclick="alert('Editar agendamento ID: <?php echo $agendamento['id']; ?>')">Editar</button>
                                <button class="action-btn delete-btn" onclick="confirm('Tem certeza que deseja excluir o agendamento ID: <?php echo $agendamento['id']; ?>?')">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- Adicionar estilos para tabelas e botões específicos aqui (no painel.css) -->
