<?php
include '../../includes/config.php';
requireAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Carregar processos para o dropdown
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.numero_processo, c.nome_razao_social 
        FROM processos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $processos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar processos: " . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("SELECT * FROM vistorias WHERE id = ?");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE vistorias 
            SET processo_id = ?, data_vistoria = ?, tecnico_responsavel = ?, 
                tipo_vistoria = ?, local_vistoria = ?, resultado = ?, 
                observacoes = ?, status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['processo_id'],
            $_POST['data_vistoria'],
            $_POST['tecnico_responsavel'],
            $_POST['tipo_vistoria'],
            $_POST['local_vistoria'],
            $_POST['resultado'] ?: null,
            $_POST['observacoes'],
            $_POST['status'],
            $id
        ]);
        
        // Registrar atividade
        registrarAtividade('VISTORIA_EDITADA', 'Vistoria editada: #' . $id, 'vistorias');
        
        $_SESSION['success_message'] = 'Vistoria actualizada com sucesso!';
        header('Location: detalhes.php?id=' . $id);
        exit;
        
    } catch (PDOException $e) {
        $error = "Erro ao actualizar vistoria: " . $e->getMessage();
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
    <title>Editar Vistoria - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-edit"></i> Editar Vistoria</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-edit"></i> Editar Vistoria #<?php echo $vistoria['id']; ?>
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
                                <label>Processo *</label>
                                <select name="processo_id" class="form-control" required>
                                    <option value="">Selecione o processo</option>
                                    <?php foreach ($processos as $processo): ?>
                                        <option value="<?php echo $processo['id']; ?>" <?php echo ($vistoria['processo_id'] == $processo['id'] || ($_POST['processo_id'] ?? '') == $processo['id']) ? 'selected' : ''; ?>>
                                            <?php echo safe_html($processo['numero_processo']) . ' - ' . safe_html($processo['nome_razao_social']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Data e Hora da Vistoria *</label>
                                <input type="datetime-local" name="data_vistoria" class="form-control" required 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($vistoria['data_vistoria'])); ?>">
                            </div>
                            <div class="form-group-full">
                                <label>Tipo de Vistoria *</label>
                                <select name="tipo_vistoria" class="form-control" required>
                                    <option value="inicial" <?php echo ($vistoria['tipo_vistoria'] == 'inicial' || ($_POST['tipo_vistoria'] ?? '') == 'inicial') ? 'selected' : ''; ?>>Inicial</option>
                                    <option value="periodica" <?php echo ($vistoria['tipo_vistoria'] == 'periodica' || ($_POST['tipo_vistoria'] ?? '') == 'periodica') ? 'selected' : ''; ?>>Periódica</option>
                                    <option value="extraordinaria" <?php echo ($vistoria['tipo_vistoria'] == 'extraordinaria' || ($_POST['tipo_vistoria'] ?? '') == 'extraordinaria') ? 'selected' : ''; ?>>Extraordinária</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Técnico Responsável *</label>
                                <input type="text" name="tecnico_responsavel" class="form-control" required 
                                       value="<?php echo safe_html($vistoria['tecnico_responsavel']); ?>">
                            </div>
                            <div class="form-group-full">
                                <label>Local da Vistoria *</label>
                                <input type="text" name="local_vistoria" class="form-control" required 
                                       value="<?php echo safe_html($vistoria['local_vistoria']); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Resultado</label>
                                <select name="resultado" class="form-control">
                                    <option value="">Selecione o resultado</option>
                                    <option value="aprovado" <?php echo ($vistoria['resultado'] == 'aprovado' || ($_POST['resultado'] ?? '') == 'aprovado') ? 'selected' : ''; ?>>Aprovado</option>
                                    <option value="reprovado" <?php echo ($vistoria['resultado'] == 'reprovado' || ($_POST['resultado'] ?? '') == 'reprovado') ? 'selected' : ''; ?>>Reprovado</option>
                                    <option value="condicionado" <?php echo ($vistoria['resultado'] == 'condicionado' || ($_POST['resultado'] ?? '') == 'condicionado') ? 'selected' : ''; ?>>Condicionado</option>
                                </select>
                            </div>
                            <div class="form-group-full">
                                <label>Status *</label>
                                <select name="status" class="form-control" required>
                                    <option value="pendente" <?php echo ($vistoria['status'] == 'pendente' || ($_POST['status'] ?? '') == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="realizada" <?php echo ($vistoria['status'] == 'realizada' || ($_POST['status'] ?? '') == 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                    <option value="cancelada" <?php echo ($vistoria['status'] == 'cancelada' || ($_POST['status'] ?? '') == 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Observações</label>
                                <textarea name="observacoes" class="form-control" rows="4"><?php echo safe_html($vistoria['observacoes']); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='detalhes.php?id=<?php echo $vistoria['id']; ?>'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-save"></i> Actualizar Vistoria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>