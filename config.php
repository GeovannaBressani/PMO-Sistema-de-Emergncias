<?php
class Database {
    private $host = "localhost";
    private $db_name = "sistema_emergencias";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            // Em produção, você pode retornar null ou tratar de outra forma
            return null;
        }
        return $this->conn;
    }
}

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Criar instância global do banco de dados
$database = new Database();
$conn = $database->getConnection();

// Verificar se a conexão foi estabelecida
if (!$conn) {
    die("Erro crítico: Não foi possível conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}
?>