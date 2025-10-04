<?php
include '../../includes/config.php';
requireAuth();

$processo_id = $_GET['processo_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $upload_dir = '../../uploads/documentos/';
        
        // Criar diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $nome_exibicao = $_POST['nome_arquivo']; // Nome para exibição
        $arquivo_temp = $_FILES['arquivo']['tmp_name'];
        $nome_original = $_FILES['arquivo']['name']; // Nome original do arquivo
        $tamanho_arquivo = $_FILES['arquivo']['size'];
        $tipo_arquivo = $_FILES['arquivo']['type'];
        
        // Validar tipo de arquivo
        $tipos_permitidos = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png'];
        
        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            throw new Exception('Tipo de arquivo não permitido. Apenas PDF, Word, Excel, JPEG e PNG são aceites.');
        }
        
        // Validar tamanho (máximo 10MB)
        if ($tamanho_arquivo > 10 * 1024 * 1024) {
            throw new Exception('Arquivo muito grande. Tamanho máximo permitido: 10MB.');
        }
        
        // Gerar nome único para o arquivo (mantém a extensão original)
        $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
        $nome_arquivo_unico = uniqid() . '.' . $extensao;
        $caminho_arquivo = $upload_dir . $nome_arquivo_unico;
        
        // Mover arquivo para o diretório de uploads
        if (!move_uploaded_file($arquivo_temp, $caminho_arquivo)) {
            throw new Exception('Erro ao fazer upload do arquivo.');
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO documentos 
            (processo_id, nome_arquivo, caminho_arquivo, tipo_documento, categoria, tamanho_arquivo, obrigatorio, upload_por)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $processo_id ?: null,
            $nome_exibicao, // Usa o nome de exibição
            $caminho_arquivo,
            $_POST['tipo_documento'],
            $_POST['categoria'],
            $tamanho_arquivo,
            $_POST['obrigatorio'] ?? 0,
            $_SESSION['user_name']
        ]);
        
        registrarAtividade('DOCUMENTO_UPLOAD', 'Documento carregado: ' . $nome_exibicao, 'documentos');
        
        $_SESSION['success_message'] = 'Documento carregado com sucesso!';
        header('Location: index.php' . ($processo_id ? '?processo_id=' . $processo_id : ''));
        exit;
        
    } catch (Exception $e) {
        $error = "Erro ao carregar documento: " . $e->getMessage();
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
    <title>Upload Documento - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-upload"></i> Upload de Documento</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <div class="card" style="max-width: 600px; margin: 0 auto;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h2 style="color: var(--azul-petroleo);">
                            <i class="fas fa-cloud-upload-alt"></i> Carregar Novo Documento
                        </h2>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" style="padding: 25px;">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($processo_id): ?>
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Processo Associado</label>
                                <input type="text" class="form-control" value="PROC-<?php echo str_pad($processo_id, 4, '0', STR_PAD_LEFT); ?>" readonly>
                                <input type="hidden" name="processo_id" value="<?php echo $processo_id; ?>">
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Nome do Documento (para exibição) *</label>
                                <input type="text" name="nome_arquivo" class="form-control" required 
                                       placeholder="Ex: Licença Operacional, Relatório Técnico..."
                                       value="<?php echo $_POST['nome_arquivo'] ?? ''; ?>">
                                <small style="color: var(--cinza-medio);">
                                    Este será o nome exibido no sistema
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Tipo de Documento *</label>
                                <select name="tipo_documento" class="form-control" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="PDF" <?php echo ($_POST['tipo_documento'] ?? '') == 'PDF' ? 'selected' : ''; ?>>PDF</option>
                                    <option value="Word" <?php echo ($_POST['tipo_documento'] ?? '') == 'Word' ? 'selected' : ''; ?>>Word</option>
                                    <option value="Excel" <?php echo ($_POST['tipo_documento'] ?? '') == 'Excel' ? 'selected' : ''; ?>>Excel</option>
                                    <option value="Imagem" <?php echo ($_POST['tipo_documento'] ?? '') == 'Imagem' ? 'selected' : ''; ?>>Imagem</option>
                                    <option value="Outro" <?php echo ($_POST['tipo_documento'] ?? '') == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>
                            <div class="form-group-full">
                                <label>Categoria *</label>
                                <select name="categoria" class="form-control" required>
                                    <option value="">Selecione a categoria</option>
                                    <option value="legal" <?php echo ($_POST['categoria'] ?? '') == 'legal' ? 'selected' : ''; ?>>Legal</option>
                                    <option value="tecnico" <?php echo ($_POST['categoria'] ?? '') == 'tecnico' ? 'selected' : ''; ?>>Técnico</option>
                                    <option value="certificado" <?php echo ($_POST['categoria'] ?? '') == 'certificado' ? 'selected' : ''; ?>>Certificado</option>
                                    <option value="outro" <?php echo ($_POST['categoria'] ?? '') == 'outro' ? 'selected' : ''; ?>>Outro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>Arquivo *</label>
                                <input type="file" name="arquivo" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <small style="color: var(--cinza-medio);">
                                    <i class="fas fa-info-circle"></i> Formatos aceites: PDF, Word, Excel, JPEG, PNG (Máx: 10MB)
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-full">
                                <label>
                                    <input type="checkbox" name="obrigatorio" value="1" <?php echo ($_POST['obrigatorio'] ?? '') ? 'checked' : ''; ?>>
                                    Documento Obrigatório
                                </label>
                                <small style="color: var(--cinza-medio); display: block;">
                                    Marque se este documento é obrigatório por lei
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" onclick="location.href='index.php<?php echo $processo_id ? '?processo_id=' . $processo_id : ''; ?>'" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn-cta">
                                <i class="fas fa-upload"></i> Carregar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>