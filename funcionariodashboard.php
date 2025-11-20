<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'funcionario') {
    header('Location: index.html');
    exit;
}

require_once 'config.php';
$database = new Database();
$db = $database->getConnection();

// Buscar equipes disponíveis
$query_equipes = "SELECT e.*, u.nome as motorista_nome, v.placa, v.modelo 
                 FROM equipes e 
                 JOIN usuarios u ON e.motorista_id = u.id 
                 JOIN veiculos v ON e.veiculo_id = v.id 
                 WHERE e.status = 'disponivel'";
$stmt_equipes = $db->prepare($query_equipes);
$stmt_equipes->execute();
$equipes_disponiveis = $stmt_equipes->fetchAll(PDO::FETCH_ASSOC);

// Buscar equipe atual do funcionário
$query_minha_equipe = "SELECT e.*, u.nome as motorista_nome, v.placa, v.modelo 
                      FROM equipes e 
                      JOIN usuarios u ON e.motorista_id = u.id 
                      JOIN veiculos v ON e.veiculo_id = v.id 
                      WHERE (e.funcionario1_id = ? OR e.funcionario2_id = ? OR e.funcionario3_id = ?) 
                      AND e.status != 'manutencao'";
$stmt_minha_equipe = $db->prepare($query_minha_equipe);
$stmt_minha_equipe->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$minha_equipe = $stmt_minha_equipe->fetch(PDO::FETCH_ASSOC);

