<?php
include '../../includes/config.php';
requireAuth();

if (!isAdmin()) {
    $_SESSION['error_message'] = 'Sem permissão para atribuir consultores!';
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM consultorias WHERE id = ?");
    $stmt->execute([$id]);
    $consultoria = $stmt->fetch();
    
    if (!$consultoria) {
        $_SESSION['error_message'] = 'Solicitação de consultoria não encontrada!';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar consultoria: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE consultorias 
            SET consultor_responsavel = ?, status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['consultor_responsavel'],
            $_POST['status'],
            $id
        ]);
        
        // Registrar atividade
        registrarAtividade('CONSULTORIA_ATRIBUIDA', 'Consultoria atribuída: ' . $_POST['consultor_responsavel'], 'consultoria');
        
        $_SESSION['success_message'] = 'Consultor atribuído com sucesso!';
        header('Location: detalhes.php?id=' . $id);
        exit;
        
    } catch (PDOException $e) {
        $error = "Erro ao atribuir consultor: " . $e->getMessage();
    }
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
    <title>Atribuir Consultor - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-user-tag"></i> Atribuir Consultor</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 600px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-user-tag"></i> Atribuir Consultor à Solicitação #<?php echo $consultoria['id']; ?>
                        </h2>
                    </div>
                    
                    <form method="POST" style="padding: 25px;">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Consultor Responsável *</label>
                                <input type="text" name="consultor_responsavel" class="form-control" required 
                                       value="<?php echo safe_html($consultoria['consultor_responsavel']); ?>"
                                       placeholder="Nome do consultor responsável">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Status *</label>
                                <select name="status" class="form-control" required>
                                    <option value="pendente" <?php echo $consultoria['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="atribuida" <?php echo $consultoria['status'] == 'atribuida' ? 'selected' : ''; ?>>Atribuída</option>
                                    <option value="em_andamento" <?php echo $consultoria['status'] == 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                    <option value="concluida" <?php echo $consultoria['status'] == 'concluida' ? 'selected' : ''; ?>>Concluída</option>
                                    <option value="cancelada" <?php echo $consultoria['status'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='detalhes.php?id=<?php echo $consultoria['id']; ?>'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-save"></i> Atribuir Consultor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>