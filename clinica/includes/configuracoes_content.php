<section class="page-section">
    <h2>Configurações do Sistema</h2>

    <div class="form-container">
        <h3>Configurações Gerais</h3>
        <form action="#" method="POST">
            <div class="input-group">
                <label for="nome_clinica">Nome da Clínica:</label>
                <input type="text" id="nome_clinica" name="nome_clinica" value="ClinMed">
            </div>
            <div class="input-group">
                <label for="email_contato">E-mail de Contato:</label>
                <input type="email" id="email_contato" name="email_contato" value="contato@clinmed.com">
            </div>
            <div class="input-group">
                <label for="telefone_contato">Telefone de Contato:</label>
                <input type="tel" id="telefone_contato" name="telefone_contato" value="(XX) XXXXX-XXXX">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
        </form>
    </div>

    <h3 style="margin-top: 40px;">Gerenciamento de Usuários e Permissões</h3>
    <div class="info-block">
        <p>Esta seção permitiria gerenciar usuários do sistema (além de pacientes), como criar novos administradores, secretárias, etc., e definir suas permissões de acesso.</p>
        <button class="btn btn-info" onclick="alert('Funcionalidade: Gerenciar Usuários do Sistema')">Gerenciar Usuários</button>
    </div>

    <h3 style="margin-top: 40px;">Backup e Restauração</h3>
    <div class="info-block">
        <p>Funcionalidades para backup do banco de dados e restauração seriam implementadas aqui.</p>
        <button class="btn btn-warning" onclick="alert('Funcionalidade: Realizar Backup')">Fazer Backup Agora</button>
    </div>
</section>