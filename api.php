<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'config.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

switch ($action) {
    case 'get_relato':
        $relato_id = $_GET['id'];
        $query = "SELECT * FROM relatos WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$relato_id]);
        $relato = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $relato]);
        break;
        
    case 'avaliar_relato':
        $relato_id = $input['relato_id'];
        $decisao = $input['decisao'];
        $observacao = $input['observacao'];
        $equipe_id = $input['equipe_id'] ?? null;
        
        $novo_status = '';
        switch ($decisao) {
            case 'aprovado': $novo_status = 'em_rota'; break;
            case 'encaminhar': $novo_status = 'avaliacao'; break;
            case 'cancelado': $novo_status = 'cancelado'; break;
        }
        
        $query = "UPDATE relatos SET status = ?, usuario_avaliacao_id = ?, observacao_avaliacao = ?, equipe_id = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$novo_status, $_SESSION['user_id'], $observacao, $equipe_id, $relato_id]);
        
        // Se aprovado, criar rota automaticamente
        if ($success && $decisao === 'aprovado' && $equipe_id) {
            criarRotaAutomatica($equipe_id, $relato_id, $db);
        }
        
        echo json_encode(['success' => $success, 'message' => $success ? 'Avaliação salva' : 'Erro ao salvar']);
        break;
        
    case 'iniciar_rota':
        $rota_id = $input['rota_id'];
        $query = "UPDATE rotas SET status = 'iniciada', data_inicio = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$rota_id]);
        
        // Atualizar status da equipe
        if ($success) {
            $query_equipe = "UPDATE equipes SET status = 'em_rota' WHERE id = (SELECT equipe_id FROM rotas WHERE id = ?)";
            $stmt_equipe = $db->prepare($query_equipe);
            $stmt_equipe->execute([$rota_id]);
        }
        
        echo json_encode(['success' => $success]);
        break;
        
    case 'finalizar_rota':
        $rota_id = $input['rota_id'];
        $query = "UPDATE rotas SET status = 'concluida', data_fim = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$rota_id]);
        
        // Atualizar status da equipe
        if ($success) {
            $query_equipe = "UPDATE equipes SET status = 'disponivel' WHERE id = (SELECT equipe_id FROM rotas WHERE id = ?)";
            $stmt_equipe = $db->prepare($query_equipe);
            $stmt_equipe->execute([$rota_id]);
        }
        
        echo json_encode(['success' => $success]);
        break;
        
    case 'marcar_executado':
        $relato_id = $input['relato_id'];
        $query = "UPDATE relatos SET status = 'concluido' WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$relato_id]);
        
        echo json_encode(['success' => $success]);
        break;
        
    case 'criar_usuario':
        $nome = $input['nome'];
        $email = $input['email'];
        $senha = password_hash($input['senha'], PASSWORD_DEFAULT);
        $tipo = $input['tipo'];
        $telefone = $input['telefone'] ?? null;
        
        try {
            $query = "INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $success = $stmt->execute([$nome, $email, $senha, $tipo, $telefone]);
            
            echo json_encode(['success' => $success, 'message' => $success ? 'Usuário criado' : 'Erro ao criar usuário']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
        break;
        
    case 'toggle_usuario':
        $usuario_id = $input['usuario_id'];
        $ativo = $input['ativo'];
        
        $query = "UPDATE usuarios SET ativo = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$ativo, $usuario_id]);
        
        echo json_encode(['success' => $success]);
        break;
        
    case 'gerenciar_equipe':
        $equipe_id = $input['equipe_id'];
        $funcionarios = $input['funcionarios'];
        
        // Limpar funcionários atuais
        $query = "UPDATE equipes SET funcionario1_id = NULL, funcionario2_id = NULL, funcionario3_id = NULL WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$equipe_id]);
        
        // Adicionar novos funcionários
        for ($i = 0; $i < min(3, count($funcionarios)); $i++) {
            $campo = 'funcionario' . ($i + 1) . '_id';
            $query = "UPDATE equipes SET $campo = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$funcionarios[$i], $equipe_id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Equipe atualizada']);
        break;
        
    case 'entrar_equipe':
        $equipe_id = $input['equipe_id'];
        $funcionario_id = $input['funcionario_id'];
        
        // Verificar se já está em alguma equipe e remover
        $query_remover = "UPDATE equipes SET 
                         funcionario1_id = NULLIF(funcionario1_id, ?),
                         funcionario2_id = NULLIF(funcionario2_id, ?), 
                         funcionario3_id = NULLIF(funcionario3_id, ?) 
                         WHERE funcionario1_id = ? OR funcionario2_id = ? OR funcionario3_id = ?";
        $stmt_remover = $db->prepare($query_remover);
        $stmt_remover->execute([$funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id]);
        
        // Adicionar à nova equipe (encontrar slot vazio)
        $query_slots = "SELECT funcionario1_id, funcionario2_id, funcionario3_id FROM equipes WHERE id = ?";
        $stmt_slots = $db->prepare($query_slots);
        $stmt_slots->execute([$equipe_id]);
        $slots = $stmt_slots->fetch(PDO::FETCH_ASSOC);
        
        $campo_vazio = null;
        foreach (['funcionario1_id', 'funcionario2_id', 'funcionario3_id'] as $campo) {
            if (!$slots[$campo]) {
                $campo_vazio = $campo;
                break;
            }
        }
        
        if ($campo_vazio) {
            $query_adicionar = "UPDATE equipes SET $campo_vazio = ? WHERE id = ?";
            $stmt_adicionar = $db->prepare($query_adicionar);
            $success = $stmt_adicionar->execute([$funcionario_id, $equipe_id]);
            echo json_encode(['success' => $success, 'message' => $success ? 'Entrou na equipe' : 'Erro']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Equipe já está cheia']);
        }
        break;
        
    case 'sair_equipe':
        $funcionario_id = $input['funcionario_id'];
        
        $query = "UPDATE equipes SET 
                 funcionario1_id = NULLIF(funcionario1_id, ?),
                 funcionario2_id = NULLIF(funcionario2_id, ?), 
                 funcionario3_id = NULLIF(funcionario3_id, ?) 
                 WHERE funcionario1_id = ? OR funcionario2_id = ? OR funcionario3_id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id, $funcionario_id]);
        
        echo json_encode(['success' => $success, 'message' => $success ? 'Saiu da equipe' : 'Erro']);
        break;
        
    case 'encaminhar_superior':
        $relato_id = $input['relato_id'];
        $query = "UPDATE relatos SET status = 'avaliacao' WHERE id = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$relato_id]);
        
        echo json_encode(['success' => $success, 'message' => $success ? 'Relato encaminhado' : 'Erro']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
        break;
}

function criarRotaAutomatica($equipe_id, $relato_id, $db) {
    // Buscar relatos pendentes para esta equipe, ordenados por prioridade
    $query = "SELECT id FROM relatos WHERE equipe_id = ? AND status = 'em_rota' ORDER BY nivel_emergencia DESC, data_criacao ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([$equipe_id]);
    $relatos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($relatos)) {
        $relatos_json = json_encode($relatos);
        
        // Verificar se já existe rota ativa
        $query_rota = "SELECT id FROM rotas WHERE equipe_id = ? AND status IN ('planejada', 'iniciada')";
        $stmt_rota = $db->prepare($query_rota);
        $stmt_rota->execute([$equipe_id]);
        $rota_existente = $stmt_rota->fetch();
        
        if ($rota_existente) {
            // Atualizar rota existente
            $query_update = "UPDATE rotas SET relatos_ids = ? WHERE id = ?";
            $stmt_update = $db->prepare($query_update);
            $stmt_update->execute([$relatos_json, $rota_existente['id']]);
        } else {
            // Criar nova rota
            $query_insert = "INSERT INTO rotas (equipe_id, relatos_ids, status) VALUES (?, ?, 'planejada')";
            $stmt_insert = $db->prepare($query_insert);
            $stmt_insert->execute([$equipe_id, $relatos_json]);
        }
    }
}
?>