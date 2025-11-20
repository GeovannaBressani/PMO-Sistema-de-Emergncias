<?php
session_start();
// Verificação mais robusta
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo'])) {
    header('Location: index.html');
    exit;
}

// Verificar se o usuário é admin
if ($_SESSION['user_tipo'] !== 'admin') {
    header('Location: index.html');
    exit;
}

require_once 'config.php';
$database = new Database();
$db = $database->getConnection();

// Buscar estatísticas
$query = "SELECT 
    COUNT(*) as total_relatos,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'avaliacao' THEN 1 ELSE 0 END) as avaliacao,
    SUM(CASE WHEN status = 'em_rota' THEN 1 ELSE 0 END) as em_rota,
    SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos
    FROM relatos";
$stmt = $db->prepare($query);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar relatos pendentes para avaliação
$query_relatos = "SELECT r.* FROM relatos r WHERE r.status = 'pendente' ORDER BY r.nivel_emergencia DESC, r.data_criacao ASC";
$stmt_relatos = $db->prepare($query_relatos);
$stmt_relatos->execute();
$relatos_pendentes = $stmt_relatos->fetchAll(PDO::FETCH_ASSOC);

// Buscar equipes
$query_equipes = "SELECT e.*, u.nome as motorista_nome, v.placa, v.modelo 
                 FROM equipes e 
                 JOIN usuarios u ON e.motorista_id = u.id 
                 JOIN veiculos v ON e.veiculo_id = v.id";
