<?php
include '../../includes/config.php';
requireAuth();

// Carregar clientes para o dropdown
try {
    $stmt = $pdo->prepare("SELECT id, nome_razao_social FROM clientes WHERE status = 'ativo' ORDER BY nome_razao_social");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar clientes: " . $e->getMessage());
}

$servico_tipo = $_GET['servico'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO consultorias 
            (cliente_id, tipo_servico, descricao, status, data_solicitacao)
            VALUES (?, ?, ?, 'pendente', NOW())
        ");
        
        $stmt->execute([
            $_POST['cliente_id'],
            $_POST['tipo_servico'],
            $_POST['descricao']
        ]);
        
        // Registrar atividade
        registrarAtividade('CONSULTORIA_SOLICITADA', 'Nova solicitação de consultoria: ' . $_POST['tipo_servico'], 'consultoria');
        
        $_SESSION['success_message'] = 'Solicitação de consultoria enviada com sucesso!';
        header('Location: index.php');
        exit;
        
    } catch (PDOException $e) {
        $error = "Erro ao solicitar consultoria: " . $e->getMessage();
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
    <title>Solicitar Consultoria - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-handshake"></i> Solicitar Consultoria</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-clipboard-list"></i> Nova Solicitação de Consultoria
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
                                <label>Cliente *</label>
                                <select name="cliente_id" class="form-control" required>
                                    <option value="">Selecione o cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>" <?php echo ($_POST['cliente_id'] ?? '') == $cliente['id'] ? 'selected' : ''; ?>>
                                            <?php echo safe_html($cliente['nome_razao_social']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Tipo de Serviço *</label>
                                <select name="tipo_servico" class="form-control" required>
                                    <option value="">Selecione o tipo de serviço</option>
                                    <option value="legal" <?php echo ($servico_tipo == 'legal' || ($_POST['tipo_servico'] ?? '') == 'legal') ? 'selected' : ''; ?>>Consultoria Legal</option>
                                    <option value="tecnica" <?php echo ($servico_tipo == 'tecnica' || ($_POST['tipo_servico'] ?? '') == 'tecnica') ? 'selected' : ''; ?>>Consultoria Técnica</option>
                                    <option value="qualidade" <?php echo ($servico_tipo == 'qualidade' || ($_POST['tipo_servico'] ?? '') == 'qualidade') ? 'selected' : ''; ?>>Consultoria em Qualidade</option>
                                    <option value="completa" <?php echo ($_POST['tipo_servico'] ?? '') == 'completa' ? 'selected' : ''; ?>>Consultoria Completa</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Descrição Detalhada *</label>
                                <textarea name="descricao" class="form-control" rows="6" required 
                                          placeholder="Descreva detalhadamente a sua necessidade de consultoria..."><?php echo $_POST['descricao'] ?? ''; ?></textarea>
                                <small style="color: var(--cinza-medio);">
                                    Inclua informações como: objetivos, prazos, orçamento disponível, etc.
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='index.php'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-paper-plane"></i> Enviar Solicitação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>