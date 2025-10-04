-- Criar base de dados
CREATE DATABASE IF NOT EXISTS petrol_check CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE petrol_check;

-- Tabela de utilizadores
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'gestor', 'tecnico') DEFAULT 'tecnico',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_razao_social VARCHAR(255) NOT NULL,
    nif VARCHAR(20) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100),
    endereco TEXT,
    tipo_cliente ENUM('distribuidor', 'retalhista', 'produtor', 'importador', 'exportador') NOT NULL,
    atividade VARCHAR(255),
    status ENUM('ativo', 'inativo', 'pendente') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de processos
CREATE TABLE processos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    numero_processo VARCHAR(50) UNIQUE,
    tipo_licenca ENUM(
        'producao_grande_escala',
        'producao_media_escala', 
        'producao_pequena_escala',
        'armazenagem_terminal',
        'armazenagem_central',
        'distribuicao',
        'retalho_posto',
        'retalho_central',
        'exportacao'
    ) NOT NULL,
    zona_licenciamento ENUM('A', 'B', 'C') DEFAULT 'B',
    data_inicio DATE NOT NULL,
    data_fim DATE,
    status ENUM('pendente', 'analise', 'aprovado', 'rejeitado', 'concluido') DEFAULT 'pendente',
    responsavel_tecnico VARCHAR(100),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabela de vistorias
CREATE TABLE vistorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    processo_id INT NOT NULL,
    data_vistoria DATETIME NOT NULL,
    tecnico_responsavel VARCHAR(100) NOT NULL,
    tipo_vistoria ENUM('inicial', 'periodica', 'extraordinaria') DEFAULT 'inicial',
    local_vistoria VARCHAR(255),
    resultado ENUM('aprovado', 'reprovado', 'condicionado') DEFAULT NULL,
    observacoes TEXT,
    status ENUM('pendente', 'realizada', 'cancelada') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (processo_id) REFERENCES processos(id) ON DELETE CASCADE
);

-- Tabela de controlo de qualidade
CREATE TABLE controlo_qualidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    processo_id INT NOT NULL,
    produto_tipo ENUM('gasolina_auto', 'gasoleo', 'GPL', 'gasolina_aviacao', 'petroleo_iluminacao', 'oleo_combustivel') NOT NULL,
    data_amostragem DATE NOT NULL,
    laboratorio VARCHAR(100),
    parametros_avaliados JSON,
    resultado ENUM('conforme', 'nao_conforme', 'condicional') NOT NULL,
    certificado_path VARCHAR(500),
    tecnico_responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (processo_id) REFERENCES processos(id) ON DELETE CASCADE
);

-- Tabela de reservas estratégicas
CREATE TABLE reservas_estrategicas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    distribuidora_id INT NOT NULL,
    produto ENUM('GPL', 'gasolina_auto', 'gasolina_aviacao', 'petroleo_iluminacao', 'gasoleo', 'oleo_combustivel') NOT NULL,
    quantidade_minima DECIMAL(10,2) NOT NULL,
    quantidade_atual DECIMAL(10,2) NOT NULL DEFAULT 0,
    local_armazenagem VARCHAR(100),
    regime_aduaneiro ENUM('direitos_suspensos', 'outro') DEFAULT 'outro',
    data_actualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (distribuidora_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabela de documentos
CREATE TABLE documentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    processo_id INT,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(500) NOT NULL,
    tipo_documento VARCHAR(100),
    categoria ENUM('legal', 'tecnico', 'certificado', 'outro') DEFAULT 'outro',
    tamanho_arquivo INT,
    obrigatorio BOOLEAN DEFAULT FALSE,
    upload_por VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (processo_id) REFERENCES processos(id) ON DELETE CASCADE
);

-- Tabela de consultorias
CREATE TABLE consultorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    tipo_servico ENUM('legal', 'tecnica', 'qualidade', 'completa') NOT NULL,
    descricao TEXT,
    status ENUM('pendente', 'atribuida', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'pendente',
    consultor_responsavel VARCHAR(100),
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_conclusao TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabela de alertas de qualidade
CREATE TABLE alertas_qualidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amostra_id INT NOT NULL,
    severidade ENUM('baixa', 'media', 'alta', 'critica') NOT NULL,
    descricao TEXT NOT NULL,
    acoes_corretivas TEXT,
    data_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolvido BOOLEAN DEFAULT FALSE,
    data_resolucao TIMESTAMP NULL,
    FOREIGN KEY (amostra_id) REFERENCES controlo_qualidade(id) ON DELETE CASCADE
);

-- Tabela de atividades do sistema
CREATE TABLE atividades_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    modulo VARCHAR(50),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Inserir dados iniciais