// Buscar rota atual da equipe
$rota_atual = null;
$relatos_rota = [];
if ($minha_equipe) {
    $query_rota = "SELECT * FROM rotas WHERE equipe_id = ? AND status IN ('iniciada') ORDER BY id DESC LIMIT 1";
    $stmt_rota = $db->prepare($query_rota);
    $stmt_rota->execute([$minha_equipe['id']]);
    $rota_atual = $stmt_rota->fetch(PDO::FETCH_ASSOC);
    
    if ($rota_atual) {
        $relatos_ids = json_decode($rota_atual['relatos_ids'], true);
        if (!empty($relatos_ids)) {
            $placeholders = str_repeat('?,', count($relatos_ids) - 1) . '?';
            $query_relatos = "SELECT * FROM relatos WHERE id IN ($placeholders) ORDER BY FIELD(id, " . implode(',', $relatos_ids) . ")";
            $stmt_relatos = $db->prepare($query_relatos);
            $stmt_relatos->execute($relatos_ids);
            $relatos_rota = $stmt_relatos->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Funcionário - Sistema de Emergências</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #34495e; color: white; padding: 1rem 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .dashboard-card { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .equipe-info { background: #e8f4f8; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .rota-item { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .rota-item.atual { border-left: 4px solid #28a745; background: #e8f5e8; }
        .emergency-3 { border-left: 4px solid #e74c3c !important; }
        .emergency-2 { border-left: 4px solid #f39c12 !important; }
        .equipamentos { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 15px 0; }
        .equipamento-item { background: #f8f9fa; padding: 10px; border-radius: 4px; text-align: center; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Dashboard - Funcionário</h1>
                <div>
                    <span>Bem-vindo, <?php echo $_SESSION['user_nome']; ?></span>
                    <button onclick="window.location.href='logout.php'" style="margin-left: 20px; background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Sair</button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (!$minha_equipe): ?>
            <!-- SEÇÃO: SELECIONAR EQUIPE (Só aparece quando não tem equipe) -->
            <div class="dashboard-card">
                <h2>Selecionar Equipe</h2>
                <p>Escolha em qual equipe você vai trabalhar hoje:</p>
                
                <form id="formSelecionarEquipe">
                    <div class="form-group">
                        <label class="form-label">Selecione a Equipe:</label>
                        <select class="form-select" id="equipeSelecionada" required>
                            <option value="">Selecione uma equipe</option>
                            <?php foreach ($equipes_disponiveis as $equipe): ?>
                                <option value="<?php echo $equipe['id']; ?>">
                                    <?php echo $equipe['motorista_nome'] . ' - ' . $equipe['modelo'] . ' (' . $equipe['placa'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Entrar na Equipe</button>
                </form>
            </div>
        <?php else: ?>
            <!-- SEÇÃO: MINHA EQUIPE E ROTA -->
            <div class="dashboard-card">
                <h2>Minha Equipe</h2>
                <div class="equipe-info">
                    <p><strong>Motorista:</strong> <?php echo $minha_equipe['motorista_nome']; ?></p>
                    <p><strong>Veículo:</strong> <?php echo $minha_equipe['modelo']; ?> (<?php echo $minha_equipe['placa']; ?>)</p>
                    <p><strong>Status da Equipe:</strong> <?php echo ucfirst(str_replace('_', ' ', $minha_equipe['status'])); ?></p>
                    <button class="btn btn-warning" onclick="sairDaEquipe()">Sair da Equipe</button>
                </div>

                <?php if ($rota_atual): ?>
                    <h3>📍 Rota em Andamento</h3>
                    <p><strong>Próxima Parada:</strong></p>
                    
                    <?php if (count($relatos_rota) > 0): ?>
                        <div class="rota-item atual emergency-<?php echo $relatos_rota[0]['nivel_emergencia']; ?>">
                            <h4>🟢 PRÓXIMA PARADA - Nível <?php echo $relatos_rota[0]['nivel_emergencia']; ?></h4>
                            <p><strong>Local:</strong> <?php echo $relatos_rota[0]['bairro']; ?>, <?php echo $relatos_rota[0]['rua']; ?></p>
                            <p><strong>Serviço:</strong> 
                                <?php 
                                $servicos = [
                                    'corte_arvore' => 'Corte de Árvore',
                                    'poda' => 'Poda',
                                    'recolher_galhos' => 'Recolher Galhos'
                                ];
                                echo $servicos[$relatos_rota[0]['tipo_servico']];
                                ?>
                            </p>
                            <p><strong>Descrição:</strong> <?php echo $relatos_rota[0]['descricao']; ?></p>
                            
                            <h4 style="margin-top: 15px;">📋 Equipamentos Necessários:</h4>
                            <div class="equipamentos">
                                <?php
                                $equipamentos = [
                                    'corte_arvore' => ['Motorsserra', 'Cunhas', 'Corda', 'EPI Completo'],
                                    'poda' => ['Poda Altura', 'Serra Poda', 'EPI Básico'],
                                    'recolher_galhos' => ['Pá', 'Rastelo', 'Carrinho', 'Luvas']
                                ];
                                $equips = $equipamentos[$relatos_rota[0]['tipo_servico']] ?? ['Equipamentos Básicos'];
                                
                                foreach ($equips as $equip):
                                ?>
                                    <div class="equipamento-item">🔧 <?php echo $equip; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h3>Próximas Paradas:</h3>
                    <?php for ($i = 1; $i < count($relatos_rota); $i++): ?>
                        <div class="rota-item emergency-<?php echo $relatos_rota[$i]['nivel_emergencia']; ?>">
                            <h4>Ponto <?php echo $i + 1; ?> - Nível <?php echo $relatos_rota[$i]['nivel_emergencia']; ?></h4>
                            <p><strong>Local:</strong> <?php echo $relatos_rota[$i]['bairro']; ?>, <?php echo $relatos_rota[$i]['rua']; ?></p>
                            <p><strong>Serviço:</strong> 
                                <?php echo $servicos[$relatos_rota[$i]['tipo_servico']]; ?>
                            </p>
                        </div>
                    <?php endfor; ?>

                <?php else: ?>
                    <div class="equipe-info">
                        <h3>⏳ Aguardando Rota</h3>
                        <p>Você está na equipe do <?php echo $minha_equipe['motorista_nome']; ?>.</p>
                        <p>Aguarde o motorista iniciar a rota ou o administrador atribuir novas tarefas.</p>
                        <p>Verifique seus equipamentos e esteja preparado para o serviço.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <h3>📋 Checklist de Equipamentos</h3>
            <div class="equipamentos">
                <div class="equipamento-item">🪖 Capacete de Segurança</div>
                <div class="equipamento-item">👷 Colete Refletivo</div>
                <div class="equipamento-item">🧤 Luvas de Proteção</div>
                <div class="equipamento-item">👢 Botas de Segurança</div>
                <div class="equipamento-item">🪚 Serra Elétrica</div>
                <div class="equipamento-item">🪓 Machado</div>
                <div class="equipamento-item">🧹 Pá e Rastelo</div>
                <div class="equipamento-item">🪵 Cunhas</div>
            </div>
        </div>
    </div>

    <script>
        // Selecionar equipe
        document.getElementById('formSelecionarEquipe').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const equipeId = document.getElementById('equipeSelecionada').value;
            
            if (!equipeId) {
                alert('Selecione uma equipe.');
                return;
            }

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'entrar_equipe',
                    equipe_id: equipeId,
                    funcionario_id: <?php echo $_SESSION['user_id']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Você entrou na equipe com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        });

        function sairDaEquipe() {
            if (confirm('Deseja sair desta equipe?')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'sair_equipe',
                        funcionario_id: <?php echo $_SESSION['user_id']; ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Você saiu da equipe com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        // Atualizar a página a cada 60 segundos para verificar novas rotas
        setInterval(() => {
            location.reload();
        }, 60000);
    </script>
</body>
</html>