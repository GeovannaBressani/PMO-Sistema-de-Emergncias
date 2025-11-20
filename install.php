<?php
// install.php - Script de instalação/reset do banco de dados

echo "<h2>Instalador do Sistema de Emergências</h2>";

// Configurações do banco
$host = "localhost";
$dbname = "sistema_emergencias";
$username = "root";
$password = "";

try {
    // Conectar sem selecionar o banco primeiro
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    echo "✅ Banco de dados conectado/criado com sucesso!<br>";

    // Drop das tabelas se existirem (em ordem correta por dependências)
    $tables = ['logs', 'rotas', 'relatos', 'equipes', 'veiculos', 'usuarios'];
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $table");
            echo "✅ Tabela $table removida se existia<br>";
        } catch (Exception $e) {
            echo "⚠️ Erro ao remover $table: " . $e->getMessage() . "<br>";
        }
    }

    // Criar tabelas na ordem correta
    echo "<h3>Criando tabelas...</h3>";

    // Tabela de usuários
    $pdo->exec("CREATE TABLE usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        tipo ENUM('admin', 'motorista', 'funcionario') NOT NULL,
        ativo BOOLEAN DEFAULT TRUE,
        telefone VARCHAR(20),
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabela 'usuarios' criada<br>";

    // Tabela de veículos
    $pdo->exec("CREATE TABLE veiculos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        placa VARCHAR(10) UNIQUE NOT NULL,
        modelo VARCHAR(50) NOT NULL,
        capacidade VARCHAR(50),
        ativo BOOLEAN DEFAULT TRUE,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabela 'veiculos' criada<br>";

    // Tabela de equipes
    $pdo->exec("CREATE TABLE equipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        motorista_id INT NOT NULL,
        veiculo_id INT NOT NULL,
        funcionario1_id INT,
        funcionario2_id INT,
        funcionario3_id INT,
        status ENUM('disponivel', 'em_rota', 'manutencao') DEFAULT 'disponivel',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (motorista_id) REFERENCES usuarios(id),
        FOREIGN KEY (veiculo_id) REFERENCES veiculos(id),
        FOREIGN KEY (funcionario1_id) REFERENCES usuarios(id),
        FOREIGN KEY (funcionario2_id) REFERENCES usuarios(id),
        FOREIGN KEY (funcionario3_id) REFERENCES usuarios(id)
    )");
    echo "✅ Tabela 'equipes' criada<br>";

    // Tabela de relatos
    $pdo->exec("CREATE TABLE relatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bairro VARCHAR(100) NOT NULL,
        rua VARCHAR(200) NOT NULL,
        numero VARCHAR(20),
        referencia TEXT,
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        nivel_emergencia INT NOT NULL,
        tipo_servico ENUM('corte_arvore', 'poda', 'recolher_galhos') NOT NULL,
        descricao TEXT NOT NULL,
        foto_path VARCHAR(500),
        status ENUM('pendente', 'avaliacao', 'aprovado', 'em_rota', 'em_execucao', 'concluido', 'cancelado') DEFAULT 'pendente',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        usuario_avaliacao_id INT,
        observacao_avaliacao TEXT,
        equipe_id INT,
        FOREIGN KEY (usuario_avaliacao_id) REFERENCES usuarios(id),
        FOREIGN KEY (equipe_id) REFERENCES equipes(id)
    )");
    echo "✅ Tabela 'relatos' criada<br>";

    // Tabela de rotas
    $pdo->exec("CREATE TABLE rotas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        equipe_id INT NOT NULL,
        relatos_ids TEXT NOT NULL,
        status ENUM('planejada', 'iniciada', 'concluida', 'cancelada') DEFAULT 'planejada',
        data_inicio TIMESTAMP NULL,
        data_fim TIMESTAMP NULL,
        distancia_total DECIMAL(8,2),
        tempo_estimado INT,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipe_id) REFERENCES equipes(id)
    )");
    echo "✅ Tabela 'rotas' criada<br>";

    // Tabela de logs
    $pdo->exec("CREATE TABLE logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        acao VARCHAR(200) NOT NULL,
        tabela_afetada VARCHAR(50),
        registro_id INT,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )");
    echo "✅ Tabela 'logs' criada<br>";

    // Inserir dados iniciais
    echo "<h3>Inserindo dados iniciais...</h3>";

    // Usuários (senha: 'password' criptografada)
    $senha_hash = password_hash('password', PASSWORD_DEFAULT);
    
    $pdo->exec("INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES 
        ('Administrador Principal', 'admin@ourinhos.sp.gov.br', '$senha_hash', 'admin', '(14) 99999-9999'),
        ('João Silva - Motorista', 'motorista@ourinhos.sp.gov.br', '$senha_hash', 'motorista', '(14) 98888-8888'),
        ('Pedro Santos - Motorista', 'motorista2@ourinhos.sp.gov.br', '$senha_hash', 'motorista', '(14) 97777-7777'),
        ('Maria Oliveira - Funcionária', 'funcionario@ourinhos.sp.gov.br', '$senha_hash', 'funcionario', '(14) 96666-6666'),
        ('Carlos Souza - Funcionário', 'funcionario2@ourinhos.sp.gov.br', '$senha_hash', 'funcionario', '(14) 95555-5555')");
    echo "✅ Usuários iniciais inseridos<br>";

    // Veículos
    $pdo->exec("INSERT INTO veiculos (placa, modelo, capacidade) VALUES 
        ('ABC-1234', 'Ford Cargo 814', 'Caminhão Poda'),
        ('DEF-5678', 'Volkswagen Delivery', 'Caminhão Coleta'),
        ('GHI-9012', 'Mercedes-Benz Accelo', 'Caminhão Misto')");
    echo "✅ Veículos iniciais inseridos<br>";

    // Equipes
    $pdo->exec("INSERT INTO equipes (motorista_id, veiculo_id, funcionario1_id, funcionario2_id, status) VALUES 
        (2, 1, 4, 5, 'disponivel'),
        (3, 2, 4, NULL, 'disponivel')");
    echo "✅ Equipes iniciais inseridas<br>";

    // Alguns relatos de exemplo
    $pdo->exec("INSERT INTO relatos (bairro, rua, numero, nivel_emergencia, tipo_servico, descricao, status) VALUES 
        ('Centro', 'Rua Prudente de Moraes', '123', 3, 'corte_arvore', 'Árvore caída bloqueando via pública', 'pendente'),
        ('Jardim Europa', 'Avenida Europa', '456', 2, 'poda', 'Galhos ameaçando cair sobre fiação', 'pendente'),
        ('Vila Industrial', 'Rua das Indústrias', '789', 1, 'recolher_galhos', 'Galhos espalhados após tempestade', 'pendente')");
    echo "✅ Relatos de exemplo inseridos<br>";

    echo "<h3 style='color: green;'>🎉 Instalação concluída com sucesso!</h3>";
    echo "<p><strong>Credenciais de teste:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@ourinhos.sp.gov.br / password</li>";
    echo "<li><strong>Motorista 1:</strong> motorista@ourinhos.sp.gov.br / password</li>";
    echo "<li><strong>Motorista 2:</strong> motorista2@ourinhos.sp.gov.br / password</li>";
    echo "<li><strong>Funcionário 1:</strong> funcionario@ourinhos.sp.gov.br / password</li>";
    echo "<li><strong>Funcionário 2:</strong> funcionario2@ourinhos.sp.gov.br / password</li>";
    echo "</ul>";
    echo "<p><a href='index.html'>Acessar o Sistema</a></p>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Erro durante a instalação:</h3>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifique se:</p>";
    echo "<ul>";
    echo "<li>O MySQL está rodando</li>";
    echo "<li>As credenciais do banco estão corretas no arquivo install.php</li>";
    echo "<li>Você tem permissão para criar bancos de dados</li>";
    echo "</ul>";
}
?>