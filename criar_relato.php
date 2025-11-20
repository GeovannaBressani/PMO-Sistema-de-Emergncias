<?php
require_once 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = '';

// Obter bairros de Ourinhos
try {
    $sql_bairros = "SELECT id, nome, zona FROM bairros_ourinhos ORDER BY zona, nome";
    $stmt_bairros = $conn->prepare($sql_bairros);
    $stmt_bairros->execute();
    $bairros = $stmt_bairros->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao carregar bairros: " . $e->getMessage());
    $bairros = [];
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bairro_id = $_POST['bairro_id'] ?? '';
    $rua = $_POST['rua'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $referencia = $_POST['referencia'] ?? '';
    $nivel_emergencia = $_POST['nivel_emergencia'] ?? '';
    $tipo_servico = $_POST['tipo_servico'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $localizacao_verificada = isset($_POST['localizacao_verificada']) ? 1 : 0;

    // Validar dados
    if (empty($bairro_id) || empty($rua) || empty($nivel_emergencia) || empty($tipo_servico) || empty($descricao)) {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        try {
            // Iniciar transação
            $conn->beginTransaction();

            // Buscar nome do bairro
            $sql_bairro = "SELECT nome FROM bairros_ourinhos WHERE id = ?";
            $stmt_bairro = $conn->prepare($sql_bairro);
            $stmt_bairro->execute([$bairro_id]);
            $bairro_nome = $stmt_bairro->fetchColumn();

            // Gerar título automático
            $titulo = "Relato - " . $bairro_nome . " - " . $tipo_servico;

            // Inserir relato
            $sql = "INSERT INTO relatos (
                usuario_id, titulo, bairro, rua, numero, referencia, 
                nivel_emergencia, tipo_servico, descricao, cep, 
                latitude, longitude, localizacao_verificada, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $user_id, $titulo, $bairro_nome, $rua, $numero, $referencia,
                $nivel_emergencia, $tipo_servico, $descricao, $cep,
                $latitude, $longitude, $localizacao_verificada
            ]);

            $relato_id = $conn->lastInsertId();

            // Processar upload de fotos
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto_processada = processarUploadFoto($_FILES['foto'], $relato_id);
                
                if ($foto_processada) {
                    $sql_update = "UPDATE relatos SET foto_analisada = 1, confianca_foto = ?, foto_path = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->execute([$foto_processada['confianca'], $foto_processada['caminho_arquivo'], $relato_id]);
                }
            }

            $conn->commit();
            
            // Registrar log
            $log_query = "INSERT INTO logs (usuario_id, acao, tabela_afetada, registro_id) VALUES (?, ?, 'relatos', ?)";
            $log_stmt = $conn->prepare($log_query);
            $acao_log = "Novo relato criado - ID: " . $relato_id;
            $log_stmt->execute([$user_id, $acao_log, $relato_id]);

            $success = "Relato criado com sucesso! Número do protocolo: #" . $relato_id;

            // Limpar formulário
            $_POST = array();

        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Erro ao criar relato: " . $e->getMessage());
            $error = "Erro ao criar relato. Por favor, tente novamente.";
        }
    }
}

/**
 * Processar upload de foto
 */
function processarUploadFoto($foto, $relato_id) {
    global $conn;
    
    // Validar tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($foto['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return false;
    }

    // Validar tamanho do arquivo (5MB)
    if ($foto['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Criar diretório se não existir
    $upload_dir = 'uploads/relatos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Gerar nome único para o arquivo
    $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . $relato_id . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    // Mover arquivo
    if (move_uploaded_file($foto['tmp_name'], $filepath)) {
        // Análise básica da foto
        $analise = analisarFotoBasica($filepath);
        
        // Salvar informações da foto no banco
        $sql_foto = "INSERT INTO relato_fotos (relato_id, caminho_arquivo, confianca_ia, tags_ia) 
                    VALUES (?, ?, ?, ?)";
        $stmt_foto = $conn->prepare($sql_foto);
        $stmt_foto->execute([
            $relato_id, 
            $filepath, 
            $analise['confianca'],
            json_encode($analise['tags'])
        ]);
        
        return [
            'confianca' => $analise['confianca'],
            'caminho_arquivo' => $filepath
        ];
    }
    
    return false;
}

/**
 * Análise básica de foto
 */
function analisarFotoBasica($image_path) {
    // Simulação de análise de IA
    $info = getimagesize($image_path);
    $file_size = filesize($image_path);
    
    $tags = [];
    $confianca = 0.7; // Confiança padrão

    // Análise básica baseada em metadados
    if ($info && $file_size > 50000) {
        $width = $info[0];
        $height = $info[1];
        
        if ($width >= 800 && $height >= 600) {
            $tags[] = ['tag' => 'resolucao_adequada', 'confianca' => 0.8];
            $confianca = 0.8;
        }
        
        if ($file_size > 100000) {
            $tags[] = ['tag' => 'tamanho_adequado', 'confianca' => 0.7];
        }
    }
    
    return [
        'confianca' => $confianca,
        'tags' => $tags
    ];
}

// Se for requisição AJAX para buscar bairros
if (isset($_GET['action']) && $_GET['action'] == 'get_bairros') {
    header('Content-Type: application/json');
    echo json_encode($bairros);
    exit;
}

// Se for requisição AJAX para criar relato
if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
    header('Content-Type: application/json');
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => $success, 'relato_id' => $relato_id]);
    } else {
        echo json_encode(['success' => false, 'message' => $error]);
    }
    exit;
}
?>