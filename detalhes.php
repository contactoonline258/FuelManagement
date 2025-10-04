<?php
include '../../includes/config.php';
requireAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT v.*, p.numero_processo, c.nome_razao_social 
        FROM vistorias v 
        LEFT JOIN processos p ON v.processo_id = p.id 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        WHERE v.id = ?
    ");
    $stmt->execute([$id]);
    $vistoria = $stmt->fetch();
    
    if (!$vistoria) {
        $_SESSION['error_message'] = 'Vistoria não encontrada!';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar vistoria: " . $e->getMessage());
}

function safe_html($value) {
    return $value !== null ? htmlspecialchars($value) : '';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Vistoria - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-clipboard-check"></i> Detalhes da Vistoria</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 900px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-info-circle"></i> Vistoria #<?php echo $vistoria['id']; ?>
                        </h2>
                        <button onclick="location.href='editar.php?id=<?php echo $vistoria['id']; ?>'" class="btn-secondary">
                            <i class="fas fa-edit"></i> Editar Vistoria
                        </button>
                    </div>
                    
                    <div style="padding: 25px;">
                        <div class="grid grid-2">
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 15px;">
                                    <i class="fas fa-info-circle"></i> Informações da Vistoria
                                </h3>
                                <div style="background: var(--cinza-fundo); padding: 20px; border-radius: 8px;">
                                    <div style="margin-bottom: 15px;">
                                        <strong>Processo:</strong><br>
                                        <a href="../processos/detalhes.php?id=<?php echo $vistoria['processo_id']; ?>" class="badge badge-info">
                                            <?php echo safe_html($vistoria['numero_processo']); ?>
                                        </a>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <strong>Cliente:</strong><br>
                                        <?php echo safe_html($vistoria['nome_razao_social']); ?>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <strong>Tipo de Vistoria:</strong><br>
                                        <span class="badge badge-primary">
                                            <?php echo ucfirst($vistoria['tipo_vistoria']); ?>
                                        </span>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <strong>Status:</strong><br>
                                        <?php echo getStatusBadge($vistoria['status']); ?>
                                    </div>
                                    <div>
                                        <strong>Resultado:</strong><br>
                                        <?php if ($vistoria['resultado']): ?>
                                            <span class="badge badge-<?php echo $vistoria['resultado'] == 'aprovado' ? 'success' : ($vistoria['resultado'] == 'reprovado' ? 'danger' : 'warning'); ?>">
                                                <?php echo ucfirst($vistoria['resultado']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Pendente</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 15px;">
                                    <i class="fas fa-calendar-alt"></i> Datas e Local
                                </h3>
                                <div style="background: var(--cinza-fundo); padding: 20px; border-radius: 8px;">
                                    <div style="margin-bottom: 15px;">
                                        <strong><i class="fas fa-calendar"></i> Data da Vistoria:</strong><br>
                                        <?php echo date('d/m/Y H:i', strtotime($vistoria['data_vistoria'])); ?>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <strong><i class="fas fa-map-marker-alt"></i> Local:</strong><br>
                                        <?php echo safe_html($vistoria['local_vistoria']); ?>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <strong><i class="fas fa-user"></i> Técnico Responsável:</strong><br>
                                        <?php echo safe_html($vistoria['tecnico_responsavel']); ?>
                                    </div>
                                    <div>
                                        <strong><i class="fas fa-clock"></i> Data de Criação:</strong><br>
                                        <?php echo date('d/m/Y H:i', strtotime($vistoria['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($vistoria['observacoes']): ?>
                        <div style="margin-top: 25px;">
                            <h3 style="color: var(--azul-petroleo); margin-bottom: 15px;">
                                <i class="fas fa-sticky-note"></i> Observações
                            </h3>
                            <div style="background: var(--cinza-fundo); padding: 20px; border-radius: 8px;">
                                <?php echo nl2br(safe_html($vistoria['observacoes'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
                            <button onclick="location.href='index.php'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar à Lista
                            </button>
                            <button onclick="location.href='editar.php?id=<?php echo $vistoria['id']; ?>'" class="btn-cta">
                                <i class="fas fa-edit"></i> Editar Vistoria
                            </button>
                            <?php if (isAdmin()): ?>
                            <button onclick="confirmarExclusao(<?php echo $vistoria['id']; ?>)" class="btn-danger">
                                <i class="fas fa-trash"></i> Excluir Vistoria
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/app.js"></script>
    <script>
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir esta vistoria? Esta ação não pode ser desfeita.')) {
            window.location.href = 'excluir.php?id=' + id;
        }
    }
    </script>
</body>
</html>