<?php
include '../../includes/config.php';
requireAuth();

// Carregar processos para o dropdown
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.numero_processo, c.nome_razao_social 
        FROM processos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        WHERE p.status NOT IN ('rejeitado')
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
            INSERT INTO controlo_qualidade 
            (processo_id, produto_tipo, data_amostragem, laboratorio, parametros_avaliados, resultado, tecnico_responsavel)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $parametros = [
            'octanagem' => $_POST['octanagem'] ?? null,
            'teor_enxofre' => $_POST['teor_enxofre'] ?? null,
            'densidade' => $_POST['densidade'] ?? null,
            'viscosidade' => $_POST['viscosidade'] ?? null
        ];
        
        $stmt->execute([
            $_POST['processo_id'],
            $_POST['produto_tipo'],
            $_POST['data_amostragem'],
            $_POST['laboratorio'],
            json_encode($parametros),
            $_POST['resultado'],
            $_POST['tecnico_responsavel']
        ]);
        
        // Se não conforme, criar alerta
        if ($_POST['resultado'] == 'nao_conforme') {
            $amostra_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("
                INSERT INTO alertas_qualidade 
                (amostra_id, severidade, descricao) 
                VALUES (?, 'alta', 'Produto não conforme nos parâmetros de qualidade')
            ");
            $stmt->execute([$amostra_id]);
        }
        
        // Registrar atividade
        registrarAtividade('AMOSTRA_REGISTADA', 'Nova amostra de qualidade registada', 'qualidade');
        
        $_SESSION['success_message'] = 'Amostra registada com sucesso!';
        header('Location: index.php');
        exit;
        
    } catch (PDOException $e) {
        $error = "Erro ao registar amostra: " . $e->getMessage();
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
    <title>Registar Amostra - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-vial"></i> Registar Amostra</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 800px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-flask"></i> Registar Nova Amostra
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
                                <label>Tipo de Produto *</label>
                                <select name="produto_tipo" class="form-control" required>
                                    <option value="">Selecione o produto</option>
                                    <option value="gasolina_auto" <?php echo ($_POST['produto_tipo'] ?? '') == 'gasolina_auto' ? 'selected' : ''; ?>>Gasolina Automóvel</option>
                                    <option value="gasoleo" <?php echo ($_POST['produto_tipo'] ?? '') == 'gasoleo' ? 'selected' : ''; ?>>Gasóleo</option>
                                    <option value="GPL" <?php echo ($_POST['produto_tipo'] ?? '') == 'GPL' ? 'selected' : ''; ?>>GPL</option>
                                    <option value="gasolina_aviacao" <?php echo ($_POST['produto_tipo'] ?? '') == 'gasolina_aviacao' ? 'selected' : ''; ?>>Gasolina Aviação</option>
                                    <option value="petroleo_iluminacao" <?php echo ($_POST['produto_tipo'] ?? '') == 'petroleo_iluminacao' ? 'selected' : ''; ?>>Petróleo Iluminação</option>
                                    <option value="oleo_combustivel" <?php echo ($_POST['produto_tipo'] ?? '') == 'oleo_combustivel' ? 'selected' : ''; ?>>Óleo Combustível</option>
                                </select>
                            </div>
                            <div class="form-group-full">
                                <label>Data de Amostragem *</label>
                                <input type="date" name="data_amostragem" class="form-control" required 
                                       value="<?php echo $_POST['data_amostragem'] ?? date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Laboratório *</label>
                                <input type="text" name="laboratorio" class="form-control" required 
                                       value="<?php echo $_POST['laboratorio'] ?? ''; ?>"
                                       placeholder="Nome do laboratório">
                            </div>
                            <div class="form-group-full">
                                <label>Resultado *</label>
                                <select name="resultado" class="form-control" required>
                                    <option value="">Selecione o resultado</option>
                                    <option value="conforme" <?php echo ($_POST['resultado'] ?? '') == 'conforme' ? 'selected' : ''; ?>>Conforme</option>
                                    <option value="nao_conforme" <?php echo ($_POST['resultado'] ?? '') == 'nao_conforme' ? 'selected' : ''; ?>>Não Conforme</option>
                                    <option value="condicional" <?php echo ($_POST['resultado'] ?? '') == 'condicional' ? 'selected' : ''; ?>>Condicional</option>
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
                        </div>
                        
                        <h3 style="color: var(--azul-petroleo); margin: 25px 0 15px 0;">
                            <i class="fas fa-chart-line"></i> Parâmetros de Qualidade
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Octanagem (RON)</label>
                                <input type="number" step="0.1" name="octanagem" class="form-control" 
                                       value="<?php echo $_POST['octanagem'] ?? ''; ?>"
                                       placeholder="Ex: 95.0">
                            </div>
                            <div class="form-group-full">
                                <label>Teor de Enxofre (ppm)</label>
                                <input type="number" step="0.1" name="teor_enxofre" class="form-control" 
                                       value="<?php echo $_POST['teor_enxofre'] ?? ''; ?>"
                                       placeholder="Ex: 10.0">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Densidade (kg/m³)</label>
                                <input type="number" step="0.1" name="densidade" class="form-control" 
                                       value="<?php echo $_POST['densidade'] ?? ''; ?>"
                                       placeholder="Ex: 830.5">
                            </div>
                            <div class="form-group-full">
                                <label>Viscosidade (cSt)</label>
                                <input type="number" step="0.1" name="viscosidade" class="form-control" 
                                       value="<?php echo $_POST['viscosidade'] ?? ''; ?>"
                                       placeholder="Ex: 3.2">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='index.php'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-save"></i> Registar Amostra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>