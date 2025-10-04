<?php
include '../../includes/config.php';
requireAuth();

// Carregar processos para o dropdown
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.numero_processo, c.nome_razao_social 
        FROM processos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        WHERE p.status NOT IN ('concluido', 'rejeitado')
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $processos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar processos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO vistorias 
            (processo_id, data_vistoria, tecnico_responsavel, tipo_vistoria, local_vistoria, observacoes, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pendente')
        ");
        
        $stmt->execute([
            $_POST['processo_id'],
            $_POST['data_vistoria'],
            $_POST['tecnico_responsavel'],
            $_POST['tipo_vistoria'],
            $_POST['local_vistoria'],
            $_POST['observacoes']
        ]);
        
        // Registrar atividade
        registrarAtividade('VISTORIA_CRIADA', 'Nova vistoria agendada', 'vistorias');
        
        $_SESSION['success_message'] = 'Vistoria agendada com sucesso!';
        header('Location: index.php');
        exit;
        
    } catch (PDOException $e) {
        $error = "Erro ao agendar vistoria: " . $e->getMessage();
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
    <title>Agendar Vistoria - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-calendar-plus"></i> Agendar Vistoria</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-clipboard-check"></i> Agendar Nova Vistoria
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
                                        <option value="<?php echo $processo['id']; ?>" <?php echo ($_POST['processo_id'] ?? '') == $processo['id'] ? 'selected' : ''; ?>>
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
                                       value="<?php echo $_POST['data_vistoria'] ?? ''; ?>">
                            </div>
                            <div class="form-group-full">
                                <label>Tipo de Vistoria *</label>
                                <select name="tipo_vistoria" class="form-control" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="inicial" <?php echo ($_POST['tipo_vistoria'] ?? '') == 'inicial' ? 'selected' : ''; ?>>Inicial</option>
                                    <option value="periodica" <?php echo ($_POST['tipo_vistoria'] ?? '') == 'periodica' ? 'selected' : ''; ?>>Periódica</option>
                                    <option value="extraordinaria" <?php echo ($_POST['tipo_vistoria'] ?? '') == 'extraordinaria' ? 'selected' : ''; ?>>Extraordinária</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Técnico Responsável *</label>
                                <input type="text" name="tecnico_responsavel" class="form-control" required 
                                       value="<?php echo $_POST['tecnico_responsavel'] ?? ''; ?>"
                                       placeholder="Nome do técnico responsável">
                            </div>
                            <div class="form-group-full">
                                <label>Local da Vistoria *</label>
                                <input type="text" name="local_vistoria" class="form-control" required 
                                       value="<?php echo $_POST['local_vistoria'] ?? ''; ?>"
                                       placeholder="Endereço/local da vistoria">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Observações</label>
                                <textarea name="observacoes" class="form-control" rows="4" 
                                          placeholder="Observações adicionais sobre a vistoria"><?php echo $_POST['observacoes'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='index.php'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-calendar-check"></i> Agendar Vistoria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>