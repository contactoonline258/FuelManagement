<?php
// dashboard.php
include 'includes/config.php';
requireAuth();

// Estatísticas para o dashboard
try {
    // Processos por status
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM processos GROUP BY status");
    $stmt->execute();
    $processos_status = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Total de clientes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE status = 'ativo'");
    $stmt->execute();
    $total_clientes = $stmt->fetchColumn();
    
    // Vistorias pendentes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vistorias WHERE status = 'pendente'");
    $stmt->execute();
    $vistorias_pendentes = $stmt->fetchColumn();
    
    // Amostras não conformes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM controlo_qualidade WHERE resultado = 'nao_conforme'");
    $stmt->execute();
    $amostras_nao_conformes = $stmt->fetchColumn();
    
    // Atividades recentes
    $stmt = $pdo->prepare("
        SELECT a.*, u.nome_completo 
        FROM atividades_sistema a 
        LEFT JOIN users u ON a.usuario_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $atividades_recentes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Erro ao carregar dados do dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PETROL CHECK</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2><i class="fas fa-gas-pump"></i> PETROL CHECK</h2>
                <small>Consultoria e Assessoria</small>
            </div>
            <nav class="menu">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="modules/clientes/">
                    <i class="fas fa-users"></i> Clientes
                </a>
                <a href="modules/processos/">
                    <i class="fas fa-file-contract"></i> Processos
                </a>
                <a href="modules/vistorias/">
                    <i class="fas fa-clipboard-check"></i> Vistorias
                </a>
                <a href="modules/qualidade/">
                    <i class="fas fa-flask"></i> Controlo de Qualidade
                </a>
                <a href="modules/reservas/">
                    <i class="fas fa-database"></i> Reservas Estratégicas
                </a>
                <a href="modules/documentos/">
                    <i class="fas fa-folder"></i> Documentos
                </a>
                <a href="modules/relatorios/">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
                <a href="modules/consultoria/">
                    <i class="fas fa-handshake"></i> Consultoria
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <h1>Dashboard - Sistema de Gestão</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                    <span class="badge badge-success" style="margin-left: 10px;"><?php echo ucfirst($_SESSION['user_role']); ?></span>
                </div>
            </header>

            <div class="content-area">
                <!-- Call to Action -->
                <div class="quick-actions" style="background: linear-gradient(135deg, var(--azul-petroleo) 0%, var(--azul-petroleo-escuro) 100%); color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <h2 style="color: white; margin-bottom: 10px;">Sistema Conectado ao MySQL</h2>
                            <p style="color: rgba(255,255,255,0.9); margin-bottom: 20px;">
                                Base de dados petrol_check conectada com sucesso.
                            </p>
                        </div>
                        <button onclick="location.href='modules/consultoria/solicitar.php'" class="btn-cta">
                            <i class="fas fa-phone-alt"></i> Solicite uma Consultoria
                        </button>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card fade-in">
                        <h3><i class="fas fa-file-contract" style="color: var(--dourado);"></i> Processos Ativos</h3>
                        <span class="stat-number" id="processos-ativos"><?php echo array_sum($processos_status); ?></span>
                        <div class="stat-trend trend-positive">
                            <i class="fas fa-database"></i> Dados em MySQL
                        </div>
                    </div>
                    <div class="stat-card fade-in">
                        <h3><i class="fas fa-clipboard-list" style="color: var(--dourado);"></i> Vistorias Pendentes</h3>
                        <span class="stat-number" id="vistorias-pendentes"><?php echo $vistorias_pendentes; ?></span>
                        <div class="stat-trend trend-warning">
                            <i class="fas fa-clock"></i> Requer Atenção
                        </div>
                    </div>
                    <div class="stat-card fade-in">
                        <h3><i class="fas fa-building" style="color: var(--dourado);"></i> Clientes Ativos</h3>
                        <span class="stat-number" id="total-clientes"><?php echo $total_clientes; ?></span>
                        <div class="stat-trend trend-positive">
                            <i class="fas fa-arrow-up"></i> Base em Crescimento
                        </div>
                    </div>
                    <div class="stat-card fade-in">
                        <h3><i class="fas fa-flask" style="color: var(--dourado);"></i> Qualidade</h3>
                        <span class="stat-number" id="amostras-nao-conformes"><?php echo $amostras_nao_conformes; ?></span>
                        <div class="stat-trend <?php echo $amostras_nao_conformes > 0 ? 'trend-negative' : 'trend-positive'; ?>">
                            <i class="fas fa-<?php echo $amostras_nao_conformes > 0 ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                            <?php echo $amostras_nao_conformes > 0 ? 'Não Conformidades' : 'Tudo Conforme'; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h2><i class="fas fa-bolt" style="color: var(--dourado);"></i> Ações Rápidas</h2>
                    <div class="action-buttons">
                        <button onclick="location.href='modules/processos/novo.php'" class="btn-primary">
                            <i class="fas fa-plus-circle"></i> Novo Processo
                        </button>
                        <button onclick="location.href='modules/clientes/novo.php'" class="btn-secondary">
                            <i class="fas fa-user-plus"></i> Novo Cliente
                        </button>
                        <button onclick="location.href='modules/vistorias/agendar.php'" class="btn-success">
                            <i class="fas fa-calendar-plus"></i> Agendar Vistoria
                        </button>
                        <button onclick="location.href='modules/qualidade/registar_amostra.php'" class="btn-cta">
                            <i class="fas fa-vial"></i> Registar Amostra
                        </button>
                    </div>
                </div>

                <div class="grid grid-2">
                    <!-- Processos por Status -->
                    <div class="card">
                        <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                            <h3 style="color: var(--azul-petroleo);">
                                <i class="fas fa-chart-pie"></i> Processos por Status
                            </h3>
                        </div>
                        <div style="padding: 25px;">
                            <?php foreach ($processos_status as $status => $count): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 10px; background: var(--cinza-fundo); border-radius: 5px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php echo getStatusBadge($status); ?>
                                    <span><?php echo ucfirst($status); ?></span>
                                </div>
                                <strong style="color: var(--azul-petroleo);"><?php echo $count; ?></strong>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Atividades Recentes -->
                    <div class="card">
                        <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                            <h3 style="color: var(--azul-petroleo);">
                                <i class="fas fa-history"></i> Atividades Recentes
                            </h3>
                        </div>
                        <div style="padding: 25px;">
                            <?php if (empty($atividades_recentes)): ?>
                                <p style="text-align: center; color: var(--cinza-medio); padding: 20px;">
                                    Nenhuma atividade recente
                                </p>
                            <?php else: ?>
                                <?php foreach ($atividades_recentes as $atividade): ?>
                                <div class="activity-item">
                                    <strong><?php echo htmlspecialchars($atividade['acao']); ?></strong>
                                    <span><?php echo htmlspecialchars($atividade['descricao']); ?></span>
                                    <small>
                                        <?php echo date('d/m/Y H:i', strtotime($atividade['created_at'])); ?>
                                        <?php if ($atividade['nome_completo']): ?>
                                            • por <?php echo htmlspecialchars($atividade['nome_completo']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Processos Recentes -->
                <div class="card" style="margin-top: 30px;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h3 style="color: var(--azul-petroleo);">
                            <i class="fas fa-file-contract"></i> Processos Recentes
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nº Processo</th>
                                    <th>Cliente</th>
                                    <th>Tipo Licença</th>
                                    <th>Data Início</th>
                                    <th>Status</th>
                                    <th>Responsável</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT p.*, c.nome_razao_social 
                                    FROM processos p 
                                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                                    ORDER BY p.created_at DESC 
                                    LIMIT 5
                                ");
                                $stmt->execute();
                                $processos_recentes = $stmt->fetchAll();
                                
                                foreach ($processos_recentes as $processo):
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($processo['numero_processo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($processo['nome_razao_social']); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo ucfirst(str_replace('_', ' ', $processo['tipo_licenca'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($processo['data_inicio'])); ?></td>
                                    <td>
                                        <?php echo getStatusBadge($processo['status']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($processo['responsavel_tecnico']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-section">
                        <h3>PETROL CHECK</h3>
                        <p>Consultoria e Assessoria especializada em conformidade para o setor de combustíveis em Moçambique.</p>
                    </div>
                    <div class="footer-section">
                        <h3>Contactos</h3>
                        <a href="tel:+258841234567"><i class="fas fa-phone"></i> +258 84 226 2987</a>
                        <a href="mailto:info@petrolcheck.co.mz"><i class="fas fa-envelope"></i> petrolcheckmz@gmail.com</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> Quelimane, Moçambique</a>
                    </div>
                    <div class="footer-section">
                        <h3>Serviços</h3>
                        <a href="#">Licenciamento</a>
                        <a href="#">Vistorias Técnicas</a>
                        <a href="#">Consultoria</a>
                        <a href="#">Controlo de Qualidade</a>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2025 PETROL CHECK. Todos os direitos reservados. | MySQL Conectado</p>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        // Animar contadores
        document.addEventListener('DOMContentLoaded', function() {
            animateValue('processos-ativos', 0, <?php echo array_sum($processos_status); ?>, 1000);
            animateValue('vistorias-pendentes', 0, <?php echo $vistorias_pendentes; ?>, 1000);
            animateValue('total-clientes', 0, <?php echo $total_clientes; ?>, 1000);
            animateValue('amostras-nao-conformes', 0, <?php echo $amostras_nao_conformes; ?>, 1000);
        });

        function animateValue(id, start, end, duration) {
            const obj = document.getElementById(id);
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                obj.textContent = value;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    </script>
</body>
</html>