-- Utilizadores padrão (password: password)
INSERT INTO users (username, password, nome_completo, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Sistema', 'admin@petrolcheck.co.mz', 'admin'),
('gestor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gestor Operacional', 'gestor@petrolcheck.co.mz', 'gestor'),
('tecnico', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Técnico Especializado', 'tecnico@petrolcheck.co.mz', 'tecnico');

-- Clientes de exemplo
INSERT INTO clientes (nome_razao_social, nif, telefone, email, endereco, tipo_cliente, atividade) VALUES 
('Posto Total Maputo', '123456789', '+258841234567', 'postototal@email.com', 'Av. 25 de Setembro, Maputo', 'retalhista', 'Comércio de combustíveis'),
('Gasolina Express Lda', '987654321', '+258842345678', 'gasolinaexpress@email.com', 'Matola Rio, Maputo', 'distribuidor', 'Distribuição de produtos petrolíferos'),
('Combustíveis Moçambique SA', '456789123', '+258843456789', 'combustiveis@email.com', 'Beira, Sofala', 'produtor', 'Produção e refinação'),
('GPL Distribuição', '789123456', '+258844567890', 'gpldist@email.com', 'Nampula Cidade', 'distribuidor', 'Distribuição de GPL');

-- Processos de exemplo
INSERT INTO processos (cliente_id, numero_processo, tipo_licenca, zona_licenciamento, data_inicio, status, responsavel_tecnico) VALUES 
(1, 'PROC-2024-001', 'retalho_posto', 'A', '2024-01-15', 'aprovado', 'Eng. João Silva'),
(2, 'PROC-2024-002', 'distribuicao', 'B', '2024-01-16', 'analise', 'Eng. Maria Santos'),
(3, 'PROC-2024-003', 'armazenagem_central', 'C', '2024-01-10', 'pendente', 'Eng. Carlos Lima'),
(4, 'PROC-2024-004', 'distribuicao', 'A', '2024-01-20', 'concluido', 'Eng. Ana Pereira');

-- Vistorias de exemplo
INSERT INTO vistorias (processo_id, data_vistoria, tecnico_responsavel, tipo_vistoria, local_vistoria, resultado, status) VALUES 
(1, '2024-01-20 09:00:00', 'Eng. João Silva', 'inicial', 'Posto Total Maputo', 'aprovado', 'realizada'),
(2, '2024-02-01 14:00:00', 'Eng. Maria Santos', 'periodica', 'Depósito Matola', NULL, 'pendente'),
(3, '2024-01-25 10:30:00', 'Eng. Carlos Lima', 'inicial', 'Refinaria Beira', 'condicionado', 'realizada');

-- Controlo de qualidade
INSERT INTO controlo_qualidade (processo_id, produto_tipo, data_amostragem, laboratorio, resultado, tecnico_responsavel) VALUES 
(1, 'gasolina_auto', '2024-01-18', 'Laboratório Nacional', 'conforme', 'Téc. Pedro Santos'),
(1, 'gasoleo', '2024-01-18', 'Laboratório Nacional', 'conforme', 'Téc. Pedro Santos'),
(2, 'GPL', '2024-01-22', 'LabPetro', 'nao_conforme', 'Téc. Luisa Mendes');

-- Reservas estratégicas
INSERT INTO reservas_estrategicas (distribuidora_id, produto, quantidade_minima, quantidade_atual, local_armazenagem) VALUES 
(2, 'gasolina_auto', 15000, 16000, 'Terminal Maputo'),
(2, 'gasoleo', 18000, 17500, 'Terminal Maputo'),
(2, 'GPL', 300, 350, 'Terminal Maputo'),
(4, 'GPL', 500, 520, 'Depósito Nampula');

-- Documentos
INSERT INTO documentos (processo_id, nome_arquivo, caminho_arquivo, tipo_documento, categoria, upload_por) VALUES 
(1, 'Licença Operacional.pdf', '/docs/licenca_001.pdf', 'Licença', 'legal', 'admin'),
(1, 'Relatório Técnico.docx', '/docs/relatorio_001.docx', 'Relatório', 'tecnico', 'tecnico'),
(2, 'Certificado Qualidade.pdf', '/docs/certificado_002.pdf', 'Certificado', 'certificado', 'gestor');

-- Consultorias
INSERT INTO consultorias (cliente_id, tipo_servico, descricao, status, consultor_responsavel) VALUES 
(3, 'legal', 'Consultoria para licenciamento de nova unidade', 'em_andamento', 'Eng. João Silva'),
(1, 'tecnica', 'Auditoria técnica das instalações', 'pendente', NULL);