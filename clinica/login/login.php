<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica - Login e Cadastro</title>
    <link rel="stylesheet" href="../assets/style/login.css">
</head>
<body>

    <div class="container">
        <!-- Formulário de Login -->
        <div class="form-card" id="login-form">
            <h2>Login</h2>
            <form action="processa_login.php" method="POST">
                <div class="input-group">
                    <label for="login-email">E-mail</label>
                    <input type="email" id="login-email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="input-group">
                    <label for="login-password">Senha</label>
                    <input type="password" id="login-password" name="senha" placeholder="********" required>
                </div>
                <button type="submit" class="btn">Entrar</button>
            </form>
            <p class="form-footer">Ainda não tem conta? <a href="#" id="show-register">Cadastre-se aqui</a></p>
        </div>

        <!-- Formulário de Cadastro -->
        <div class="form-card" id="register-form" style="display: none;">
            <h2>Cadastro</h2>
            <form action="processa_cadastro.php" method="POST">
                <div class="input-group">
                    <label for="register-full-name">Nome Completo</label>
                    <input type="text" id="register-full-name" name="nome_completo" placeholder="Seu nome completo" required>
                </div>
                <div class="input-group">
                    <label for="register-email">E-mail</label>
                    <input type="email" id="register-email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="input-group">
                    <label for="register-phone">Telefone</label>
                    <input type="tel" id="register-phone" name="telefone" placeholder="(XX) XXXXX-XXXX">
                </div>
                <div class="input-group">
                    <label for="register-password">Senha</label>
                    <input type="password" id="register-password" name="senha" placeholder="Crie uma senha forte" required>
                </div>
                <div class="input-group">
                    <label for="register-confirm-password">Confirmar Senha</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Confirme sua senha" required>
                </div>
                <button type="submit" class="btn">Cadastrar</button>
            </form>
            <p class="form-footer">Já tem conta? <a href="#" id="show-login">Faça login</a></p>
        </div>
    </div>

    <script>
        // Script para alternar entre os formulários
        document.getElementById('show-register').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });

        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });
    </script>

</body>
</html>
