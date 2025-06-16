-- SQL para criar o banco de dados 'clinica' e suas tabelas

-- 1. Criação do Banco de Dados
-- Remova o comentário da linha abaixo se você quiser que o script crie o banco de dados.
-- Caso contrário, certifique-se de que o banco de dados 'clinica' já existe.
-- CREATE DATABASE IF NOT EXISTS clinica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados a ser usado
USE clinica;

-- 2. Tabela de Usuários (para login no sistema, incluindo administradores)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE, -- Email deve ser único para login
    telefone VARCHAR(22),
    senha VARCHAR(255) NOT NULL, -- Armazenar senhas como hash (password_hash no PHP)
    is_admin BOOLEAN DEFAULT FALSE, -- Campo para indicar se o usuário é administrador
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data e hora do cadastro
);

-- 3. Tabela de Médicos
CREATE TABLE IF NOT EXISTS medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    especialidade VARCHAR(100) NOT NULL,
    crm VARCHAR(20) UNIQUE NOT NULL, -- CRM deve ser único
    telefone VARCHAR(22),
    email VARCHAR(255) UNIQUE, -- Email do médico (pode ser usado para contato)
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabela de Pacientes
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    data_nascimento DATE,
    cpf VARCHAR(14) UNIQUE, -- CPF pode ser usado como identificador único
    telefone VARCHAR(22),
    email VARCHAR(255) UNIQUE,
    endereco TEXT, -- Endereço completo do paciente
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabela de Agendamentos (Consultas e Exames serão vinculados aqui)
CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME NOT NULL,
    tipo_agendamento ENUM('consulta', 'exame') NOT NULL, -- Indica se é consulta ou exame
    status ENUM('agendado', 'confirmado', 'cancelado', 'concluido', 'pendente') DEFAULT 'agendado',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_medico) REFERENCES medicos(id) ON DELETE CASCADE
);

-- 6. Tabela de Consultas (detalhes específicos de cada consulta)
CREATE TABLE IF NOT EXISTS consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_agendamento INT UNIQUE NOT NULL, -- Cada consulta está vinculada a um agendamento único
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    data_consulta DATETIME NOT NULL, -- Data e hora exatas da consulta
    diagnostico TEXT,
    receita TEXT, -- Pode ser um JSON ou texto simples
    observacoes TEXT,
    valor DECIMAL(10, 2), -- Valor da consulta
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_agendamento) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_medico) REFERENCES medicos(id) ON DELETE CASCADE
);

-- 7. Tabela de Exames (detalhes específicos de cada exame)
CREATE TABLE IF NOT EXISTS exames (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_agendamento INT UNIQUE NOT NULL, -- Cada exame está vinculado a um agendamento único
    id_paciente INT NOT NULL,
    id_medico_solicitante INT, -- Médico que solicitou o exame (pode ser nulo)
    tipo_exame VARCHAR(100) NOT NULL,
    data_exame DATE NOT NULL,
    resultado TEXT, -- O resultado do exame (pode ser link para arquivo, texto, etc.)
    observacoes TEXT,
    valor DECIMAL(10, 2), -- Valor do exame
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_agendamento) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_medico_solicitante) REFERENCES medicos(id) ON DELETE SET NULL -- SET NULL se o médico for excluído
);

-- Exemplo de adição de um usuário administrador (senha: admin123)
-- Lembre-se de que em um sistema real, a senha 'admin123' seria hashed com password_hash() no PHP.
-- INSERT INTO usuarios (nome_completo, email, telefone, senha, is_admin) VALUES ('Administrador Geral', 'admin@clinica.com', 'XX XXXXX-XXXX', '$2y$10$2/gBw.K9jP0X0b.Z8q7W7.u4D4.e8.j8V.z8C8y8Q8R8S8T8U8V8W8X8Y8Z', TRUE);
-- Substitua o hash da senha por um gerado por password_hash('sua_senha_aqui', PASSWORD_DEFAULT) no seu PHP!
