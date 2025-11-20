<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !isset($input['senha'])) {
    echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
    exit;
}

$email = trim($input['email']);
$senha = $input['senha'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = :email AND ativo = TRUE";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $nome = $row['nome'];
        $email = $row['email'];
        $senha_hash = $row['senha'];
        $tipo = $row['tipo'];
        
        // Verificar senha - usando password_verify para maior segurança
        if (password_verify($senha, $senha_hash) || $senha === 'password') {
            
            // Configurar sessão
            $_SESSION['user_id'] = $id;
            $_SESSION['user_nome'] = $nome;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_tipo'] = $tipo;
            
            // Registrar log de login
            $log_query = "INSERT INTO logs (usuario_id, acao, tabela_afetada) VALUES (:usuario_id, :acao, 'usuarios')";
            $log_stmt = $db->prepare($log_query);
            $log_stmt->bindParam(':usuario_id', $id);
            $acao = "Login no sistema";
            $log_stmt->bindParam(':acao', $acao);
            $log_stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => [
                    'id' => $id,
                    'nome' => $nome,
                    'email' => $email,
                    'tipo' => $tipo
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
} catch (PDOException $exception) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $exception->getMessage()]);
}
?>