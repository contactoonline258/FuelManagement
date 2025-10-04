<?php
include '../../includes/config.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vistorias - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-clipboard-check"></i> Gestão de Vistorias</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <!-- Barra de Ações -->
                <div class="quick-actions">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2><i class="fas fa-calendar-alt"></i> Vistorias Agendadas</h2>
                        <button onclick="location.href='agendar.php'" class="btn-cta">
                            <i class="fas fa-calendar-plus"></i> Agendar Vistoria
                        </button>
                    </div>
                </div>

                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT v.*, p.numero_processo, c.nome_razao_social 
                        FROM vistorias v 
                        LEFT JOIN processos p ON v.processo_id = p.id 
                        LEFT JOIN clientes c ON p.cliente_id = c.id 
                        ORDER BY v.data_vistoria DESC
                    ");
                    $stmt->execute();
                    $vistorias = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Erro ao carregar vistorias: " . $e->getMessage() . "</div>";
                    $vistorias = [];
                }
                ?>

                <!-- Tabela de Vistorias -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Cliente</th>
                                    <th>Data Vistoria</th>
                                    <th>Técnico</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Resultado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($vistorias)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 30px; color: var(--cinza-medio);">
                                            <i class="fas fa-clipboard-check" style="font-size: 3em; margin-bottom: 15px; display: block; color: var(--cinza-claro);"></i>
                                            Nenhuma vistoria encontrada
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($vistorias as $vistoria): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($vistoria['numero_processo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($vistoria['nome_razao_social']); ?></td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($vistoria['data_vistoria'])); ?>
                                            <?php if (strtotime($vistoria['data_vistoria']) < time() && $vistoria['status'] == 'pendente'): ?>
                                                <br><small style="color: var(--vermelho);">Atrasada</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($vistoria['tecnico_responsavel']); ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo ucfirst($vistoria['tipo_vistoria']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo getStatusBadge($vistoria['status']); ?>
                                        </td>
                                        <td>
                                            <?php if ($vistoria['resultado']): ?>
                                                <span class="badge badge-<?php echo $vistoria['resultado'] == 'aprovado' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($vistoria['resultado']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
                                                <button onclick="location.href='detalhes.php?id=<?php echo $vistoria['id']; ?>'" 
                                                        class="btn btn-small btn-primary" title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/app.js"></script>
</body>
</html>