<?php
session_start();
header("Content-Type: application/json");

if (isset($_SESSION['user_id']) && isset($_SESSION['user_tipo'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nome' => $_SESSION['user_nome'],
            'email' => $_SESSION['user_email'],
            'tipo' => $_SESSION['user_tipo']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Não logado']);
}
?>