$stmt_equipes = $db->prepare($query_equipes);
$stmt_equipes->execute();
$equipes = $stmt_equipes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistema de Emergências</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        header { background: #1e3c72; color: white; padding: 1rem 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .nav-tabs { display: flex; background: white; border-radius: 8px 8px 0 0; overflow: hidden; }
        .nav-tab { padding: 15px 20px; cursor: pointer; border-bottom: 3px solid transparent; }
        .nav-tab.active { border-bottom: 3px solid #1e3c72; background: #f8f9fa; font-weight: bold; }
        .tab-content { display: none; background: white; padding: 20px; border-radius: 0 0 8px 8px; }
        .tab-content.active { display: block; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; margin: 10px 0; }
        .table-container { background: white; border-radius: 8px; padding: 20px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 2px; }
        .btn-primary { background: #1e3c72; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .emergency-3 { border-left: 4px solid #e74c3c; background: #ffeaea; }
        .emergency-2 { border-left: 4px solid #f39c12; background: #fff3cd; }
        .emergency-1 { border-left: 4px solid #f1c40f; }
        .emergency-0 { border-left: 4px solid #27ae60; background: #e8f5e8; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .photo-preview { max-width: 100%; max-height: 300px; margin: 10px 0; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-em_rota { background: #d1ecf1; color: #0c5460; }
        .status-concluido { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Dashboard - Administrador</h1>
                <div>
                    <span>Bem-vindo, <?php echo $_SESSION['user_nome']; ?></span>
                    <button onclick="window.location.href='logout.php'" style="margin-left: 20px; background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Sair</button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <h3>Relatos Pendentes</h3>
                <div class="stat-number"><?php echo $stats['pendentes']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Em Avaliação</h3>
                <div class="stat-number"><?php echo $stats['avaliacao']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Em Rota</h3>
                <div class="stat-number"><?php echo $stats['em_rota']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Concluídos</h3>
                <div class="stat-number"><?php echo $stats['concluidos']; ?></div>
            </div>
        </div>

        <div class="nav-tabs">
            <div class="nav-tab active" onclick="showTab('avaliacao')">Avaliar Relatos</div>
            <div class="nav-tab" onclick="showTab('equipes')">Gerenciar Equipes</div>
            <div class="nav-tab" onclick="showTab('usuarios')">Gerenciar Usuários</div>
            <div class="nav-tab" onclick="showTab('relatorios')">Relatórios</div>
        </div>

        <!-- ABA: AVALIAR RELATOS -->
        <div id="avaliacao" class="tab-content active">
            <h2>Relatos Pendentes de Avaliação</h2>
            <div class="table-container">
                <?php if (count($relatos_pendentes) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Localização</th>
                                <th>Emergência</th>
                                <th>Serviço</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatos_pendentes as $relato): ?>
                                <tr class="emergency-<?php echo $relato['nivel_emergencia']; ?>">
                                    <td><?php echo $relato['id']; ?></td>
                                    <td>
                                        <strong><?php echo $relato['bairro']; ?></strong><br>
                                        <?php echo $relato['rua']; ?>
                                        <?php if ($relato['numero']): ?>, <?php echo $relato['numero']; ?><?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight: bold; font-size: 1.2em;">
                                            Nível <?php echo $relato['nivel_emergencia']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $servicos = [
                                            'corte_arvore' => 'Corte Árvore',
                                            'poda' => 'Poda',
                                            'recolher_galhos' => 'Recolher Galhos'
                                        ];
                                        echo $servicos[$relato['tipo_servico']];
                                        ?>
                                    </td>
                                    <td><?php echo substr($relato['descricao'], 0, 50) . '...'; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($relato['data_criacao'])); ?></td>
                                    <td>
                                        <button class="btn btn-primary" onclick="verDetalhesRelato(<?php echo $relato['id']; ?>)">Avaliar</button>
                                        <button class="btn btn-warning" onclick="encaminharSuperior(<?php echo $relato['id']; ?>)">Encaminhar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum relato pendente para avaliação.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ABA: GERENCIAR EQUIPES -->
        <div id="equipes" class="tab-content">
            <h2>Gerenciar Equipes</h2>
            <button class="btn btn-success" onclick="abrirModalNovaEquipe()" style="margin-bottom: 20px;">Nova Equipe</button>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Motorista</th>
                            <th>Veículo</th>
                            <th>Funcionários</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipes as $equipe): ?>
                            <tr>
                                <td><?php echo $equipe['id']; ?></td>
                                <td><?php echo $equipe['motorista_nome']; ?></td>
                                <td><?php echo $equipe['modelo'] . ' - ' . $equipe['placa']; ?></td>
                                <td>
                                    <?php
                                    $funcionarios = [];
                                    if ($equipe['funcionario1_id']) {
                                        $query_func = "SELECT nome FROM usuarios WHERE id = ?";
                                        $stmt_func = $db->prepare($query_func);
                                        $stmt_func->execute([$equipe['funcionario1_id']]);
                                        $func1 = $stmt_func->fetch(PDO::FETCH_COLUMN);
                                        $funcionarios[] = $func1;
                                    }
                                    if ($equipe['funcionario2_id']) {
                                        $query_func = "SELECT nome FROM usuarios WHERE id = ?";
                                        $stmt_func = $db->prepare($query_func);
                                        $stmt_func->execute([$equipe['funcionario2_id']]);
                                        $func2 = $stmt_func->fetch(PDO::FETCH_COLUMN);
                                        $funcionarios[] = $func2;
                                    }
                                    if ($equipe['funcionario3_id']) {
                                        $query_func = "SELECT nome FROM usuarios WHERE id = ?";
                                        $stmt_func = $db->prepare($query_func);
                                        $stmt_func->execute([$equipe['funcionario3_id']]);
                                        $func3 = $stmt_func->fetch(PDO::FETCH_COLUMN);
                                        $funcionarios[] = $func3;
                                    }
                                    echo implode(', ', $funcionarios);
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $equipe['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $equipe['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-warning" onclick="editarEquipe(<?php echo $equipe['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger" onclick="desativarEquipe(<?php echo $equipe['id']; ?>)">Desativar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ABA: GERENCIAR USUÁRIOS -->
        <div id="usuarios" class="tab-content">
            <h2>Gerenciar Usuários</h2>
            <button class="btn btn-success" onclick="abrirModalNovoUsuario()" style="margin-bottom: 20px;">Novo Usuário</button>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_usuarios = "SELECT * FROM usuarios ORDER BY tipo, nome";
                        $stmt_usuarios = $db->prepare($query_usuarios);
                        $stmt_usuarios->execute();
                        $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($usuarios as $usuario):
                        ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo $usuario['nome']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td><?php echo ucfirst($usuario['tipo']); ?></td>
                                <td><?php echo $usuario['telefone'] ?? 'N/A'; ?></td>
                                <td><?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?></td>
                                <td>
                                    <button class="btn btn-warning" onclick="editarUsuario(<?php echo $usuario['id']; ?>)">Editar</button>
                                    <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <button class="btn btn-danger" onclick="toggleUsuario(<?php echo $usuario['id']; ?>, <?php echo $usuario['ativo'] ? 0 : 1; ?>)">
                                            <?php echo $usuario['ativo'] ? 'Desativar' : 'Ativar'; ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ABA: RELATÓRIOS -->
        <div id="relatorios" class="tab-content">
            <h2>Relatórios e Estatísticas</h2>
            <div class="stats">
                <div class="stat-card">
                    <h3>Total de Relatos</h3>
                    <div class="stat-number"><?php echo $stats['total_relatos']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Taxa de Conclusão</h3>
                    <div class="stat-number">
                        <?php 
                        $taxa = $stats['total_relatos'] > 0 ? ($stats['concluidos'] / $stats['total_relatos']) * 100 : 0;
                        echo number_format($taxa, 1) . '%';
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="table-container">
                <h3>Relatos por Tipo de Serviço</h3>
                <?php
                $query_servicos = "SELECT tipo_servico, COUNT(*) as total FROM relatos GROUP BY tipo_servico";
                $stmt_servicos = $db->prepare($query_servicos);
                $stmt_servicos->execute();
                $servicos_stats = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo de Serviço</th>
                            <th>Quantidade</th>
                            <th>Percentual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicos_stats as $servico): ?>
                            <tr>
                                <td><?php echo $servicos[$servico['tipo_servico']]; ?></td>
                                <td><?php echo $servico['total']; ?></td>
                                <td>
                                    <?php 
                                    $percentual = ($servico['total'] / $stats['total_relatos']) * 100;
                                    echo number_format($percentual, 1) . '%';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL PARA AVALIAR RELATO -->
    <div id="modalAvaliacao" class="modal">
        <div class="modal-content">
            <h2>Avaliar Relato</h2>
            <div id="detalhesRelato"></div>
            <div class="form-group">
                <label class="form-label">Decisão:</label>
                <select class="form-select" id="decisaoAvaliacao">
                    <option value="aprovado">Aprovar e Encaminhar para Equipe</option>
                    <option value="encaminhar">Encaminhar para Superior</option>
                    <option value="cancelado">Rejeitar Relato</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Observações:</label>
                <textarea class="form-textarea" id="observacaoAvaliacao" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Equipe para Atendimento:</label>
                <select class="form-select" id="equipeAtendimento">
                    <option value="">Selecione uma equipe</option>
                    <?php foreach ($equipes as $equipe): ?>
                        <option value="<?php echo $equipe['id']; ?>">
                            <?php echo $equipe['motorista_nome'] . ' - ' . $equipe['placa']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <button class="btn btn-warning" onclick="fecharModal('modalAvaliacao')">Cancelar</button>
                <button class="btn btn-success" onclick="salvarAvaliacao()">Salvar Avaliação</button>
            </div>
        </div>
    </div>

    <!-- MODAL NOVO USUÁRIO -->
    <div id="modalNovoUsuario" class="modal">
        <div class="modal-content">
            <h2>Adicionar Novo Usuário</h2>
            <form id="formNovoUsuario">
                <div class="form-group">
                    <label class="form-label">Nome Completo:</label>
                    <input type="text" class="form-input" id="novoUsuarioNome" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">E-mail:</label>
                    <input type="email" class="form-input" id="novoUsuarioEmail" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Senha:</label>
                    <input type="password" class="form-input" id="novoUsuarioSenha" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Usuário:</label>
                    <select class="form-select" id="novoUsuarioTipo" required>
                        <option value="">Selecione o tipo</option>
                        <option value="admin">Administrador</option>
                        <option value="motorista">Motorista</option>
                        <option value="funcionario">Funcionário</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telefone:</label>
                    <input type="text" class="form-input" id="novoUsuarioTelefone" placeholder="(14) 99999-9999">
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <button type="button" class="btn btn-warning" onclick="fecharModal('modalNovoUsuario')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Criar Usuário</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let relatoAtual = null;

        function showTab(tabName) {
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            document.querySelector(`.nav-tab[onclick="showTab('${tabName}')"]`).classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        function verDetalhesRelato(relatoId) {
            fetch(`api.php?action=get_relato&id=${relatoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        relatoAtual = data.data;
                        const relato = data.data;
                        
                        document.getElementById('detalhesRelato').innerHTML = `
                            <div class="form-group">
                                <strong>Localização:</strong> ${relato.bairro}, ${relato.rua} ${relato.numero || ''}
                            </div>
                            <div class="form-group">
                                <strong>Nível de Emergência:</strong> ${relato.nivel_emergencia}
                            </div>
                            <div class="form-group">
                                <strong>Tipo de Serviço:</strong> ${relato.tipo_servico}
                            </div>
                            <div class="form-group">
                                <strong>Descrição:</strong> ${relato.descricao}
                            </div>
                            ${relato.foto_path ? `<div class="form-group">
                                <strong>Foto:</strong><br>
                                <img src="${relato.foto_path}" class="photo-preview" alt="Foto do relato">
                            </div>` : ''}
                        `;
                        
                        document.getElementById('modalAvaliacao').style.display = 'block';
                    }
                });
        }

        function salvarAvaliacao() {
            const decisao = document.getElementById('decisaoAvaliacao').value;
            const observacao = document.getElementById('observacaoAvaliacao').value;
            const equipeId = document.getElementById('equipeAtendimento').value;

            if (decisao === 'aprovado' && !equipeId) {
                alert('Selecione uma equipe para atendimento');
                return;
            }

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'avaliar_relato',
                    relato_id: relatoAtual.id,
                    decisao: decisao,
                    observacao: observacao,
                    equipe_id: equipeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Avaliação salva com sucesso!');
                    fecharModal('modalAvaliacao');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        }

        // Função para abrir modal de novo usuário
        function abrirModalNovoUsuario() {
            document.getElementById('modalNovoUsuario').style.display = 'block';
            document.getElementById('formNovoUsuario').reset();
        }

        // Formulário de novo usuário
        document.getElementById('formNovoUsuario').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const dados = {
                nome: document.getElementById('novoUsuarioNome').value,
                email: document.getElementById('novoUsuarioEmail').value,
                senha: document.getElementById('novoUsuarioSenha').value,
                tipo: document.getElementById('novoUsuarioTipo').value,
                telefone: document.getElementById('novoUsuarioTelefone').value
            };

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'criar_usuario',
                    ...dados
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Usuário criado com sucesso!');
                    fecharModal('modalNovoUsuario');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        });

        function fecharModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Funções auxiliares
        function editarUsuario(usuarioId) {
            alert('Editar usuário ' + usuarioId + ' - Funcionalidade em desenvolvimento');
        }

        function toggleUsuario(usuarioId, novoStatus) {
            if (confirm(`Deseja ${novoStatus ? 'ativar' : 'desativar'} este usuário?`)) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'toggle_usuario',
                        usuario_id: usuarioId,
                        ativo: novoStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuário atualizado com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        function abrirModalNovaEquipe() {
            alert('Nova equipe - Funcionalidade em desenvolvimento');
        }

        function editarEquipe(equipeId) {
            alert('Editar equipe ' + equipeId + ' - Funcionalidade em desenvolvimento');
        }

        function desativarEquipe(equipeId) {
            if (confirm('Deseja desativar esta equipe?')) {
                alert('Desativar equipe ' + equipeId + ' - Funcionalidade em desenvolvimento');
            }
        }

        function encaminharSuperior(relatoId) {
            if (confirm('Encaminhar este relato para superior?')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'encaminhar_superior',
                        relato_id: relatoId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Relato encaminhado com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>