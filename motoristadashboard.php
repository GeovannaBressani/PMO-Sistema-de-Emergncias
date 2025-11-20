<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'motorista') {
    header('Location: index.html');
    exit;
}

require_once 'config.php';
$database = new Database();
$db = $database->getConnection();

// Buscar equipe do motorista
$query_equipe = "SELECT e.*, v.placa, v.modelo 
                FROM equipes e 
                JOIN veiculos v ON e.veiculo_id = v.id 
                WHERE e.motorista_id = ? AND e.status != 'manutencao'";
$stmt_equipe = $db->prepare($query_equipe);
$stmt_equipe->execute([$_SESSION['user_id']]);
$equipe = $stmt_equipe->fetch(PDO::FETCH_ASSOC);

// Buscar funcionários disponíveis para esta equipe
$query_funcionarios = "SELECT id, nome FROM usuarios WHERE tipo = 'funcionario' AND ativo = TRUE";
$stmt_funcionarios = $db->prepare($query_funcionarios);
$stmt_funcionarios->execute();
$funcionarios_disponiveis = $stmt_funcionarios->fetchAll(PDO::FETCH_ASSOC);

// Buscar rota atual
$rota_atual = null;
$relatos_rota = [];
if ($equipe) {
    $query_rota = "SELECT * FROM rotas WHERE equipe_id = ? AND status IN ('planejada', 'iniciada') ORDER BY id DESC LIMIT 1";
    $stmt_rota = $db->prepare($query_rota);
    $stmt_rota->execute([$equipe['id']]);
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
    <title>Dashboard Motorista - Sistema de Emergências</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #2c3e50; color: white; padding: 1rem 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .dashboard-card { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; margin: 5px; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #1e3c72; color: white; }
        .btn:disabled { background: #6c757d; cursor: not-allowed; }
        .rota-item { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .rota-item.atual { border-left: 4px solid #28a745; background: #e8f5e8; }
        .rota-item.proximo { border-left: 4px solid #ffc107; background: #fff3cd; }
        .emergency-3 { border-left: 4px solid #e74c3c !important; }
        .emergency-2 { border-left: 4px solid #f39c12 !important; }
        .gps-container { height: 400px; background: #e9ecef; border-radius: 8px; margin: 20px 0; display: flex; align-items: center; justify-content: center; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-disponivel { background: #d4edda; color: #155724; }
        .status-em_rota { background: #fff3cd; color: #856404; }
        .equipe-section { background: #e8f4f8; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .funcionario-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 10px 0; }
        .funcionario-item { background: white; padding: 10px; border-radius: 4px; text-align: center; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Dashboard - Motorista</h1>
                <div>
                    <span>Bem-vindo, <?php echo $_SESSION['user_nome']; ?></span>
                    <span class="status-badge <?php echo $equipe ? 'status-' . $equipe['status'] : 'status-disponivel'; ?>" style="margin-left: 15px;">
                        <?php echo $equipe ? ucfirst(str_replace('_', ' ', $equipe['status'])) : 'Sem equipe'; ?>
                    </span>
                    <button onclick="window.location.href='logout.php'" style="margin-left: 20px; background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Sair</button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (!$equipe): ?>
            <div class="dashboard-card">
                <h2>Sem Equipe Atribuída</h2>
                <p>Você não está atribuído a nenhuma equipe no momento. Aguarde a designação do administrador.</p>
            </div>
        <?php else: ?>
            <!-- SEÇÃO: GERENCIAR EQUIPE (Só aparece quando NÃO tem rota ativa) -->
            <?php if (!$rota_atual || $rota_atual['status'] === 'planejada'): ?>
            <div class="dashboard-card">
                <h2>Minha Equipe - <?php echo $equipe['modelo']; ?> (<?php echo $equipe['placa']; ?>)</h2>
                
                <div class="equipe-section">
                    <h3>👥 Gerenciar Equipe</h3>
                    <p>Selecione os funcionários que estarão com você nesta rota:</p>
                    
                    <form id="formGerenciarEquipe">
                        <div class="funcionario-list">
                            <?php foreach ($funcionarios_disponiveis as $funcionario): ?>
                                <div class="funcionario-item">
                                    <label>
                                        <input type="checkbox" name="funcionarios[]" value="<?php echo $funcionario['id']; ?>" 
                                            <?php 
                                            if ($equipe['funcionario1_id'] == $funcionario['id'] || 
                                                $equipe['funcionario2_id'] == $funcionario['id'] || 
                                                $equipe['funcionario3_id'] == $funcionario['id']) echo 'checked'; 
                                            ?>>
                                        <?php echo $funcionario['nome']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-success" style="margin-top: 15px;">Salvar Equipe</button>
                    </form>
                </div>

                <?php if ($rota_atual): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                        <h3>Rota <?php echo ucfirst($rota_atual['status']); ?></h3>
                        <button class="btn btn-success" onclick="iniciarRota()">Iniciar Rota</button>
                    </div>
                <?php else: ?>
                    <div class="equipe-section">
                        <p>✅ Equipe configurada. Aguarde o administrador atribuir uma rota.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- SEÇÃO: ROTA EM ANDAMENTO (Só aparece quando tem rota iniciada) -->
            <?php if ($rota_atual && $rota_atual['status'] === 'iniciada'): ?>
            <div class="dashboard-card">
                <h2>Rota em Andamento - <?php echo $equipe['modelo']; ?> (<?php echo $equipe['placa']; ?>)</h2>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>📍 Rota Iniciada</h3>
                    <button class="btn btn-danger" onclick="finalizarRota()">Finalizar Rota</button>
                </div>

                <div class="gps-container" id="gpsMap">
                    <div style="text-align: center;">
                        <h3>🚗 Sistema de Navegação GPS</h3>
                        <p>Calculando melhor rota para os pontos de atendimento...</p>
                        <div id="rotaInfo" style="margin-top: 20px;"></div>
                    </div>
                </div>

                <h3>Pontos da Rota (Ordenados por Prioridade):</h3>
                <?php foreach ($relatos_rota as $index => $relato): ?>
                    <div class="rota-item emergency-<?php echo $relato['nivel_emergencia']; ?> 
                        <?php echo $index === 0 ? 'atual' : ''; ?>
                        <?php echo $index === 1 ? 'proximo' : ''; ?>">
                        <h4>
                            <?php if ($index === 0): ?>
                                🟢 ATUAL - 
                            <?php elseif ($index === 1): ?>
                                🟡 PRÓXIMO - 
                            <?php endif; ?>
                            Ponto <?php echo $index + 1; ?> - Nível <?php echo $relato['nivel_emergencia']; ?>
                        </h4>
                        <p><strong>Local:</strong> <?php echo $relato['bairro']; ?>, <?php echo $relato['rua']; ?></p>
                        <p><strong>Serviço:</strong> 
                            <?php 
                            $servicos = [
                                'corte_arvore' => 'Corte de Árvore',
                                'poda' => 'Poda',
                                'recolher_galhos' => 'Recolher Galhos'
                            ];
                            echo $servicos[$relato['tipo_servico']];
                            ?>
                        </p>
                        <p><strong>Descrição:</strong> <?php echo $relato['descricao']; ?></p>
                        
                        <?php if ($index === 0): ?>
                            <button class="btn btn-success" onclick="marcarComoExecutado(<?php echo $relato['id']; ?>)">
                                ✅ Marcar como Executado
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Gerenciar equipe
        document.getElementById('formGerenciarEquipe').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const funcionariosSelecionados = Array.from(document.querySelectorAll('input[name="funcionarios[]"]:checked'))
                .map(input => input.value);
            
            if (funcionariosSelecionados.length === 0) {
                alert('Selecione pelo menos um funcionário para a equipe.');
                return;
            }

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'gerenciar_equipe',
                    equipe_id: <?php echo $equipe ? $equipe['id'] : 'null'; ?>,
                    funcionarios: funcionariosSelecionados
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Equipe atualizada com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        });

        function iniciarRota() {
            if (confirm('Deseja iniciar a rota? O sistema começará a calcular o tempo de atendimento.')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'iniciar_rota',
                        rota_id: <?php echo $rota_atual ? $rota_atual['id'] : 'null'; ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rota iniciada com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        function finalizarRota() {
            if (confirm('Deseja finalizar a rota? Todos os pontos devem estar concluídos.')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'finalizar_rota',
                        rota_id: <?php echo $rota_atual ? $rota_atual['id'] : 'null'; ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rota finalizada com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        function marcarComoExecutado(relatoId) {
            if (confirm('Marcar este serviço como executado?')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'marcar_executado',
                        relato_id: relatoId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Serviço marcado como executado!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        function atualizarGPS() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.getElementById('rotaInfo').innerHTML = `
                        <p><strong>Posição Atual:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                        <p><strong>Próxima parada:</strong> 
                            <?php echo isset($relatos_rota[0]) ? $relatos_rota[0]['bairro'] . ', ' . $relatos_rota[0]['rua'] : 'Nenhuma'; ?>
                        </p>
                        <p><strong>Tempo estimado:</strong> Calculando...</p>
                    `;
                });
            }
        }

        // Atualizar GPS a cada 30 segundos
        setInterval(atualizarGPS, 30000);
        atualizarGPS();
    </script>
</body>
</html>