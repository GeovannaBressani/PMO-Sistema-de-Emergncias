<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if (isset($_FILES['foto'])) {
    $uploadDir = 'uploads/';
    
    // Criar diretório se não existir
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($_FILES['foto']['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Upload realizado com sucesso',
            'file_path' => $targetPath
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao fazer upload do arquivo'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Nenhum arquivo recebido'
    ]);
}
?>