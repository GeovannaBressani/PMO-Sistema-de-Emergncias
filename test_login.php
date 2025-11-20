<?php
session_start();
echo "<h1>Teste de Login e Sessão</h1>";

echo "<h3>Variáveis de Sessão:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Teste de Banco:</h3>";
require_once 'config.php';
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Conexão com banco OK<br>";
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Usuários no banco: " . $result['total'] . "<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
}

echo "<h3>Teste de Login:</h3>";
echo "<form action='test_login.php' method='post'>";
echo "Email: <input type='email' name='email' value='admin@ourinhos.sp.gov.br'><br>";
echo "Senha: <input type='password' name='senha' value='password'><br>";
echo "<input type='submit' value='Testar Login'>";
echo "</form>";

if ($_POST) {
    require_once 'login.php';
}
?>