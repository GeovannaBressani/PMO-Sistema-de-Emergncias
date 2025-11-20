<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case 'GET':
        // Listar relatos (para dashboards)
        $query = "SELECT r.*, u.nome as usuario_nome, m.nome as motorista_nome 
                  FROM relatos r 
                  LEFT JOIN usuarios u ON r.usuario_id = u.id 
                  LEFT JOIN usuarios m ON r.motorista_id = m.id 
                  ORDER BY r.data_criacao DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $relatos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $relatos[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $relatos]);
        break;
        
    case 'POST':
        // Criar novo relato
        $input = json_decode(file_get_contents('php://input'), true);
        
        $required_fields = ['bairro', 'rua', 'nivel_emergencia', 'tipo_servico', 'descricao'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "Campo $field é obrigatório"]);
                exit;
            }
        }
        
        try {
            $query = "INSERT INTO relatos 
                     (bairro, rua, referencia, nivel_emergencia, tipo_servico, descricao, foto_path, status) 
                     VALUES 
                     (:bairro, :rua, :referencia, :nivel_emergencia, :tipo_servico, :descricao, :foto_path, 'pendente')";
            
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':bairro', $input['bairro']);
            $stmt->bindParam(':rua', $input['rua']);
            $stmt->bindParam(':referencia', $input['referencia']);
            $stmt->bindParam(':nivel_emergencia', $input['nivel_emergencia']);
            $stmt->bindParam(':tipo_servico', $input['tipo_servico']);
            $stmt->bindParam(':descricao', $input['descricao']);
            $stmt->bindParam(':foto_path', $input['foto_path']);
            
            if ($stmt->execute()) {
                $relato_id = $db->lastInsertId();
                
                // Registrar log
                $log_query = "INSERT INTO logs (acao, tabela_afetada, registro_id) VALUES (:acao, 'relatos', :registro_id)";
                $log_stmt = $db->prepare($log_query);
                $acao = "Novo relato criado - " . $input['bairro'] . ", " . $input['rua'];
                $log_stmt->bindParam(':acao', $acao);
                $log_stmt->bindParam(':registro_id', $relato_id);
                $log_stmt->execute();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Relato criado com sucesso',
                    'relato_id' => $relato_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao criar relato']);
            }
            
        } catch (PDOException $exception) {
            echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $exception->getMessage()]);
        }
        break;
        
    case 'PUT':
        // Atualizar relato (status, atribuir motorista, etc)
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id']) || !isset($input['acao'])) {
            echo json_encode(['success' => false, 'message' => 'ID e ação são obrigatórios']);
            exit;
        }
        
        $relato_id = $input['id'];
        $acao = $input['acao'];
        $usuario_id = isset($input['usuario_id']) ? $input['usuario_id'] : null;
        
        try {
            switch ($acao) {
                case 'atribuir_motorista':
                    if (!isset($input['motorista_id'])) {
                        echo json_encode(['success' => false, 'message' => 'Motorista ID é obrigatório']);
                        exit;
                    }
                    $query = "UPDATE relatos SET motorista_id = :motorista_id, status = 'em_andamento' WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':motorista_id', $input['motorista_id']);
                    $stmt->bindParam(':id', $relato_id);
                    break;
                    
                case 'concluir':
                    $query = "UPDATE relatos SET status = 'concluido' WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $relato_id);
                    break;
                    
                case 'cancelar':
                    $query = "UPDATE relatos SET status = 'cancelado' WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $relato_id);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
                    exit;
            }
            
            if ($stmt->execute()) {
                // Registrar log
                $log_query = "INSERT INTO logs (usuario_id, acao, tabela_afetada, registro_id) VALUES (:usuario_id, :acao, 'relatos', :registro_id)";
                $log_stmt = $db->prepare($log_query);
                $log_acao = "Relato atualizado - $acao";
                $log_stmt->bindParam(':usuario_id', $usuario_id);
                $log_stmt->bindParam(':acao', $log_acao);
                $log_stmt->bindParam(':registro_id', $relato_id);
                $log_stmt->execute();
                
                echo json_encode(['success' => true, 'message' => 'Relato atualizado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar relato']);
            }
            
        } catch (PDOException $exception) {
            echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $exception->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        break;
}
?>